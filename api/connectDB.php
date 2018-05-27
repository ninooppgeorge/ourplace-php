<?php
    include_once('constants.php');
    class connectDB {
        public function connect(){
            try{
                $conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME,DB_USERNAME,DB_PASSWORD);
                $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                return $conn;
            }catch(Exception $e){
                echo "Database Error ".$e->getMessage();
            }
        }
    }
?>