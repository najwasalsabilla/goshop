<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/config.php";

echo "OK CONNECT. DB=" . $DB_NAME . " | charset=" . $mysqli->character_set_name();
