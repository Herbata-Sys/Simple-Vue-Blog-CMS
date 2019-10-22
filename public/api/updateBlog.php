<?php
SESSION_START();

require_once "model/api.php";
require_once "model/blog.php";
$api = new Api;
$blog = new Blog;

if (empty($_SESSION['logged']) || empty($_SESSION['admin']) || $_SESSION['admin'] == "0") {
  echo $api->create_object('Nie jesteś zalogowany jako administrator');
  exit();
}

if (!empty($_POST['title']) && !empty($_POST['subtitle']) && !empty($_POST['captcha']) && !empty($_FILES['file'])) {
  $data = [
    'title' => $_POST['title'],
    'subtitle' => $_POST['subtitle'],
    'captcha' => $_POST['captcha'],
  ];
  echo $blog->updateBlog($data, (isset($_POST['file']) == 'empty' ? 'empty' : $_FILES['file']));
}
else
  echo $api->create_object('Nie podano wszystkich danych');
?>