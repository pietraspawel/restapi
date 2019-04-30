<?php
namespace pietras;
class ProductDatabase extends \mysqli {
	private $validLanguages = [];
	private $result = [];

	public function __construct($host, $user, $password, $dbName) {
		parent::__construct($host, $user, $password, $dbName);
		if ($this->connect_error) {
			header($_SERVER["SERVER_PROTOCOL"]." 500 Connection failed.");
			die();
		}
		$this->autocommit(false);
		$this->validLanguages = $this->fetchValidLanguages();
	}
		private function fetchValidLanguages() {
			$arr = [];
			$res = $this->query("SELECT * FROM language");
			while ($row = $res->fetch_assoc()) $arr[$row['id']] = $row['abbr'];
			return $arr;
		} 

	public function getValidLanguages() { return $this->validLanguages; }

	public function getResult() { return $this->result; }

	/**
		* Zwraca wszystkie produkty z bazy danych w formie tablicy.
		* 	[
		* 		[
		* 			[id] => int,
		* 			[price] => int,
		* 			[quantity] => int,
		*			nazwa_języka => 
		*				[
		*					"name" => string,
		*					"description" => string
		*				],
		*			kolejny_język =>
		*				[
		*					...
		*				]
		* 		],
		* 		[
		* 			..kolejny produkt..
		* 		]
		* 	]
	*/
	public function fetchAll($page, $pagesize) {
		$offset = ($page-1) * $pagesize;
		$sql = "
			SELECT 
				p.*, 
				product_name.name AS name,
				product_name.description AS description,
				`language`.abbr AS 'language'
			FROM ( SELECT * FROM product LIMIT $offset, $pagesize) AS p
			INNER JOIN product_name ON p.id = product_name.product_id
			INNER JOIN `language` ON product_name.language_id = `language`.id
		";
		$res = $this->query($sql);
		return $this->resultToProductArrayCollection($res);
	}

	/**
		* Zwraca produkt o podanym id w formie tablicy.
		* 	[
		* 		[
		* 			[id] => int,
		* 			[price] => int,
		* 			[quantity] => int,
		*			nazwa_języka => 
		*				[
		*					"name" => string,
		*					"description" => string
		*				],
		*			kolejny_język =>
		*				[
		*					...
		*				]
		* 		]
		*	]
	*/
	public function fetch($id) {
		$sql = "
			SELECT 
				product.*, 
				product_name.name AS name,
				product_name.description AS description,
				`language`.abbr AS 'language'
			FROM product 
			INNER JOIN product_name ON product.id = product_name.product_id
			INNER JOIN `language` ON product_name.language_id = `language`.id
			WHERE product.id = ?
		";
		$stmt = $this->prepare($sql);
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$res = $stmt->get_result();
		return $this->resultToProductArrayCollection($res);
	}

	/**
		* Dodaje produkt do bazy. Tablica wejściowa musi być w formacie:
		* 	[
		* 		[id] => int,						// nieistotna, nie musi i nie powinna być przekazywana
		* 		[price] => int,						// jeśli nie będzie podana, to domyślnie = 0
		* 		[quantity] => int,					// jeśli nie będzie podana, to domyślnie = 0
		*		nazwa_języka => 					// działa tylko, jeśli nazwa_języka istnieje
		*			[
		*				"name" => string,			// musi być podana przynajmniej dla jednego języka
		*				"description" => string 	// jeśli nie będzie podana, to domyślnie = ""
		*			],
		*		kolejny_język =>
		*			[
		*				...
		*			]
		*	]
	*/
	public function insert($data) {
		$err = false;
		$langAdded = false;

		$price = isset($data["price"])? $data["price"]: 0;
		$quantity = isset($data["quantity"])? $data["quantity"]: 0;

		$sql = "INSERT INTO product (price, quantity) VALUES (?, ?)";
		$stmt = $this->prepare($sql);
		$stmt->bind_param("ii", $price, $quantity);
		if (!$stmt->execute()) $err = true;
		$lastId = $this->insert_id;

		foreach ($data as $langName => $lang) {
			if (in_array($langName, $this->validLanguages)) {
				if (!isset($lang["name"])) {
					$this->rollback();
					return false;
				}
				$name = $lang["name"];
				$description = isset($lang["description"])? $lang["description"]: "";

				$languageId = array_search($langName, $this->validLanguages);
				$sql = "INSERT INTO product_name (product_id, language_id, name, description) VALUES (?,?,?,?)";
				$stmt = $this->prepare($sql);
				$stmt->bind_param("iiss", $lastId, $languageId, $name, $description);
				if (!$stmt->execute()) {
					$err = true;
				} else {
					$langAdded = true;
				}
			}
		}
		if (!$err AND $langAdded) {
			$this->commit();
			return true;
		} else {
			$this->rollback();
			return false;
		}
	}

	/**
		* Aktualizuje produkt. Każde z pól jest opcjonalne:
		* 	[
		* 		[price] => int,						
		* 		[quantity] => int,					
		*		nazwa_języka => 					
		*			[
		*				"name" => string,			
		*				"description" => string 	
		*			],
		*		kolejny_język =>
		*			[
		*				...
		*			]
		*	]
	*/
	public function update($id, $changes) {
		$err = false;

		$origin = $this->fetch($id);
		if (empty($origin)) return false;
		else $origin = current($origin);

		$data = $this->getDataForUpdate($origin, $changes);

		$sql = "UPDATE product SET price=?, quantity=? WHERE id=?";
		$stmt = $this->prepare($sql);
		$stmt->bind_param("iii", $data["price"], $data["quantity"], $id);
		if (!$stmt->execute()) $err = true;
		$lastId = $this->insert_id;

		foreach ($data["languages"] as $langName => $lang) {
			if (in_array($langName, $this->validLanguages)) {
				$languageId = array_search($langName, $this->validLanguages);
				$sql = "SELECT id FROM product_name WHERE product_id=? AND language_id=?";
				$stmt = $this->prepare($sql);
				$stmt->bind_param("ii", $id, $languageId);
				if (!$stmt->execute()) $err = true;
				$res = $stmt->get_result();
				if ($res->num_rows) {
					$sql = "UPDATE product_name SET name=?, description=? WHERE product_id=? AND language_id=?";
					$stmt = $this->prepare($sql);
					$stmt->bind_param("ssii", $lang["name"], $lang["description"], $id, $languageId);
					if (!$stmt->execute()) $err = true;
				} else {
					$sql = "INSERT INTO product_name (product_id, language_id, name, description) VALUES (?,?,?,?)";
					$stmt = $this->prepare($sql);
					$stmt->bind_param("iiss", $id, $languageId, $lang["name"], $lang["description"]);
					if (!$stmt->execute()) $err = true;
				}
			}
		}

		if (!$err) {
			$this->commit();
			return true;
		} else {
			$this->rollback();
			return false;
		}
	}

		private function getDataForUpdate($origin, $changes) {
			$data = [];
			$data["price"] = isset($changes["price"])? $changes["price"]: $origin["price"];
			$data["quantity"] = isset($changes["quantity"])? $changes["quantity"]: $origin["quantity"];
			$data["languages"] = [];
			foreach ($changes as $langName => $lang) {
				if (in_array($langName, $this->validLanguages)) {
					$languageId = array_search($langName, $this->validLanguages);
					$data["languages"][$langName]["name"] = isset($changes[$langName]["name"])? $changes[$langName]["name"]: $origin[$langName]["name"];
					$data["languages"][$langName]["description"] = isset($changes[$langName]["description"])? $changes[$langName]["description"]: $origin[$langName]["description"];
				}
			}

			return $data;
		} 

	/**
		* Usuwa produkt z bazy.
	*/
	public function delete($id) {
		$err = false;
		
		$sql = "DELETE FROM product WHERE id=?";
		$stmt = $this->prepare($sql);
		$stmt->bind_param("i", $id);
		if (!$stmt->execute()) $err = true;

		if (!$err) {
			$this->commit();
			return true;
		} else {
			$this->rollback();
			return false;
		}
	}

	private function resultToArray(\mysqli_result $res) {
		$arr = [];
		while($row = $res->fetch_assoc()) $arr[] = $row;
		return $arr;
	}

	private function resultToProductArrayCollection(\mysqli_result $res) {
		$arr = $this->resultToArray($res);
		$coll = [];
		foreach ($arr as $row) {
			$coll[$row["id"]]["id"] = $row["id"];
			$coll[$row["id"]]["price"] = $row["price"];
			$coll[$row["id"]]["quantity"] = $row["quantity"];
			$coll[$row["id"]][$row["language"]]["name"] = $row["name"];
			$coll[$row["id"]][$row["language"]]["description"] = $row["description"];
		}
		return $coll;		
	}

}
