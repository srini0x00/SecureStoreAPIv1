<?php
require_once 'db.php';

// --- Step 1: Initialize variables and functions
 
/**
 * Deliver HTTP Response
 * @param string $format The desired HTTP response content type: [json, html, xml]
 * @param string $api_response The desired HTTP response data
 * @return void
 **/

function deliver_response($format, $api_response){
 
    // Define HTTP responses
    $http_response_code = array(
        200 => 'OK',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found'
    );

    // Set HTTP Response
    
    header('HTTP/1.1 '.$api_response['status'].' '.$http_response_code[ $api_response['status'] ]);
    
    // Process different content types
    
     if( strcasecmp($format,'json') == 0 ){
 
        // Set HTTP Response Content Type
        header('Content-Type: application/json; charset=utf-8');
 
        // Format data into a JSON response
        $json_response = json_encode($api_response);
 
        // Deliver formatted data
        echo $json_response;
 
    }elseif( strcasecmp($format,'xml') == 0 ){
 
        // Set HTTP Response Content Type
        header('Content-Type: application/xml; charset=utf-8');
 
        // Format data into an XML response (This is only good at handling string data, not arrays)
        $xml_response = '<?xml version="1.0" encoding="UTF-8"?>'."\n".
            '<response>'."\n".
            "\t".'<code>'.$api_response['code'].'</code>'."\n".
            "\t".'<status>'.$api_response['status'].'</status>'."\n".
            //"\t".'<data>'.$api_response['data'].'</data>'."\n".      
                "\t".'<data>'."\n".  
                "\t".'<user>'.$api_response['data']['user'].'</user>'."\n". 
                 "\t".'<message>'.$api_response['data']['message'].'</message>'."\n". 
                "\t".'<data>'."\n". 
            '</response>';
 
        // Deliver formatted data
        echo $xml_response;
 
    }else{
 
        // Set HTTP Response Content Type (This is only good at handling string data, not arrays)
        header('Content-Type: text/html; charset=utf-8');
 
        // Deliver formatted data
        
        echo "code:";
        echo $api_response['code'];
        echo "\n";
        echo "status:";
        echo $api_response['status'];
        echo "\n";
        echo "user:";
        echo $api_response['data']['user'];
        echo "\n";
        echo "message:";
        echo $api_response['data']['message'];
 
    }
 
    // End script process
    exit;
}
// Define whether an HTTPS connection is required
$HTTPS_required = FALSE;
 
// Define whether user authentication is required
$authentication_required = FALSE;
 
// Define API response codes and their related HTTP response
$api_response_code = array(
    0 => array('HTTP Response' => 400, 'Message' => 'Unknown Error'),
    1 => array('HTTP Response' => 200, 'Message' => 'Success'),
    2 => array('HTTP Response' => 403, 'Message' => 'Method Incorrect'),
    3 => array('HTTP Response' => 401, 'Message' => 'Authentication Required'),
    4 => array('HTTP Response' => 401, 'Message' => 'Authentication Failed'),
    5 => array('HTTP Response' => 404, 'Message' => 'Invalid Request'),
    6 => array('HTTP Response' => 400, 'Message' => 'Invalid Response Format')
);
 
// Set default HTTP response of 'ok'

$response['code'] = 0;
$response['status'] = 404;
$response['data'] = NULL;
 


// --- Step 2: Authorization
 
// Optionally require connections to be made via HTTPS
if( $HTTPS_required && $_SERVER['HTTPS'] != 'on' ){
    $response['code'] = 2;
    $response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
    $response['data'] = $api_response_code[ $response['code'] ]['Message'];
 
    // Return Response to browser. This will exit the script.
    deliver_response($_GET['format'], $response);
}
 
// Optionally require user authentication
if( $authentication_required ){
 
    if( empty($_POST['username']) || empty($_POST['password']) ){
        $response['code'] = 3;
        $response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
        $response['data'] = $api_response_code[ $response['code'] ]['Message'];
 
        // Return Response to browser
        deliver_response($_GET['format'], $response);
 
    }
 
    // Return an error response if user fails authentication. This is a very simplistic example
    // that should be modified for security in a production environment
    elseif( $_POST['username'] != 'foo' && $_POST['password'] != 'bar' ){
        $response['code'] = 4;
        $response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
        $response['data'] = $api_response_code[ $response['code'] ]['Message'];
 
        // Return Response to browser
        deliver_response($_GET['format'], $response);
 
    }
 
}

// --- Step 3: Process Request


//LOGIN and VIEW PROFILE CODE

if( $_SERVER['REQUEST_METHOD'] == "GET"){
   
    if(isset($_GET['token'])){   
        
        $token = $_GET['token'];
        
        $selectquer = $mysqli->query("SELECT * FROM user WHERE token='".$token."'");
         if($selectquer->num_rows > 0) {

             while($row = $selectquer->fetch_assoc()) {
         
            
             $username = $row['username'];
             $email = $row['email'];
             $token = $row['token'];
             $password = $row['password'];
             
             $response['code'] = 1;
             $response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
             $response['data'] =  array(
                 username => $username,
                 emailid => $email,
                 token => $token,
                 password => $password);
             }
        }
        else{
            
    $response['code'] = 4;
    $response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
    $response['data'] = 'No Data found with this token';
   
        }
  
       
    }
    else{
    
    $username = $_GET['username'];
    $password = $_GET['password'];
    
    if(!empty($username)&&!empty($password))
    {
    $selectquer = $mysqli->query("SELECT * FROM user WHERE username='".$username." 'and password='".$password." ' ");
    
        if($selectquer->num_rows > 0) {
        
        while($row = $selectquer->fetch_assoc()) {
            
             $name = $row['username'];
             $email = $row['email'];
             $token = $row['token'];
             
             $response['code'] = 1;
             $response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
             $response['data'] =  array(
                 username => $username,
                 emailid => $email,
                 token => $token);
        }
       
        
    }
     else{
     
    $response['code'] = 4;
    $response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
    $response['data'] = 'Invalid Username or Password';
     }
    }
    else
    {
    $response['code'] = 0;
    $response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
    $response['data']['user'] = 'unknown';   
    $response['data']['message'] = 'incomplete';  
    }
    }
}
//REGISTER CODE
else if($_SERVER['REQUEST_METHOD'] == "POST") {
       
    parse_str(file_get_contents("php://input"),$input);
   
    $email = $input['email'];
    $username = $input['username'];
    $password = $input['password'];
    $cnfpassword = $input['cnfpassword'];
    
    if(!empty($email)&&!empty($username)&&!empty($password)&&!empty($cnfpassword))
    {
    
    if($password!=$cnfpassword){   
        
    
    $response['code'] = 0;
    $response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
    $response['data'] = 'Passwords didnt match';
    }
    else{
        
    $token = md5($username);
        
    $insertquer = $mysqli->query("INSERT INTO `securestore`.`user` (`email`, `username`, `password`, `token`) 
         VALUES ('$email', '$username', '$password','$token')");
    
    $response['code'] = 1;
    $response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
    $response['data']['user'] = $username;
    $response['data']['message'] = 'reg_success';
    }
    
    }
    else
    {
    $response['code'] = 0;
    $response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
    $response['data']['user'] = 'unknown';   
    $response['data']['message'] = 'incomplete';   
    }
}

//UPDATE PASSWORD CODE
else if($_SERVER['REQUEST_METHOD'] == "PUT") {
       
    parse_str(file_get_contents("php://input"),$input);
   
    $email = $input['email'];
    $username = $input['username'];
    $newpassword = $input['newpassword'];
    $cnfnewpassword = $input['cnfnewpassword'];
    
    if(!empty($email)&&!empty($username)&&!empty($newpassword)&&!empty($cnfnewpassword))
    {
    
    if($newpassword!=$cnfnewpassword){
        
    
    $response['code'] = 0;
    $response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
    $response['data'] = 'Passwords didnt match';
    }
    else{
    
    $selectquer = $mysqli->query("SELECT * FROM user WHERE username='".$username." 'and email='".$email." ' ");
    
    if($selectquer->num_rows > 0) {
        
    $updatequer = $mysqli->query("UPDATE user set password='$newpassword' where username='$username' ");
    
    if($updatequer){
    $response['code'] = 1;
    $response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
    $response['data']['user'] = $username;
    $response['data']['message'] = 'update success';
    }
    else{
    $response['code'] = 1;
    $response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
    $response['data']['user'] = $username;
    $response['data']['message'] = 'update not success';
    }
    }
    else
    {
    $response['code'] = 1;
    $response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
    $response['data']['user'] = $username;
    $response['data']['message'] = 'invalid user';  
    }
    }
    
    
    }
    else
    {
    $response['code'] = 0;
    $response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
    $response['data']['user'] = 'unknown';   
    $response['data']['message'] = 'incomplete';   
    }
}

//DELETE CODE
else if($_SERVER['REQUEST_METHOD'] == "DELETE") {
       
    parse_str(file_get_contents("php://input"),$input);
    
    $token = $input['token'];
    
    if(!empty($token))
    {
    
    $selectquer = $mysqli->query("SELECT * FROM user WHERE token='".$token."' ");
    
    if($selectquer->num_rows > 0) {
         while($row = $selectquer->fetch_assoc()) {
            
             $username = $row['username'];
        
        $deletequer = $mysqli->query("DELETE FROM user WHERE token='".$token."' ");
        $response['code'] = 1;
        $response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
        $response['data']['user']=$username;
        $response['data']['message'] = 'delete success';  
         }
    }
    else{
        $response['code'] = 0;
        $response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
        $response['data']['message'] = 'Invalid Request';  
    }
    
    }
    else
    {
    $response['code'] = 0;
    $response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
    $response['data']['user'] = 'unknown';   
    $response['data']['message'] = 'incomplete';   
    }
}



else{
    
    $response['code'] = 2;
    $response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
    $response['data'] = 'Method Incorrect';

}

 
// --- Step 4: Deliver Response

// Return Response to browser
deliver_response($_GET['format'], $response);
?>
