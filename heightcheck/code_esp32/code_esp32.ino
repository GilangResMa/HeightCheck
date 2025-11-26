#include <WiFi.h>
#include <WebServer.h>
#include <HTTPClient.h>

// --- KONFIGURASI WIFI & SERVER ---
const char* ssid = "advan";
const char* password = "kontolbanget";

// IP Komputer Server (Tempat file PHP berada)
const char* phpServer = "http://10.14.115.150/project_iot/Project-IoT---HeightCheck/heightcheck/config/data_processing.php";

// Membuat Web Server di port 80
WebServer server(80);

// --- KONFIGURASI PIN ---
const int trigPin = 5;
const int echoPin = 18;
const int ledIndikator = 2; // LED Builtin atau eksternal untuk tanda aktif

// Variabel
long duration;
float distance;

void setup() {
  Serial.begin(115200);
  
  pinMode(trigPin, OUTPUT);
  pinMode(echoPin, INPUT);
  pinMode(ledIndikator, OUTPUT);

  // Koneksi WiFi
  WiFi.begin(ssid, password);
  Serial.print("Connecting to WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.print("Connected! IP ESP A: ");
  Serial.println(WiFi.localIP()); // <--- CATAT IP INI UNTUK ESP B & WEB

  // Definisi Route API
  // Jika ada yang akses http://ip-esp-a/ukur, jalankan fungsi handleUkur
  server.on("/ukur", handleUkur);
  
  server.begin();
  Serial.println("HTTP server started");
}

void loop() {
  server.handleClient(); // Wajib ada untuk mendengarkan request
}

// --- FUNGSI UTAMA ---
void handleUkur() {
  Serial.println("Perintah diterima! Memulai pengukuran...");
  digitalWrite(ledIndikator, HIGH); // Nyalakan LED tanda sibuk
  server.sendHeader("Access-Control-Allow-Origin", "*");

  // 1. Beritahu pengirim (ESP B / Web) bahwa perintah diterima
  server.send(200, "text/plain", "OK, Mengukur...");

  // 2. Baca Sensor Ultrasonik
  digitalWrite(trigPin, LOW);
  delayMicroseconds(2);
  digitalWrite(trigPin, HIGH);
  delayMicroseconds(10);
  digitalWrite(trigPin, LOW);

  duration = pulseIn(echoPin, HIGH);
  distance = duration * 0.034 / 2;
  
  Serial.print("Jarak: ");
  Serial.println(distance);

  // 3. Kirim Hasil ke Database (data_processing.php)
  // Hanya kirim jika jarak valid (misal 2cm - 400cm)
  if(distance > 2 && distance < 400) {
      if(WiFi.status() == WL_CONNECTED) {
        HTTPClient http;
        http.begin(phpServer);
        http.addHeader("Content-Type", "application/json");
        
        // Buat JSON payload
        String jsonPayload = "{\"jarak_sensor\":" + String(distance, 2) + "}";
        
        int httpResponseCode = http.POST(jsonPayload);
        
        if(httpResponseCode > 0) {
          String response = http.getString();
          Serial.println("Sukses kirim ke DB: " + response);
        } else {
          Serial.print("Error sending POST: ");
          Serial.println(httpResponseCode);
        }
        http.end();
      }
  } else {
    Serial.println("Jarak error/out of range");
  }

  digitalWrite(ledIndikator, LOW); // Matikan LED
}