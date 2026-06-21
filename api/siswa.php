<?php
require_once 'config.php';

header('Cache-Control: public, max-age=30');

$pdo    = getDB();
$action = $_GET['action'] ?? 'all';
$tahun  = $_GET['tahun']  ?? '';

// Total semua siswa (tahun ajaran terbaru)
if ($action === 'total') {
    $q = $pdo->query("
        SELECT COALESCE(SUM(laki_laki + perempuan), 0) AS total
        FROM data_siswa
        WHERE tahun_ajaran = (SELECT MAX(tahun_ajaran) FROM data_siswa)
    ");
    jsonResponse(['total' => (int)$q->fetchColumn()]);
}

// Semua kelas
$where = $tahun ? "WHERE tahun_ajaran = ?" : "WHERE tahun_ajaran = (SELECT MAX(tahun_ajaran) FROM data_siswa)";
$stmt  = $pdo->prepare("
    SELECT id, tahun_ajaran, kelas, tingkat, rombel,
           laki_laki, perempuan, (laki_laki + perempuan) AS total,
           wali_kelas, catatan
    FROM data_siswa $where
    ORDER BY tingkat, rombel
");
$tahun ? $stmt->execute([$tahun]) : $stmt->execute();

$rows      = $stmt->fetchAll();
$totalL    = array_sum(array_column($rows, 'laki_laki'));
$totalP    = array_sum(array_column($rows, 'perempuan'));
$tahunList = $pdo->query("SELECT DISTINCT tahun_ajaran FROM data_siswa ORDER BY tahun_ajaran DESC")->fetchAll(PDO::FETCH_COLUMN);

jsonResponse([
    'tahun_aktif' => $tahun ?: ($rows[0]['tahun_ajaran'] ?? '-'),
    'tahun_list'  => $tahunList,
    'kelas'       => $rows,
    'total'       => [
        'laki_laki' => $totalL,
        'perempuan' => $totalP,
        'semua'     => $totalL + $totalP,
        'rombel'    => count($rows),
    ],
]);
