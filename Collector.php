<?php
	$servername = "localhost";
	$username = "student_11901231";
	$password = "csOgBOHWisQx";
	$dbname = "student_11901231";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
	  die("Connection failed: " . $conn->connect_error);
	}
	if( isset($_GET['word']) && $_GET['word'] != ''){
		$sql "INSERT INTO example (word, score, time)
			VALUES (" . $_GET['word'] . ",0, now())";
		if ($conn->query($sql) === TRUE) {
			echo "New record created successfully";
		} else {
			echo "Error: " . $sql . "<br>" . $conn->error;
		}
	} else {
		?>
			<form method="get">
			<input type="text" name="word" />
			<input type="submit" />
			</form><br />
		<?php
	}
	if( isset( $_GET['id'])) {
		$sql = "UPDATE example SET score = score + 1 WHERE id = " . $_GET['id'];
		if ($conn->query($sql) === TRUE) {
			echo "Vote registered";
		} else {
			echo "Error: " . $sql . "<br>" . $conn->error;
		}
	}

	$sql = "SELECT ID, word, score FROM example ORDER BY score DESC";
	$result = Sconn->query($sql);

	if (Sresult->num_rows > 0) (
		// output data of each row
		while($row = $reslut->fetch_assoc()) {
			?>
				<a href="?id=<?php print( $row["ID"] ); ?>">
					<?php print( $row["word"]); ?>
					<i>(<?php print($row["score"]); ?>)</i>
				</a><br />
			<?php
		}
	} else {
		echo "0 results";
	}

	$conn-close();
?>
