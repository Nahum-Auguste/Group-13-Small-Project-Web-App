<?php
	$inData = getRequestInfo();
	
	

	$first_name = $inData["first_name"];
	$last_name = $inData["last_name"];
	$phone = $inData["phone"];
	$email = $inData["email"];
	$userId = $inData["userId"];
	
	$db_host = getenv('DB_SERVER');
	$db_user = getenv('DB_USER');
	$db_password = getenv('DB_PASS');
	$db_name = getenv('DB_NAME');

	$conn = new mysqli($db_host, $db_user, $db_password, $db_name);
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$stmt = $conn->prepare("INSERT into Contacts (FirstName , LastName , Phone , Email, UserID) VALUES (?, ?, ?, ?, ?);");
		$stmt->bind_param("ss", $first_name, $last_name, $phone, $email, $userId);
		$stmt->execute();
		$stmt->close();
		$conn->close();
		returnWithError("");
	}

	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
	
	function returnWithError( $err )
	{
		$retValue = '{"error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
?>