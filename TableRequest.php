<?php
	$server = "localhost";
	$username = "student_11901231";
	$password = "csOgBOHWisQx";
	$database = "student_11901231";

	$conn = mysqli_connect($server, $username, $password, $database);
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}

	$id = $_GET['id'];
	$minValue = strval($_GET['minValue']);
	$maxValue = strval($_GET['maxValue']);

	$next_day = strtotime("1 day", strtotime($_GET['date']));
	$next_day = date("Y-m-d", $next_day);
	$date = strval($_GET['date']);

	$query = "SELECT sensor_id, waarde, datum FROM Sensor_waardes WHERE sensor_id= '$id' AND waarde >= '$minValue' AND waarde <= '$maxValue' AND datum >'$date' AND datum < '$next_day'";
	$stmt = $conn->prepare($query);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $waarde, $tijd);

	echo "<table class=\"tabel\">";
		echo "<thead>";
			echo "<tr>";
					echo "<th><span class=\"text\">id</span></th>";
					echo "<th><span class=\"text\">tijd</span></th>";
					echo "<th><span class=\"text\">waarde</span></th>";
			echo "</tr>";
		echo "</thead>";
		echo "<tbody>";
			while($row = $stmt->fetch())
			{
				echo "<tr>";
					echo "<td>$id</td>";
					echo "<td>$tijd</td>";
					echo "<td>$waarde</td>";
				echo "</tr>";
			}
		echo "</tbody>";
	echo "</table>";
	$stmt->close();
?>