<?php

    class User{
        private $tableName = 'users';
        private $id;
        private $name;
        private $email;
        private $password;
        private $dbConn;

        function setId($id){$this->id=$id;}
        function setName($name){$this->name=$name;}
        function setEmail($email){$this->email=$email;}
        function setPassword($password){$this->password=md5(sha1($password));}
        function getId($id){ return $this->id;}
        function getName($name){ return $this->name;}
        function getEmail($email){ return $this->email;}

        public function __construct(){
            $db = new connectDB;
            $this->dbConn = $db->connect();
        }

        public function addUser(){
            $sql = "INSERT INTO ".$this->tableName."(name,email,password) VALUES (:name,:email,:password)";
            $stmt = $this->dbConn->prepare($sql);
            $stmt->bindParam(':name',$this->name);
            $stmt->bindParam(':email',$this->email);
            $stmt->bindParam(':password',$this->password);
            if($stmt->execute()){
                return true;
            }else{
                return false;
            }
        }

        //get user with email
        public function getUser(){
            $colname = "uid";
            $colval = $this->id;
            if($this->id){
                $colname = "uid";
                $colval = $this->id;
            }else{
                $colname = "email";
                $colval = $this->email;
            }
            $sql = "SELECT uid,name,email FROM ".$this->tableName." WHERE ".$colname."=:colname order by uid LIMIT 1";
            $stmt = $this->dbConn->prepare($sql);
            $stmt->bindParam(':colname',$colval);
            if($stmt->execute()){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result;
            }else{
                return false;
            }
        }

    }
?>