<?php
SESSION_START();

require_once "model/api.php";
require_once "model/article.php";
$api = new Api;
$article = new Article;

if (!empty($_GET['id'])) {
  $data = [
    'article_id' => $_GET['id'],
  ];
  echo $article->getArticle($data);
}
else
  echo $api->create_object('Nie podano wszystkich danych');
?>