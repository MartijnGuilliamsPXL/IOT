<!DOCTYPE HTML>
<?php 
$server = "localhost";
$username = "student_11901231";
$password = "csOgBOHWisQx";
$database = "student_11901231";
$conn = new mysqli($server, $username, $password, $database);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT * FROM Sensor_waardes WHERE sensor_id = 1";
$response = mysqli_query($conn,$query);
$data = array();

while($row = mysqli_fetch_array($response)) {
	$Time = strtotime($row['datum'])*1000;
	$data[] = array("x"=>$Time,"y"=>$row["waarde"]);  	
}

$query = "SELECT * FROM Sensor_waardes WHERE sensor_id = 2";
$response = mysqli_query($conn,$query);
$data2 = array();

while($row = mysqli_fetch_array($response)) {
	$Time = strtotime($row['datum'])*1000;
	$data2[] = array("x"=>$Time,"y"=>$row["waarde"]); 	
}

mysqli_close($conn);
?>
<html>
    <head>
        
    <script src="https://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>
    <script src="https://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script>

    <button id="24hButton">Last 24h</button>
    <button id="Reset">Reset Graph</button>
        <script>
            window.onload = function() {
                var chartdata;

                var options =  {
                    interactivityEnabled: true,
                    animationDuration: 1500,
                    animationEnabled: true,
                    zoomEnabled: true,
                    theme: "dark2",
                    title: {
                        text: "Temperatuur - Vochtigheid"
                    },
                    axisX: {
                        title: "Datum",
                    },
                    axisY: {
                        title: "Waarde",
                    },
                    legend: {
                        cursor: "pointer",
                        itemclick: toggleDataSeries
                    },
                    data: [
                        {
                            type: "line",
                            name: "Temperatuur",
                            markerSize: 5,
                            xValueFormatString: "DD/MM/YYYY hh;mm;ss",
                            xValueType: "dateTime",
                            yValueFormatString: "##°C",
                            showInLegend: true,
                            dataPoints: <?php
                                echo json_encode($data, JSON_NUMERIC_CHECK);
                            ?>
                        },
                        {
                            type: "line",
                            name: "Vochtigheid",
                            markerSize: 5,
                            xValueFormatString: "DD/MM/YYYY hh:mm:ss",
                            xValueType: "dateTime",
                            yValueFormatString: "##,##%",
                            showInLegend: true,
                            dataPoints: <?php 
                                echo json_encode($data2, JSON_NUMERIC_CHECK); 
                            ?>
                        }
                    ]

                };
                chart = $("#chartContainer").CanvasJSChart(options);

                document.getElementById("24hButton").addEventListener("click", function(){
                    click24Hour(chartdata);
                });

                document.getElementById("Reset").addEventListener("click", function(){
                    clickReset(chartdata);
                });

                function toggleDataSeries(e) {
                    //console.log(e);
                    if (typeof (e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                        e.dataSeries.visible = false;
                    } 
                    else {
                        e.dataSeries.visible = true;
                    }
                    chartdata = e.chart;
                    e.chart.render();
                }

                function click24Hour(cd){
                    var today = new Date();
                    var yesterday = new Date();
                    yesterday.setDate(yesterday.getDate()-1);

                    cd.axisX[0].set("viewportMinimum", yesterday, false);
                    cd.axisX[0].set("viewportMaximum", today);                 
                }

                function clickReset(cd){
                    cd.axisX[0].set("viewportMinimum", null, false);
                    cd.axisX[0].set("viewportMaximum", null); 
                }

            }
        </script>
    </head>
    <body>
        <div id="chartContainer" style="height: 400px; width: 100%;"></div>  
    </body>
</html>