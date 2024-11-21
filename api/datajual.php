<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');  
require_once '../includes/db.php';

$method = $_SERVER['REQUEST_METHOD'];  // Cek metode request

if ($method == 'GET') {
    // Mengambil data berdasarkan ID atau semua data jika ID tidak ada
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    if ($id > 0) {
        // Ambil data berdasarkan ID
        $stmt = $pdo->prepare("SELECT * FROM penjualan_bulanan WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    } else {
        // Ambil semua data
        $stmt = $pdo->prepare("SELECT * FROM penjualan_bulanan ORDER BY id ASC");
    }

    try {
        $stmt->execute();
        $results = $stmt->fetchAll();
        echo json_encode($results);
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }

} elseif ($method == 'POST') {
    // Menambahkan data baru
    $inputData = json_decode(file_get_contents("php://input"), true);

    if (!empty($inputData)) {
        $id = isset($inputData['id']) ? $inputData['id'] : null;
        $nama_ikan = isset($inputData['nama_ikan']) ? $inputData['nama_ikan'] : null;
        $bulan = isset($inputData['bulan']) ? $inputData['bulan'] : null;
        $jumlah_penjualan = isset($inputData['jumlah_penjualan']) ? $inputData['jumlah_penjualan'] : null;
        $total_penjualan = isset($inputData['total_penjualan']) ? $inputData['total_penjualan'] : null;

        // Cek apakah semua data ada
        if ($id && $nama_ikan && $bulan && $jumlah_penjualan && $total_penjualan) {
            // Masukkan data ke dalam database
            $stmt = $pdo->prepare("INSERT INTO penjualan_bulanan (id, nama_ikan, bulan, jumlah_penjualan, total_penjualan) 
                                   VALUES (:id, :nama_ikan, :bulan, :jumlah_penjualan, :total_penjualan)");
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nama_ikan', $nama_ikan);
            $stmt->bindParam(':bulan', $bulan);
            $stmt->bindParam(':jumlah_penjualan', $jumlah_penjualan);
            $stmt->bindParam(':total_penjualan', $total_penjualan);

            try {
                $stmt->execute();
                echo json_encode(['message' => 'Data berhasil ditambahkan']);
            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Data tidak lengkap']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Data tidak valid']);
    }

} elseif ($method == 'PUT') {
    // Update data berdasarkan ID
    $inputData = json_decode(file_get_contents("php://input"), true);
    $id = isset($inputData['id']) ? $inputData['id'] : null;

    if ($id) {
        $nama_ikan = isset($inputData['nama_ikan']) ? $inputData['nama_ikan'] : null;
        $bulan = isset($inputData['bulan']) ? $inputData['bulan'] : null;
        $jumlah_penjualan = isset($inputData['jumlah_penjualan']) ? $inputData['jumlah_penjualan'] : null;
        $total_penjualan = isset($inputData['total_penjualan']) ? $inputData['total_penjualan'] : null;

        // Cek apakah semua data ada
        if ($nama_ikan && $bulan && $jumlah_penjualan && $total_penjualan) {
            $stmt = $pdo->prepare("UPDATE penjualan_bulanan SET nama_ikan = :nama_ikan, bulan = :bulan, 
                                   jumlah_penjualan = :jumlah_penjualan, total_penjualan = :total_penjualan 
                                   WHERE id = :id");

            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nama_ikan', $nama_ikan);
            $stmt->bindParam(':bulan', $bulan);
            $stmt->bindParam(':jumlah_penjualan', $jumlah_penjualan);
            $stmt->bindParam(':total_penjualan', $total_penjualan);

            try {
                $stmt->execute();
                echo json_encode(['message' => 'Data berhasil diperbarui']);
            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Data tidak lengkap']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'ID tidak ditemukan']);
    }

} elseif ($method == 'DELETE') {
    // Menghapus data berdasarkan ID
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    if ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM penjualan_bulanan WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        try {
            $stmt->execute();
            echo json_encode(['message' => 'Data berhasil dihapus']);
        } catch(PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'ID tidak ditemukan']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
}
?>
