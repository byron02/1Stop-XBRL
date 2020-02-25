 <?php
	$servername = "35.230.134.121";
	$username = "root";
	$password = "O8xqqHMOAeytroJq";

	// Create connection
	$conn = new mysqli($servername, $username, $password);

	// Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}
	echo "Connected successfully";
?> 