<?php
SESSION_START();

require_once "model/api.php";
require_once "model/account.php";
$api = new Api;
$account = new Account;

if (!empty($_POST['captcha'])) {
  $data = [
    'captcha' => $_POST['captcha']
  ];
  echo $account->deleteAccount($data);
}
else
  echo $api->create_object('Uzupełnij wszystkie pola');
?>