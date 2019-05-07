<?php

$username = "";
$macAddr = "";
$error = false;
$response = null;

if(isset($_GET['username']) && isset($_GET['macAddr'])){
    $username = $_GET['username'];
    $macAddr = $_GET['macAddr'];
}else{
    $error = true;
    echo("error");
}

if(!$error){
    $users = file('users.txt');
    $users = json_decode($users[0], true);
    $exists = false;

    foreach($users as $key => $user){
        if(!strcmp($username, $user["username"])){
            $exists = true;
            if(isset($user['macAddr'])){
                if(!strcmp($macAddr, $user['macAddr'])){
                    $response = "yes";
                }else{
                    $response = "no";
                }
            }else{
                $user['macAddr'] = $macAddr;
                $users[$key] = $user;
                $response = "yes";
            }
        }
    }

    if (!$exists){
        $response = "no";
    }

    if(!strcmp($response, "yes")){

        date_default_timezone_set('Europe/Lisbon');
        $date = date('Y-m-d H:i:s');
        $newEntrance = array('username' => $username, 'timestamp' => $date);

        if(file_exists('entrances.txt')){
            $entrances = file('entrances.txt');
            $entrances = json_decode($entrances[0], true);
        }else{
            $entrances = array();
        }
        array_push($entrances, $newEntrance);

        $myfile = fopen("entrances.txt", "w");
        fwrite($myfile, json_encode($entrances));
        fclose($myfile);
        
        $myfile = fopen("users.txt", "w");
        fwrite($myfile, json_encode($users));

        fclose($myfile);
    }else{

        $myfile = fopen("alarms.txt", "a");
        date_default_timezone_set('Europe/Lisbon');

        $date = new DateTime();
        fwrite($myfile, $date->format('Y-m-d H:i:s') . "\n");

        fclose($myfile);

        // Send notification to the admin
        $url = "https://fcm.googleapis.com/fcm/send";
        $notification = array("title" => "Alarm Alert !!!!!", "body" => "Someone try to access at ". $date->format('Y-m-d H:i:s'));

        $adminsTokens = file("tokens.txt");
        $adminsTokens = json_decode($adminsTokens[0], true);

        foreach ($adminsTokens as $adminToken){
            $data = array("to" => $adminToken,
                "notification" => $notification);                                                                    
            $data_string = json_encode($data);                                                                                   
                                                                            
            $ch = curl_init($url);                                                                      
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
            'Content-Type: application/json',
            'Authorization: key=AIzaSyAdX_1cRwWSZyU993VEUFJLzqodQD_gT-g',                                                                                
            'Content-Length: ' . strlen($data_string))                                                                       
            ); 
            $result = curl_exec($ch);
            var_dump($adminToken);
        }
    }
    echo($response);
}
?>