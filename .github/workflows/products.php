<?php
require_once __DIR__ . "/auth.php";
$user   = current_user();
$active = "products";

// =========================
// ADD TO CART (CRUD)
// =========================
if (isset($_GET["add"])) {
  cart_add((int)$_GET["add"], 1);
  header("Location: cart.php");
  exit;
}

// =========================
// SEARCH + FILTER (GET)
// =========================
$q = trim($_GET["q"] ?? "");
$category = trim($_GET["category"] ?? "all");

// kategori FIX sesuai request (bukan dari DB)
$fixedCategories = [
  "Fashion",
  "Elektronik",
  "Makanan & Minuman",
  "Merchandise",
];

// NORMALISASI CATEGORY
// - kalau 'all' => kosongin biar gak ikut ngefilter di function
// - kalau value aneh => anggap all juga
if ($category === "all" || $category === "" || !in_array($category, $fixedCategories, true)) {
  $category = ""; // berarti "semua kategori"
}

// ambil produk dari DB
if (function_exists("get_products_filtered")) {
  $products = get_products_filtered($q, $category); // category "" = semua
} else {
  $products = get_all_products();
  if ($q !== "" || $category !== "") {
    $products = array_values(array_filter($products, function($p) use ($q, $category) {
      $ok = true;
      if ($q !== "") $ok = $ok && (stripos($p["name"] ?? "", $q) !== false);
      if ($category !== "") $ok = $ok && (trim($p["category"] ?? "") === $category);
      return $ok;
    }));
  }
}


$avatar = !empty($user["avatar_url"]) ? $user["avatar_url"] : "assets/profile-budi.jpg";
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GO SHOP - Produk & Stok</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

  <!-- CSS kecil khusus toolbar search+kategori (minimalis) -->
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
    .go-search-form{
      display:flex; gap:8px; align-items:center;
      flex:1;
      max-width:520px;
      justify-content:flex-end;
    }
    .go-search-input{
      width:100%;
      padding:10px 12px;
      border:1px solid rgba(233,30,99,.25);
      border-radius:14px;
      outline:none;
      font-family:inherit;
    }
    .go-search-input:focus{
      border-color: rgba(233,30,99,.55);
      box-shadow: 0 0 0 3px rgba(233,30,99,.08);
    }
    .go-search-submit{
      border:none;
      background:#e91e63;
      color:#fff;
      padding:10px 14px;
      border-radius:14px;
      font-weight:700;
      cursor:pointer;
      white-space:nowrap;
    }

    /* panel kategori dropdown */
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

    /* bikin header card rapi kayak halaman kamu */
    .card-header.products-header{
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap:14px;
      position:relative; /* supaya panel kategori nempel */
    }
    .products-header-left h2{ margin:0; }
    .products-header-left{ max-width: 520px; }
    @media (max-width: 820px){
      .card-header.products-header{ flex-direction:column; align-items:stretch; }
      .go-toolbar{ justify-content:flex-start; }
      .go-search-form{ max-width:none; }
      .go-cat-panel{ left:0; right:auto; width:100%; }
    }
  </style>
</head>

<body>
  <div class="app">

    <!-- TOPBAR -->
    <header class="topbar">
      <div class="topbar-left">
        <div class="logo-circle">GS</div>
        <div>
          <h1 class="app-title">GO SHOP</h1>
          <p class="app-subtitle">Produk & Ketersediaan Stok</p>
        </div>
      </div>
      <div class="topbar-right">
        <div class="user-info">
          <span class="user-name">Hai, <?= htmlspecialchars($user["name"] ?? "Pelanggan") ?></span>
          <span class="user-role">Pelanggan GO SHOP</span>
        </div>
        <a href="profile.php" class="topbar-avatar-link">
         <img src="assets/profile-budi.jpg" alt="Foto Profil" class="topbar-avatar" />
        </a>
      </div>
    </header>

    <!-- NAVBAR -->
    <?php require_once __DIR__ . "/navbar.php"; ?>

    <main class="main">
      <section class="card card-products">

        <div class="card-header products-header">
          <div class="products-header-left">
            <h2>Daftar Produk GO SHOP</h2>
            <p class="points-desc" style="margin:10px 0 0;">
              Lihat harga dan stok produk secara realtime agar lebih nyaman berbelanja di GO SHOP.
            </p>
          </div>

          <!-- toolbar search + kategori (minimalis GO SHOP) -->
          <div class="go-toolbar">
            <button type="button" class="go-cat-btn" id="catBtn" aria-expanded="false">
              Kategori ▾
            </button>

            <form method="get" class="go-search-form">
              <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
              <input
                type="text"
                name="q"
                class="go-search-input"
                placeholder="Cari produk..."
                value="<?= htmlspecialchars($q) ?>"
              />
              <button class="go-search-submit" type="submit">Cari</button>
            </form>

            <div class="go-cat-panel" id="catPanel" aria-hidden="true">
              <div class="go-cat-head">
                <div class="go-cat-title">Kategori</div>
                <button type="button" class="go-cat-close" id="catClose">✕</button>
              </div>
              <div class="go-cat-list">
              <a class="go-cat-item <?= ($category===''?'active':'') ?>"
                href="products.php">Semua kategori</a>
                <?php foreach ($fixedCategories as $c): ?>
                  <a class="go-cat-item <?= ($category===$c?'active':'') ?>"
                     href="?<?= http_build_query(["q"=>$q, "category"=>$c]) ?>"><?= htmlspecialchars($c) ?></a>
                <?php endforeach; ?>
              </div>
            </div>

          </div>
        </div>

        <div class="products-grid">
          <?php if (empty($products)): ?>
            <p class="points-desc">Produk tidak ditemukan. Coba kata kunci lain atau ganti kategori.</p>
          <?php endif; ?>

          <?php foreach ($products as $p): ?>
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
              <p class="product-stock">
                <span>Stok:</span> <?= $stock > 0 ? $stock : "Habis" ?>
              </p>

              <?php if ($stock <= 0): ?>
                <button class="btn-small" disabled>Stok Habis</button>
              <?php else: ?>
                <a class="btn-small" href="products.php?add=<?= $pid ?>">Tambah ke Keranjang</a>
              <?php endif; ?>
            </article>
          <?php endforeach; ?>
        </div>

      </section>
    </main>

    <footer class="footer">
      <p>© 2025 GO SHOP Loyalty Platform. Semua hak dilindungi.</p>
    </footer>
  </div>

  <script>
    (function(){
      const btn = document.getElementById("catBtn");
      const panel = document.getElementById("catPanel");
      const closeBtn = document.getElementById("catClose");

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
