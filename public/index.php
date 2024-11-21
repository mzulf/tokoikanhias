<?php
include '../includes/config.php';  // Menggunakan koneksi database

// Query data dari tabel
$query = "SELECT id, nama_ikan, bulan, jumlah_penjualan, total_penjualan FROM penjualan_bulanan";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penjualan Bulanan</title>
    <style>
        /* Global Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: rgb(42, 131, 176); /* Mengganti warna header dengan RGB(42, 131, 176) */
            font-size: 2rem;
        }

        /* Styling Table */
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            margin: 0 auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        table th, table td {
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
        }

        table th {
            background-color: rgb(42, 131, 176); /* Mengganti warna header tabel dengan RGB(42, 131, 176) */
            color: white;
            font-weight: bold;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        /* Styling Link */
        a {
            display: inline-block;
            text-align: center;
            margin-top: 30px;
            padding: 12px 25px;
            background-color: rgb(42, 131, 176); /* Mengganti warna latar belakang link dengan RGB(42, 131, 176) */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1rem;
        }

        a:hover {
            background-color: rgb(32, 112, 146); /* Mengganti warna hover dengan lebih gelap dari RGB(42, 131, 176) */
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            h1 {
                font-size: 1.5rem;
            }

            table {
                font-size: 14px;
            }

            table th, table td {
                padding: 10px;
            }

            a {
                font-size: 0.9rem;
                padding: 10px 20px;
            }
        }
    </style>
</head>
<body>
    <h1>Data Penjualan Bulanan</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Ikan</th>
                <th>Bulan</th>
                <th>Jumlah Penjualan</th>
                <th>Total Penjualan</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["id"] . "</td>";
                    echo "<td>" . $row["nama_ikan"] . "</td>";
                    echo "<td>" . $row["bulan"] . "</td>";
                    echo "<td>" . $row["jumlah_penjualan"] . "</td>";
                    echo "<td>" . $row["total_penjualan"] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>Tidak ada data.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <a href="tokoikan.html">Kembali ke Halaman Utama</a>
</body>
</html>

<?php
$conn->close();  
?>
