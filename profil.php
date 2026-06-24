<?php
require_once 'config.php';

$pdo = getDB();
$limit = (int)($_GET['limit'] ?? 6);

$stmt = $pdo->prepare("
    SELECT id, nama_prestasi, deskripsi, tingkat, juara, medali, gambar, raih_oleh, tahun, kategori
    FROM prestasi
    ORDER BY is_featured DESC, tahun DESC, urutan ASC
    LIMIT ?
");
$stmt->execute([$limit]);

jsonResponse($stmt->fetchAll());
