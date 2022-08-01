<?php
   $jsonAPI = file_get_contents("https://api.openweathermap.org/data/2.5/weather?q=Tongeren&appid=6ecd1e7d9194daf803e101447789c618&units=metric&lang=nl");
   
   $data = json_decode($jsonAPI);
   //echo json_encode($data);
   $temp = $data->main->temp;
   $pressure = $data->main->pressure;
   $wind = $data->wind->speed;
   $humidity = $data->main->humidity;
   $desc = $data->weather[0]->description;

   echo "<?xml version='1.0' encoding='UTF-8'?>";
   echo "<rss version='2.0'>";
      echo "<channel>";
         echo "<title>ESP-weatherstation RSS</title>";
         echo "<item>";
            echo "<H3>Weer in Tongeren</H3>";

            echo "Weersituatie: ";
            echo $desc;
            echo "<br>";

            echo "Temperatuur: ";
            echo $temp;
            echo "&degC";
            echo "<br>";

            echo "Windkracht: ";
            echo $wind;
            echo "bft";
            echo "<br>";

            echo "Luchtvochtigheid: ";
            echo $humidity;
            echo "%";
            echo "<br>";

            echo "Luchtdruk: ";
            echo $pressure;
            echo "hPa";
            echo "<br>";
            
         echo" </item>";
      echo "</channel>";
   echo "</rss>";
?>