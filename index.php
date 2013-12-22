<?php 
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Bay Area Bike Share</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
      <![endif]-->

      <meta http-equiv="CACHE-CONTROL" content="NO-CACHE">
      <meta http-equiv="EXPIRES" content="Sat, 26 Jul 1997 05:00:00 GMT">
      <style>
      	body {
      		padding-top: 150px;
      	}
      	#bike-stations-list {
      		text-align: right;
      	}
      	#time {
      		text-align: right;
      	}
      	.navbar-nav {
      		float: right;
      	}

      	#bikestationslist {
      		float: right;
      		padding-right: 170px;
      	}

      	#station-map { 
      		height:600px; 
      		width: 50%; 
      		float: left;
      	}

      	/* Foundation Causes the Zoom/Pan image to flub up.  These lines fix that */
      	#station-map img, 
      	#station-map object, 
      	#station-map embed { max-width:none; }
      	#station-map div { -webkit-box-sizing: content-box; -moz-box-sizing: content-box; box-sizing: content-box; }

    /*
     * Station Map Popup Window *
     */
     #station-map table tbody tr th, table tbody tr td {font-size: 14px; font-family: GtPressuraRegular,sans-serif; font-weight: bold;}
     #station-map div.station-window { overflow: hidden; text-align:center }
     #station-map div.station-window h2 { color:#0070CD; margin:0px 0px 10px 0px; letter-spacing:-0.3px; font-weight: bold; font-size:19px;}
     .chrome #station-map div.station-window h2 { letter-spacing:-1.5px; } 
     #station-map div.station-window table { margin:0px; box-shadow:none; -webkit-box-shadow:none; -moz-box-shadow:none; border:none; min-width:0px; width:auto; }
     #station-map div.station-window table th { background:none; border:none; text-shadow:none; filter:none; }   
     #station-map div.station-window table tbody th { color:#0070CD; vertical-align:middle; padding:5px; text-align:right; }
     #station-map div.station-window table tbody td { color:#82C7BC; text-align:center; padding:5px 0px 5px 0px; border:none;  }
     #station-map div.station-window table tbody tr { background: none; }
     #station-map div.station-data { width:150px; margin:0 auto; }

     /* Fixes a clash with Foundations and google maps windows */
     #station-map div { -webkit-box-sizing: content-box; -moz-box-sizing: content-box; box-sizing: content-box; }

     #cities { position:relative; z-index:1; -webkit-box-shadow: 0 0 4px 2px #666666; box-shadow: 0 0 4px 2px #666666; }
     #cities button.tiny,
     #cities .button.tiny { padding:9px 10px; }

 </style>
</head>
<body>
	<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<div class="navbar-brand"><h1>Bay Area Bike Share</h1></div>
			</div>
			<div class="collapse navbar-collapse">
				<ul class="nav navbar-nav">
					<li>
						<ul class="navbar-brand" id="time">
						</ul>
					</li>
				</ul>
			</div><!--/.nav-collapse -->
		</div>
	</div>

	<div id="station-map"></div>

	<div id="bikestationslist">

		<div class="row">
			<div class="span3">
				<ul id="bike-stations-list">
				</ul>
			</div>
		</div>
	</div>

	<script src="https://code.jquery.com/jquery.js"></script>
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
	<script type="application/json" src="BayAreaBikeShare.php"></script>

	<!-- Stations JS -->
	<script src="https://maps.googleapis.com/maps/api/js?v=3&sensor=false" type="text/javascript"></script>	
	<script src="stationMap.js" type="text/javascript" charset="utf-8"></script>

	<script>
		$.getJSON("BayAreaBikeShare.php", function(bikeshare) {

			var currentDateTimeArray = [bikeshare.executionTime.split(" ")];
			var currentDateArray = currentDateTimeArray[0][0].split("-");

			function displayTime(timeInfo){
				var timeParagraph = $('<p>');
				timeParagraph.html('<h4>Current information as of: <br />' + currentDateTimeArray[0][1] + ' ' + currentDateTimeArray[0][2] + '<br />' + currentDateArray[1] + '-' + currentDateArray[2] + '-' + currentDateArray[0] + '</h4><br />');
				var timeDisplay = $('#time');
				timeDisplay.append(timeParagraph);
			}

			displayTime(currentDateTimeArray);

			var stationArray = [bikeshare.stationBeanList[52], bikeshare.stationBeanList[35], bikeshare.stationBeanList[31]];

			function addStationToList(stationInfo){
				var stationName = stationInfo.stationName;
				var availableBikes = stationInfo.availableBikes;
				var availableDocks = stationInfo.availableDocks;
				var totalDocks = stationInfo.totalDocks;
				var stationParagraph = $('<p>');
				stationParagraph.html('<h3>' + stationName + '</h3><strong>Available Bikes: ' + availableBikes + '<br />Available Docks: ' + availableDocks + ' out of ' + totalDocks + '<br/><br/><br/></strong>');
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