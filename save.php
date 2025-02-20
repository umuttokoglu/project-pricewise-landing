<?php
header("Content-Type: application/json"); // JSON yanıt döndür
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "deneme";

// Sadece POST isteklerine izin ver
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["message" => "Sadece POST isteği kabul edilir."]);
    exit;
}

// JSON verisini al ve doğrula
$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    http_response_code(400);
    echo json_encode(["message" => "Geçersiz JSON formatı."]);
    exit;
}

// Giriş verilerini kontrol et
if (empty($data['name']) || empty($data['email']) || empty($data['phone']) || empty($data['message'])) {
    http_response_code(400);
    echo json_encode(["message" => "Tüm alanlar zorunludur."]);
    exit;
}

// E-posta doğrulaması
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["message" => "Geçersiz e-posta adresi."]);
    exit;
}

// Telefon numarası sadece rakamlardan oluşmalı
if (!preg_match('/^[0-9]+$/', $data['phone'])) {
    http_response_code(400);
    echo json_encode(["message" => "Geçersiz telefon numarası."]);
    exit;
}

// Mesaj uzunluğu kontrolü (200 karakter sınırı)
if (strlen($data['message']) > 200) {
    http_response_code(400);
    echo json_encode(["message" => "Mesaj en fazla 200 karakter olmalıdır."]);
    exit;
}

// XSS ve zararlı kodlara karşı giriş verilerini temizle
$name = htmlspecialchars(strip_tags($data['name']));
$email = htmlspecialchars(strip_tags($data['email']));
$phone = htmlspecialchars(strip_tags($data['phone']));
$message = htmlspecialchars(strip_tags($data['message']));


// MySQL bağlantısını oluştur
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantıyı kontrol et
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["message" => "Bağlantı hatası: " . $conn->connect_error]);
    exit;
}

// SQL sorgusu
$sql = "INSERT INTO gelen_kutusu (name, eposta, numara, mesaj) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(["message" => "Veritabanı hatası: " . $conn->error]);
    exit;
}

$stmt->bind_param("ssss", $name, $email, $phone, $message);
if ($stmt->execute()) {
    http_response_code(201);
    echo json_encode(["message" => "Mesaj başarıyla gönderildi!"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Veritabanına kayıt sırasında bir hata oluştu!"]);
}

// Bağlantıyı kapat
$stmt->close();
$conn->close();
?>