<?php 
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Bicycling App</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
	<link rel="stylesheet" href="main.css">

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
      <![endif]-->

      <meta http-equiv="CACHE-CONTROL" content="NO-CACHE">
      <meta http-equiv="EXPIRES" content="Sat, 26 Jul 1997 05:00:00 GMT">
  </head>
  <body>
  	<div class="navbar navbar-inverse navbar-static-top" role="navigation">
  		<div class="container">
  			<div class="navbar-header">
  				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
  					<span class="sr-only">Toggle navigation</span>
  					<span class="icon-bar"></span>
  					<span class="icon-bar"></span>
  					<span class="icon-bar"></span>
  				</button>
  				<div class="navbar-brand"><h1>Bay Area Bicycling App</h1></div>
  			</div>
  			<div class="collapse navbar-collapse">
  				<ul class="nav navbar-nav navbar-right">
  					<li>
  						<ul class="navbar-brand" id="time">
  						</ul>
  					</li>
  				</ul>
  			</div><!--/.nav-collapse -->
  		</div>
  	</div>

  	<div class="row">



  		<div class="col-md-4" id="bikestationslist">

  			<form name="bikeshareform">
  				<h3>City</h3>
  				<select class="btn btn-default btn-lg" id="cities" name="cities" onchange="setCity(this.options[this.selectedIndex].value)">
  					<option value="San Francisco">San Francisco</option>
  					<option value="Redwood City">Redwood City</option>
  					<option value="Palo Alto">Palo Alto</option>
  					<option value="Mountain View">Mountain View</option>
  					<option value="San Jose">San Jose</option>
  				</select>
  				<br/>
  				<br/>
  				<h3>Starting Bike Station</h3>
  				<select class="btn btn-default btn-lg bike-stations-dropdown" id="start">
  				</select>
  				<br/>
  				<br/>
  				<h3>Ending Bike Station</h3>
  				<select class="btn btn-default btn-lg bike-stations-dropdown" id="end">
  				</select>
  				<br/>
  				<br/>
  				<button type="button" class="btn btn-success btn-toggle" id="Submit" onclick="calcRoute();">Submit</button>
  			</form>

  		</div> <!-- end of bikestationsList -->

  		<div class="col-md-8" id="station-map"></div>

  	</div>

  	<script src="https://code.jquery.com/jquery.js"></script>
  	<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
  	<script type="application/json" src="BayAreaBikeShare.php"></script>
  	<script src="https://maps.googleapis.com/maps/api/js?v=3&sensor=false" type="text/javascript"></script>	
  	<script src="main.js" type="text/javascript" charset="utf-8"></script>

  </body>
  </html>