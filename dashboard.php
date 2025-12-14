<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/goshop_functions.php";

$user   = current_user();
$active = "dashboard";

$avatarSrc = trim($user["avatar_url"] ?? "");
if ($avatarSrc === "") $avatarSrc = "assets/profile-budi.jpg";
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GO SHOP - Dashboard Poin</title>

  <link rel="stylesheet" href="styles.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

  <style>
  .go-toolbar{
    display:flex; gap:10px; align-items:center; justify-content:flex-end;
  }
  .go-cat-btn{
    border:1px solid rgba(233,30,99,.35);
    background:#fff;
    padding:8px 12px;
    border-radius:12px;
    font-weight:600;
    cursor:pointer;
    display:inline-flex; align-items:center; gap:6px;
  }
  .go-cat-panel{
    display:none;
    position:absolute;
    right:0;
    margin-top:10px;
    width:260px;
    background:#fff;
    border:1px solid rgba(233,30,99,.18);
    border-radius:16px;
    box-shadow: 0 14px 40px rgba(0,0,0,.08);
    overflow:hidden;
    z-index:50;
  }
  .go-cat-panel.open{ display:block; }
  .go-cat-head{
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:12px 14px;
    background:rgba(233,30,99,.06);
    border-bottom:1px solid rgba(233,30,99,.12);
  }
  .go-cat-title{ font-weight:800; }
  .go-cat-close{
    border:none;
    background:transparent;
    font-size:16px;
    cursor:pointer;
    opacity:.7;
  }
  .go-cat-list{
    display:flex;
    flex-direction:column;
    padding:8px;
    gap:6px;
  }
  .go-cat-item{
    display:block;
    padding:10px 12px;
    border-radius:12px;
    text-decoration:none;
    color:#222;
    border:1px solid transparent;
    font-weight:600;
  }
  .go-cat-item:hover{
    background:rgba(233,30,99,.06);
    border-color: rgba(233,30,99,.18);
  }
  .go-cat-item.active{
    background:rgba(233,30,99,.12);
    border-color: rgba(233,30,99,.28);
    color:#b0003a;
  }

  .dash-products-header{
    position:relative;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
  }
</style>

</head>
<body>
  <div class="app">
    <!-- Topbar -->
    <header class="topbar">
      <div class="topbar-left">
        <div class="logo-circle">GS</div>
        <div>
          <h1 class="app-title">GO SHOP</h1>
          <p class="app-subtitle">Portal Loyalitas & Stok Produk</p>
        </div>
      </div>

      <div class="topbar-right">
        <div class="user-info">
          <span class="user-name">Hai, <?= htmlspecialchars($user["name"]) ?></span>
          <span class="user-role">Pelanggan GO SHOP</span>
        </div>
        <a href="profile.php" class="topbar-avatar-link">
          <img src="assets/profile-budi.jpg" alt="Foto Profil" class="topbar-avatar" />
        </a>
      </div>
    </header>

    <?php require_once __DIR__ . "/navbar.php"; ?>

    <main class="main">
      <!-- Kartu Poin -->
      <section class="card card-points">
        <div>
          <h2>Saldo Poin Anda</h2>
          <p class="points-balance">
            <span id="pointsBalance"><?= (int)($user["points_balance"] ?? 2000) ?></span> <span>Poin</span>
          </p>
          <p class="points-desc">
            Kumpulkan poin dari setiap transaksi dan tukarkan dengan hadiah menarik.
          </p>
          <div class="points-badges">
            <span class="badge badge-primary">Member <?= htmlspecialchars($user["tier"] ?? "SILVER") ?></span>
            <span class="badge badge-soft">Berlaku s/d 31 Des 2025</span>
          </div>
        </div>
        <div class="card-points-actions">
          <a href="redeem.php" class="btn-primary">Lihat Katalog Hadiah</a>
          <a href="redeem.php" class="btn-ghost">Tukar Poin Sekarang</a>
        </div>
      </section>

      <section class="grid-2">
        <!-- Riwayat Poin REALTIME -->
        <section class="card">
          <div class="card-header">
            <h2>Riwayat Poin</h2>
            <select class="select-small" id="historyRange">
              <option value="30" selected>30 hari terakhir</option>
              <option value="90">3 bulan terakhir</option>
              <option value="180">6 bulan terakhir</option>
            </select>
          </div>

          <div class="table-wrapper">
            <table class="table">
              <thead>
                <tr>
                  <th>Tanggal</th>
                  <th>Deskripsi</th>
                  <th>Tipe</th>
                  <th class="text-right">Poin</th>
                </tr>
              </thead>
              <tbody id="pointsHistoryBody">
                <tr>
                  <td colspan="4" class="text-muted">Memuat riwayat...</td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <!-- Promo -->
        <section class="card card-promo">
          <div class="card-header">
            <h2>Promo Aktif</h2>
            <button class="link-small">Lihat semua</button>
          </div>

          <div class="promo-list">
            <article class="promo-item">
              <div class="promo-tag">Poin Ekstra</div>
              <h3>Double Poin Weekend</h3>
              <p>Belanja setiap Sabtu & Minggu dan dapatkan 2x poin.</p>
              <span class="promo-date">Berlaku s/d 30 Nov 2025</span>
            </article>

            <article class="promo-item">
              <div class="promo-tag promo-tag-soft">Voucher</div>
              <h3>Diskon 50% Penukaran Poin</h3>
              <p>Tukar poin untuk voucher makan dengan setengah poin saja.</p>
              <span class="promo-date">Berlaku s/d 15 Des 2025</span>
            </article>

            <article class="promo-item">
              <div class="promo-tag promo-tag-outline">Spesial Member</div>
              <h3>Hadiah Ulang Tahun</h3>
              <p>Dapatkan 100 poin bonus di bulan ulang tahunmu.</p>
              <span class="promo-date">Khusus Member Terdaftar</span>
            </article>
          </div>
        </section>
      </section>

<section class="card card-products">
  <?php
    $dashCategory = trim($_GET["category"] ?? "all");
    $fixedCategories = ["Fashion","Elektronik","Makanan & Minuman","Merchandise"];

    $allProducts = function_exists("get_all_products") ? get_all_products() : [];

    $productsDash = $allProducts;
    if ($dashCategory !== "" && $dashCategory !== "all") {
      $productsDash = array_values(array_filter($allProducts, function($p) use ($dashCategory) {
        return trim((string)($p["category"] ?? "")) === $dashCategory;
      }));
    }

    $productsDash = array_slice($productsDash, 0, 4);
  ?>

  <div class="dash-products-header">
    <h2 style="margin:0;">Produk GO SHOP</h2>

    <div class="go-toolbar">
      <button type="button" class="go-cat-btn" id="dashCatBtn" aria-expanded="false">
        Kategori ▾
      </button>

      <div class="go-cat-panel" id="dashCatPanel" aria-hidden="true">
        <div class="go-cat-head">
          <div class="go-cat-title">Kategori</div>
          <button type="button" class="go-cat-close" id="dashCatClose">✕</button>
        </div>

        <div class="go-cat-list">
          <a class="go-cat-item <?= ($dashCategory==='all'?'active':'') ?>"
             href="?<?= http_build_query(array_merge($_GET, ["category"=>"all"])) ?>">
            Semua kategori
          </a>

          <?php foreach ($fixedCategories as $c): ?>
            <a class="go-cat-item <?= ($dashCategory===$c?'active':'') ?>"
               href="?<?= http_build_query(array_merge($_GET, ["category"=>$c])) ?>">
              <?= htmlspecialchars($c) ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <div class="products-grid">
    <?php if (empty($productsDash)): ?>
      <p class="points-desc" style="margin:0;">
        Produk tidak ditemukan untuk kategori <b><?= htmlspecialchars($dashCategory) ?></b>.
        Coba pilih kategori lain.
      </p>
    <?php endif; ?>

    <?php foreach ($productsDash as $p): ?>
      <?php
        $pid   = (int)($p["id"] ?? 0);
        $name  = (string)($p["name"] ?? "Produk");
        $cat   = trim((string)($p["category"] ?? "")) !== "" ? (string)$p["category"] : "Produk";
        $price = (int)($p["price"] ?? 0);
        $stock = (int)($p["stock"] ?? 0);

        $img = trim((string)($p["image_url"] ?? ""));
        $nameNorm = strtolower(trim($name));

        if ($img === "") {
          if ($nameNorm === strtolower("GO SHOP Tote Bag")) $img = "assets/tote-bag.jpg";
          elseif ($nameNorm === strtolower("Earphone GO Sound")) $img = "assets/earphone.jpg";
          elseif ($nameNorm === strtolower("Snack Pack GO Crunch")) $img = "assets/snack-pack.jpg";
          elseif ($nameNorm === strtolower("GO SHOP Hoodie")) $img = "assets/hoodie.jpg";
          else $img = "assets/product-placeholder.jpg";
        } else {
          $img = str_replace("\\", "/", $img);
          if (!preg_match('/^https?:\/\//i', $img)) {
            $img = ltrim($img, "./");
            $img = ltrim($img, "/");
            if (strpos($img, "assets/") !== 0) $img = "assets/" . $img;
          }
        }

        $cardClass = "product-card";
        if ($stock > 0 && $stock <= 5) $cardClass .= " product-warning";
        if ($stock <= 0) $cardClass .= " product-danger";
      ?>

      <article class="<?= $cardClass ?>">
        <div class="product-image-wrapper">
          <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($name) ?>" class="product-image" />
        </div>
        <div class="product-tag"><?= htmlspecialchars($cat) ?></div>
        <h3><?= htmlspecialchars($name) ?></h3>
        <p class="product-price">Rp <?= number_format($price, 0, ",", ".") ?></p>
        <p class="product-stock"><span>Stok:</span> <?= $stock > 0 ? $stock : "Habis" ?></p>
        <a class="btn-small" href="products.php">Lihat Produk</a>
      </article>
    <?php endforeach; ?>
  </div>
</section>

    </main>

    <footer class="footer">
      <p>© 2025 GO SHOP Loyalty Platform. Semua hak dilindungi.</p>
    </footer>
  </div>

  <!-- Realtime points -->
  <script src="points_realtime.js?v=2"></script>
  <script>
  (function(){
    const btn = document.getElementById("dashCatBtn");
    const panel = document.getElementById("dashCatPanel");
    const closeBtn = document.getElementById("dashCatClose");

    function openPanel(){
      panel.classList.add("open");
      btn.setAttribute("aria-expanded","true");
      panel.setAttribute("aria-hidden","false");
    }
    function closePanel(){
      panel.classList.remove("open");
      btn.setAttribute("aria-expanded","false");
      panel.setAttribute("aria-hidden","true");
    }

    btn?.addEventListener("click", function(){
      if (panel.classList.contains("open")) closePanel();
      else openPanel();
    });

    closeBtn?.addEventListener("click", closePanel);

    document.addEventListener("click", function(e){
      if (!panel.contains(e.target) && !btn.contains(e.target)) closePanel();
    });

    document.addEventListener("keydown", function(e){
      if (e.key === "Escape") closePanel();
    });
  })();
</script>

</body>
</html>
