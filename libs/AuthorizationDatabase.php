<?php
namespace pietras;

class AuthorizationDatabase {
	private $dbHost, $dbUser, $dbPass, $dbName;

	public function __construct($host, $user, $password, $dbName) {
		$this->dbHost = $host;
		$this->dbUser = $user;
		$this->dbPass = $password;
		$this->dbName = $dbName;
	}

	public function send401() {
		header($_SERVER["SERVER_PROTOCOL"]." 401 Authorization Required.");
		die();
	}

	public function isReadingPermitted($username, $password) {
		$user = $this->fetchUser($username);
		if (!$user) return false;
		if (!($user["hash"] == crypt($password, $user["hash"]))) return false;
		if (!$user["read"]) return false;
		return true;
	}

	public function isWritingPermitted($username, $password) {
		$user = $this->fetchUser($username);
		if (!$user) return false;
		if (!($user["hash"] == crypt($password, $user["hash"]))) return false;
		if (!$user["write"]) return false;
		return true;
	}

	private function fetchUser($user) {
		$db = new \mysqli($this->dbHost, $this->dbUser, $this->dbPass, $this->dbName);
		$sql = "SELECT * FROM user WHERE username=?";
		$stmt = $db->prepare($sql);
		$stmt->bind_param("s", $user);
		$stmt->execute();
		$res = $stmt->get_result();
		while($row = $res->fetch_assoc()) $arr[] = $row;
		$db->close();
		if (isset($arr)) return current($arr);
		else return false;
	}
}
