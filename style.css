<?php
require_once 'config.php';

$pdo = getDB();
$jabatan = $_GET['jabatan'] ?? 'semua';

if ($jabatan !== 'semua') {
    $stmt = $pdo->prepare("
        SELECT id, nama, nip, jabatan, jabatan_singkat, mapel, bidang, kelas, foto
        FROM guru
        WHERE is_active = 1 AND jabatan_singkat = ?
        ORDER BY urutan ASC
    ");
    $stmt->execute([$jabatan]);
} else {
    $stmt = $pdo->prepare("
        SELECT id, nama, nip, jabatan, jabatan_singkat, mapel, bidang, kelas, foto
        FROM guru
        WHERE is_active = 1
        ORDER BY urutan ASC
    ");
    $stmt->execute();
}

jsonResponse($stmt->fetchAll());
