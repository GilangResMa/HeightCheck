#include <Wire.h> 
#include <LiquidCrystal_I2C.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>  // Library ArduinoJson
// --- KONFIGURASI PIN ---
const int trigPin = 5;   // Pin Trigger
const int echoPin = 18;  // Pin Echo

//Konfigurasi LED
const int ledM = 25;
const int ledK = 26;
const int ledH = 27;

const int butt = 4;
volatile bool buttonPressed = false;
volatile bool isMeasuring = false;
volatile unsigned long last_interrupt_time = 0;

// --- KONFIGURASI LCD ---
LiquidCrystal_I2C lcd(0x27, 16, 2); 

const char* ssid = "No Signal!";                       // Nama WiFi Kamu
const char* password = "randuekuota";              // Password WiFI Kamu
const char* server = "http://172.26.7.12/HeightCheck/heightcheck/config/data_processing.php";  // IP PC kamu - PHP 
// const char* server = "http://10.238.92.150:3000/api";  // IP PC kamu - Express
const char* DEVICE_ID = "esp32-unit-001";             // (opsional) untuk ngasih tau Device aja

unsigned long lastSend = 0;
const unsigned long sendInterval = 15 * 1000UL; // kirim tiap 15 detik
// const unsigned long sendInterval = 5 * 1000UL; // kirim tiap 5 detik
unsigned long lastPoll = 0;
const unsigned long pollInterval = 2 * 1000UL; // cek perintah tiap 2 detik
// const unsigned long sendInterval = 1 * 1000UL; // cek perintah tiap 1 detik

// Variabel untuk kalkulasi
long duration;
int distance;

void IRAM_ATTR buttonISR() {
  unsigned long now = millis();
  if (now - last_interrupt_time > 300) {      // debounce 300ms
    isMeasuring = !isMeasuring;               // HANYA flip di sini
    last_interrupt_time = now;
  }
}

void setup() {
  Serial.begin(115200); // Untuk debug di Serial Monitor

  // Setup Pin Mode Ultrasonik
  pinMode(trigPin, OUTPUT); // Trig mengirim suara
  pinMode(echoPin, INPUT);  // Echo mendengar pantulan
  pinMode(ledM, OUTPUT);
  pinMode(ledK, OUTPUT);
  pinMode(ledH, OUTPUT);
  pinMode(butt, INPUT_PULLUP);

  // Setup LCD
  lcd.init();
  lcd.backlight();
  
  WiFi.begin(ssid, password);                    // Koneksi ke Hospot kita
	Serial.println("Connecting to WiFi...");       // Ngasih tau kalo lagi konek
	int retries = 0;
	while (WiFi.status() != WL_CONNECTED) {        // Handler untuk status
	  delay(1000);
	  Serial.print("Status: ");
	  Serial.println(WiFi.status());               // Kode Status cek "Contoh Status"
	  retries++;
	  if (retries > 20) {                          // Kalo gak konek konek langsung tampil error
	    Serial.println("Failed to connect to WiFi.");  
	    return; // keluar dari setup
	  }
	}
	Serial.println("Connected!");                  // Berhasil Connect
	Serial.println(WiFi.localIP());                // Nampilin IP ESP32

  isMeasuring = false;
  digitalWrite(ledM, LOW);

  // Tampilan Awal
  lcd.setCursor(0, 0);
  attachInterrupt(digitalPinToInterrupt(butt), buttonISR, FALLING);
  lcd.print("Sensor Jarak");
  lcd.setCursor(0, 1);
  lcd.print("Mulai...");
  delay(2000);
  lcd.clear();
}

void loop() {
  long duration;
  float distance;

  if (!isMeasuring) {
    static unsigned long lastBlink = 0;
    if (millis() - lastBlink >= 1000) {
      digitalWrite(ledM, !digitalRead(ledM));  // blink LED merah
      lastBlink = millis();
    }

    lcd.setCursor(0,0);
    lcd.print("Tekan tombol   ");
    lcd.setCursor(0,1);
    lcd.print("untuk mulai    ");
    delay(100);
    return;                        // langsung keluar, jangan lanjut ke bawah
  }

  // —— MODE MEASURING (hanya masuk sini kalau tombol ditekan) ——
  lcd.clear();
  lcd.print("Mengukur...");

  // 1. Trigger ultrasonik
  digitalWrite(trigPin, LOW);
  delayMicroseconds(2);
  digitalWrite(trigPin, HIGH);
  delayMicroseconds(10);
  digitalWrite(trigPin, LOW);

  digitalWrite(ledK, HIGH);        // LED kuning = lagi baca sensor
  duration = pulseIn(echoPin, HIGH);
  distance = duration * 0.034 / 2;

  Serial.print("Jarak: ");
  Serial.print(distance);
  Serial.println(" cm");

  delay(1000);
  digitalWrite(ledK, LOW);

  // 2. Kirim ke server kalau jarak valid
  if (WiFi.status() == WL_CONNECTED && distance > 5 && distance < 400) {
    HTTPClient http;
    http.begin(server);
    http.addHeader("Content-Type", "application/json");

    String jsonPayload = "{\"jarak_sensor\":" + String(distance, 2) + "}";

    digitalWrite(ledH, HIGH);      // LED hijau = lagi kirim data
    int httpResponseCode = http.POST(jsonPayload);

    if (httpResponseCode > 0) {
      String response = http.getString();
      Serial.println(response);

      // Parsing tinggi_final (sama seperti kode kamu)
      int indexKey = response.indexOf("\"tinggi_final\":");
      if (indexKey > 0) {
        int startNum = indexKey + 15;
        int endNum = response.indexOf(",", startNum);
        if (endNum < 0) endNum = response.indexOf("}", startNum);
        String strTinggi = response.substring(startNum, endNum);

        lcd.clear();
        lcd.setCursor(0,0);
        lcd.print("Tinggi Anda:");
        lcd.setCursor(0,1);
        lcd.print(strTinggi);
        lcd.print(" cm");
        digitalWrite(ledH, LOW);
      }
    } else {
      lcd.clear();
      lcd.print("Error POST");
      Serial.println(httpResponseCode);
    }
    http.end();
  } else {
    lcd.clear();
    lcd.print("WiFi putus /");
    lcd.setCursor(0,1);
    lcd.print("Jarak error");
  }

  // Delay sebelum pengukuran berikutnya (bisa diatur)
  delay(10000);   // ukur tiap 3 detik saat mode aktif
}