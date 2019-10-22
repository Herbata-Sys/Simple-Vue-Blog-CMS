<?php
SESSION_START();

require_once "model/api.php";
require_once "model/account.php";
$api = new Api;
$account = new Account;

if (!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['passwordRepeat']) && !empty($_POST['captcha'])) {
  $data = [
    'name' => $_POST['name'],
    'email' => $_POST['email'],
    'password' => $_POST['password'],
    'passwordRepeat' => $_POST['passwordRepeat'],
    'captcha' => $_POST['captcha']
  ];
  echo $account->register($data);
}
else
  echo $api->create_object('Uzupełnij wszystkie pola');
?>