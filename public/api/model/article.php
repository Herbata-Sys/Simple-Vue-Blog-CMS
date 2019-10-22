<?php
require_once "api.php";

class Article extends api {
	function getFive($data) {
		$data = $this->test_input($data);
		['page' => $page] = $data;

		try {
			$stmt = $this->pdo->prepare('SELECT articles.*, users.name as "author" FROM articles JOIN users WHERE articles.author = users.id ORDER BY id DESC LIMIT 6 OFFSET :offset');
			$stmt->bindValue(':offset', ($page-1)*6, PDO::PARAM_INT);
			$stmt->execute();

			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			foreach ($rows as &$row) {
				$tags = [];
				foreach (unserialize($row["tags"]) as $tag) {
					$stmt = $this->pdo->prepare('SELECT * FROM tags WHERE id = :tag');
					$stmt->bindValue(':tag', $tag, PDO::PARAM_INT);
					$stmt->execute();

					$result = $stmt->fetch(PDO::FETCH_ASSOC);
					$stmt->closeCursor();

					array_push($tags, $result);
				}

				$row["tags"] = $tags;
			}

			return $this->create_object('Artykuły', true, $rows);
		} catch(PDOException $e) {
			return $this->create_object('Połączenie z bazą nie mogło zostać utworzone / Błąd zapytania');
		}
	}

	function getAll() {
		try {
			$stmt = $this->pdo->prepare('SELECT articles.*, users.name as "author" FROM articles JOIN users WHERE articles.author = users.id ORDER BY id DESC');
			$stmt->execute();

			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();

			return $this->create_object('Artykuły', true, $rows);
		} catch(PDOException $e) {
			return $this->create_object('Połączenie z bazą nie mogło zostać utworzone / Błąd zapytania');
		}
	}

	function getArticle($data) {
		$data = $this->test_input($data);
		['article_id' => $article_id] = $data;

		try {
			$stmt = $this->pdo->prepare('SELECT articles.*, users.name as "author" FROM articles JOIN users WHERE articles.author = users.id AND articles.id = :article_id');
			$stmt->bindValue(':article_id', $article_id, PDO::PARAM_INT);
			$stmt->execute();

			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$stmt->closeCursor();

			$tags = [];
			foreach (unserialize($row["tags"]) as $tag) {
				$stmt = $this->pdo->prepare('SELECT * FROM tags WHERE id = :tag');
				$stmt->bindValue(':tag', $tag, PDO::PARAM_INT);
				$stmt->execute();

				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				array_push($tags, $result);
			}

			$row["tags"] = $tags;

			return $this->create_object('Artykuł', true, $row);
		} catch(PDOException $e) {
			return $this->create_object('Połączenie z bazą nie mogło zostać utworzone / Błąd zapytania');
		}
	}

	function searchArticles($data) {
		$data = $this->test_input($data);
		['search' => $search] = $data;

		try {
			$stmt = $this->pdo->prepare('SELECT articles.*, users.name as "author" FROM articles JOIN users WHERE articles.author = users.id AND (articles.text LIKE :search OR title LIKE :search) ORDER BY id DESC');
			$stmt->bindValue(':search', '%'.$search.'%', PDO::PARAM_STR);
			$stmt->execute();

			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			foreach ($rows as &$row) {
				$tags = [];
				foreach (unserialize($row["tags"]) as $tag) {
					$stmt = $this->pdo->prepare('SELECT * FROM tags WHERE id = :tag');
					$stmt->bindValue(':tag', $tag, PDO::PARAM_INT);
					$stmt->execute();

					$result = $stmt->fetch(PDO::FETCH_ASSOC);
					$stmt->closeCursor();

					array_push($tags, $result);
				}

				$row["tags"] = $tags;
			}

			return $this->create_object('Artykuły', true, $rows);
		} catch(PDOException $e) {
			return $this->create_object('Połączenie z bazą nie mogło zostać utworzone / Błąd zapytania');
		}
	}

	function createArticleImages($file) {
		$time = time();
		$image_path = '../img/articles/image_'.$time.'.webp';
		$thumb_path = '../img/articles/thumb/image_'.$time.'.webp';
		$im = imagecreatefromstring(file_get_contents($file['tmp_name']));
		$im = imagescale($im, 1100);
		$thumb = imagescale($im, 500);

		imagewebp($im, $image_path, 70);
		imagewebp($thumb, $thumb_path, 40);
		imagedestroy($im);
		imagedestroy($thumb);
		return 'image_'.$time.'.webp';
	}

	function deleteArticleImages($file) {
		$image_path = '../img/articles/'.$file;
		$thumb_path = '../img/articles/thumb/'.$file;
		unlink($image_path);
		unlink($thumb_path);
	}

	function addArticle($data, $file, $tags, $text) {
		$data = $this->test_input($data);
		['title' => $title, 'shortText' => $shortText] = $data;
		$tags = json_decode($tags);
		foreach ($tags as $key=>&$value) {
			$value = (int)$value;
		}
		$tags = serialize($tags);

		$imageName = $this->createArticleImages($file);

		try {
			$stmt = $this->pdo->prepare('INSERT INTO articles (author, date, image, tags, title, text, shortText) VALUES (:author, :date, :image, :tags, :title, :text, :shortText)');
			$stmt->bindValue(':author', $_SESSION['user_id'], PDO::PARAM_INT);
			$stmt->bindValue(':date', date('d-m-Y G:i'), PDO::PARAM_STR);
			$stmt->bindValue(':image', $imageName, PDO::PARAM_STR);
			$stmt->bindValue(':tags', $tags, PDO::PARAM_STR);
			$stmt->bindValue(':title', $title, PDO::PARAM_STR);
			$stmt->bindValue(':text', $text, PDO::PARAM_STR);
			$stmt->bindValue(':shortText', $shortText, PDO::PARAM_STR);
			$stmt->execute();
			$stmt->closeCursor();

			$addedId = $this->pdo->lastInsertId();
			return $this->create_object('Dodano artykuł', true, $addedId);
		} catch(PDOException $e) {
			return $this->create_object('Połączenie z bazą nie mogło zostać utworzone / Błąd zapytania');
		}
	}

	function editArticle($data, $file, $tags, $text) {
		$data = $this->test_input($data);
		['id' => $id, 'title' => $title, 'shortText' => $shortText, 'oldImage' => $oldImage] = $data;
		$tags = json_decode($tags);
		foreach ($tags as $key=>&$value) {
			$value = (int)$value;
		}
		$tags = serialize($tags);

		if ($file != 'empty') {
			$this->deleteArticleImages($oldImage);
			$imageName = $this->createArticleImages($file);
		} else {
			$imageName = $oldImage;
		}

		try {
			$stmt = $this->pdo->prepare('UPDATE articles SET image = :image, tags = :tags, title = :title, text = :text, shortText = :shortText WHERE id = :id');
			$stmt->bindValue(':image', $imageName, PDO::PARAM_STR);
			$stmt->bindValue(':tags', $tags, PDO::PARAM_STR);
			$stmt->bindValue(':title', $title, PDO::PARAM_STR);
			$stmt->bindValue(':text', $text, PDO::PARAM_STR);
			$stmt->bindValue(':shortText', $shortText, PDO::PARAM_STR);
			$stmt->bindValue(':id', $id, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();

			return $this->create_object('Artykuł został zedytowany', true);
		} catch(PDOException $e) {
			return $this->create_object('Połączenie z bazą nie mogło zostać utworzone / Błąd zapytania');
		}
	}

	function deleteArticle($data) {
		$data = $this->test_input($data);
		['id' => $id] = $data;

		try {
			$stmt = $this->pdo->prepare('DELETE FROM comments WHERE article = :id');
			$stmt->bindValue(':id', $id, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();

			$stmt = $this->pdo->prepare('DELETE FROM answers WHERE article = :id');
			$stmt->bindValue(':id', $id, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();

			$stmt = $this->pdo->prepare('DELETE FROM articles WHERE id = :id');
			$stmt->bindValue(':id', $id, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();

			return $this->create_object('Artykuł został usunięty', true);
		} catch(PDOException $e) {
			return $this->create_object('Połączenie z bazą nie mogło zostać utworzone / Błąd zapytania');
		}
	}
}
?>
