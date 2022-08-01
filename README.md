# IoT
In dit project heb ik ervoor gezorgd dat de PSOC 6, webpagina, een database en API kunnen communiceren met elkaar.<br>

## Data Collection
De data van de sensoren worden via [request.php](https://github.com/MartijnGuilliamsPXL/IOT/blob/main/index.html) met een GET-methode doorgestuurd naar een MySQL database. De invulvakjes op [form.html](https://github.com/MartijnGuilliamsPXL/IOT/blob/main/form.html) functioneren op dezelfde manier.

## Grafiek
Op de [index.html](https://github.com/MartijnGuilliamsPXL/IOT/blob/main/index.html) pagina staat een grafiek met alle beschikbare datapunten. Deze datapunten zijn een combinatie van data dat de sensoren hebben gestuurd en data die op de [form.html](https://github.com/MartijnGuilliamsPXL/IOT/blob/main/form.html) pagina zijn ingevuld. Deze grafiek wordt weergegeven in een iframe. De [index.html](https://github.com/MartijnGuilliamsPXL/IOT/blob/main/index.html) pagina krijgt deze grafiek van [grafiek.php](https://github.com/MartijnGuilliamsPXL/IOT/blob/main/grafiek.php). De [grafiek.php](https://github.com/MartijnGuilliamsPXL/IOT/blob/main/grafiek.php) maakt gebruik van de graph api van het canvaJS-framework. De communicatie tussen deze twee loopt via JSON. De graph API haalt zijn data uit de MySQL-database. 

## Tabel
De tabel op [index.html](https://github.com/MartijnGuilliamsPXL/IOT/blob/main/index.html) wordt gegenereerd op basis van de ingevoerde data in de 'selectievakjes'. Deze data wordt doorgestuurd naar [TableRequest.php](https://github.com/MartijnGuilliamsPXL/IOT/blob/main/TableRequest.php) met Ajax. [TableRequest.php](https://github.com/MartijnGuilliamsPXL/IOT/blob/main/TableRequest.php) communiceert dan op zijn beurt met de MySQL-server. 

## Weather API
Op [index.html](https://github.com/MartijnGuilliamsPXL/IOT/blob/main/index.html) heb ik een RSS-Feed gemaakt waarop enkele gegevens van het weer in Tongeren weergegeven worden. Deze informatie krijgt de [index.html](https://github.com/MartijnGuilliamsPXL/IOT/blob/main/index.html) pagina van [openweahther.php](https://github.com/MartijnGuilliamsPXL/IOT/blob/main/openweahther.php). Deze kreeg de data op zijn beurt van de openweather API via JSON. 

![Flowchart](https://github.com/MartijnGuilliamsPXL/IOT/blob/main/FlowChart.png?raw=true)

