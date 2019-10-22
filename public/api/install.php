<?php
SESSION_START();

require_once "model/api.php";
require_once "model/install.php";
$api = new Api;
$install = new Install;

//install
if (!empty($_POST['blogName']) && !empty($_POST['blogSubtitle']) && !empty($_FILES['logo']) && !empty($_POST['blogUserEmail']) && !empty($_POST['blogUserName']) && !empty($_POST['blogUserPassword']) && !empty($_POST['servername']) && !empty($_POST['username']) && !empty($_POST['database']) && !empty($_POST['recaptcha_website_key']) && !empty($_POST['recaptcha_server_key'])) {
  $data = [
    'blogName' => $_POST['blogName'],
    'blogSubtitle' => $_POST['blogSubtitle'],
    'logo' => $_FILES['logo'],
    'blogUserEmail' => $_POST['blogUserEmail'],
    'blogUserName' => $_POST['blogUserName'],
    'blogUserPassword' => $_POST['blogUserPassword'],
    'servername' => $_POST['servername'],
    'username' => $_POST['username'],
    'password' => $_POST['password'],
    'database' => $_POST['database'],
    'recaptcha_website_key' => $_POST['recaptcha_website_key'],
    'recaptcha_server_key' => $_POST['recaptcha_server_key'],
  ];
  echo $install->installation($data);
} else {
  echo $api->create_object('Nie podano wszystkich danych', false);
}
?>