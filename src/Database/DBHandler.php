<?php
namespace cds\Database;

use cds\Config\Config;
use mysqli;

class DBHandler
{
    private $mysqli;

    function __construct()
    {
        $this->connect();
    }

    function connect()
    {
        $config = Config::get();
        $this->mysqli = new mysqli(
            $config['DBHandler']['url'],
            $config['DBHandler']['user'],
            $config['DBHandler']['pass'],
            $config['DBHandler']['db']
        );

        if ($this->mysqli->connect_errno) {
            error_log("Failed to connect to MySQL: (" . $this->mysqli->connect_errno . ") " . $this->mysqli->connect_error);
        }

        if (!$this->mysqli->set_charset("utf8")) {
            error_log("Failed to set charset to UTF-8");
        }
    }

    /**
     * mysqli
     * @return mysqli
     */
    public function getMysqli()
    {
        return $this->mysqli;
    }

    /**
     * mysqli
     * @param mysqli $mysqli
     */
    public function setMysqli($mysqli)
    {
        $this->mysqli = $mysqli;
    }
}

?>