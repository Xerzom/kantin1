<?php 
include 'db.php';
include 'header.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Inisialisasi variabel
    $total = 0;
    $nama_pelanggan = "Pelanggan";
    
    // Mulai transaksi database
    mysqli_begin_transaction($conn);
    
    try {
        // 1. Buat record pesanan utama
        $insert_pesanan = "INSERT INTO pesanan (nama_pelanggan, total_harga) VALUES ('$nama_pelanggan', 0)";
        mysqli_query($conn, $insert_pesanan);
        $pesanan_id = mysqli_insert_id($conn);
        
        // 2. Proses setiap item yang dipilih
        foreach ($_POST['items'] as $item_id => $value) {
            // Perbaikan utama: pengecekan quantity yang benar
            if (isset($_POST['quantities'][$item_id]) && $_POST['quantities'][$item_id] > 0) {
                $quantity = (int)$_POST['quantities'][$item_id];
                
                // Dapatkan detail menu
                $query = "SELECT harga, nama, stok FROM menu WHERE id = $item_id";
                $result = mysqli_query($conn, $query);
                $menu = mysqli_fetch_assoc($result);
                
                // Validasi stok
                if ($menu['stok'] < $quantity) {
                    throw new Exception("Stok {$menu['nama']} tidak mencukupi");
                }
                
                // Hitung subtotal
                $subtotal = $menu['harga'] * $quantity;
                $total += $subtotal;
                
                // Simpan detail pesanan
                $insert_detail = "INSERT INTO pesanan_detail 
                                (pesanan_id, menu_id, quantity, harga_satuan) 
                                VALUES ($pesanan_id, $item_id, $quantity, {$menu['harga']})";
                mysqli_query($conn, $insert_detail);
                
                // Kurangi stok
                $update_stok = "UPDATE menu SET stok = stok - $quantity WHERE id = $item_id";
                mysqli_query($conn, $update_stok);
            }
        }
        
        // 3. Update total harga pesanan
        $update_total = "UPDATE pesanan SET total_harga = $total WHERE id = $pesanan_id";
        mysqli_query($conn, $update_total);
        
        // Commit transaksi jika semua berhasil
        mysqli_commit($conn);
        
    } catch (Exception $e) {
        // Rollback jika ada error
        mysqli_rollback($conn);
        die("Terjadi kesalahan: " . $e->getMessage());
    }
}
?>

<section class="order-section py-5">
    <div class="container">
        <h2 class="text-center mb-5">Cara Memesan</h2>
        
        <?php if(isset($total)): ?>
        <div class="order-summary mb-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-center">Pesanan Anda</h4>
                    
                    <!-- Daftar item yang dipesan -->
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Menu</th>
                                <th>Jumlah</th>
                                <th>Harga</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Ambil detail pesanan terakhir
                            $detail_query = "SELECT m.nama, pd.quantity, pd.harga_satuan 
                                            FROM pesanan_detail pd
                                            JOIN menu m ON pd.menu_id = m.id
                                            WHERE pd.pesanan_id = $pesanan_id";
                            $detail_result = mysqli_query($conn, $detail_query);
                            
                            while ($detail = mysqli_fetch_assoc($detail_result)):
                            ?>
                            <tr>
                                <td><?= $detail['nama'] ?></td>
                                <td><?= $detail['quantity'] ?></td>
                                <td>Rp <?= number_format($detail['harga_satuan'], 0, ',', '.') ?></td>
                                <td>Rp <?= number_format($detail['harga_satuan'] * $detail['quantity'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3">Total Pembayaran</th>
                                <th>Rp <?= number_format($total, 0, ',', '.') ?></th>
                            </tr>
                        </tfoot>
                    </table>
                    
                    <div class="text-center mt-3">
                        <div id="qrcode" class="my-3"></div>
                        <p>Silahkan scan QR code di atas untuk melakukan pembayaran</p>
                        <a href="menu.php" class="btn btn-primary">Pesan Lagi</a>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <form action="order.php" method="POST">
            <div class="row">
                <!-- Kantin Ibu Rika -->
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Kantin Ibu Rika</h5>
                        </div>
                        <div class="card-body">
                            <?php
                            $query = "SELECT * FROM menu WHERE kantin_id = 1";
                            $result = mysqli_query($conn, $query);
                            while ($row = mysqli_fetch_assoc($result)): ?>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="items[<?= $row['id'] ?>]" 
                                    id="item<?= $row['id']; ?>">
                                <label class="form-check-label d-flex justify-content-between" for="item<?= $row['id']; ?>">
                                    <span><?= $row['nama']; ?> (Stok: <?= $row['stok']; ?>)</span>
                                    <span>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></span>
                                </label>
                                <input type="number" name="quantities[<?= $row['id'] ?>]" class="form-control mt-1" 
                                    min="1" max="<?= $row['stok']; ?>" value="1" style="width: 80px;">
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Kantin Batagor Mas Riki -->
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Kantin Batagor Mas Riki</h5>
                        </div>
                        <div class="card-body">
                            <?php
                            $query = "SELECT * FROM menu WHERE kantin_id = 2";
                            $result = mysqli_query($conn, $query);
                            while ($row = mysqli_fetch_assoc($result)): ?>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="items[<?= $row['id'] ?>]" 
                                    id="item<?= $row['id']; ?>">
                                <label class="form-check-label d-flex justify-content-between" for="item<?= $row['id']; ?>">
                                    <span><?= $row['nama']; ?> (Stok: <?= $row['stok']; ?>)</span>
                                    <span>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></span>
                                </label>
                                <input type="number" name="quantities[<?= $row['id'] ?>]" class="form-control mt-1" 
                                    min="1" max="<?= $row['stok']; ?>" value="1" style="width: 80px;">
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Kantin Masakan Rumah bu Eka -->
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">Kantin Masakan Rumah bu Eka</h5>
                        </div>
                        <div class="card-body">
                            <?php
                            $query = "SELECT * FROM menu WHERE kantin_id = 3";
                            $result = mysqli_query($conn, $query);
                            while ($row = mysqli_fetch_assoc($result)): ?>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="items[<?= $row['id'] ?>]" 
                                    id="item<?= $row['id']; ?>">
                                <label class="form-check-label d-flex justify-content-between" for="item<?= $row['id']; ?>">
                                    <span><?= $row['nama']; ?> (Stok: <?= $row['stok']; ?>)</span>
                                    <span>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></span>
                                </label>
                                <input type="number" name="quantities[<?= $row['id'] ?>]" class="form-control mt-1" 
                                    min="1" max="<?= $row['stok']; ?>" value="1" style="width: 80px;">
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-lg">Pesan Sekarang</button>
                    </div>
                </div>
            </div>
        </form>
        <?php endif; ?>
    </div>
</section>

<script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
<script>
    <?php if(isset($total)): ?>
    new QRCode(document.getElementById("qrcode"), {
        text: "Pembayaran: Rp <?= number_format($total, 0, ',', '.'); ?>\nID Pesanan: <?= $pesanan_id ?>",
        width: 200,
        height: 200
    });
    <?php endif; ?>
</script>

<?php include 'footer.php'; ?>