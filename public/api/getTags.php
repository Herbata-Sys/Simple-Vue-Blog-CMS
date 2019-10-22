<?php
SESSION_START();

require_once "model/api.php";
require_once "model/tags.php";
$api = new Api;
$tags = new Tags;

echo $tags->getTags();
?>