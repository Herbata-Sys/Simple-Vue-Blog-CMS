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

if (!empty($_POST['old']) && !empty($_POST['new']) && !empty($_POST['newRepeat'])) {
  $data = [
    'old' => $_POST['old'],
    'new' => $_POST['new'],
    'newRepeat' => $_POST['newRepeat'],
  ];
  echo $account->changePassword($data);
}
else
  echo $api->create_object('Nie podano wszystkich danych');
?>