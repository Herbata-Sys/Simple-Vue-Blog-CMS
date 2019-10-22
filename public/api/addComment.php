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

if (!empty($_POST['content']) && !empty($_POST['article_id'])) {
  $data = [
    'content' => $_POST['content'],
    'article_id' => $_POST['article_id'],
  ];
  echo $comments->addComment($data);
}
else
  echo $api->create_object('Nie podano wszystkich danych');
?>