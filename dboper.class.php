<?php
include_once __DIR__."/config.php" ;
class dboper{

    private static $_inst = [] ;
    private $db ;

    public static function inst($dbname){
        if(!isset(self::$_inst[$dbname])|| !self::$_inst[$dbname] || !(self::$_inst[$dbname] instanceof self)){
            self::$_inst[$dbname] = new self($dbname) ;
        }

        return self::$_inst[$dbname] ;
    }

    private function __construct($dbname){
        $this->db = new PDO("mysql:host=127.0.0.1; dbname={$dbname}", DB_USER, DB_PWD) ;
        $this->db->query("set names utf8");
    }

    private function __clone(){}

    public function add($sql, $params=[]){
        $statement = $this->db->prepare($sql) ;
        if(!$statement->execute($params)){
            return false;
        }
        return $this->db->lastInsertId();
    }

    public function delete($sql, $params=[]){
        $statement = $this->db->prepare($sql) ;
        if(!$statement->execute($params)){
            return false;
        }
        return $statement->rowCount();
    }

    public function update($sql, $params=[]){
        $statement = $this->db->prepare($sql) ;
        if(!$statement->execute($params)){
            return false;
        }
        return $statement->rowCount();
    }

    public function select($sql, $params=[]){
        $statement = $this->db->prepare($sql) ;
        if(!$statement->execute($params)){
            return false;
        }
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}