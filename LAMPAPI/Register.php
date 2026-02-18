
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
            // Check if login already exists
            $isDuplicate= $conn->prepare(
                "SELECT * FROM Users WHERE LOGIN = ?"
            );

            $isDuplicate->bind_param("s", $login);
            $isDuplicate->execute();
            $checkDup = $isDuplicate->get_result();

            // If login exists in table, return error
            if ($checkDup->num_rows > 0) {
                returnWithError("Username already exists; please choose another one.");
                $isDuplicate->close();
                
            }
            
            // Login unique; insert into table
            else {

                $stmt = $conn->prepare(

                "INSERT INTO Users (firstName, lastName, login, password)

                VALUES (?,?,?,?)");



                $stmt->bind_param("ssss",$firstName,$lastName,$login,$password);

                $stmt->execute();

                $stmt->close();



                returnWithInfo( $firstName, $lastName, $login);

            }
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