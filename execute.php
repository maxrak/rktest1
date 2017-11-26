<?php
$content = file_get_contents("php://input");
$update = json_decode($content, true);

function pg_connection_string_from_database_url() {
  extract(parse_url($_ENV["DATABASE_URL"]));
  return "user=$user password=$pass host=$host dbname=" . substr($path, 1); # <- you may want to add sslmode=require there too
}

if(!$update)
{
  exit;
}

$message = isset($update['message']) ? $update['message'] : "";
$messageId = isset($message['message_id']) ? $message['message_id'] : "";
$chatId = isset($message['chat']['id']) ? $message['chat']['id'] : "";
$firstname = isset($message['chat']['first_name']) ? $message['chat']['first_name'] : "";
$lastname = isset($message['chat']['last_name']) ? $message['chat']['last_name'] : "";
$username = isset($message['chat']['username']) ? $message['chat']['username'] : "";
$date = isset($message['date']) ? $message['date'] : "";
$request =isset($message['text']) ? $message['text'] : "";

$text = "";

if ($request == "manca") 
{
	$text += "mancano:\n";

	# Here we establish the connection. Yes, that's all.
	$pg_conn = pg_connect(pg_connection_string_from_database_url());

	# Now let's use the connection for something silly just to prove it works:
	$result = pg_query($pg_conn, "SELECT * FROM dispensa.manca");

	print "<pre>\n";
	if (!pg_num_rows($result)) {
		print("Your connection is working, but your database is empty.\nFret not. This is expected for new apps.\n");
	} else {
  		print "Tables in your database:\n";
  		while ($row = pg_fetch_row($result)) { 
  			$text += "- $row[1]\n"; 
  		}
	}
	print "\n";
} else {
	$text= "illaltro";	
}


header("Content-Type: application/json");
$parameters = array('chat_id' => $chatId, "text" => $text);
$parameters["method"] = "sendMessage";
echo json_encode($parameters);
