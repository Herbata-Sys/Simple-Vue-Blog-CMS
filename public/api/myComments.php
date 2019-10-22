<?php
SESSION_START();

require_once "model/api.php";
require_once "model/comments.php";
$api = new Api;
$comments = new Comments;

if (empty($_SESSION['logged'])) {
  echo $api->create_object('Najpierw musisz się zalogować');
  exit();
}

echo $comments->myComments();
?>