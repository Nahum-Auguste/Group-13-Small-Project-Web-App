<?php
	$inData = getRequestInfo();

	// open up env file
	$env = parse_ini_file('.env');
	$contactId = $inData["contactId"];
	$userId = $inData["userId"];


	// Database settings
	$db_host = $env['DB_SERVER'];
	$db_user = $env['DB_USER'];
	$db_password = $env['DB_PASS'];
	$db_name = $env['DB_NAME'];

	$conn = new mysqli($db_host, $db_user, $db_password, $db_name);
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		// delete contact only if it belongs to the user
		$stmt = $conn->prepare("DELETE FROM Contacts WHERE ID = ? AND UserID = ?");
		$stmt->bind_param("ii", $contactId, $userId);
		$stmt->execute();
		
		// confirm if the row was actually deleted
		if ($stmt->affected_rows > 0)
		{
			returnWithError("");
		}
		else
		{
			returnWithError("Contact not found or unauthorized");
		}
		
		$stmt->close();
		$conn->close();
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