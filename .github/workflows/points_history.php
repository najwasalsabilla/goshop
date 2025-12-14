<?php
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../auth.php";
require_once __DIR__ . "/../goshop_functions.php";

$user = current_user();
if (!$user || !isset($user["id"])) {
  echo json_encode(["ok" => false, "message" => "Unauthorized"]);
  exit;
}

$days = isset($_GET["days"]) ? (int)$_GET["days"] : 30;
if ($days <= 0) $days = 30;
if ($days > 365) $days = 365;

$customerId = (int)$user["id"];

if (!function_exists("db_fetch_all")) {
  echo json_encode(["ok" => false, "message" => "db_fetch_all() not found"]);
  exit;
}

$sql = "
  SELECT type, description, points, created_at
  FROM point_transactions
  WHERE customer_id = ?
    AND created_at >= (NOW() - INTERVAL ? DAY)
  ORDER BY created_at DESC
  LIMIT 50
";
$rows = db_fetch_all($sql, [$customerId, $days]);

echo json_encode([
  "ok" => true,
  "history" => $rows ?: [],
]);
