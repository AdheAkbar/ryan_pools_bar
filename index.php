<?php
require_once 'config/koneksi.php';
include 'includes/header.php';

// Query backend tetap aman tanpa perlu diubah
$query = "SELECT m.*, b.nama_pelanggan, b.jenis_booking, b.waktu_mulai, b.waktu_selesai 
          FROM meja m 
          LEFT JOIN booking b ON m.id_meja = b.id_meja AND b.status_billing = 'active'
          ORDER BY m.nomor_meja ASC";
$result = $conn->query($query);
?>

<style>
    /* Tema Premium Black & Gold */
    :root {
        --gold-primary: #D4B27A;
        --gold-dark: #A8864E;
        --bg-dark: #121212;
        --card-bg: #1A1A1A;
        --text-light: #E0E0E0;
    }

    body {
        background-color: var(--bg-dark);
        background-image: radial-gradient(circle at 50% 0%, #1f1f1f 0%, #121212 70%);
        min-height: 100vh;
        color: var(--text-light);
    }

    .hero-title {
        text-align: center;
        margin-bottom: 50px;
    }
    .hero-title h1 {
        font-size: 38px;
        background: -webkit-linear-gradient(var(--gold-primary), var(--gold-dark));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 5px;
        letter-spacing: 2px;
        text-transform: uppercase;
    }
    .hero-title p { color: #888; font-size: 15px; }

    .meja-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
        padding: 0 20px;
    }

    /* Kartu UI Minimalis Eksklusif */
    .card-meja {
        background: var(--card-bg);
        border: 1px solid rgba(212, 178, 122, 0.15); /* Border emas tipis */
        border-radius: 16px;
        padding: 35px 25px 25px;
        text-align: center;
        position: relative;
        box-shadow: 0 10px 20px rgba(0,0,0,0.5);
        transition: transform 0.3s ease, border-color 0.3s ease;
    }
    .card-meja:hover {
        transform: translateY(-8px);
        border-color: var(--gold-primary);
    }

    /* Bola Biliar Hitam elegan dengan font Emas */
    .bola-biliar {
        width: 70px;
        height: 70px;
        background: #0a0a0a;
        border-radius: 50%;
        margin: -60px auto 20px; 
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--gold-primary);
        font-size: 24px;
        font-weight: bold;
        box-shadow: inset -5px -5px 15px rgba(255,255,255,0.05), 0 8px 15px rgba(0,0,0,0.8);
        border: 2px solid var(--gold-primary);
    }

    .tipe-meja {
        font-size: 13px;
        color: #888;
        letter-spacing: 3px;
        text-transform: uppercase;
        margin-bottom: 15px;
    }

    /* Status Badge bentuk tegas */
    .status-badge {
        font-size: 12px;
        padding: 6px 16px;
        border-radius: 4px; /* Dibuat agak kotak agar lebih maskulin/formal */
        display: inline-block;
        font-weight: bold;
        letter-spacing: 1px;
        margin-bottom: 25px;
        text-transform: uppercase;
    }
    .status-available { background: rgba(212, 178, 122, 0.1); color: var(--gold-primary); border: 1px solid var(--gold-primary); }
    .status-booked { background: rgba(166, 61, 64, 0.1); color: #D9534F; border: 1px solid #D9534F; }
    .status-open { background: rgba(224, 224, 224, 0.1); color: #FFF; border: 1px solid #FFF; }

    .info-waktu {
        font-size: 15px;
        color: #fff;
        margin-bottom: 25px;
        min-height: 45px;
        font-family: 'Courier New', Courier, monospace; 
        font-size: 20px;
        font-weight: bold;
    }
    .info-waktu span.label-kecil {
        display: block;
        font-family: 'Segoe UI', sans-serif;
        font-size: 12px;
        color: #777;
        margin-top: 5px;
        font-weight: normal;
    }

    /* Tombol Premium */
    .btn {
        display: block;
        width: 100%;
        padding: 14px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: bold;
        font-size: 14px;
        text-decoration: none;
        transition: all 0.3s;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .btn-booking { 
        background: var(--gold-primary); 
        color: #111; 
    }
    .btn-booking:hover { 
        background: var(--gold-dark); 
        box-shadow: 0 0 15px rgba(212, 178, 122, 0.4);
    }
    
    .btn-waiting { 
        background: transparent; 
        color: var(--text-light); 
        border: 1px solid #444;
    }
    .btn-waiting:hover { 
        border-color: var(--gold-primary);
        color: var(--gold-primary);
    }
</style>

<div class="hero-title">
    <h1>Live Reservation</h1>
    <p>Premium Billiard & Lounge Dashboard</p>
</div>

<div class="meja-grid">
    <?php while ($row = $result->fetch_assoc()): ?>
        <?php
        $angka_bola = substr($row['nomor_meja'], -2);
        
        // Border color dinamis
        $border_active = 'rgba(212, 178, 122, 0.15)';
        if ($row['status_meja'] == 'booked') $border_active = 'rgba(217, 83, 79, 0.3)';
        if ($row['status_meja'] == 'open_table') $border_active = 'rgba(255, 255, 255, 0.2)';
        ?>
        
        <div class="card-meja" style="border-color: <?= $border_active; ?>;">
            <div class="bola-biliar"><?= $angka_bola; ?></div>
            <div class="tipe-meja"><?= htmlspecialchars($row['tipe_meja']); ?> TABLE</div>
            
            <?php if ($row['status_meja'] == 'available'): ?>
                <div class="status-badge status-available">Available</div>
                <div class="info-waktu" style="font-family: 'Segoe UI', sans-serif; font-size: 14px; color: #888; margin-top:10px;">
                    Ready for reservation
                </div>
                <a href="booking.php?id=<?= $row['id_meja']; ?>" class="btn btn-booking">Pesan Sekarang</a>
            
            <?php elseif ($row['status_meja'] == 'booked'): ?>
                <div class="status-badge status-booked">Booked (Reguler)</div>
                <div class="info-waktu">
                    <?php
                    $waktu_selesai_js = date('Y-m-d\TH:i:s', strtotime($row['waktu_selesai']));
                    ?>
                    <b class="timer-reguler" data-batas="<?= $waktu_selesai_js; ?>">--:--:--</b>
                    <span class="label-kecil">Penyewa: <?= htmlspecialchars($row['nama_pelanggan']); ?></span>
                </div>
                <a href="waiting_list.php?id=<?= $row['id_meja']; ?>" class="btn btn-waiting">Waiting List</a>
            
            <?php elseif ($row['status_meja'] == 'open_table'): ?>
                <div class="status-badge status-open">Open Table</div>
                <div class="info-waktu">
                    <?php
                    $waktu_mulai_js = date('Y-m-d\TH:i:s', strtotime($row['waktu_mulai']));
                    ?>
                    <b class="timer-open" data-mulai="<?= $waktu_mulai_js; ?>">--:--:--</b>
                    <span class="label-kecil">Min. 2 Jam (Berjalan)</span>
                </div>
                <a href="waiting_list.php?id=<?= $row['id_meja']; ?>" class="btn btn-waiting">Waiting List</a>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
</div>

<script>
function padZero(num) { return num < 10 ? "0" + num : num; }

setInterval(function() {
    let waktuSekarang = new Date().getTime();

    // Logika Hitung Mundur (Reguler)
    document.querySelectorAll('.timer-reguler').forEach(function(el) {
        let waktuBatas = new Date(el.getAttribute('data-batas')).getTime();
        let jarak = waktuBatas - waktuSekarang;

        if (jarak < 0) {
            el.innerHTML = "<span style='color:#D9534F; font-size: 16px;'>TIME'S UP!</span>";
        } else {
            let h = Math.floor((jarak % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            let m = Math.floor((jarak % (1000 * 60 * 60)) / (1000 * 60));
            let s = Math.floor((jarak % (1000 * 60)) / 1000);
            el.innerHTML = padZero(h) + ":" + padZero(m) + ":" + padZero(s);
        }
    });

    // Logika Hitung Maju (Open Table)
    document.querySelectorAll('.timer-open').forEach(function(el) {
        let waktuMulai = new Date(el.getAttribute('data-mulai')).getTime();
        let berjalan = waktuSekarang - waktuMulai;

        if (berjalan > 0) {
            let h = Math.floor((berjalan % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            let m = Math.floor((berjalan % (1000 * 60 * 60)) / (1000 * 60));
            let s = Math.floor((berjalan % (1000 * 60)) / 1000);
            el.innerHTML = padZero(h) + ":" + padZero(m) + ":" + padZero(s);
        }
    });
}, 1000);
</script>

<?php include 'includes/footer.php'; ?>