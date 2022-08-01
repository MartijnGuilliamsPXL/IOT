<?php
    $jsonAPI = file_get_contents("https://api.openweathermap.org/data/2.5/weather?q=Tongeren&appid=6ecd1e7d9194daf803e101447789c618&units=metric&lang=nl");
    echo $jsonAPI;
?>