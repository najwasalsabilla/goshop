<?php
require_once __DIR__ . "/config.php";

function db_prepare_or_die(string $sql) {
  global $mysqli;
  $stmt = $mysqli->prepare($sql);
  if (!$stmt) die("Prepare error: " . $mysqli->error);
  return $stmt;
}

function db_bind_dynamic($stmt, array $params): void {
  if (!$params) return;
  $types = "";
  foreach ($params as $p) $types .= is_int($p) ? "i" : "s";
  $stmt->bind_param($types, ...$params);
}

function db_fetch_all(string $sql, array $params = []): array {
  $stmt = db_prepare_or_die($sql);
  db_bind_dynamic($stmt, $params);
  $stmt->execute();
  $res = $stmt->get_result();
  $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
  $stmt->close();
  return $rows;
}

function db_fetch_one(string $sql, array $params = []): ?array {
  $rows = db_fetch_all($sql, $params);
  return $rows[0] ?? null;
}

function db_execute(string $sql, array $params = []): bool {
  $stmt = db_prepare_or_die($sql);
  db_bind_dynamic($stmt, $params);
  $ok = $stmt->execute();
  if (!$ok) die("Execute error: " . $stmt->error);
  $stmt->close();
  return $ok;
}

function add_point_tx(int $customer_id, string $type, string $desc, int $points, string $source='ONLINE'): void {
  db_execute(
    "INSERT INTO point_transactions (customer_id, type, description, points, source, created_at)
     VALUES (?, ?, ?, ?, ?, NOW())",
    [$customer_id, $type, $desc, $points, $source]
  );
}

function get_point_history(int $customer_id, int $limit=20): array {
  return db_fetch_all(
    "SELECT id, type, description, points, created_at
     FROM point_transactions
     WHERE customer_id=?
     ORDER BY created_at DESC, id DESC
     LIMIT ?",
    [$customer_id, $limit]
  );
}

function get_customer_by_email(string $email): ?array {
  return db_fetch_one("SELECT * FROM customers WHERE email=? LIMIT 1", [$email]);
}

function get_customer_by_id(int $id): ?array {
  return db_fetch_one("SELECT * FROM customers WHERE id=? LIMIT 1", [$id]);
}

function update_customer_profile(int $id, string $name, string $phone, string $city, string $address): bool {
  return db_execute(
    "UPDATE customers SET name=?, phone=?, city=?, address=? WHERE id=?",
    [$name, $phone, $city, $address, $id]
  );
}

function get_all_products(): array {
  return db_fetch_all("SELECT * FROM products WHERE is_active=1 ORDER BY name ASC");
}

function get_product_by_id(int $id): ?array {
  return db_fetch_one("SELECT * FROM products WHERE id=? LIMIT 1", [$id]);
}

function get_product_categories(): array {
  $rows = db_fetch_all(
    "SELECT DISTINCT category
     FROM products
     WHERE is_active=1 AND category IS NOT NULL AND category <> ''
     ORDER BY category ASC"
  );
  return array_map(fn($r) => $r["category"], $rows);
}

function get_products_filtered(string $q = "", string $category = "all"): array {
  $sql = "SELECT * FROM products WHERE is_active=1";
  $params = [];

  if ($q !== "") {
    $sql .= " AND name LIKE ?";
    $params[] = "%".$q."%";
  }
  if ($category !== "" && $category !== "all") {
    $sql .= " AND category = ?";
    $params[] = $category;
  }

  $sql .= " ORDER BY name ASC";
  return db_fetch_all($sql, $params);
}

function get_all_rewards(): array {
  return db_fetch_all("SELECT * FROM rewards WHERE is_active=1 ORDER BY points_required ASC");
}

function get_reward_by_id(int $id): ?array {
  return db_fetch_one("SELECT * FROM rewards WHERE id=? LIMIT 1", [$id]);
}

function get_reward_categories(): array {
  $rows = db_fetch_all(
    "SELECT DISTINCT category
     FROM rewards
     WHERE is_active=1 AND category IS NOT NULL AND category <> ''
     ORDER BY category ASC"
  );
  return array_map(fn($r) => $r["category"], $rows);
}

function get_rewards_filtered(string $q = "", string $category = "all"): array {
  $sql = "SELECT * FROM rewards WHERE is_active=1";
  $params = [];

  if ($q !== "") {
    $sql .= " AND name LIKE ?";
    $params[] = "%".$q."%";
  }
  if ($category !== "" && $category !== "all") {
    $sql .= " AND category = ?";
    $params[] = $category;
  }

  $sql .= " ORDER BY points_required ASC";
  return db_fetch_all($sql, $params);
}

function redeem_reward(int $customer_id, int $reward_id): array {
  global $mysqli;
  $mysqli->begin_transaction();

  try {
    $cust = db_fetch_one("SELECT * FROM customers WHERE id=? FOR UPDATE", [$customer_id]);
    if (!$cust) throw new Exception("Customer tidak ditemukan.");

    $reward = db_fetch_one("SELECT * FROM rewards WHERE id=? FOR UPDATE", [$reward_id]);
    if (!$reward) throw new Exception("Reward tidak ditemukan.");

    if ((int)$reward["stock"] <= 0) throw new Exception("Stok reward habis.");

    $need = (int)$reward["points_required"];
    if ((int)$cust["points_balance"] < $need) throw new Exception("Poin tidak cukup.");

    $new_balance = (int)$cust["points_balance"] - $need;

    add_point_tx($customer_id, "REDEEM", "Redeem: ".$reward["name"], -$need, "ONLINE");

    db_execute(
      "UPDATE customers
       SET points_balance=?, total_rewards_redeemed=total_rewards_redeemed+1
       WHERE id=?",
      [$new_balance, $customer_id]
    );

    db_execute("UPDATE rewards SET stock=stock-1 WHERE id=?", [$reward_id]);

    $mysqli->commit();
    return ["ok"=>true, "message"=>"Redeem berhasil! Saldo sekarang: {$new_balance} poin."];
  } catch (Exception $e) {
    $mysqli->rollback();
    return ["ok"=>false, "message"=>$e->getMessage()];
  }
}

function cart_init(): void {
  if (!isset($_SESSION["cart"])) $_SESSION["cart"] = [];
}

function cart_add(int $product_id, int $qty=1): void {
  cart_init();
  $_SESSION["cart"][$product_id] = ($_SESSION["cart"][$product_id] ?? 0) + max(1,$qty);
}

function cart_update(int $product_id, int $qty): void {
  cart_init();
  if ($qty <= 0) unset($_SESSION["cart"][$product_id]);
  else $_SESSION["cart"][$product_id] = $qty;
}

function cart_remove(int $product_id): void {
  cart_init();
  unset($_SESSION["cart"][$product_id]);
}

function cart_clear(): void {
  $_SESSION["cart"] = [];
}

function cart_items_with_details(): array {
  cart_init();
  $items = [];
  $total = 0;

  foreach ($_SESSION["cart"] as $pid => $qty) {
    $p = get_product_by_id((int)$pid);
    if (!$p) continue;

    $sub = (int)$p["price"] * (int)$qty;
    $total += $sub;

    $items[] = [
      "id" => (int)$p["id"],
      "name" => $p["name"],
      "price" => (int)$p["price"],
      "qty" => (int)$qty,
      "subtotal" => $sub,
      "image_url" => $p["image_url"] ?: "assets/product-placeholder.jpg",
      "stock" => (int)$p["stock"]
    ];
  }
  return ["items"=>$items, "total"=>$total];
}

function cart_get(): array {
  return cart_items_with_details();
}

function checkout_cart(int $customer_id): array {
  global $mysqli;

  $cart = cart_items_with_details();
  if (empty($cart["items"])) return ["ok"=>false, "message"=>"Keranjang masih kosong."];

  $POINTS_PER = 10000; 

  $mysqli->begin_transaction();
  try {
    $cust = db_fetch_one("SELECT * FROM customers WHERE id=? FOR UPDATE", [$customer_id]);
    if (!$cust) throw new Exception("Customer tidak ditemukan.");

    foreach ($cart["items"] as $it) {
      $p = db_fetch_one("SELECT id, stock, name, price FROM products WHERE id=? FOR UPDATE", [$it["id"]]);
      if (!$p) throw new Exception("Produk tidak ditemukan.");
      if ((int)$p["stock"] < (int)$it["qty"]) {
        throw new Exception("Stok tidak cukup untuk: ".$p["name"]);
      }
    }

    $total = (int)$cart["total"];
    $points_earned = (int) floor($total / $POINTS_PER);
    $new_balance = (int)$cust["points_balance"] + $points_earned;

    db_execute(
      "INSERT INTO orders (customer_id, total_amount, points_earned, status, created_at)
       VALUES (?, ?, ?, 'PAID', NOW())",
      [$customer_id, $total, $points_earned]
    );
    $order_id = (int)$mysqli->insert_id;

    foreach ($cart["items"] as $it) {
      db_execute(
        "INSERT INTO order_items (order_id, product_id, qty, price, subtotal)
         VALUES (?, ?, ?, ?, ?)",
        [$order_id, $it["id"], $it["qty"], $it["price"], $it["subtotal"]]
      );
      db_execute("UPDATE products SET stock=stock-? WHERE id=?", [$it["qty"], $it["id"]]);
    }

    db_execute("UPDATE customers SET points_balance=? WHERE id=?", [$new_balance, $customer_id]);

    if ($points_earned > 0) {
      add_point_tx($customer_id, "EARN", "Checkout order #".$order_id, $points_earned, "ONLINE");
    }

    $mysqli->commit();
    cart_clear();

    return ["ok"=>true, "message"=>"Checkout berhasil! Order #{$order_id}. Poin bertambah {$points_earned}.", "order_id"=>$order_id];
  } catch (Exception $e) {
    $mysqli->rollback();
    return ["ok"=>false, "message"=>$e->getMessage()];
  }
}
