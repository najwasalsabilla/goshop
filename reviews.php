<?php
require_once __DIR__ . "/auth.php";
$user   = current_user();
$active = "reviews";
require_once __DIR__ . "/navbar.php";

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GO SHOP - Review Pelanggan</title>
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
      </div>
    </header>

    <!-- NAVBAR -->
    <?php require_once __DIR__ . "/navbar.php"; ?>

    <main class="main">
      <!-- Ringkasan rating -->
      <section class="card">
        <div class="card-header">
          <h2>Ringkasan Rating</h2>
        </div>
        <div class="reviews-summary">
          <div class="reviews-score">
            <span class="reviews-score-main">4.7</span>
            <span class="reviews-score-stars">★★★★★</span>
            <span class="reviews-score-count">berdasarkan 124 review</span>
          </div>
          <div class="reviews-bars">
            <div class="reviews-bar-row">
              <span>5 ★</span>
              <div class="reviews-bar"><div class="reviews-bar-fill" style="width: 70%;"></div></div>
              <span class="reviews-bar-count">87</span>
            </div>
            <div class="reviews-bar-row">
              <span>4 ★</span>
              <div class="reviews-bar"><div class="reviews-bar-fill" style="width: 20%;"></div></div>
              <span class="reviews-bar-count">25</span>
            </div>
            <div class="reviews-bar-row">
              <span>3 ★</span>
              <div class="reviews-bar"><div class="reviews-bar-fill" style="width: 7%;"></div></div>
              <span class="reviews-bar-count">8</span>
            </div>
            <div class="reviews-bar-row">
              <span>2 ★</span>
              <div class="reviews-bar"><div class="reviews-bar-fill" style="width: 2%;"></div></div>
              <span class="reviews-bar-count">3</span>
            </div>
            <div class="reviews-bar-row">
              <span>1 ★</span>
              <div class="reviews-bar"><div class="reviews-bar-fill" style="width: 1%;"></div></div>
              <span class="reviews-bar-count">1</span>
            </div>
          </div>
        </div>
      </section>

      <!-- List review -->
      <section class="card">
        <div class="card-header">
          <h2>Review Pelanggan</h2>

          <a class="btn-primary" href="formReview.php">Tulis Review</a>
        </div>

        <div class="review-list">
          <article class="review-card">
            <div class="review-header">
              <div class="review-avatar-wrapper">
                <img src="assets/foto-ani.jpg" alt="Foto Ani" class="review-avatar" />
              </div>
              <div>
                <h3>Ani Lestari</h3>
                <p class="review-meta">Rating: ★★★★★ • 20 Nov 2025</p>
              </div>
            </div>
            <p class="review-text">
              Program loyalitas GO SHOP sangat membantu. Poinnya cepat terkumpul dan katalog hadiahnya menarik!
            </p>
          </article>

          <article class="review-card">
            <div class="review-header">
              <div class="review-avatar-wrapper">
                <img src="assets/foto-rudi.jpg" alt="Foto Rudi" class="review-avatar" />
              </div>
              <div>
                <h3>Rudi Hidayat</h3>
                <p class="review-meta">Rating: ★★★★☆ • 18 Nov 2025</p>
              </div>
            </div>
            <p class="review-text">
              Web-nya mudah digunakan, terutama untuk cek stok produk sebelum datang ke mall. Kadang promo telat tampil saja.
            </p>
          </article>

          <article class="review-card">
            <div class="review-header">
              <div class="review-avatar-wrapper">
                <img src="assets/foto-sinta.jpg" alt="Foto Sinta" class="review-avatar" />
              </div>
              <div>
                <h3>Sinta Dewi</h3>
                <p class="review-meta">Rating: ★★★★★ • 15 Nov 2025</p>
              </div>
            </div>
            <p class="review-text">
              Suka banget sama tampilan GO SHOP, warna fusia-nya bikin fresh. Redeem poin juga prosesnya cepat.
            </p>
          </article>
        </div>
      </section>
    </main>

    <footer class="footer">
      <p>© 2025 GO SHOP Loyalty Platform. Semua hak dilindungi.</p>
    </footer>
  </div>
</body>
</html>
