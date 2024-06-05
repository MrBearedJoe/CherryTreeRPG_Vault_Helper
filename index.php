<?php
include_once "./template/header.html";

$filePath = "./src/json.json";
include_once "./_functions.php";

if (isset($_GET['admin']) || isset($_GET['user'])) include_once "./sections/_admin.php";
if (isset($_GET['admin'])) include_once "./sections/_admin_forms.html";


$jsonData = openFile($filePath);
include_once "./sections/_stats.php";
include_once "./sections/_list.php";
include_once "./sections/_logs.php";

include_once "./template/footer.html";
