<?php
SESSION_START();

require_once "model/api.php";
require_once "model/tags.php";
$api = new Api;
$tags = new Tags;

if (!empty($_GET['id'])) {
  $data = [
    'tag_id' => $_GET['id'],
  ];
  echo $tags->getTagInfo($data);
}
else
  echo $api->create_object('Nie podano wszystkich danych');
?>