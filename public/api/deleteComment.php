<?php
SESSION_START();

require_once "model/api.php";
require_once "model/comments.php";
$api = new Api;
$comments = new Comments;

if (empty($_SESSION['logged']) || empty($_SESSION['admin']) || $_SESSION['admin'] == "0") {
  echo $api->create_object('Nie jesteś zalogowany jako administrator');
  exit();
}

if (!empty($_GET['id'])) {
  $data = [
    'id' => $_GET['id'],
  ];
  echo $comments->deleteComment($data);
}
else
  echo $api->create_object('Nie podano wszystkich danych');
?>