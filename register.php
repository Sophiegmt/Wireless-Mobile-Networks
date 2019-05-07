<?php

$username = $_GET['username'];
$password = $_GET['password'];
$minTemp= $_GET['minTemp'];
$maxTemp= $_GET['maxTemp'];
$WBstatus= $_GET['WBstatus'];

$users = file('users.txt');
$exists = false;
$response = array_fill_keys(
	array('error', 'message'), NULL);

$users = json_decode($users[0], true);
$exists = false;

// verificar que nao existe ja um user com um username igual
foreach ($users as $key => $user) {
	if(!strcmp($username, $user['username'])){	
            $exists = true;	
			break;
	}
}

if ($exists) {
	$response['error'] = true;
	$response['message'] = 'Username already registered in the database';	
}else{
	$response['error'] = false;
	$response['message'] = 'Success';
	
	//--------- update list of users for bluetooth permission ----------
	$newUser = array('username' => $username, 'password'=>$password, 'macAddr' => NULL,'minTemp'=> $minTemp, 'maxTemp'=>$maxTemp, 'WBstatus'=>$WBstatus );
	array_push($users, $newUser);
    $myfile = fopen("users.txt", "w");
	fwrite($myfile, json_encode($users));
	fclose($myfile);
	//------------------------------------------------------------------

	//----------------- update list of users for login -----------------
	$logins = file('logins.txt');
	$logins = json_decode($logins[0], true);
	$newLogin = array('username' => $username, 'password' => $password, 'type' => 'user');
	array_push($logins, $newLogin);
	$myfile = fopen("logins.txt", "w");
	fwrite($myfile, json_encode($logins));
	fclose($myfile);
	//------------------------------------------------------------------
}

echo(json_encode($response));

?>