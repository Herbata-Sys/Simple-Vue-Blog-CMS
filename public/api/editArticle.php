<?php
SESSION_START();

require_once "model/api.php";
require_once "model/article.php";
$api = new Api;
$article = new Article;

if (empty($_SESSION['logged']) || empty($_SESSION['admin']) || $_SESSION['admin'] == "0") {
  echo $api->create_object('Nie jesteś zalogowany jako administrator');
  exit();
}

if (!empty($_POST['id']) && !empty($_POST['title']) && !empty($_POST['text']) && !empty($_POST['shortText']) && !empty($_POST['tags']) && !empty($_POST['oldImage'])) {
  $data = [
    'id' => $_POST['id'],
    'title' => $_POST['title'],
    'shortText' => $_POST['shortText'],
    'oldImage' => $_POST['oldImage'],
  ];
  echo $article->editArticle($data, (isset($_POST['file']) == 'empty' ? 'empty' : $_FILES['file']), $_POST['tags'], $_POST['text']);
}
else
  echo $api->create_object('Nie podano wszystkich danych');
?>