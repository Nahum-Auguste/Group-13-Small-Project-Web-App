

<?php

    $inData = getRequestInfo();
	
    // Open up env file
	$env = parse_ini_file('.env');
	$userId = $inData["userId"];

	// Database settings
	$db_host = $env['DB_SERVER'];
	$db_user = $env['DB_USER'];
	$db_password = $env['DB_PASS'];
	$db_name = $env['DB_NAME'];
  

    if (empty($userId)) {
        returnWithError("Missing or incorrect userId field.");
    }

	$conn = new mysqli($db_host, $db_user, $db_password, $db_name);
	
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
        //Check if Contact and User exist
        $stmt = $conn->prepare("SELECT * FROM Contacts WHERE UserID=?");
		$stmt->bind_param("s", $userId);
		$stmt->execute();
        $result = $stmt->get_result();
        $json = array();

        if ($stmt->error) {
		    returnWithError($stmt->error);
        }

        if (empty($result)) {
            returnWithError("Contact User not found.");
        }
		else {
            while($record = $result->fetch_assoc()) {
                array_push($json,$record);
            }
            echo json_encode($json);
        }
        
        

        $stmt->close();

        //End connection
		$conn->close();
	}

	function returnWithError( $err )
    {
        $retValue = '{"error":"' . $err . '"}';
        sendResultInfoAsJson( $retValue );
    }

    function returnWithInfo($info)
	{
		sendResultInfoAsJson( $info );
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

?>
