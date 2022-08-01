<?php
  $server = "localhost";
  $username = "student_11901231";
  $password = "csOgBOHWisQx";
  $database = "student_11901231";

  $id = $_GET["id"];
  $waarde = $_GET["waarde"];
  $conn = new mysqli($server, $username, $password, $database);

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $query = "INSERT INTO Sensor_waardes (sensor_id, waarde, datum) VALUES ($id, $waarde, now())";
  if ($conn->query($query) === TRUE) {
    echo "New record created successfully!";
    echo "<br>";
    echo "id is $id";
    echo "<br>";
    echo "waarde is $waarde";
  }
  else {
    echo "Error: $query";
    echo "<br>";
    echo "$conn->error";
  }

  $conn->close();

?>