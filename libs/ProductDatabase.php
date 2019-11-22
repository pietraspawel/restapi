<?php

namespace pietras;

/**
 * Provide product database methods.
 */
class ProductDatabase extends \mysqli
{
    /**
     * @var array $validLanguages Stores valid languages.
     */
    private $validLanguages = [];
    /**
     * @var array $result Stores query result.
     */
    private $result = [];

    /**
     * Class constructor.
     *
     * Connect to database, disable autocommit, get valid languages.
     *
     * @param string $host    Database host.
     * @param string $user    Database user.
     * @param string $pasword Database password.
     * @param string $dbName  Database name.
     */
    public function __construct(string $host, string $user, string $password, string $dbName)
    {
        parent::__construct($host, $user, $password, $dbName);
        if ($this->connect_error) {
            header($_SERVER["SERVER_PROTOCOL"] . " 500 Connection failed.");
            die();
        }
        $this->autocommit(false);
        $this->validLanguages = $this->fetchValidLanguages();
    }

    /**
     * Fetch valid languages.
     *
     * @return array
     */
    private function fetchValidLanguages(): array
    {
        $arr = [];
        $res = $this->query("SELECT * FROM language");
        while ($row = $res->fetch_assoc()) {
            $arr[$row['id']] = $row['abbr'];
        }
        return $arr;
    }

    /**
     * @return array
     */
    public function getValidLanguages(): array
    {
        return $this->validLanguages;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Fetch all products.
     *
     * @param  int $page     Page number.
     * @param  int $pagesize Page size.
     * @return array
     *   [
     *       [
     *           [id] => int,
     *           [price] => int,
     *           [quantity] => int,
     *           language =>
     *               [
     *                   "name" => string,
     *                   "description" => string
     *               ],
     *           next_language =>
     *               [
     *                   ...
     *               ]
     *       ],
     *       [
     *           ..next product..
     *       ]
     *   ]
     */
    public function fetchAll(int $page, int $pagesize): array
    {
        $offset = ($page - 1) * $pagesize;
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
     * Fetch product.
     *
     * @param  int $id Product id.
     * @return array
     *   [
     *       [
     *           [id] => int,
     *           [price] => int,
     *           [quantity] => int,
     *           language =>
     *               [
     *                   "name" => string,
     *                   "description" => string
     *               ],
     *           next_language =>
     *               [
     *                   ...
     *               ]
     *       ]
     *   ]
     */
    public function fetch(int $id): array
    {
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
     * Insert product.
     *
     * @param  array $data
     *   [
     *       [price] => int,                     // if omitted, default is 0
     *       [quantity] => int,                  // if omitted, default is 0
     *       language =>                         // works only if language is valid, minimum one is required
     *           [
     *               "name" => string,           // required
     *               "description" => string     // if omitted, default is null
     *           ],
     *       next_language =>
     *           [
     *               ...
     *           ]
     *   ]
     * @return bool Return true if everything is ok, else return false.
     */
    public function insert(array $data): bool
    {
        $err = false;
        $langAdded = false;

        $price = isset($data["price"]) ? $data["price"] : 0;
        $quantity = isset($data["quantity"]) ? $data["quantity"] : 0;

        $sql = "INSERT INTO product (price, quantity) VALUES (?, ?)";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("ii", $price, $quantity);
        if (!$stmt->execute()) {
            $err = true;
        }
        $lastId = $this->insert_id;

        foreach ($data as $langName => $lang) {
            if (in_array($langName, $this->validLanguages)) {
                if (!isset($lang["name"])) {
                    $this->rollback();
                    return false;
                }
                $name = $lang["name"];
                $description = isset($lang["description"]) ? $lang["description"] : "";

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
        if (!$err and $langAdded) {
            $this->commit();
            return true;
        } else {
            $this->rollback();
            return false;
        }
    }

    /**
     * Update product.
     *
     * Every field is optional.
     *
     * @param  int $id        Product id.
     * @param  array $changes Array of changes.
     *   [
     *       [price] => int,
     *       [quantity] => int,
     *       language =>
     *           [
     *               "name" => string,
     *               "description" => string
     *           ],
     *       next_language =>
     *           [
     *               ...
     *           ]
     *   ]
     * @return bool Return true if everything is ok, else return false.
     */
    public function update(int $id, array $changes): bool
    {
        $err = false;

        $origin = $this->fetch($id);
        if (empty($origin)) {
            return false;
        } else {
            $origin = current($origin);
        }

        $data = $this->getDataForUpdate($origin, $changes);

        $sql = "UPDATE product SET price=?, quantity=? WHERE id=?";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("iii", $data["price"], $data["quantity"], $id);
        if (!$stmt->execute()) {
            $err = true;
        }
        $lastId = $this->insert_id;

        foreach ($data["languages"] as $langName => $lang) {
            if (in_array($langName, $this->validLanguages)) {
                $languageId = array_search($langName, $this->validLanguages);
                $sql = "SELECT id FROM product_name WHERE product_id=? AND language_id=?";
                $stmt = $this->prepare($sql);
                $stmt->bind_param("ii", $id, $languageId);
                if (!$stmt->execute()) {
                    $err = true;
                }
                $res = $stmt->get_result();
                if ($res->num_rows) {
                    $sql = "UPDATE product_name SET name=?, description=? WHERE product_id=? AND language_id=?";
                    $stmt = $this->prepare($sql);
                    $stmt->bind_param("ssii", $lang["name"], $lang["description"], $id, $languageId);
                    if (!$stmt->execute()) {
                        $err = true;
                    }
                } else {
                    $sql = "INSERT INTO product_name (product_id, language_id, name, description) VALUES (?,?,?,?)";
                    $stmt = $this->prepare($sql);
                    $stmt->bind_param("iiss", $id, $languageId, $lang["name"], $lang["description"]);
                    if (!$stmt->execute()) {
                        $err = true;
                    }
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

    /**
     * Return origin data with permitted changes.
     *
     * @param  array $origin  Origin data.
     * @param  array $changes Changes.
     * @return array
     */
    private function getDataForUpdate(array $origin, array $changes): array
    {
        $data = [];
        $data["price"] = isset($changes["price"]) ? $changes["price"] : $origin["price"];
        $data["quantity"] = isset($changes["quantity"]) ? $changes["quantity"] : $origin["quantity"];
        $data["languages"] = [];
        foreach ($changes as $langName => $lang) {
            if (in_array($langName, $this->validLanguages)) {
                $languageId = array_search($langName, $this->validLanguages);
                $data["languages"][$langName]["name"] = isset($changes[$langName]["name"]) ?
                    $changes[$langName]["name"] :
                    $origin[$langName]["name"];
                $data["languages"][$langName]["description"] = isset($changes[$langName]["description"]) ?
                    $changes[$langName]["description"] :
                    $origin[$langName]["description"];
            }
        }

        return $data;
    }

    /**
     * Delete product.
     *
     * @param  int $id Product id.
     * @return bool Return true if everything is ok, else return false.
     */
    public function delete(int $id): bool
    {
        $err = false;

        $origin = $this->fetch($id);
        if (empty($origin)) {
            return false;
        }

        $sql = "DELETE FROM product WHERE id=?";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            $err = true;
        }

        if (!$err) {
            $this->commit();
            return true;
        } else {
            $this->rollback();
            return false;
        }
    }

    /**
     * Convert mysqli_result to array.
     *
     * @param  \mysqli_result $res
     * @return array
     */
    private function resultToArray(\mysqli_result $res): array
    {
        $arr = [];
        while ($row = $res->fetch_assoc()) {
            $arr[] = $row;
        }
        return $arr;
    }

    /**
     * Conver mysqli_result to product collection.
     *
     * @param  \mysqli_result $res
     * @return array
     */
    private function resultToProductArrayCollection(\mysqli_result $res): array
    {
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
