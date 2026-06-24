<?php
require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    http_response_code(400);
    jsonResponse(['error' => 'ID tidak valid']);
}

$pdo = getDB();

// Ambil berita berdasarkan ID
$stmt = $pdo->prepare("
    SELECT id, judul, slug, kategori, konten, ringkasan, gambar, icon,
           DATE_FORMAT(tanggal, '%d %M %Y') AS tanggal_format
    FROM berita
    WHERE id = ? AND status = 'publish'
    LIMIT 1
");
$stmt->execute([$id]);
$berita = $stmt->fetch();

if (!$berita) {
    http_response_code(404);
    jsonResponse(['error' => 'Berita tidak ditemukan']);
}

// Ambil berita terkait (kategori sama, beda ID)
$stmt2 = $pdo->prepare("
    SELECT id, judul, kategori, ringkasan, gambar, icon,
           DATE_FORMAT(tanggal, '%d %M %Y') AS tanggal_format
    FROM berita
    WHERE status = 'publish' AND kategori = ? AND id != ?
    ORDER BY tanggal DESC
    LIMIT 3
");
$stmt2->execute([$berita['kategori'], $id]);
$berita['terkait'] = $stmt2->fetchAll();

jsonResponse($berita);
