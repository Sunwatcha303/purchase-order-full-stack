<?php
class Database
{
    public static $link = null;

    static function connect()
    {
        if (self::$link === null) {
            self::$link = mysqli_connect("localhost", "root", "", "demo", 3307);

            if (!self::$link) {
                die("Connection failed: " . mysqli_connect_error());
            }
        }
        return self::$link;
    }

    static function close()
    {
        if (self::$link !== null) {
            mysqli_close(self::$link);
            self::$link = null;
        }
    }

    static function rollback()
    {
        if (self::$link !== null) {
            mysqli_rollback(self::$link);
        }
    }
}
?>