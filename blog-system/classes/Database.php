<?php
namespace classes;

class Database
{
    private static $host = "localhost";
    private static $user = "root";
    private static $password = "";
    private static $dbName = "blog_db";
    private static $connection = null;

    public static function getConnection(): ?\mysqli
    {
        if (empty(self::$connection)) {
            self::$connection = new \mysqli(self::$host, self::$user, self::$password, self::$dbName);
            if (self::$connection->connect_errno === 0) {
                return self::$connection;
            } else {
                return null;
            }
        }
        return self::$connection;
    }
}