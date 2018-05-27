<?php
 class Api extends Rest{

    public $dbConn;
    public function __construct(){
        parent::__construct();
        $db = new connectDB;
        $this->dbConn = $db->connect();
    }


    //generate a JWT Token with given username and password
    public function generateToken(){
        $email = $this->validateParameter('email',$this->param['email'], STRING);
        $password = md5(sha1($this->validateParameter('password',$this->param['password'], STRING)));
        try{
            $stmt = $this->dbConn->prepare("SELECT * FROM users WHERE email = :email AND password = :password");
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $password);
            $stmt->execute();
    
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!is_array($user)){
                $this->throwError(INVALID_USER,"email & password is incorrect.");
            }else{
                $payload = [
                    'iat' => time(),
                    'iss' => 'localhost',
                    'exp' => time()+(60*10), //token valid for this much time
                    'userid' => $user['uid'],
                    'email' => $user['email']

                ];
                $token = JWT::encode($payload, APP_JWT_SECRET);
                $this->returnResponse(SUCCESS_RESPONSE,[
                    'uid' => $user['uid'],
                    'token'=> $token
                ]);
            }
        }catch(Exception $e){
            $this-> throwError(JWT_PROCESSING_ERROR,$e->getMessage());
        }
    }

    public function createUser(){
        $name = $this->validateParameter('name',$this->param['name'], STRING);
        $email = $this->validateParameter('email',$this->param['email'], STRING);
        $password = $this->validateParameter('password',$this->param['password'], STRING);

        $user = new User;
        $user->setName($name);
        $user->setEmail($email);
        $user->setPassword($password);
        $check=$user->getUser();
        if($check!=false){
            $this->throwError(USER_EXISTS,'User Already Exists');
        } else{
            if($user->addUser()){
                $get=$user->getUser();
                $payload = [
                    'iat' => time(),
                    'iss' => 'localhost',
                    'exp' => time()+(60*10), //token valid for this much time
                    'userid' => $get['uid'],
                    'email' => $get['email']
                ];
                $token = JWT::encode($payload, APP_JWT_SECRET);
                $this->returnResponse(SUCCESS_RESPONSE,[
                    'uid' => $get['uid'],
                    'token'=> $token
                ]);
            }else{
                $this->throwError(UNKNOWN_ERROR,'Unknown Error');
            }
        }
        
    }

    public function getUserDetails(){
        try{
            $token = $this->getJWTToken();
            $decode = JWT::decode($token, APP_JWT_SECRET, ['HS256']);

            $user = new User;
            $user->setId($decode->userid);
            $us = $user->getUser();
            if($us!=false){
                $this->returnResponse(SUCCESS_RESPONSE,$us);
            }else{
                $this->throwError(INVALID_USER,'Invalid User');
            }
        }catch(Exception $e){
            $this->throwError(ACCESS_TOKEN_ERROR,$e->getMessage());
        }
    }

 }
?>