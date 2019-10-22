<?php
SESSION_START();

require_once "model/api.php";
require_once "model/account.php";
$api = new Api;
$account = new Account;

echo $account->logout();
?>