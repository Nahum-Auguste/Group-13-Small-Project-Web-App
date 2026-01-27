
<?php
    $env = parse_ini_file('.env');

    $db_host = $env['DB_SERVER'];
	$db_user = $env['DB_USER'];
	$db_password = $env['DB_PASS'];
	$db_name = $env['DB_NAME'];

    $conn = new mysqli($db_host, $db_user, $db_password, $db_name);

    if ($conn->connect_error) {

        returnWithError($conn->connect_error);

    }

    else {

        $inData = getRequestInfo();

        $firstName = $inData["firstName"];

        $lastName = $inData["lastName"];

        $login = $inData["login"];

        $password = $inData["password"];



        if (empty($firstName)) {

            returnWithError("Please enter a firstname.");

        }

        elseif (empty($lastName)) {

            returnWithError("Please enter a lastname.");

        }

        elseif (empty($login)) {

            returnWithError("Please enter a login (username).");

        }

        elseif (empty($password)) {

            returnWithError("Please enter a password.");

        }

        else {
            // Database doesn't allow duplicate logins; be sure to check for
            // that somewhere here - mehreen

            $stmt = $conn->prepare(

            "INSERT INTO Users (firstName, lastName, login, password)

             VALUES (?,?,?,?)");



            $stmt->bind_param("ssss",$firstName,$lastName,$login,$password);

            $stmt->execute();

            $stmt->close();



            returnWithInfo( $firstName, $lastName, $login);

        }

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



    function returnWithInfo( $firstName, $lastName, $login)

	{

		$retValue = '{"firstName":"' . $firstName . '","lastName":"' . $lastName . '","login":"' . $login . '","error":""}';

		sendResultInfoAsJson( $retValue );

	}



    function returnWithError( $err )

    {

        $retValue = '{"error":"' . $err . '"}';

        sendResultInfoAsJson( $retValue );

    }



?>