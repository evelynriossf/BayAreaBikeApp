<?php 
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
?>
<!doctype html>
<html>
 <head>
  <meta charset="utf-8">
  <title>Bay Area Bike Share</title>
  <link rel="stylesheet" type="text/css" href="http://teaching-materials.org/ajax/lib/bootstrap.css">
  <meta http-equiv="CACHE-CONTROL" content="NO-CACHE">
  <meta http-equiv="EXPIRES" content="Sat, 26 Jul 1997 05:00:00 GMT">
 </head>
 <body>

<div class="container">
 
 <div class="row">
   <div class="span12">
    <h2 class="page-header">Bay Area Bike Share</h2>
    <h3>My Important Bike Stations</h3>
   </div>
 </div>
 
 <div class="row">
   <div class="span3">
     <ul id="bike-stations-list">
    </ul>
   </div>
   <div class="span9" id="station-info">
   </div>
 </div>
</div>

  <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
<script type="application/json" src="BayAreaBikeShare.php"></script>

 <script>
$.getJSON("BayAreaBikeShare.php", function(bikeshare) {
    var stationArray = [bikeshare.stationBeanList[52], bikeshare.stationBeanList[35], bikeshare.stationBeanList[31]];

    function addStationToList(stationInfo){
      var stationName = stationInfo.stationName;
      var availableBikes = stationInfo.availableBikes;
      var availableDocks = stationInfo.availableDocks;
      var totalDocks = stationInfo.totalDocks;
      var stationParagraph = $('<p>');
      stationParagraph.html('<h4>' + stationName + '</h4><strong><br />Available Bikes: ' + availableBikes + '<br />Available Docks: ' + availableDocks + ' out of ' + totalDocks + '<br/></strong>');
      var bikeStationList = $('#bike-stations-list');
      bikeStationList.append(stationParagraph);
    }

      for (var i = 0; i < stationArray.length; i++) {
       addStationToList(stationArray[i]);
     }

    });

 </script>
 </body>
</html>
