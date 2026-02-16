

<?php

    $inData = getRequestInfo();
	
	$color = $inData["color"];
	$userId = $inData["userId"];
    $newColor = $inData["newColor"];

    if (empty($color) || empty($userId) || empty($newColor)) {
        returnWithError("Missing 1 or more appropriate request fields. Looking for color, userId, and newColor.");
    }

	$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
	
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
        //Check if Color and User exist
        $stmt = $conn->prepare("SELECT ID,Name,UserID FROM Colors WHERE UserID=? AND Name=?");
		$stmt->bind_param("ss", $userId, $color);
		$stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (empty($result)) {
            returnWithError("Color or User not found.");
            return;
        }
        
        
        //Update User Color
		$stmt = $conn->prepare("UPDATE Colors SET Name=? WHERE UserID=? AND Name=?;");
		$stmt->bind_param("sis", $newColor, $userId, $color);
		$stmt->execute();
		
        if ($stmt->error) {
		    returnWithError($stmt->error);
        }
        else {
            returnWithInfo("Successfully edited a color value.");
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
