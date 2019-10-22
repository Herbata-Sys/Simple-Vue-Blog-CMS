<?php
require_once "api.php";

class Tags extends api {
	function getTags() {
		try {
			$stmt = $this->pdo->prepare('SELECT * FROM tags');
			$stmt->execute();

			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();

			return $this->create_object('Tagi', true, $rows);
		} catch(PDOException $e) {
			return $this->create_object('Połączenie z bazą nie mogło zostać utworzone / Błąd zapytania');
		}
	}

	function getTagArticles($data) {
		$data = $this->test_input($data);
		['tag_id' => $tag_id] = $data;
		try {
			$stmt = $this->pdo->prepare('SELECT articles.*, users.name as "author" FROM articles JOIN users WHERE articles.author = users.id ORDER BY id DESC');
			$stmt->execute();

			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();

			$articles = [];

			foreach ($rows as $article) {
				$tags = [];
				$x = false;

				foreach(unserialize($article['tags']) as $tag) {
					$stmt = $this->pdo->prepare('SELECT * FROM tags WHERE id = :tag');
					$stmt->bindValue(':tag', $tag, PDO::PARAM_INT);
					$stmt->execute();
					$result = $stmt->fetch(PDO::FETCH_ASSOC);
					$stmt->closeCursor();

					array_push($tags, $result);
					if($tag == $tag_id) {
						$x = true;
					}
				}

				$article["tags"] = $tags;
				if($x === true) {
					array_push($articles, $article);
				}
			}

			return $this->create_object('Artykuły', true, $articles);
		} catch(PDOException $e) {
			return $this->create_object('Połączenie z bazą nie mogło zostać utworzone / Błąd zapytania');
		}
	}

	function getTagInfo($data) {
		$data = $this->test_input($data);
		['tag_id' => $tag_id] = $data;

		try {
			$stmt = $this->pdo->prepare('SELECT * FROM tags WHERE id = :id');
			$stmt->bindValue(':id', $tag_id, PDO::PARAM_INT);
			$stmt->execute();

			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$stmt->closeCursor();

			return $this->create_object('Tag', true, $row);
		} catch(PDOException $e) {
			return $this->create_object('Połączenie z bazą nie mogło zostać utworzone / Błąd zapytania');
		}
	}

	function createTag($data) {
		$data = $this->test_input($data);
		['name' => $name] = $data;

		try {
			$stmt = $this->pdo->prepare('INSERT INTO tags (name) VALUES (:name)');
			$stmt->bindValue(':name', $name, PDO::PARAM_STR);
			$stmt->execute();
			$stmt->closeCursor();
			$tag['id'] = $this->pdo->lastInsertId();
			$tag['name'] = $name;

			return $this->create_object('Dodano', true, $tag);
		} catch(PDOException $e) {
			return $this->create_object('Połączenie z bazą nie mogło zostać utworzone / Błąd zapytania');
		}
	}
}
?>
