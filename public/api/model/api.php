<?php
  class Api {
    public $pdo;

		function __construct() {
			include 'config.php';
			$this->pdo = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}

    public function test_input($data) {
			foreach( $data as $key => $value ){
        $data[$key] = trim($value);
        $data[$key] = stripslashes($value);
        $data[$key] = htmlspecialchars($value);
      }
			return $data;
    }

    public function create_object($info, $success = false, $data = null) {
      return json_encode(["info" => $info, "success" => $success, "data" => $data]);
    }

    public function captcha_verify($captcha) {
      include 'config.php';
      $url = 'https://www.google.com/recaptcha/api/siteverify';
      $data = array(
        'secret' => $recaptcha_server_key,
        'response' => $captcha,
      );
      $options = array(
        'http' => array (
          'header' => "Content-Type: application/x-www-form-urlencoded",
          'method' => 'POST',
          'content' => http_build_query($data)
        )
      );
      $context  = stream_context_create($options);
      $verify = file_get_contents($url, false, $context);
      $captcha_success = json_decode($verify);
      if ($captcha_success->success==false) {
        return false;
      } else if ($captcha_success->success==true) {
        return true;
      }
    }

    public function getDate() {
      return date("d-m-Y H:i");
    }
  }
?>