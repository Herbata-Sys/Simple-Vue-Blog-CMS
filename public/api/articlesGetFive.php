<?php
SESSION_START();

require_once "model/api.php";
require_once "model/article.php";
$api = new Api;
$article = new Article;

if (!empty($_GET['page'])) {
  $data = [
    'page' => $_GET['page'],
  ];
  echo $article->getFive($data);
}
else
  echo $api->create_object('Nie podano strony ?page=1');
?>