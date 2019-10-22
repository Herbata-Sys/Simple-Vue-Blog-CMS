<?php
SESSION_START();

require_once "model/api.php";
require_once "model/account.php";
$api = new Api;
$account = new Account;

if (!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['captcha'])) {
  $data = [
    'email' => $_POST['email'],
    'password' => $_POST['password'],
    'captcha' => $_POST['captcha'],
  ];
  echo $account->login($data);
}
else
  echo $api->create_object('Nie podano wszystkich danych');
?>