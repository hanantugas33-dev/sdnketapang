<?php
require_once 'config.php';

$pdo = getDB();

$stmtFas = $pdo->query("
    SELECT id, nama, deskripsi, foto, jumlah, kondisi
    FROM fasilitas
    WHERE is_active = 1
    ORDER BY urutan ASC
");

$stmtEkskul = $pdo->query("
    SELECT id, nama, foto, hari, jam_mulai, jam_selesai, pembina, deskripsi
    FROM ekskul
    WHERE is_active = 1
    ORDER BY urutan ASC
");

jsonResponse([
    'fasilitas' => $stmtFas->fetchAll(),
    'ekskul'    => $stmtEkskul->fetchAll()
]);
