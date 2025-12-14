<?php
require_once __DIR__ . "/auth.php";
$user   = current_user();
require_once __DIR__ . "/navbar.php";

$active = "reviews";

$avatarSrc = $user["avatar_url"] ?: "assets/profile-budi.jpg";
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GO SHOP - Tulis Review</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
</head>
<body>
  <div class="app">
    <!-- TOPBAR -->
    <header class="topbar">
      <div class="topbar-left">
        <div class="logo-circle">GS</div>
        <div>
          <h1 class="app-title">GO SHOP</h1>
          <p class="app-subtitle">Review & Kepuasan Pelanggan</p>
        </div>
      </div>
      <div class="topbar-right">
        <div class="user-info">
          <span class="user-name">Hai, <?= htmlspecialchars($user["name"]) ?></span>
          <span class="user-role">Pelanggan GO SHOP</span>
        </div>
        <?php $avatar = $user["avatar_url"] ?: "assets/profile-budi.jpg"; ?>
          <img src="assets/profile-budi.jpg" alt="Foto Profil" class="topbar-avatar" />
        </a>

        </a>
      </div>
    </header>

    <!-- NAVBAR -->
    <?php require_once __DIR__ . "/navbar.php"; ?>

    <main class="main">
      <section class="card">
        <div class="card-header">
          <h2>Tulis Review</h2>
          <a class="btn-small" href="reviews.php">Kembali</a>
        </div>

        <div class="container" style="max-width: 560px;">
          <!-- Pakai form kamu, tapi aku rapihin atribut penting -->
          <form id="reviewForm" method="post" action="#">
            <label>Nama</label>
            <input type="text" id="nama" placeholder="Masukkan nama Anda" value="<?= htmlspecialchars($user["name"]) ?>">

            <label>Produk / Layanan</label>
            <select id="produk">
              <option value="">Pilih...</option>
              <option value="Produk A">Produk A</option>
              <option value="Produk B">Produk B</option>
              <option value="Layanan C">Layanan C</option>
            </select>

            <label>Rating (1–5)</label>
            <input type="number" id="rating" min="1" max="5" placeholder="Masukkan rating">

            <label>Kelebihan</label>
            <input type="text" id="kelebihan" placeholder="Contoh: Kualitas bagus">

            <label>Kekurangan</label>
            <input type="text" id="kekurangan" placeholder="Contoh: Pengiriman lama">

            <label>Review Lengkap</label>
            <textarea id="review" placeholder="Tulis review Anda di sini..."></textarea>

            <p id="message"></p>

            <button class="btn" type="submit">Kirim</button>
          </form>
        </div>
      </section>
    </main>

    <footer class="footer">
      <p>© 2025 GO SHOP Loyalty Platform. Semua hak dilindungi.</p>
    </footer>
  </div>

  <script src="script.js"></script>
</body>
</html>
