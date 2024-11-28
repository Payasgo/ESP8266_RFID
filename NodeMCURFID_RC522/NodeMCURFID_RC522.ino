#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <WiFiClient.h>
#include <SPI.h>
#include <MFRC522.h>

// Pin configuration
#define SS_PIN D2  // SDA / SS
#define RST_PIN D1 // RST
#define ON_BOARD_LED 2 // Onboard LED for status

MFRC522 mfrc522(SS_PIN, RST_PIN); // RFID instance

// WiFi credentials
const char* ssid = "yourwifiname";
const char* password = "password";

// Globals
HTTPClient http;
WiFiClient client;
String StrUID;

// Function prototypes
int getid();
void array_to_string(byte array[], unsigned int len, char buffer[]);

void setup() {
  Serial.begin(115200); 
  SPI.begin(); 
  mfrc522.PCD_Init(); 

  // Onboard LED setup
  pinMode(ON_BOARD_LED, OUTPUT);
  digitalWrite(ON_BOARD_LED, HIGH); 

  // Connect to WiFi
  Serial.println("Connecting to WiFi...");
  WiFi.begin(ssid, password);
  
  unsigned long startAttemptTime = millis();
  while (WiFi.status() != WL_CONNECTED && millis() - startAttemptTime < 30000) {
    Serial.print(".");
    digitalWrite(ON_BOARD_LED, LOW);
    delay(250);
    digitalWrite(ON_BOARD_LED, HIGH);
    delay(250);
  }

  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\nWiFi Connected!");
    Serial.print("IP Address: ");
    Serial.println(WiFi.localIP());
  } else {
    Serial.println("\nFailed to connect to WiFi.");
    while (true) {
      digitalWrite(ON_BOARD_LED, LOW);
      delay(100);
      digitalWrite(ON_BOARD_LED, HIGH);
      delay(100);
    }
  }

  Serial.println("Please tag a card or keychain to see the UID!");
}

void loop() {
  if (getid()) {
    digitalWrite(ON_BOARD_LED, LOW);

    String postData = "UIDresult=" + StrUID;

    http.begin(client, "http://ip_addr/NodeMCU_RC522_Mysql/getUID.php"); // Target server
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");

    int httpCode = http.POST(postData); 
    String payload = http.getString(); 

    // Logging responses
    Serial.println("UID Sent: " + StrUID);
    Serial.println("HTTP Response Code: " + String(httpCode));
    Serial.println("Server Response: " + payload);

    http.end(); 
    delay(1000);

    digitalWrite(ON_BOARD_LED, HIGH);
  }
}

// Get UID from RFID
int getid() {
  if (!mfrc522.PICC_IsNewCardPresent() || !mfrc522.PICC_ReadCardSerial()) {
    return 0;
  }

  byte readcard[4];
  char str[9]; // UID is 4 bytes, hence 8 characters + null terminator

  for (int i = 0; i < 4; i++) {
    readcard[i] = mfrc522.uid.uidByte[i];
  }

  array_to_string(readcard, 4, str);
  StrUID = str;

  Serial.print("Card UID: ");
  Serial.println(StrUID);

  mfrc522.PICC_HaltA();
  return 1;
}

// Convert array to string
void array_to_string(byte array[], unsigned int len, char buffer[]) {
  for (unsigned int i = 0; i < len; i++) {
    byte nib1 = (array[i] >> 4) & 0x0F;
    byte nib2 = (array[i] >> 0) & 0x0F;
    buffer[i * 2 + 0] = nib1 < 0xA ? '0' + nib1 : 'A' + nib1 - 0xA;
    buffer[i * 2 + 1] = nib2 < 0xA ? '0' + nib2 : 'A' + nib2 - 0xA;
  }
  buffer[len * 2] = '\0';
}
