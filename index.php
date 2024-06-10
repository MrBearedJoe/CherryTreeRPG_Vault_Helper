<?php
// testing login system
if (empty($_COOKIE['Vault_login'])) echo "<meta http-equiv='refresh' content=\"0; url=./login/\">";
$vault_login = explode("-", $_COOKIE['Vault_login']);
$login_type = $vault_login[0];
$login_as = ($vault_login[1]) ? $vault_login[1] : "";

include_once "./template/header.html";

$filePath = "./src/json.json";
include_once "./_functions.php";

//might move to json file to add a remove pass from site
// $adminPasswords = [
//   'QWRhbUlzUmVhbGx5U2V4eQ==' //hint: adam
// ];
// $modPasswords = [
//   'QWRhbUlzUmVhbGx5U2V4eQ==' //hint: adam
// ];

$users_filePath = "./src/users.json";
$users = openFile($users_filePath);
$adminPasswords = $users['admin'];
$modPasswords = $users['mod'];
if ($login_type == "admin" && in_array(base64_encode($login_as), $adminPasswords)) include_once "./sections/_admin.php";
elseif ($login_type == "mod" && in_array(base64_encode($login_as), $modPasswords)) include_once "./sections/_admin.php";
elseif ($login_type == "user" && isset($_GET['user'])) include_once "./sections/_user_pull.php";

$jsonData = openFile($filePath);
if (!empty($jsonData['codes'])) {
  include_once "./sections/_stats.php";
  include_once "./sections/_list.php";
  if ($login_type == "admin" || $login_type == "mod") include_once "./sections/_logs.php";
}

include_once "./template/footer.html";