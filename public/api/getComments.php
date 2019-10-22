<?php
SESSION_START();

require_once "model/api.php";
require_once "model/comments.php";
$api = new Api;
$comments = new Comments;

if (!empty($_GET['article_id'])) {
  $data = [
    'article_id' => $_GET['article_id'],
  ];
  echo $comments->getComments($data);
}
else
  echo $api->create_object('Nie podano wszystkich danych');
?>