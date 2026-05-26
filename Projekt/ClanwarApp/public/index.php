<?php
// Vstupní bod celé aplikace — všechny požadavky procházejí přes tento soubor
session_start();

// Zobrazení chyb — vhodné pro vývoj, před nasazením vypnout
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// BASE_URL se počítá dynamicky, aby aplikace fungovala i v podsložce
$baseDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
define('BASE_URL', $baseDir);

require_once '../core/App.php';
$app = new App(); // Router parsuje URL a spustí příslušný kontroler