<?php
SESSION_START();

require_once "model/api.php";
require_once "model/users.php";
$api = new Api;
$users = new Users;

if (empty($_SESSION['logged']) || empty($_SESSION['admin']) || $_SESSION['admin'] == "0") {
  echo $api->create_object('Nie jesteś zalogowany jako administrator');
  exit();
}

echo $users->getAll();
?>