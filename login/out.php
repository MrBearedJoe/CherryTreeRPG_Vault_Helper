<?php
if (isset($_COOKIE['Vault_login'])) {
  unset($_COOKIE['Vault_login']);
  setcookie('Vault_login', '', -1, '/');
}
if (isset($_GET['alert'])) echo "<meta http-equiv='refresh' content=\"0; url=./?alert={$_GET['alert']}\">";
else echo "<meta http-equiv='refresh' content=\"0; url=./\">";
