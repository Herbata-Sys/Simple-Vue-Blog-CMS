<?php
require_once "api.php";

class Account extends api {
	function login($data) {
		$data = $this->test_input($data);
		['email' => $email, 'password' => $password, 'captcha' => $captcha] = $data;

		if (!$this->captcha_verify($captcha))
			return $this->create_object('Nieprawidłowa captcha');

		try {
			$stmt = $this->pdo->prepare('SELECT * FROM users WHERE email LIKE :email LIMIT 1');
			$stmt->bindValue(':email', $email, PDO::PARAM_STR);
			$stmt->execute();

			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$stmt->closeCursor();

			if (password_verify($password, $row['password'])) {
				$_SESSION['logged'] = true;
				$_SESSION['name'] = $row['name'];
				$_SESSION['email'] = $row['email'];
				$_SESSION['admin'] = $row['admin'];
				$_SESSION['user_id'] = $row['id'];

				unset($row['password']);
				return $this->create_object('Zalogowano', true, $row);
			} else {
				return $this->create_object('Email lub hasło się nie zgadzają');
			}
		} catch(PDOException $e) {
			return $this->create_object('Połączenie z bazą nie mogło zostać utworzone / Błąd zapytania');
		}
	}

	function logout() {
		session_destroy();
		return $this->create_object('Wylogowano');
	}

	function isLogged() {
		if (isset($_SESSION['logged'])) {
			if ($_SESSION['logged'] !== true)
				return $this->create_object('Niezalogowany');

			try {
				$stmt = $this->pdo->prepare('SELECT * FROM users WHERE name LIKE :name');
				$stmt->bindValue(':name', $_SESSION['name'], PDO::PARAM_STR);
				$stmt->execute();
				$count = $stmt->rowCount();
				$stmt->closeCursor();
				if($count)
					return $this->create_object('Zalogowany', true);
				else
					return $this->create_object('Niealogowany');
			} catch(PDOException $e) {
				return $this->create_object('Połączenie z bazą nie mogło zostać utworzone / Błąd zapytania');
			}
		} else
		return $this->create_object('Niezalogowany');
	}

	function register($data) {
		$data = $this->test_input($data);
		['name' => $name, 'email' => $email, 'password' => $password, 'passwordRepeat' => $passwordRepeat, 'captcha' => $captcha] = $data;

		if (!$this->captcha_verify($captcha))
			return $this->create_object('Nieprawidłowa captcha');

		try {
			$stmt = $this->pdo->prepare('SELECT * FROM users WHERE email LIKE :email');
			$stmt->bindValue(':email', $email, PDO::PARAM_STR);
			$stmt->execute();
			$count = $stmt->rowCount();
			$stmt->closeCursor();

			if ($count)
				return $this->create_object('Konto o podanym adresie email już istnieje');

			$stmt = $this->pdo->prepare('SELECT * FROM users WHERE name LIKE :name');
			$stmt->bindValue(':name', $name, PDO::PARAM_STR);
			$stmt->execute();
			$count = $stmt->rowCount();
			$stmt->closeCursor();

			if ($count)
				return $this->create_object('Konto o podanej nazwie już istnieje');

			if ($password !== $passwordRepeat)
				return $this->create_object('Podane hasła nie są takie same');

			$stmt = $this->pdo->prepare('INSERT INTO users (name, email, password, reg_date, admin) VALUES (:name, :email, :password, :reg_date, 0)');
			$stmt->bindValue(':name', $name, PDO::PARAM_STR);
			$stmt->bindValue(':email', $email, PDO::PARAM_STR);
			$stmt->bindValue(':password', password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
			$stmt->bindValue(':reg_date', date('d-m-Y G:i'), PDO::PARAM_STR);
			$stmt->execute();
			$stmt->closeCursor();
			return $this->create_object('Zostałeś zarejestrowany, możesz się zalogować', true);

		} catch(PDOException $e) {
			return $this->create_object('Połączenie z bazą nie mogło zostać utworzone / Błąd zapytania');
		}
	}

	function changePassword($data) {
		$data = $this->test_input($data);
		['old' => $old, 'new' => $new, 'newRepeat' => $newRepeat] = $data;

		try {
			$stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :user_id');
			$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
			$stmt->execute();

			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$stmt->closeCursor();

			if (!password_verify($old, $row['password']))
				return $this->create_object('Podałeś nieprawidłowe stare hasło');

			if ($new !== $newRepeat)
				return $this->create_object('Nowe hasło nie jest takie samo w obu polach');

			$stmt = $this->pdo->prepare('UPDATE users SET password = :password WHERE id = :user_id');
			$stmt->bindValue(':password', password_hash($new, PASSWORD_DEFAULT), PDO::PARAM_STR);
			$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();
			return $this->create_object('Hasło zostało pomyślnie zmienione', true);

		} catch(PDOException $e) {
			return $this->create_object('Połączenie z bazą nie mogło zostać utworzone / Błąd zapytania');
		}
	}
}
?>
