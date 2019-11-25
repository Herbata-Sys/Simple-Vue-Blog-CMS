<?php
SESSION_START();

require_once "model/api.php";
require_once "model/account.php";
$api = new Api;
$account = new Account;

if (empty($_SESSION['logged'])) {
  echo $api->create_object('Nie jesteś zalogowany');
  exit();
}

if (isset($_FILES['file'])) {
  echo $account->changeAvatar($_FILES['file']);
}
else
  echo $api->create_object('Nie podano wszystkich danych');
?>