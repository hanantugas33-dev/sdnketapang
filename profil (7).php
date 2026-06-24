<?php
require_once 'config.php';

$pdo = getDB();
$kategori = $_GET['kategori'] ?? 'semua';
$limit = (int)($_GET['limit'] ?? 12);

if ($kategori !== 'semua') {
    $stmt = $pdo->prepare("
        SELECT id, judul, deskripsi, kategori, gambar, icon, is_large
        FROM galeri
        WHERE status = 'aktif' AND kategori = ?
        ORDER BY urutan ASC
        LIMIT ?
    ");
    $stmt->execute([$kategori, $limit]);
} else {
    $stmt = $pdo->prepare("
        SELECT id, judul, deskripsi, kategori, gambar, icon, is_large
        FROM galeri
        WHERE status = 'aktif'
        ORDER BY urutan ASC
        LIMIT ?
    ");
    $stmt->execute([$limit]);
}

jsonResponse($stmt->fetchAll());
