<?php
include('retention_science_api.php');
$api_user = "asdfb";
$api_pass = "";
$testmode = true;
$user_record_id = '74c51153bf1b47c9926be8c1b24dfa85';

$client = new RetentionScienceApi($api_user, $api_pass, $testmode);

// GET the last user record id 
echo "Does this user ". $user_record_id . " exist?";
if ($client->user_exists($user_record_id)){
	echo "yes";
	$show = $client->show_user($user_record_id);
	var_dump($show);

} else {
	echo "no";
}
	echo "\n";
?>
