<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$body = json_decode(file_get_contents('php://input'), true);
if (!$body) {
    jsonResponse(['success' => false, 'message' => 'Data tidak valid'], 400);
}

$nama      = trim($body['nama'] ?? '');
$hp        = trim($body['hp'] ?? '');
$email     = trim($body['email'] ?? '');
$keperluan = trim($body['keperluan'] ?? '');
$pesan     = trim($body['pesan'] ?? '');

// Validasi server-side
if (!$nama || !$keperluan || !$pesan) {
    jsonResponse(['success' => false, 'message' => 'Nama, keperluan, dan pesan wajib diisi'], 422);
}
if (strlen($nama) > 150 || strlen($pesan) > 3000) {
    jsonResponse(['success' => false, 'message' => 'Input terlalu panjang'], 422);
}
if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['success' => false, 'message' => 'Format email tidak valid'], 422);
}

$ip = $_SERVER['REMOTE_ADDR'] ?? null;

$pdo = getDB();
$stmt = $pdo->prepare("
    INSERT INTO buku_tamu (nama, hp, email, keperluan, pesan, ip_address)
    VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->execute([$nama, $hp, $email, $keperluan, $pesan, $ip]);

jsonResponse([
    'success' => true,
    'message' => 'Pesan berhasil dikirim! Kami akan merespons dalam 1–2 hari kerja.',
    'id'      => $pdo->lastInsertId()
]);
