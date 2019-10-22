<?php
SESSION_START();

require_once "model/api.php";
require_once "model/blog.php";
$api = new Api;
$blog = new Blog;

echo $blog->info();
?>