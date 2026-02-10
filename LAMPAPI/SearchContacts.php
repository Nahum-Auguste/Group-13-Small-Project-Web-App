<?php

	$inData = getRequestInfo();
	$env = parse_ini_file('.env');

	$searchResults = "";
	$searchCount = 0;

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
		
		$stmt = $conn->prepare(
    		"select FirstName, LastName, Phone, Email from Contacts where (FirstName like ? or LastName like ?) and UserID=?"
		);

		$contactName = "%" . $inData["search"] . "%";
		$stmt->bind_param("ssi", $contactName, $contactName, $inData["userId"]);
		$stmt->execute();

		
		$result = $stmt->get_result();
		
		while($row = $result->fetch_assoc())
		{
			if( $searchCount > 0 )
			{
				$searchResults .= ",";
			}
			$searchCount++;
			$searchResults .= '{"FirstName":"' . $row["FirstName"] . '","LastName":"' . $row["LastName"] . '","Phone":"' . $row["Phone"] . '","Email":"' . $row["Email"] . '"}';
		}
		
		if( $searchCount == 0 )
		{
			returnWithError( "No Records Found" );
		}
		else
		{
			returnWithInfo( $searchResults );
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
		$retValue = '{"id":0,"firstName":"","lastName":"","error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
	function returnWithInfo( $searchResults )
	{
		$retValue = '{"results":[' . $searchResults . '],"error":""}';
		sendResultInfoAsJson( $retValue );
	}
	
?>