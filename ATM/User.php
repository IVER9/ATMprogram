<?php

class User {
    private static $id;
    private static $name;
    private static $balance;

    const USER_LIST = array(
        1 => array(
            "id" => "1",
            "name" => "iver",
            "password" => "1234",
            "balance" => "10000"
        ),

        2 => array(
            "id" => "2",
            "name" => "tanaka",
            "password" => "5678",
            "balance" => "20000"
        )
    );

    public function getUserById($id) {
        return self::USER_LIST[$id];
    }

    public function checkUserList($id) {
        if (!isset(self::USER_LIST[$id])) {
            return false;
        }
        return true;
    }
}

 ?>
