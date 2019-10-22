<?php
require_once "api.php";

	class Blog extends api {
		function info() {
			try {
				$stmt = $this->pdo->prepare('SELECT * FROM blog WHERE id = 1');
				$stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

				return $this->create_object('Blog info', true, $row);
			} catch(PDOException $e) {
				return $this->create_object('Połączenie z bazą nie mogło zostać utworzone / Błąd zapytania');
			}
		}

		function updateBlog($data, $logo) {
			$data = $this->test_input($data);
			['title' => $title, 'subtitle' => $subtitle, 'captcha' => $captcha] = $data;

			try {
				if ($logo != 'empty') {
					$im = imagecreatefromstring(file_get_contents($logo['tmp_name']));
					$im = imagescale($im, 32);
					imagewebp($im, '../img/favicon.ico');
					$im = imagecreatefromstring(file_get_contents($logo['tmp_name']));
					$im = imagescale($im, 50);
					imagewebp($im, '../img/logo.png');
				}

				$stmt = $this->pdo->prepare('UPDATE blog SET title = :title, subtitle = :subtitle, captcha = :captcha WHERE id = 1');
				$stmt->bindValue(':title', $title, PDO::PARAM_STR);
				$stmt->bindValue(':subtitle', $subtitle, PDO::PARAM_STR);
				$stmt->bindValue(':captcha', $captcha, PDO::PARAM_STR);
				$stmt->execute();
				$stmt->closeCursor();

				return $this->create_object('Zmiany zostały zapisane', true);
			} catch(PDOException $e) {
				return $this->create_object('Połączenie z bazą nie mogło zostać utworzone / Błąd zapytania');
			}
		}
	}
?>
