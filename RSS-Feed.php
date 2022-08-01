<?php
   $server = "localhost";
   $username = "student_11901231";
   $password = "csOgBOHWisQx";
   $database = "student_11901231";

   $conn = new mysqli($server, $username, $password, $database);

   if ($conn->connect_error) {
         die("Connection failed: " . $conn->connect_error);
   }
   $date = date("Y-m-d H:i:s");
   $yesterday = date("Y-m-d H:i:s",strtotime("-1 days"));
   
   $query = "SELECT waarde, sensor_id, datum FROM Sensor_waardes WHERE datum < '$date' AND datum > '$yesterday'";
   $stmt = $conn->prepare($query);
   $stmt->execute();
   $stmt->store_result();
   $stmt->bind_result($waarde, $id, $tijd);

   $avg24hS1 = 0;
   $avg24hS2 = 0;

   $max24hS1 = -200;
   $max24hS2 = -200;

   $min24hS1 = 200;
   $min24hS2 = 200;

   $counterS1 = 0;
   $counterS2 = 0;
   $data = '';

   while($row = $stmt->fetch()){
      $data = "$data $id $tijd $waarde #\n";
      if($id == '1'){
         $avg24hS1 += $waarde;
         $counterS1++;
         if($max24hS1 < $waarde ){
            $max24hS1 = $waarde;
         }
         if($min24hS1 > $waarde){
            $min24hS1 = $waarde;
         }
      }
      else{
         $avg24hS2 += $waarde;
         $counterS2++;
         if($max24hS2 < $waarde ){
            $max24hS2 = $waarde;
         }
         if($min24hS2 > $waarde){
            $min24hS2 = $waarde;
         }
      }
   }
   $conn->close();
   $avg24hS1 = $avg24hS1/$counterS1;
   $avg24hS2 = $avg24hS2/$counterS2;

   $dataArray = explode("#", $data);


   echo "<?xml version='1.0' encoding='UTF-8'?>";
      echo "<rss version='2.0'>";
         echo "<channel>";
            echo "<title>latest sensor data statistics RSS</title>";
            echo "<H3>Last 24hour statistics</H3>";
            echo "<item>";

               echo "Temp average: $avg24hS1"; 
               echo "<br>";
               echo "Temp Max: $max24hS1"; 
               echo "<br>";
               echo "Temp Min: $min24hS1"; 
               echo "<br>";   
               echo "<br>";     
               echo "Humidity average: $avg24hS2";
               echo "<br>";     
               echo "Humidity Max: $max24hS2"; 
               echo "<br>";
               echo "Humidity Min: $min24hS2"; 

                  
            echo" </item>";
            echo "<H3>Last 24hour readings</H3>";
            echo "<item>";

               for($i= 0; $i < count($dataArray); $i++){
                  echo $dataArray[$i];
                  echo "<br>";
               }
                  
            echo" </item>";
         echo "</channel>";
      echo "</rss>";
?>