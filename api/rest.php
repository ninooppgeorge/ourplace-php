<?php
    include_once('constants.php');
    include_once('jwt.php');
    class Rest {
        
        protected $request;
        protected $serviceName;
        protected $param;


        public function __construct(){
            if($_SERVER['REQUEST_METHOD']=='POST'){
                $handler = fopen('php://input','r');
                $this->request = stream_get_contents($handler);
                $this->validateRequest($this->request);
                //echo $this->request;
            }else{
                $this->throwError(REQUEST_METHOD_NOT_VALID,'Request Method Not Valid');
            }
        }

        public function validateRequest($request){
            //check if content type is json
            if($_SERVER['CONTENT_TYPE'] !== 'application/json'){
                $this->throwError(REQUEST_CONTENTTYPE_NOT_VALID,'Request Content Type not valid');
            }else{
                
                $data = json_decode($this->request,true);
                //check if api name is specified
                if(!isset($data['name']) || $data['name']==''){
                    $this->throwError(API_NAME_NOT_SPECIFIED,'API Name required');
                }else{
                    $this->serviceName = $data['name'];
                    //check if the data is array
                    if(!is_array($data['data'])){
                        $this->throwError(API_DATA_NOT_VALID, 'API Data not valid');
                    }else{
                        $this->param = $data['data'];
                    }
                }
            }
        }

        //validate Paramters
        public function validateParameter($fieldname, $value, $dataType, $required = true){
            if($required==true && empty($value)==true){
                $this->throwError(VALIDATE_PARAMETER_REQUIRED, $fieldname. ' parameter is required');
            }else{
                switch ($dataType) {
                    case BOOLEAN:
                        if(!is_bool($value))
                            $this->throwError(VALIDATE_PARAMETER_DATATYPE, $fieldname.' datatype is not valid. Needs boolean value.');
                        break;
                    case INTEGER:
                        if(!is_numeric($value))
                            $this->throwError(VALIDATE_PARAMETER_DATATYPE, $fieldname.' datatype is not valid. Needs neumeric value.');
                        break;
                    case STRING:
                        if(!is_string($value))
                            $this->throwError(VALIDATE_PARAMETER_DATATYPE, $fieldname.' datatype is not valid. Needs string value.');
                        break;
                    
                    default:
                        # code...
                        break;
                }
                return $value;
            }
        }

        //check if the api exists or not
        public function processApi(){
            $api = new Api;
            if(!method_exists($api, $this->serviceName)){
                $this->throwError(API_DOES_NOT_EXIST, 'Api Does not exist');
            }else{
                $rmethod = new reflectionMethod('API', $this->serviceName);
                $rmethod->invoke($api);
            }
        }

        //throw error codes
        public function throwError($errorcode, $msg){
            header('content-type: application/json');
            $error = json_encode([
                'error' => [
                    'status' => $errorcode,
                    'message' => $msg
                ]
            ]);
            echo $error;
            exit;
        }

        //return a response
        public function returnResponse($code, $data){
            header('content-type: application/json');
            $resp = json_encode([
                'response' => [
                    'status' => $code,
                    'data' => $data
                ]
            ]);
            echo $resp;
            exit;
        }

        //get auth header
        public function getAuthorizationHeader(){
            $headers = null;
            if(isset($_SERVER['Authorization'])){
                $headers = trim($_SERVER['Authorization']);
            }else if(isset($_SERVER['HTTP_AUTHORIZATION'])){
                $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
            }else if(function_exists('apache_request_headers')){
                $requestHeaders = apache_request_headers();
                $requestHeaders = array_combine(array_map('ucwords',
                    array_keys($requestHeaders)
                ), array_values($requestHeaders));
                if(isset($requestHeaders['Authorization'])){
                    $headers = trim($requestHeaders['Authorization']);
                }
            }
            return $headers;
        }

        //get the JWT token
        public function getJWTToken(){
            $headers = $this->getAuthorizationHeader();
            if(!empty($headers)){
                if(preg_match('/JWT\s(\S+)/',$headers,$matches)){
                    return $matches[1];
                }
            }else{
                $this->throwError(AUTH_HEADER_NOT_FOUND,'Auth Header Not Found');
            }
        }
    }
?>