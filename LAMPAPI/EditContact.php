

<?php

    $inData = getRequestInfo();
	
    // Open up env file
	$env = parse_ini_file('.env');
	$first_name = $inData["firstName"];
	$last_name = $inData["lastName"];
    if (array_key_exists("phone",$inData)) {
        $phone = $inData["phone"];
    }
    else {
        $phone = null;
    }
    
    if (array_key_exists("email",$inData)) {
        $email = $inData["email"];
    }
    else {
        $email = null;
    }
	
	$userId = $inData["userId"];

	// Database settings
	$db_host = $env['DB_SERVER'];
	$db_user = $env['DB_USER'];
	$db_password = $env['DB_PASS'];
	$db_name = $env['DB_NAME'];
  

    if (empty($first_name) || empty($last_name) || (empty($phone)&&empty($email)) || empty($userId)) {
        returnWithError("Missing 1 or more appropriate request fields. Looking for userId, firstname, lastname, phone, and email.");
    }

	$conn = new mysqli($db_host, $db_user, $db_password, $db_name);
	
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
        //Check if Contact and User exist
        $stmt = $conn->prepare("SELECT * FROM Contacts WHERE UserID=? AND FirstName=? AND LastName=?");
		$stmt->bind_param("sss", $userId,$first_name, $last_name);
		$stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (empty($result)) {
            returnWithError("Contact User not found.");
            return;
        }
        
        $editedPhone = false;
        $editedEmail = false;
        
        //Update User Contact Phone Number
        if (!empty($phone)) {
            $stmt = $conn->prepare("UPDATE Contacts SET Phone=? WHERE UserID=? AND FirstName=? AND LastName=?;");
            $stmt->bind_param("ssss", $phone,$userId,$first_name, $last_name);
            $stmt->execute();
            if ($stmt->error) {
                returnWithError($stmt->error);
            }
            $editedPhone = true;
        }

        //Update User Contact Email
        if (!empty($email)) {
            $stmt = $conn->prepare("UPDATE Contacts SET Email=? WHERE UserID=? AND FirstName=? AND LastName=?;");
            $stmt->bind_param("ssss", $email,$userId,$first_name, $last_name);
            $stmt->execute();
            $editedEmail = true;
        }
		
        if ($stmt->error) {
		    returnWithError($stmt->error);
        }
        else {
            if ($editedEmail==true || $editedPhone==true) {
                if ($editedEmail==false) {
                    returnWithInfo("Successfully edited the phone number.");
                }
                if ($editedPhone==false) {
                    returnWithInfo("Successfully edited the email.");
                }
                if ($editedEmail && $editedPhone) {
                    returnWithInfo("Successfully edited the phone number and email.");
                }
            }
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
