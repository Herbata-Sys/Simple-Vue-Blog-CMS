<?php
SESSION_START();

require_once "model/install.php";
$install = new Install;

//install
if (!empty($_POST['blogName']) && !empty($_POST['blogSubtitle']) && !empty($_FILES['logo']) && !empty($_POST['blogUserEmail']) && !empty($_POST['blogUserName']) && !empty($_POST['blogUserPassword']) && !empty($_POST['servername']) && !empty($_POST['username']) && !empty($_POST['database']) && !empty($_POST['recaptcha_website_key']) && !empty($_POST['recaptcha_server_key']) && extension_loaded('gd') && is_writable('./model/') && is_writable('../api/') && is_writable('../img/avatars') && is_writable('../img/articles') && is_writable('../img/articles/thumb') && is_writable('../install')) {
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
} else if (!extension_loaded('gd')) {
  echo $install->create_object('Nie zainstalowano biblioteki GD', false);
} else if (!is_writable('./model/') || !is_writable('../api/') || !is_writable('../img/avatars') || !is_writable('../img/articles') || !is_writable('../img/articles/thumb') || !is_writable('../install')) {
  echo $install->create_object('Uprawnienia do zapisu plików nie zostały ustawione prawidłowo', false);
} else {
  echo $install->create_object('Nie podano wszystkich danych', false);
}

?>