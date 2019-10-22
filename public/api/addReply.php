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

if (!empty($_POST['content']) && !empty($_POST['comment_id']) && !empty($_POST['article'])) {
  $data = [
    'content' => $_POST['content'],
    'comment_id' => $_POST['comment_id'],
    'article' => $_POST['article'],
  ];
  echo $comments->addReply($data);
}
else
  echo $api->create_object('Nie podano wszystkich danych');
?>