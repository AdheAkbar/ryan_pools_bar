<?php
require_once 'config/koneksi.php';

// --- LOGIKA BACKEND: PROSES FORM KETIKA DI-SUBMIT ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_meja        = $_POST['id_meja'];
    $nama_pelanggan = $_POST['nama_pelanggan'];
    $no_hp          = $_POST['no_hp'];
    $jenis_booking  = $_POST['jenis_booking']; // 'reguler' atau 'open_table'
    
    // Set zona waktu (sesuaikan jika perlu, misal: 'Asia/Jakarta')
    date_default_timezone_set('Asia/Jakarta');
    $waktu_mulai = date('Y-m-d H:i:s');
    
    // Validasi waktu berdasarkan jenis booking
    if ($jenis_booking == 'open_table') {
        // Otomatis tambah 2 jam dari sekarang sebagai batas minimal
        $waktu_selesai = date('Y-m-d H:i:s', strtotime('+2 hours'));
        $status_meja   = 'open_table';
    } else {
        // Ambil durasi dari input form (misal: 1, 2, atau 3 jam)
        $durasi        = (int)$_POST['durasi'];
        $waktu_selesai = date('Y-m-d H:i:s', strtotime("+$durasi hours"));
        $status_meja   = 'booked';
    }
    
    // 1. Simpan data ke tabel booking
    $query_booking = "INSERT INTO booking (id_meja, nama_pelanggan, no_hp, jenis_booking, waktu_mulai, waktu_selesai) 
                      VALUES ('$id_meja', '$nama_pelanggan', '$no_hp', '$jenis_booking', '$waktu_mulai', '$waktu_selesai')";
    
    // 2. Update status di tabel meja
    $query_meja = "UPDATE meja SET status_meja = '$status_meja' WHERE id_meja = '$id_meja'";
    
    if ($conn->query($query_booking) === TRUE && $conn->query($query_meja) === TRUE) {
        // Jika sukses, munculkan alert dan lempar kembali ke halaman utama
        echo "<script>
                alert('Booking berhasil disave bro!');
                window.location.href = 'index.php';
              </script>";
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}

// --- LOGIKA FRONTEND: TAMPILAN FORM ---
include 'includes/header.php'; 

// Cek apakah ada ID meja yang dilempar dari tombol "Booking Sekarang" di index.php
$id_meja_terpilih = isset($_GET['id']) ? $_GET['id'] : '';
?>

<style>
    .form-container {
        background-color: #1e1e1e;
        border: 1px solid #2d2d2d;
        border-radius: 12px;
        padding: 30px;
        max-width: 500px;
        margin: 0 auto;
        box-shadow: 0 8px 16px rgba(0,0,0,0.5);
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #bb86fc;
        font-weight: bold;
    }
    .form-control {
        width: 100%;
        padding: 12px;
        background-color: #121212;
        border: 1px solid #333;
        color: #fff;
        border-radius: 6px;
        box-sizing: border-box;
        font-family: inherit;
    }
    .form-control:focus {
        outline: none;
        border-color: #6200ea;
    }
    .btn-submit {
        width: 100%;
        padding: 15px;
        background-color: #03dac6;
        color: #000;
        border: none;
        border-radius: 6px;
        font-weight: bold;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    .btn-submit:hover {
        background-color: #018786;
        color: #fff;
    }
</style>

<div class="form-container">
    <h2 style="text-align: center; color: #fff; margin-top: 0;">Form Reservasi Meja</h2>
    
    <form action="booking.php" method="POST">
        
        <div class="form-group">
            <label for="id_meja">Pilih Meja</label>
            <select name="id_meja" id="id_meja" class="form-control" required>
                <option value="">-- Pilih Meja Kosong --</option>
                <?php
                // Hanya tampilkan meja yang statusnya 'available'
                $query_meja_kosong = "SELECT * FROM meja WHERE status_meja = 'available'";
                $result_meja = $conn->query($query_meja_kosong);
                
                while ($row = $result_meja->fetch_assoc()) {
                    // Auto-select jika berasal dari klik tombol di index.php
                    $selected = ($row['id_meja'] == $id_meja_terpilih) ? 'selected' : '';
                    echo "<option value='{$row['id_meja']}' $selected>{$row['nomor_meja']} ({$row['tipe_meja']})</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="nama_pelanggan">Nama Pelanggan</label>
            <input type="text" name="nama_pelanggan" id="nama_pelanggan" class="form-control" placeholder="Masukkan nama" required>
        </div>

        <div class="form-group">
            <label for="no_hp">No. HP / WhatsApp</label>
            <input type="number" name="no_hp" id="no_hp" class="form-control" placeholder="08xxxxxxxxxx" required>
        </div>

        <div class="form-group">
            <label for="jenis_booking">Jenis Booking</label>
            <select name="jenis_booking" id="jenis_booking" class="form-control" onchange="toggleDurasi()" required>
                <option value="reguler">Reguler (Sewa per Jam)</option>
                <option value="open_table">Open Table (Bebas Main)</option>
            </select>
        </div>

        <div class="form-group" id="grup_durasi">
            <label for="durasi">Durasi (Jam)</label>
            <select name="durasi" id="durasi" class="form-control">
                <option value="1">1 Jam</option>
                <option value="2">2 Jam</option>
                <option value="3">3 Jam</option>
                <option value="4">4 Jam</option>
            </select>
        </div>

        <button type="submit" class="btn-submit">Konfirmasi Booking</button>
    </form>
</div>

<script>
    function toggleDurasi() {
        var jenis = document.getElementById("jenis_booking").value;
        var grupDurasi = document.getElementById("grup_durasi");
        
        if (jenis === "open_table") {
            // Sembunyikan input durasi karena sistem otomatis set minimal 2 jam
            grupDurasi.style.display = "none";
        } else {
            // Tampilkan kembali jika pilih reguler
            grupDurasi.style.display = "block";
        }
    }
</script>

<?php include 'includes/footer.php'; ?>