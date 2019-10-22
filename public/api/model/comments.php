<?php
require_once "api.php";

class Comments extends api {
	function getComments($data) {
		$data = $this->test_input($data);
		['article_id' => $article_id] = $data;

		try {
			$stmt = $this->pdo->prepare('SELECT comments.*, comments.author as "author_id", users.name as "author" FROM comments JOIN users WHERE comments.author = users.id AND article = :article_id ORDER BY id DESC');
			$stmt->bindValue(':article_id', $article_id, PDO::PARAM_INT);
			$stmt->execute();

			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			foreach ($rows as &$row) {
				if (!empty($_SESSION['logged'])) {
					$stmt = $this->pdo->prepare('SELECT type FROM votes_comments WHERE user = :user AND comment = :comment_id');
					$stmt->bindValue(':user', $_SESSION['user_id'], PDO::PARAM_INT);
					$stmt->bindValue(':comment_id', $row['id'], PDO::PARAM_INT);
					$stmt->execute();
					$count = $stmt->rowCount();
					$vote = $stmt->fetch(PDO::FETCH_ASSOC);
					$stmt->closeCursor();
					if ($count) {
						if ($vote['type'] == "up")
							$type = 1;
						else
							$type = -1;
						$row['voted'] = $type;
					}
				}

				$stmt = $this->pdo->prepare('SELECT answers.*, answers.author as "author_id", users.name as "author" FROM answers JOIN users WHERE answers.author = users.id AND comment = :comment_id');
				$stmt->bindValue(':comment_id', $row["id"], PDO::PARAM_INT);
				$stmt->execute();

				$answers = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				foreach ($answers as &$answer) {
					if (!empty($_SESSION['logged'])) {
						$stmt = $this->pdo->prepare('SELECT type FROM votes_answers WHERE user = :user AND answer = :answer_id');
						$stmt->bindValue(':user', $_SESSION['user_id'], PDO::PARAM_INT);
						$stmt->bindValue(':answer_id', $answer['id'], PDO::PARAM_INT);
						$stmt->execute();
						$count = $stmt->rowCount();
						$vote = $stmt->fetch(PDO::FETCH_ASSOC);
						$stmt->closeCursor();
						if ($count) {
							if ($vote['type'] == "up")
								$type = 1;
							else
								$type = -1;
							$answer['voted'] = $type;
						}
					}
				}

				$row["answers"] = $answers;
			}

			return $this->create_object('Komentarze', true, $rows);
		} catch(PDOException $e) {
			return $this->create_object('Połączenie z bazą nie mogło zostać utworzone / Błąd zapytania');
		}
	}

	function addComment($data) {
		$data = $this->test_input($data);
		['content' => $content, 'article_id' => $article_id] = $data;
		$author = $_SESSION['user_id'];
		$date = $this->getDate();

		try {
			$stmt = $this->pdo->prepare('INSERT INTO comments (author, content, date, down, up, article) VALUES (:author, :content, :date, 0, 0, :article)');
			$stmt->bindValue(':author', $author, PDO::PARAM_STR);
			$stmt->bindValue(':content', $content, PDO::PARAM_STR);
			$stmt->bindValue(':date', $date, PDO::PARAM_STR);
			$stmt->bindValue(':article', $article_id, PDO::PARAM_INT);
			$stmt->execute();
			$addedId['comment_id'] = $this->pdo->lastInsertId();
			$stmt->closeCursor();


			return $this->create_object('Dodano komentarz', true, $addedId);
		} catch(PDOException $e) {
			return $this->create_object('Połączenie z bazą nie mogło zostać utworzone / Błąd zapytania');
		}
	}

	function addReply($data) {
		$data = $this->test_input($data);
		['content' => $content, 'comment_id' => $comment_id, 'article' => $article] = $data;
		$author = $_SESSION['user_id'];
		$date = $this->getDate();

		try {
			$stmt = $this->pdo->prepare('INSERT INTO answers (author, content, date, down, up, comment, article) VALUES (:author, :content, :date, 0, 0, :comment, :article)');
			$stmt->bindValue(':author', $author, PDO::PARAM_STR);
			$stmt->bindValue(':content', $content, PDO::PARAM_STR);
			$stmt->bindValue(':date', $date, PDO::PARAM_STR);
			$stmt->bindValue(':comment', $comment_id, PDO::PARAM_INT);
			$stmt->bindValue(':article', $article, PDO::PARAM_INT);
			$stmt->execute();
			$addedId['answer_id'] = $this->pdo->lastInsertId();
			$stmt->closeCursor();

			return $this->create_object('Dodano odpowiedź', true, $addedId);
		} catch(PDOException $e) {
			return $this->create_object('Połączenie z bazą nie mogło zostać utworzone / Błąd zapytania');
		}
	}

	function voteComment($data) {
		$data = $this->test_input($data);
		['type' => $type, 'comment_id' => $comment_id] = $data;
		$user = $_SESSION['user_id'];
		$date = $this->getDate();

		try {
			$stmt = $this->pdo->prepare('SELECT * FROM votes_comments WHERE user = :user AND comment = :comment');
			$stmt->bindValue(':user', $user, PDO::PARAM_INT);
			$stmt->bindValue(':comment', $comment_id, PDO::PARAM_INT);
			$stmt->execute();
			$count = $stmt->rowCount();
			$vote = $stmt->fetch(PDO::FETCH_ASSOC);
			$stmt->closeCursor();

			if ($count) {
				$stmt = $this->pdo->prepare('DELETE FROM votes_comments WHERE user = :user AND comment = :comment');
				$stmt->bindValue(':user', $user, PDO::PARAM_INT);
				$stmt->bindValue(':comment', $comment_id, PDO::PARAM_INT);
				$stmt->execute();
				$stmt->closeCursor();

				if ($vote['type'] == "up")
					$stmt = $this->pdo->prepare('UPDATE comments SET up = up - 1 WHERE id = :comment');
				elseif ($vote['type'] == "down")
					$stmt = $this->pdo->prepare('UPDATE comments SET down = down - 1 WHERE id = :comment');
				$stmt->bindValue(':comment', $comment_id, PDO::PARAM_INT);
				$stmt->execute();
				$stmt->closeCursor();

				return $this->create_object('Głos został cofnięty', true);
			}


			$stmt = $this->pdo->prepare('INSERT INTO votes_comments (user, date, type, comment) VALUES (:user, :date, :type, :comment)');
			$stmt->bindValue(':user', $user, PDO::PARAM_INT);
			$stmt->bindValue(':date', $date, PDO::PARAM_STR);
			$stmt->bindValue(':type', $type, PDO::PARAM_STR);
			$stmt->bindValue(':comment', $comment_id, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();

			if ($type === "up")
				$stmt = $this->pdo->prepare('UPDATE comments SET up = up + 1 WHERE id = :comment');
			elseif ($type === "down")
				$stmt = $this->pdo->prepare('UPDATE comments SET down = down + 1 WHERE id = :comment');

			$stmt->bindValue(':comment', $comment_id, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();

			return $this->create_object('Głos dodany', true);
		} catch(PDOException $e) {
			return $this->create_object('Połączenie z bazą nie mogło zostać utworzone / Błąd zapytania');
		}
	}

	function voteAnswer($data) {
		$data = $this->test_input($data);
		['type' => $type, 'answer_id' => $answer_id] = $data;
		$user = $_SESSION['user_id'];
		$date = $this->getDate();

		try {
			$stmt = $this->pdo->prepare('SELECT * FROM votes_answers WHERE user = :user AND answer = :answer');
			$stmt->bindValue(':user', $user, PDO::PARAM_INT);
			$stmt->bindValue(':answer', $answer_id, PDO::PARAM_INT);
			$stmt->execute();
			$count = $stmt->rowCount();
			$vote = $stmt->fetch(PDO::FETCH_ASSOC);
			$stmt->closeCursor();

			if ($count) {
				$stmt = $this->pdo->prepare('DELETE FROM votes_answers WHERE user = :user AND answer = :answer');
				$stmt->bindValue(':user', $user, PDO::PARAM_INT);
				$stmt->bindValue(':answer', $answer_id, PDO::PARAM_INT);
				$stmt->execute();
				$stmt->closeCursor();

				if ($vote['type'] == "up")
					$stmt = $this->pdo->prepare('UPDATE answers SET up = up - 1 WHERE id = :answer');
				elseif ($vote['type'] == "down")
					$stmt = $this->pdo->prepare('UPDATE answers SET down = down - 1 WHERE id = :answer');
				$stmt->bindValue(':answer', $answer_id, PDO::PARAM_INT);
				$stmt->execute();
				$stmt->closeCursor();

				return $this->create_object('Głos został cofnięty', true);
			}


			$stmt = $this->pdo->prepare('INSERT INTO votes_answers (user, date, type, answer) VALUES (:user, :date, :type, :answer)');
			$stmt->bindValue(':user', $user, PDO::PARAM_INT);
			$stmt->bindValue(':date', $date, PDO::PARAM_STR);
			$stmt->bindValue(':type', $type, PDO::PARAM_STR);
			$stmt->bindValue(':answer', $answer_id, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();

			if ($type === "up")
				$stmt = $this->pdo->prepare('UPDATE answers SET up = up + 1 WHERE id = :answer');
			elseif ($type === "down")
				$stmt = $this->pdo->prepare('UPDATE answers SET down = down + 1 WHERE id = :answer');

			$stmt->bindValue(':answer', $answer_id, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();

			return $this->create_object('Głos dodany', true);
		} catch(PDOException $e) {
			return $this->create_object('Połączenie z bazą nie mogło zostać utworzone / Błąd zapytania');
		}
	}

	function myComments() {
		$user_id = $_SESSION['user_id'];

		try {
			$stmt = $this->pdo->prepare('SELECT comments.*, users.name as "author" FROM comments JOIN users WHERE comments.author = users.id AND users.id = :user_id ORDER BY comments.id DESC');
			$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
			$stmt->execute();

			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			foreach ($rows as &$row) {
				if (!empty($_SESSION['logged'])) {
					$stmt = $this->pdo->prepare('SELECT type FROM votes_comments WHERE user = :user AND comment = :comment_id');
					$stmt->bindValue(':user', $user_id, PDO::PARAM_INT);
					$stmt->bindValue(':comment_id', $row['id'], PDO::PARAM_INT);
					$stmt->execute();
					$count = $stmt->rowCount();
					$vote = $stmt->fetch(PDO::FETCH_ASSOC);
					$stmt->closeCursor();
					if ($count) {
						if ($vote['type'] == "up")
							$type = 1;
						else
							$type = -1;
						$row['voted'] = $type;
					}
				}

				$stmt = $this->pdo->prepare('SELECT answers.*, users.name as "author" FROM answers JOIN users WHERE answers.author = users.id AND comment = :comment_id');
				$stmt->bindValue(':comment_id', $row["id"], PDO::PARAM_INT);
				$stmt->execute();

				$answers = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				foreach ($answers as &$answer) {
					if (!empty($_SESSION['logged'])) {
						$stmt = $this->pdo->prepare('SELECT type FROM votes_answers WHERE user = :user AND answer = :answer_id');
						$stmt->bindValue(':user', $user_id, PDO::PARAM_INT);
						$stmt->bindValue(':answer_id', $answer['id'], PDO::PARAM_INT);
						$stmt->execute();
						$count = $stmt->rowCount();
						$vote = $stmt->fetch(PDO::FETCH_ASSOC);
						$stmt->closeCursor();
						if ($count) {
							if ($vote['type'] == "up")
								$type = 1;
							else
								$type = -1;
							$answer['voted'] = $type;
						}
					}
				}

				$row["answers"] = $answers;
			}

			return $this->create_object('Komentarze', true, $rows);
		} catch(PDOException $e) {
			return $this->create_object('Połączenie z bazą nie mogło zostać utworzone / Błąd zapytania');
		}
	}

	function deleteComment($data) {
		$data = $this->test_input($data);
		['id' => $id] = $data;

		try {
      $stmt = $this->pdo->prepare('DELETE FROM answers WHERE comment = :id');
			$stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
			$stmt->closeCursor();

			$stmt = $this->pdo->prepare('DELETE FROM votes_comments WHERE comment = :id');
			$stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
      $stmt->closeCursor();

      $stmt = $this->pdo->prepare('DELETE FROM comments WHERE id = :id');
			$stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
      $stmt->closeCursor();

			return $this->create_object('Komentarz został usunięty', true);
		} catch(PDOException $e) {
			return $this->create_object('Połączenie z bazą nie mogło zostać utworzone / Błąd zapytania');
		}
	}

	function deleteAnswer($data) {
		$data = $this->test_input($data);
		['id' => $id] = $data;

		try {
			$stmt = $this->pdo->prepare('DELETE FROM votes_answers WHERE answer = :id');
			$stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
			$stmt->closeCursor();

      $stmt = $this->pdo->prepare('DELETE FROM answers WHERE id = :id');
			$stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
			$stmt->closeCursor();

			return $this->create_object('Odpowiedź została usunięta', true);
		} catch(PDOException $e) {
			return $this->create_object('Połączenie z bazą nie mogło zostać utworzone / Błąd zapytania');
		}
  }
}
?>
