<?php
include_once "./template/header.html";

$filePath = "./src/json.json";
include_once "./_functions.php";

$adminPasswords = [
  'QWRhbUlzUmVhbGx5U2V4eQ==' //hint: adam
];

if (isset($_GET['admin']) && in_array(base64_encode($_GET['admin']), $adminPasswords)) include_once "./sections/_admin.php";

$jsonData = openFile($filePath);
include_once "./sections/_stats.php";
include_once "./sections/_list.php";
include_once "./sections/_logs.php";

include_once "./template/footer.html";
