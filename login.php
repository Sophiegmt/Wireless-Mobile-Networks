<?php

$username = $_GET['username'];
$password = $_GET['password'];

$logins = file('logins.txt');
$logins = json_decode($logins[0], true);

$response = array_fill_keys(
	array('error', 'message', 'type'), NULL);

//--------- search for user ----------
foreach ($logins as $key=>$login){
	if(!strcmp($username, $login[username])){
		if(isset($login['password'])){
			if(!strcmp($password, $login[password])){		
				$response['error'] = false;
				$response['message'] = 'success';
				$response['type'] = $login[type];		
			}else{	
				$response['error'] = true;
				$response['message'] = 'The username and the password do not match';	
			}
			break;
		}/* else{
                $login['password'] = $password;
                $logins[$key] = $login;
				$response['error'] = false;
				$response['message'] = 'success';
				$response['type'] = $login[type];
		} */
	}
}
//---------------------------------------

//--------- update logins list ----------
$myfile = fopen("logins.txt", "w");
fwrite($myfile, json_encode($logins));
fclose($myfile);
//---------------------------------------

if(!isset($response['error'])){
	$response['error'] = true;
	$response['message'] = 'Username does not exist';
}	

echo(json_encode($response));

?>