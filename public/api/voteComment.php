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

if (!empty($_POST['type']) && !empty($_POST['comment_id'])) {
  $data = [
    'type' => $_POST['type'],
    'comment_id' => $_POST['comment_id'],
  ];
  echo $comments->voteComment($data);
}
else
  echo $api->create_object('Nie podano wszystkich danych');
?>