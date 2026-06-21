<?php
require_once 'config.php';

$pdo = getDB();
$kategori = $_GET['kategori'] ?? 'semua';
$limit = (int)($_GET['limit'] ?? 6);

if ($kategori !== 'semua' && in_array($kategori, ['berita', 'pengumuman', 'kegiatan'])) {
    $stmt = $pdo->prepare("
        SELECT id, judul, slug, kategori, ringkasan, gambar, icon,
               DATE_FORMAT(tanggal, '%d %M %Y') AS tanggal_format
        FROM berita
        WHERE status = 'publish' AND kategori = ?
        ORDER BY tanggal DESC
        LIMIT ?
    ");
    $stmt->execute([$kategori, $limit]);
} else {
    $stmt = $pdo->prepare("
        SELECT id, judul, slug, kategori, ringkasan, gambar, icon,
               DATE_FORMAT(tanggal, '%d %M %Y') AS tanggal_format
        FROM berita
        WHERE status = 'publish'
        ORDER BY tanggal DESC
        LIMIT ?
    ");
    $stmt->execute([$limit]);
}

jsonResponse($stmt->fetchAll());
