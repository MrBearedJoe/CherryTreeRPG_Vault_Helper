<?php
include_once "./template/header.php";
$filePath = "./json.json";
include_once "./_functions.php";

if ($_GET['admin']) include_once "_admin.php";
include_once "_stats.php";
include_once "_list.php";
include_once "_logs.php";

include_once "./template/footer.php";
