#include <Wire.h> 
#include <LiquidCrystal_I2C.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>  // Library ArduinoJson
// --- KONFIGURASI PIN ---
const int trigPin = 5;   // Pin Trigger
const int echoPin = 18;  // Pin Echo

// --- KONFIGURASI LCD ---
LiquidCrystal_I2C lcd(0x27, 16, 2); 

const char* ssid = "Lab Jaringan";                       // Nama WiFi Kamu
const char* password = "ifjaringan";              // Password WiFI Kamu
const char* server = "http://10.10.10.116/project_iot/Project-IoT---HeightCheck/heightcheck/config/data_processing.php";  // IP PC kamu - PHP 
// const char* server = "http://10.238.92.150:3000/api";  // IP PC kamu - Express
const char* DEVICE_ID = "esp32-unit-001";             // (opsional) untuk ngasih tau Device aja

unsigned long lastSend = 0;
const unsigned long sendInterval = 30 * 1000UL; // kirim tiap 15 detik
// const unsigned long sendInterval = 5 * 1000UL; // kirim tiap 5 detik
unsigned long lastPoll = 0;
const unsigned long pollInterval = 2 * 1000UL; // cek perintah tiap 2 detik
// const unsigned long sendInterval = 1 * 1000UL; // cek perintah tiap 1 detik

// Variabel untuk kalkulasi
long duration;
int distance;

void setup() {
  Serial.begin(115200); // Untuk debug di Serial Monitor

  // Setup Pin Mode Ultrasonik
  pinMode(trigPin, OUTPUT); // Trig mengirim suara
  pinMode(echoPin, INPUT);  // Echo mendengar pantulan

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

  // Tampilan Awal
  lcd.setCursor(0, 0);
  lcd.print("Sensor Jarak");
  lcd.setCursor(0, 1);
  lcd.print("Mulai...");
  delay(2000);
  lcd.clear();
}

void loop() {
  // 1. Baca Jarak (Ultrasonik)
  long duration;
  float distance;
  
  digitalWrite(trigPin, LOW);
  delayMicroseconds(2);
  digitalWrite(trigPin, HIGH);
  delayMicroseconds(10);
  digitalWrite(trigPin, LOW);
  
  duration = pulseIn(echoPin, HIGH);
  distance = duration * 0.034 / 2;

  Serial.print("Jarak: ");
  Serial.print(distance);
  Serial.println(" cm");

  // 2. Kirim Data (Hanya jika WiFi Connect & Jarak Valid)
  if(WiFi.status() == WL_CONNECTED && distance > 0 && distance < 400) {
    HTTPClient http;
    
    http.begin(server);
    http.addHeader("Content-Type", "application/json");
    String jsonPayload = "{\"jarak_sensor\":" + String(distance) + "}";
    
    int httpResponseCode = http.POST(jsonPayload);

    if (httpResponseCode > 0) {
      String response = http.getString();
      Serial.println(response); // Cek respons JSON dari server

      // Parsing Manual untuk mengambil "tinggi_final"
      // Format JSON: {..., "tinggi_final": 165.5, ...}
      int indexKey = response.indexOf("\"tinggi_final\":");
      if (indexKey > 0) {
        int startNum = indexKey + 15; // Panjang string key
        int endNum = response.indexOf(",", startNum); 
        if (endNum < 0) endNum = response.indexOf("}", startNum);
        
        String strTinggi = response.substring(startNum, endNum);
        
        // Tampilkan di LCD
        lcd.clear();
        lcd.setCursor(0, 0);
        lcd.print("Tinggi Anda:");
        lcd.setCursor(0, 1);
        lcd.print(strTinggi);
        lcd.print(" cm");
      } else {
        Serial.print("Error on sending POST: ");
        lcd.setCursor(0, 0);
        lcd.print("Error");
        Serial.println(httpResponseCode);
      }
    }
    http.end();
  } else {
    Serial.println("WiFi Disconnected atau Jarak Error");
  }


  // 3. JEDA 15 DETIK
  Serial.println("Tunggu 15 detik...");
  delay(15000); 
  lcd.clear();
}