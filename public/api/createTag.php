<?php
SESSION_START();

require_once "model/api.php";
require_once "model/tags.php";
$api = new Api;
$tag = new Tags;

if (empty($_SESSION['logged']) || empty($_SESSION['admin']) || $_SESSION['admin'] == "0") {
  echo $api->create_object('Nie jesteś zalogowany jako administrator');
  exit();
}

if (!empty($_POST['name'])) {
  $data = [
    'name' => $_POST['name'],
  ];
  echo $tag->createTag($data);
}
else
  echo $api->create_object('Nie podano wszystkich danych');
?>