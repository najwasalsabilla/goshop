<?php
require_once __DIR__ . "/auth.php";
$user   = current_user();
$active = "redeem";

$flash = null;


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["reward_id"])) {
  $flash = redeem_reward((int)$user["id"], (int)$_POST["reward_id"]);
  $user  = get_customer_by_id((int)$user["id"]); // refresh saldo
}


$category = trim($_GET["category"] ?? "all");

// kategori 
$fixedCategories = [
  "Voucher Belanja",
  "Makanan & Minuman",
  "Merchandise",
];


$rewards = get_all_rewards();


if ($category !== "" && $category !== "all") {
  $rewards = array_values(array_filter($rewards, function($r) use ($category) {
    return trim((string)($r["category"] ?? "")) === $category;
  }));
}

$avatarSrc = trim($user["avatar_url"] ?? "");
if ($avatarSrc === "") $avatarSrc = "assets/profile-budi.jpg";
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GO SHOP - Redeem Poin</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

  <style>
    .go-toolbar{
      display:flex; gap:10px; align-items:center; justify-content:flex-end;
      position:relative;
    }
    .go-cat-btn{
      border:1px solid rgba(233,30,99,.35);
      background:#fff;
      padding:8px 12px;
      border-radius:12px;
      font-weight:600;
      cursor:pointer;
      display:inline-flex; align-items:center; gap:6px;
      white-space:nowrap;
    }
    .go-cat-panel{
      display:none;
      position:absolute;
      right:0;
      top:42px;
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

    .redeem-header{
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap:14px;
    }
    @media (max-width: 820px){
      .redeem-header{ flex-direction:column; align-items:stretch; }
      .go-cat-panel{ left:0; right:auto; width:100%; }
      .go-toolbar{ justify-content:flex-start; }
    }
  </style>
</head>

<body>
  <div class="app">
    <header class="topbar">
      <div class="topbar-left">
        <div class="logo-circle">GS</div>
        <div>
          <h1 class="app-title">GO SHOP</h1>
          <p class="app-subtitle">Katalog Redeem Poin</p>
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

    <?php require_once __DIR__ . "/navbar.php"; ?>

    <main class="main">
      <?php if ($flash): ?>
        <section class="card" style="border:1px solid rgba(233,30,99,.25);">
          <p style="margin:0;font-weight:600;">
            <?= $flash["ok"] ? "✅ " : "❌ " ?>
            <?= htmlspecialchars($flash["message"]) ?>
          </p>
        </section>
      <?php endif; ?>

      <section class="card card-products">

        <div class="redeem-header">
          <div>
            <h2 style="margin:0;">Katalog Hadiah GO SHOP</h2>
            <p class="points-desc" style="margin:10px 0 0;">
              Saldo Anda: <b><?= (int)($user["points_balance"] ?? 0) ?> Poin</b> •
              Member <?= htmlspecialchars($user["tier"] ?? "SILVER") ?>
            </p>
            <p class="points-desc" style="margin:6px 0 0;">
              Pilih hadiah dan tukarkan poin. Pastikan poin mencukupi sebelum redeem.
            </p>
          </div>

          <!-- dropdown kategori ala products/dashboard -->
          <div class="go-toolbar">
            <button type="button" class="go-cat-btn" id="redeemCatBtn" aria-expanded="false">
              Kategori ▾
            </button>

            <div class="go-cat-panel" id="redeemCatPanel" aria-hidden="true">
              <div class="go-cat-head">
                <div class="go-cat-title">Kategori</div>
                <button type="button" class="go-cat-close" id="redeemCatClose">✕</button>
              </div>

              <div class="go-cat-list">
                <a class="go-cat-item <?= ($category==='all'?'active':'') ?>"
                   href="?<?= http_build_query(["category"=>"all"]) ?>">
                  Semua kategori
                </a>

                <?php foreach ($fixedCategories as $c): ?>
                  <a class="go-cat-item <?= ($category===$c?'active':'') ?>"
                     href="?<?= http_build_query(["category"=>$c]) ?>">
                    <?= htmlspecialchars($c) ?>
                  </a>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>

        <div class="products-grid" style="margin-top:16px;">
          <?php if (!count($rewards)): ?>
            <p class="points-desc" style="margin:0;">
              Reward tidak ditemukan untuk kategori <b><?= htmlspecialchars($category) ?></b>.
              Coba pilih kategori lain.
            </p>
          <?php endif; ?>

          <?php foreach ($rewards as $r): ?>
            <?php
              $stock = (int)($r["stock"] ?? 0);
              $need  = (int)($r["points_required"] ?? 0);
              $tag   = trim((string)($r["category"] ?? "")) !== "" ? (string)$r["category"] : "Reward";

              $dbImg = strtolower(trim((string)($r["image_url"] ?? "")));
              $nameNorm = strtolower(trim((string)($r["name"] ?? "")));

              $img = "assets/voucher.jpg";

              if ($dbImg !== "") {
                if (str_contains($dbImg, "totebag") || str_contains($dbImg, "tote")) {
                  $img = "assets/tote-bag.jpg";
                } elseif (str_contains($dbImg, "tshirt") || str_contains($dbImg, "t-shirt") || str_contains($dbImg, "t_shirt")) {
                  $img = "assets/t-shirt.jpg";
                } elseif (str_contains($dbImg, "voucher")) {
                  $img = "assets/voucher.jpg";
                }
              }

              if ($nameNorm !== "") {
                if (str_contains($nameNorm, "tote bag")) $img = "assets/tote-bag.jpg";
                elseif (str_contains($nameNorm, "t-shirt") || str_contains($nameNorm, "t shirt")) $img = "assets/t-shirt.jpg";
                elseif (str_contains($nameNorm, "voucher")) $img = "assets/voucher.jpg";
              }



              $cardClass = "product-card";
              if ($stock > 0 && $stock <= 5) $cardClass .= " product-warning";
              if ($stock <= 0) $cardClass .= " product-danger";

              $canRedeem = $stock > 0 && (int)($user["points_balance"] ?? 0) >= $need;
            ?>

            <article class="<?= $cardClass ?>">
              <div class="product-image-wrapper">
                <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($r["name"] ?? "Reward") ?>" class="product-image" />
              </div>

              <div class="product-tag"><?= htmlspecialchars($tag) ?></div>
              <h3><?= htmlspecialchars($r["name"] ?? "Reward") ?></h3>
              <p class="product-price">Tukar <?= number_format($need, 0, ",", ".") ?> Poin</p>
              <p class="product-stock"><span>Stok:</span> <?= $stock > 0 ? $stock : "Habis" ?></p>

              <?php if ($stock <= 0): ?>
                <button class="btn-small" disabled>Stok Habis</button>
              <?php elseif (!$canRedeem): ?>
                <button class="btn-small" disabled>Poin Tidak Cukup</button>
              <?php else: ?>
                <form method="post" style="margin-top:10px;">
                  <input type="hidden" name="reward_id" value="<?= (int)($r["id"] ?? 0) ?>">
                  <button class="btn-small" type="submit">Tukar Poin</button>
                </form>
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
      const btn = document.getElementById("redeemCatBtn");
      const panel = document.getElementById("redeemCatPanel");
      const closeBtn = document.getElementById("redeemCatClose");

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
