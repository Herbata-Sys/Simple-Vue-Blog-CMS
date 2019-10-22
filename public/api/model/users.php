<?php
require_once "api.php";

class Users extends api {
	function getAll() {
		try {
			$stmt = $this->pdo->prepare('SELECT id, email, name, reg_date, admin FROM users');
			$stmt->execute();
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();

			return $this->create_object('Użytkownicy', true, $rows);
		} catch(PDOException $e) {
			return $this->create_object('Połączenie z bazą nie mogło zostać utworzone / Błąd zapytania');
		}
  }

  function deleteUser($data) {
		$data = $this->test_input($data);
    ['id' => $id] = $data;

    if ($id == $_SESSION['user_id'])
      return $this->create_object('Nie możesz usunąć swojego konta');

		try {
      $stmt = $this->pdo->prepare('DELETE FROM comments WHERE author = :id');
			$stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
      $stmt->closeCursor();

      $stmt = $this->pdo->prepare('DELETE FROM answers WHERE author = :id');
			$stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
      $stmt->closeCursor();

      $stmt = $this->pdo->prepare('DELETE FROM articles WHERE author = :id');
			$stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
      $stmt->closeCursor();

			$stmt = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
			$stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
      $stmt->closeCursor();

			return $this->create_object('Użytkownik został usunięty', true);
		} catch(PDOException $e) {
			return $this->create_object('Połączenie z bazą nie mogło zostać utworzone / Błąd zapytania');
		}
  }

  function removeAdmin($data) {
		$data = $this->test_input($data);
    ['id' => $id] = $data;

    if ($id == $_SESSION['user_id'])
      return $this->create_object('Nie możesz odebrać uprawnień samemu sobie');

		try {
			$stmt = $this->pdo->prepare('UPDATE users SET admin = 0 WHERE id = :id');
			$stmt->bindValue(':id', $id, PDO::PARAM_INT);
			$stmt->execute();

			return $this->create_object('Uprawnienia administracyjne zostały usunięte', true);
		} catch(PDOException $e) {
			return $this->create_object('Połączenie z bazą nie mogło zostać utworzone / Błąd zapytania');
		}
	}

	function addAdmin($data) {
		$data = $this->test_input($data);
    ['id' => $id] = $data;

    if ($id == $_SESSION['user_id'])
      return $this->create_object('Nie możesz nadać uprawnień samemu sobie');

		try {
			$stmt = $this->pdo->prepare('UPDATE users SET admin = 1 WHERE id = :id');
			$stmt->bindValue(':id', $id, PDO::PARAM_INT);
			$stmt->execute();

			return $this->create_object('Uprawnienia administracyjne zostały dodane', true);
		} catch(PDOException $e) {
			return $this->create_object('Połączenie z bazą nie mogło zostać utworzone / Błąd zapytania');
		}
	}
}
?>