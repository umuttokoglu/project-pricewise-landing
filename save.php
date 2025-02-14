<?php
header("Content-Type: application/json"); // JSON yanıt döndür
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "deneme";

// MySQL bağlantısını oluştur
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantıyı kontrol et
if ($conn->connect_error) {
    die(json_encode(["message" => "Bağlantı hatası: " . $conn->connect_error]));
}

// JSON verisini al
$data = json_decode(file_get_contents("php://input"), true);

// Verileri al
$name = $data['name'];
$email = $data['email'];
$phone = $data['phone'];
$message = $data['message'];

// SQL sorgusu
$sql = "INSERT INTO gelen_kutusu (name, eposta, numara, mesaj) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $name, $email, $phone, $message);

if ($stmt->execute()) {
    echo json_encode(["message" => "Mesaj başarıyla gönderildi!"]);
} else {
    echo json_encode(["message" => "Bir hata oluştu!"]);
}

// Bağlantıyı kapat
$stmt->close();
$conn->close();
?>