<?php
if (isset($_COOKIE['Vault_login'])) {
  unset($_COOKIE['Vault_login']);
  setcookie('Vault_login', '', -1, '/');
}
echo "<meta http-equiv='refresh' content=\"0; url=./\">";
