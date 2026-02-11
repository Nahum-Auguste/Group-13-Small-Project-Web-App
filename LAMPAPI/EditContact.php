

<?php

    $inData = getRequestInfo();
	
    // Open up env file
	$env = parse_ini_file('.env');
	$first_name = $inData["firstName"];
	$last_name = $inData["lastName"];

    if (array_key_exists("newFirstName",$inData)) {
        $newFirstName = $inData["newFirstName"];
    }
    else {
        $newFirstName = null;
    }

    if (array_key_exists("newLastName",$inData)) {
        $newLastName = $inData["newLastName"];
    }
    else {
        $newLastName = null;
    }

    if (array_key_exists("newPhone",$inData)) {
        $phone = $inData["newPhone"];
    }
    else {
        $phone = null;
    }
    
    if (array_key_exists("newEmail",$inData)) {
        $email = $inData["newEmail"];
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
  

    if (empty($first_name) || empty($last_name) || (empty($phone)&&empty($email)&&empty($newFirstName)&&empty($newLastName)) || empty($userId)) {
        returnWithError("Missing 1 or more appropriate request fields. Looking for userId, firstname, lastname, and at least one of these values: newFirstName, newLastName, newPhone, or newEmail.");
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
        $editedFirstName = false;
        $editedLastName = false;

        //Update User Contact First Name
        if (!empty($newFirstName)) {
            $stmt = $conn->prepare("UPDATE Contacts SET FirstName=? WHERE UserID=? AND FirstName=? AND LastName=?;");
            $stmt->bind_param("ssss", $newFirstName,$userId,$first_name, $last_name);
            $stmt->execute();
            if ($stmt->error) {
                returnWithError($stmt->error);
            }
            else {
                $editedFirstName = true;
            }
        }

        //Update User Contact Last Name
        if (!empty($newLastName)) {
            $stmt = $conn->prepare("UPDATE Contacts SET LastName=? WHERE UserID=? AND FirstName=? AND LastName=?;");
            if ($editedFirstName) {
                $stmt->bind_param("ssss", $newLastName,$userId,$newFirstName, $last_name);
            }
            else {
                $stmt->bind_param("ssss", $newLastName,$userId,$first_name, $last_name);
            }
            $stmt->execute();
            if ($stmt->error) {
                returnWithError($stmt->error);
            }
            else {
                $editedLastName = true;
            }
        }
        
        //Update User Contact Phone Number
        if (!empty($phone)) {
            $stmt = $conn->prepare("UPDATE Contacts SET Phone=? WHERE UserID=? AND FirstName=? AND LastName=?;");
            if ($editedFirstName && !$editedLastName) {
                $stmt->bind_param("ssss", $phone,$userId,$newFirstName, $last_name);
            }
            elseif ($editedLastName && !$editedFirstName) {
                $stmt->bind_param("ssss", $phone,$userId,$first_name, $newLastName);
            }
            elseif ($editedFirstName && $editedLastName) {
                $stmt->bind_param("ssss", $phone,$userId,$newFirstName, $newLastName);
            }
            else {
                $stmt->bind_param("ssss", $phone,$userId,$first_name, $last_name);
            }
            $stmt->execute();
            if ($stmt->error) {
                returnWithError($stmt->error);
            }
            else {
                $editedPhone = true;
            }
        }

        //Update User Contact Email
        if (!empty($email)) {
            
            $stmt = $conn->prepare("UPDATE Contacts SET Email=? WHERE UserID=? AND FirstName=? AND LastName=?;");
            if ($editedFirstName && !$editedLastName) {
                $stmt->bind_param("ssss", $email,$userId,$newFirstName, $last_name);
            }
            elseif ($editedLastName && !$editedFirstName) {
                $stmt->bind_param("ssss", $email,$userId,$first_name, $newLastName);
            }
            elseif ($editedFirstName && $editedLastName) {
                $stmt->bind_param("ssss", $email,$userId,$newFirstName, $newLastName);
            }
            else {
                $stmt->bind_param("ssss", $email,$userId,$first_name, $last_name);
            }
            
            $stmt->execute();
            if ($stmt->error) {
                returnWithError($stmt->error);
            }
            else {
                $editedEmail = true;
            }
        }
		
        if ($stmt->error) {
		    returnWithError($stmt->error);
        }
        else {
            $info = "";

            if ($editedPhone) {
                $info .= "Successfully edited the phone number.\n";
            }
            if ($editedEmail) {
                $info .= "Successfully edited the email.\n";
            }
            if ($editedFirstName) {
                $info .= "Successfully edited the firstname.\n";
            }
            if ($editedLastName) {
                $info .= "Successfully edited the lastname.\n";
            }

            returnWithInfo($info);
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
