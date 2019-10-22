<?php
require_once "api.php";

class Install extends api {
  public $install_pdo;

  public function __construct() {
    return true;
  }

  function connect() {
    include 'model/config.php';
    $this->install_pdo = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);
    $this->install_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }

  public function create_object($info, $success = false, $data = null) {
    return json_encode(["info" => $info, "success" => $success, "data" => $data]);
  }

  function connectNoDb() {
    include 'model/config.php';
    $this->install_pdo = new PDO("mysql:host=$servername;charset=utf8", $username, $password);
    $this->install_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }

  function installation($data) {
    //['servername' => $servername, 'username' => $username, 'password' => $password, 'database' => $database, 'recaptcha_server_key' => $recaptcha_server_key] = $data;
    //create config file with database and captcha data
    if (!$this->createConfig($data))
      return $this->create_object('Nie udało się utworzyć pliku konfiguracyjnego');

    //connect to database
    $this->connectNoDb();

    //create database, add tables and structures
    if (!$this->createDatabase($data))
      return $this->create_object('Nie udało się połączyć z bazą i utworzyć tabel (możliwe, że baza danych o nazwie "'.$data['database'].'" już istnieje, jeśli tak jest zmień nazwę bazy lub ją skasuj)');

    //insert into blog table blog information
    if (!$this->createBlogInfo($data))
      return $this->create_object('Nie udało się dodać informacji o blogu do tabeli "blog"');

    //create logo and favicon
    if (!$this->createLogo($data))
      return $this->create_object('Nie udało się utworzyć logo');

    //create admin account
    if (!$this->createAdmin($data))
      return $this->create_object('Nie udało się utworzyć konta administratora');

    if (!$this->clearFiles())
      return $this->create_object('Nie udało się usunąć folderu "Install" z serwera, koniecznie zrób to ręcznie!');

    return $this->create_object('Instalacja zakończona powodzeniem, możesz przejść do głównego katalogu strony, wszystkie pliki instalacyjne zostały automatycznie usunięte w celach bezpieczeństwa. Po przejściu na stronę zaloguj się danymi do konta administratora podanymi w tym formularzu.', true);
  }

	function createConfig($data) {
    ['servername' => $servername, 'username' => $username, 'password' => $password, 'database' => $database, 'recaptcha_server_key' => $recaptcha_server_key] = $data;
    $content = '<?php'."\r\n";
    $content .= '  //database config'."\r\n";
    $content .= '  $servername = \''.$servername.'\'; //host'."\r\n";
    $content .= '  $username = \''.$username.'\'; //username'."\r\n";
    $content .= '  $password = \''.$password.'\'; //password'."\r\n";
    $content .= '  $database = \''.$database.'\'; //database'."\r\n";
    $content .= '  $recaptcha_server_key = \''.$recaptcha_server_key.'\'; //recaptcha serverside key'."\r\n";
    $content .= '?>';
		try {
      file_put_contents('model/config.php', $content);
      return true;
		} catch(Exception $e) {
			return false;
		}
  }

  function createDatabase($data) {
    ['database' => $database] = $data;

    try {
      //create database
      $this->install_pdo->exec("CREATE DATABASE `$database`");
      $this->connect();
      //create answers table
      $this->install_pdo->exec('CREATE TABLE `answers` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `author` int(11) NOT NULL,
        `content` text COLLATE utf8_polish_ci NOT NULL,
        `date` text COLLATE utf8_polish_ci NOT NULL,
        `down` int(11) NOT NULL,
        `up` int(11) NOT NULL,
        `comment` int(11) NOT NULL,
        `article` int(11) NOT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;');

      //create table articles
      $this->install_pdo->exec('CREATE TABLE `articles` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `author` int(11) NOT NULL,
        `date` text COLLATE utf8_polish_ci NOT NULL,
        `image` text COLLATE utf8_polish_ci NOT NULL,
        `tags` text COLLATE utf8_polish_ci NOT NULL,
        `title` text COLLATE utf8_polish_ci NOT NULL,
        `text` text COLLATE utf8_polish_ci NOT NULL,
        `shortText` text COLLATE utf8_polish_ci NOT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;');

      //create table blog
      $this->install_pdo->exec('CREATE TABLE `blog` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `title` text COLLATE utf8_polish_ci NOT NULL,
        `subtitle` text COLLATE utf8_polish_ci NOT NULL,
        `captcha` text COLLATE utf8_polish_ci NOT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;');

      //create table comments
      $this->install_pdo->exec('CREATE TABLE `comments` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `author` int(11) NOT NULL,
        `content` text COLLATE utf8_polish_ci NOT NULL,
        `date` text COLLATE utf8_polish_ci NOT NULL,
        `down` int(11) NOT NULL,
        `up` int(11) NOT NULL,
        `article` int(11) NOT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;');

      //create table tags
      $this->install_pdo->exec('CREATE TABLE `tags` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` text COLLATE utf8_polish_ci NOT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;');

      //create table users
      $this->install_pdo->exec('CREATE TABLE `users` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `email` varchar(60) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
        `name` varchar(60) COLLATE utf8_polish_ci NOT NULL,
        `password` varchar(100) COLLATE utf8_polish_ci NOT NULL,
        `reg_date` varchar(60) COLLATE utf8_polish_ci NOT NULL,
        `admin` tinyint(1) NOT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;');

      //create table votes_answers
      $this->install_pdo->exec('CREATE TABLE `votes_answers` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `type` varchar(4) COLLATE utf8_polish_ci NOT NULL,
        `user` int(11) NOT NULL,
        `date` varchar(20) COLLATE utf8_polish_ci NOT NULL,
        `answer` int(11) NOT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;');

      //create table votes_comments
      $this->install_pdo->exec('CREATE TABLE `votes_comments` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `type` varchar(4) COLLATE utf8_polish_ci NOT NULL,
        `user` int(11) NOT NULL,
        `date` varchar(20) COLLATE utf8_polish_ci NOT NULL,
        `comment` int(11) NOT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;');

      return true;
    } catch(PDOException $e) {
      return false;
    }
  }

  function createBlogInfo($data) {
    ['blogName' => $blogName, 'blogSubtitle' => $blogSubtitle, 'recaptcha_website_key' => $recaptcha_website_key] = $data;

    try {
      $this->install_pdo->exec("INSERT INTO blog (title, subtitle, captcha) VALUES ( '$blogName' , '$blogSubtitle' , '$recaptcha_website_key' )");

      return true;
    } catch(PDOException $e) {
      return false;
    }
  }

  function createLogo($data) {
    ['logo' => $logo] = $data;

    try {
      $im = imagecreatefromstring(file_get_contents($logo['tmp_name']));
      $im = imagescale($im, 32);
      imagewebp($im, '../img/favicon.ico');
      $im = imagecreatefromstring(file_get_contents($logo['tmp_name']));
      $im = imagescale($im, 50);
      imagewebp($im, '../img/logo.png');

      return true;
    } catch(PDOException $e) {
      return false;
    }
  }

  function createAdmin($data) {
    ['blogUserEmail' => $blogUserEmail, 'blogUserName' => $blogUserName, 'blogUserPassword' => $blogUserPassword] = $data;
    $blogUserPassword = password_hash($blogUserPassword, PASSWORD_DEFAULT);
    $reg_date = date('d-m-Y G:i');

    try {
      $this->install_pdo->exec("INSERT INTO users (name, email, password, reg_date, admin) VALUES ('$blogUserName', '$blogUserEmail', '$blogUserPassword', '$reg_date', 1)");

      return true;
    } catch(PDOException $e) {
      return false;
    }
  }

  function clearFiles() {
    try {
      unlink('../install/index.html');
      rmdir('../install');
      unlink('install.php');

      return true;
    } catch(PDOException $e) {
      return false;
    }
  }
}
?>
