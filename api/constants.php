<?php

    //DATABASE CONSTANTS 
    define('DB_HOST','localhost');
    define('DB_NAME','ourplace');
    define('DB_USERNAME','root');
    define('DB_PASSWORD','');

    //APPLICATION SPECIFIC
    define('APP_JWT_SECRET','@123NINoop');

    //DATA TYPE
    define('BOOLEAN',1);
    define('INTEGER',2);
    define('STRING',3);

    //EXCEPTION CONSTANT
    define('REQUEST_METHOD_NOT_VALID',100);
    define('REQUEST_CONTENTTYPE_NOT_VALID',101);
    define('API_NAME_NOT_SPECIFIED',102);
    define('API_DATA_NOT_VALID',103);
    define('API_DOES_NOT_EXIST',104);
    define('VALIDATE_PARAMETER_REQUIRED',105);
    define('VALIDATE_PARAMETER_DATATYPE',106);
    define('INVALID_USER',108);
    
    
    define('USER_EXISTS',112);



    define('JWT_PROCESSING_ERROR',300);
    define('AUTH_HEADER_NOT_FOUND',301);
    define('ACCESS_TOKEN_ERROR',302);
    
    define('UNKNOWN_ERROR',501);
    define('SUCCESS_RESPONSE',200);
?>