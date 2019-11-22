<?php

namespace pietras;

/**
 * Provide authorization methods.
 */
class AuthorizationDatabase
{
    /**
     * @var string $dbHost Database host.
     */
    private $dbHost;
    /**
     * @var string $dbUser Database username.
     */
    private $dbUser;
    /**
     * @var string $dbPass Database password.
     */
    private $dbPass;
    /**
     * @var string $dbName Database name.
     */
    private $dbName;

    /**
     * Class constructor.
     *
     * @param string $host     Database host.
     * @param string $user     Database user.
     * @param string $password Database password.
     * @param string $dbName   Database name.
     */
    public function __construct($host, $user, $password, $dbName)
    {
        $this->dbHost = $host;
        $this->dbUser = $user;
        $this->dbPass = $password;
        $this->dbName = $dbName;
    }

    /**
     * Send http response.
     */
    public function send401()
    {
        header($_SERVER["SERVER_PROTOCOL"] . " 401 Authorization Required.");
        die();
    }

    /**
     * Check if user has reading permission.
     *
     * @param  string|null $username
     * @param  string|null $password
     * @return boolean
     */
    public function isReadingPermitted(?string $username, ?string $password): bool
    {
        $user = $this->fetchUser($username);
        if (!$user) {
            return false;
        }
        if (!($user["hash"] == crypt($password, $user["hash"]))) {
            return false;
        }
        if (!$user["read"]) {
            return false;
        }
        return true;
    }

    /**
     * Check if user has writing permission.
     *
     * @param  string|null $username
     * @param  string|null $password
     * @return boolean
     */
    public function isWritingPermitted(?string $username, ?string $password): bool
    {
        $user = $this->fetchUser($username);
        if (!$user) {
            return false;
        }
        if (!($user["hash"] == crypt($password, $user["hash"]))) {
            return false;
        }
        if (!$user["write"]) {
            return false;
        }
        return true;
    }

    /**
     * Fetch user.
     *
     * @param  string|null
     * @return array|false
     */
    private function fetchUser(?string $user)
    {
        $db = new \mysqli($this->dbHost, $this->dbUser, $this->dbPass, $this->dbName);
        $sql = "SELECT * FROM user WHERE username=?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $arr[] = $row;
        }
        $db->close();
        if (isset($arr)) {
            return current($arr);
        } else {
            return false;
        }
    }
}
