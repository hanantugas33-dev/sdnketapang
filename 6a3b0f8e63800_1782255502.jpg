<?php
require_once 'config.php';

// Cache response di browser selama 30 detik
// Setelah admin simpan data baru, user akan dapat data terbaru dalam maks 30 detik
header('Cache-Control: public, max-age=30');

$pdo = getDB();
$action = $_GET['action'] ?? 'all';

// Action: ambil visi, misi, tujuan, sejarah
if ($action === 'visi_misi') {
    $profil = $pdo->query("SELECT visi, misi, tujuan, sejarah FROM profil_sekolah LIMIT 1")->fetch();
    jsonResponse([
        'visi'    => $profil['visi']    ?? '',
        'misi'    => $profil['misi']    ?? '',
        'tujuan'  => $profil['tujuan']  ?? '',
        'sejarah' => $profil['sejarah'] ?? '',
    ]);
}

if ($action === 'stats') {
    $profil = $pdo->query("SELECT tahun_berdiri, total_siswa, total_guru, akreditasi FROM profil_sekolah LIMIT 1")->fetch();

    // Total siswa: jumlahkan semua kelas dari data_siswa (tahun ajaran terbaru).
    // Fallback ke profil_sekolah.total_siswa kalau tabel kosong.
    $totalSiswa = $profil['total_siswa'] ?? 0;
    try {
        $row = $pdo->query("
            SELECT COALESCE(SUM(laki_laki + perempuan), 0) AS total
            FROM data_siswa
            WHERE tahun_ajaran = (SELECT MAX(tahun_ajaran) FROM data_siswa)
        ")->fetch();
        if ($row && (int)$row['total'] > 0) {
            $totalSiswa = (int)$row['total'];
        }
    } catch (Exception $e) { /* tabel belum ada, pakai fallback */ }

    jsonResponse([
        'tahun_berdiri' => $profil['tahun_berdiri'] ?? '—',
        'total_siswa'   => $totalSiswa,
        'total_guru'    => $profil['total_guru'] ?? '—',
        'akreditasi'    => $profil['akreditasi'] ?? 'A',
    ]);
}

if ($action === 'struktur') {
    $rows = $pdo->query("SELECT id, nama, jabatan, nip, level, parent_id FROM struktur_organisasi WHERE is_active=1 ORDER BY urutan ASC")->fetchAll();
    jsonResponse($rows);
}

// Default: ambil semua data profil
$profil = $pdo->query("SELECT * FROM profil_sekolah LIMIT 1")->fetch();
$akreditasi = $pdo->query("SELECT * FROM akreditasi ORDER BY tanggal_penilaian DESC LIMIT 1")->fetch();

jsonResponse([
    'profil'     => $profil,
    'akreditasi' => $akreditasi
]);
