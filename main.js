var stationCity;
var dropdownList;
var stationDropdown;
var stationName;
var latitude;
var longitude;

function getBikeData(){
	$.getJSON("station_status.php", jsonDataCallback);

	function jsonDataCallback(json) {
		var currentDate = new Date([json.last_updated.toString()] *1000);
		
		function displayTime(timeInfo){
			var timeParagraph = $('<p>');
			timeParagraph.html('<a href="http://bayareabikeshare.com/stations" target=new>Bay Area Bike Share</a> data current as of:<br />' + currentDate);
			var timeDisplay = $('#time');
			timeDisplay.html(timeParagraph);
		}

		displayTime(currentDate);

		var stationArray = [];

		for (var i = 0; i < json.data.stations.length; i++){
			stationArray.push(json.data[i]);
		}
		console.log(stationArray);

		function addStationToDropdown(stationInfo){
			var stationName = stationInfo.station_id;
			console.log(stationName);
			var availableBikes = stationInfo.availableBikes;
			var availableDocks = stationInfo.availableDocks;
			var totalDocks = stationInfo.totalDocks;
			stationCity = stationInfo.city;
			var latitude = stationInfo.latitude;
			var longitude = stationInfo.longitude;
			stationDropdown = $('<option value="' + latitude + ', ' + longitude + '">');
			stationDropdown.html('<a href="#">' + stationName + '</a>');
			dropdownList = $('.bike-stations-dropdown');
			if (stationCity == "San Francisco"){
				dropdownList.append(stationDropdown);
			}
		}

		for (var i = 0; i < stationArray.length; i++) {
			addStationToDropdown(stationArray[i]);
		}

		// Create a new instance of LatLngBounds to use for re-centering the map after all the stations are loaded
		var bounds = new google.maps.LatLngBounds();

		$.each(json.stations, function(i, station) {

			// Show only In Service Stations
			if (station.statusValue == 'In Service'){
				var icon = new google.maps.MarkerImage(
					'http://bayareabikeshare.com/assets/images/bayarea/icons/stations/map-icons.png',
					new google.maps.Size(42,53),
					new google.maps.Point(0,sprite_offset(station.availableBikes,station.availableDocks)),
					new google.maps.Point(22,53)
					);
			}
			
			// Check for valid Lat (-90 to 90) and long (-180 to 180) and ignore 0,0	
			valid_lat_long = (station.latitude!= 0 &&  station.latitude >= -90 && station.latitude <= 90 &&  
				station.longitude!= 0 &&  station.longitude >= -180 && station.longitude <= 180)

			//omit the stations with bogus lat/long or 0 lat and long 
			if (valid_lat_long    ) {

				// Create a Google LatLng object to pass to the Google Marker
				var point = new google.maps.LatLng(station.latitude, station.longitude);

				// Create the Google Marker Point with the LatLng object
				var marker = new google.maps.Marker({
					position : point,
					map : map,
					icon : icon,
					title : station.stationName
				});

				// Create an Event Listener that pops up the infoWindow when a user clicks a station
				google.maps.event.addListener(marker, 'click', function(event) {
					var elevator = new google.maps.ElevationService();
					var locations = [];
					var clickedLocation = event.latLng;
					locations.push(clickedLocation);
					var positionalRequest = {
						'locations': locations
					}
					var elevationData = 0;
					elevator.getElevationForLocations(positionalRequest, function(results, status) {
						if (status == google.maps.ElevationStatus.OK) {
							if (results[0]) {
								elevationData = Math.round(results[0].elevation * 3.28084);
								popup(elevationData);
							}
							else {
								elevationData = 0;
								popup(elevationData);
							}
						}
						else {
							elevationData = 0;
							popup(elevationData);
						}
					}); // End elevator

					function popup(elevationData){
						contentString='<div class="station-window">' +
						'<h2>' + station.stationName + '</h2>' +
						(station.statusValue == 'Planned' ?	"<i>(planned station)</i>" :
							'<div class="station-data">' +
							'<table id="station-table">' + 
							'<tr><th>Available Bikes:</th><td>' + station.availableBikes + '</td></tr>' +
							'<tr><th>Available Docks:</th><td>' + station.availableDocks + ' out of ' + station.totalDocks + '</td></tr>' +
							'<tr><th>Elevation:</th><td> ' + elevationData + ' feet</td></tr>' +
							'</table>'	+
							'<a onclick="setStartingBikeStation(' + station.latitude + ',' + station.longitude + ')" id="startingbikestation">Set as starting bike station</a>' +
							'<p>' +
							'<a onclick="setEndingBikeStation(' + station.latitude + ',' + station.longitude + ')" id="endingbikestation">Set as ending bike station</a>' +
							'<button type="button" class="btn btn-link" class="Submit" onclick="calcRoute();">Get Directions</button>' +
							'</div>'
							) +
						'</div>';
						var div = document.createElement('div');
						div.innerHTML = contentString;
						station_infowindow.setContent(div);
						station_infowindow.open(map, marker);
						google.maps.event.addListener(station_infowindow, 'domready', function() {
							$('.temp-padding').css('padding-right', '0');
							var table_height = $('#station-table').height();
							var table_margin = ($('.sponsor-img').attr("height") - table_height) / 2;
							table_margin = Math.max(table_margin, 0);
							$('.station-data-w-table').css('margin-top', table_margin);
							var img_margin = (table_height - $('.sponsor-img').attr("height")) / 2;
							img_margin = Math.max(img_margin, 0);
							$('.sponsor-img').css('margin-top', img_margin);
						});
					}

	            }); // End InfoWindow event listener
bounds.extend(point);
			} //end of hard-coded station omission
		}); // End of $.each() json station

		// Reset the center of the map to the station coordinates and zoom to the bounds
		if (!use_preset_zoom_center) 
			map.setCenter(bounds.getCenter(), map.fitBounds(bounds));

	} //end jsonDataCallback
} // end get()

getBikeData();

setInterval(function(){
	getBikeData();
}, 60000);

//change list of bike stations in dropdown, by city
function setCity(chosen){
	console.log(chosen);
	$.getJSON("BayAreaBikeShare.php", function(changeCities) {
		var newStationArray = [];

		for (var i = 0; i < changeCities.stations.length; i++){
			newStationArray.push(changeCities.stations[i]);
		}

		function changeStationDropdown(stationInfo){
			stationName = stationInfo.stationName;
			stationCity = stationInfo.city;
			latitude = stationInfo.latitude;
			longitude = stationInfo.longitude;
			stationDropdown = $('<option value="' + latitude + ', ' + longitude + '">');
			stationDropdown.html('<a href="#">' + stationName + '</a>');
			dropdownList = $('.bike-stations-dropdown');
			if (stationCity == chosen){
				dropdownList.append(stationDropdown);
			}

		}
		dropdownList.empty();
		for (var i = 0; i < newStationArray.length; i++) {
			changeStationDropdown(newStationArray[i]);
		}
		changeMapLocation(chosen);

	});
}

function setStartingBikeStation(latitude, longitude) {
	document.getElementById("start").value = latitude + ', ' + longitude;
	$( "#startingbikestation" ).replaceWith("This is your starting bike station");
}
function setEndingBikeStation(latitude, longitude) {
	document.getElementById("end").value = latitude + ', ' + longitude;
	$( "#endingbikestation" ).replaceWith("This is your ending bike station");
}



//Map starts here
var use_preset_zoom_center = true;
var center = new google.maps.LatLng(37.790,-122.4037);


// Set Map Options
var mapOptions = {
	mapTypeId: google.maps.MapTypeId.TERRAIN,
	mapTypeControlOptions: { style: google.maps.MapTypeControlStyle.DROPDOWN_MENU },
	zoom: 14,
	center: center
};

// Create the Google Map
var map = new google.maps.Map(document.getElementById("station-map"), mapOptions);

// Add the Bicycling Layer
var bikeLayer = new google.maps.BicyclingLayer();
bikeLayer.setMap(map);

// Create the Popup Window when a user clicks on a station
var station_infowindow = new google.maps.InfoWindow({
});

 // Pans the map to the specified lat/long
 function changeMapLocation(chosen){
 	if (chosen == "San Francisco"){
 		panToLocation(37.790,-122.4037)
 	}
 	else if (chosen == "Redwood City"){
 		panToLocation(37.4885,-122.2290);
 	}
 	else if (chosen == "Palo Alto"){
 		panToLocation(37.4390,-122.1525);
 	}
 	else if (chosen == "Mountain View"){
 		panToLocation(37.3975,-122.0875);
 	}
 	else if (chosen == "San Jose"){
 		panToLocation(37.3420,-121.8925);
 	}
 }

 function panToLocation(lat, lng)
 {
// Create a Lat/Long Object
var latLng = new google.maps.LatLng(lat, lng);		
// Pan To the Lat/Long 
map.panTo(latLng);
map.setZoom(14);
}

/* sprite_offset
 * This function helps display different pins based on station bike/dock availability
 * 0 is 0% shaded (empty), 1 is 25% shaded, 2 is 50% shaded, 3 is 75% shaded, 4 is 100% shaded (full), 5 is all grey "not in service"
 * 
 * @param bikes
 * @param docks
 * 
 */
 function sprite_offset(bikes,docks) {
 	var index_offset=11;  

// Only if the station is not reporting 0 bikes and 0 docks
if (!(bikes==0 && docks==0)) 
{
	var percent=Math.round(bikes/(bikes+docks)*100);

	// Use the empty icon only for empty stations, ditto for full. Anything in-between, show different icon
	if (percent==0)
		index_offset=0;
	else if (percent>0 && percent<=20)
		index_offset=2;
	else if (percent>20 && percent<=30)
		index_offset=3;
	else if (percent>30 && percent<=40)
		index_offset=4;
	else if (percent>40 && percent<=50)
		index_offset=5;
	else if (percent>50 && percent<=60)
		index_offset=6;
	else if (percent>60 && percent<=70)
		index_offset=7;
	else if (percent>70 && percent<=80)
		index_offset=8;
	else if (percent>80 && percent<100)
		index_offset=9;
	else if (percent==100)
		index_offset=10;
}

var offset=index_offset*(53+50); //53 the height of the pin portion of the image, 50 the whitespace b/t the pin portions
return offset;
}
/**
* Make the map container sit in the remaining browser space*/

function resizeMapToFit() {

	var $window = $(window);
	var elementHeight = 0;

	elementHeight += $('#header:visible, #mobile-nav:visible').height();
	elementHeight +=  $('#legend').height() + 50;
	elementHeight +=  $('#cities').height();
	elementHeight +=  $('#footer').height();

	$('#station-map').width($window.width()).height($window.height() - elementHeight);

}

//get and render station directions
var directionsDisplay;
directionsDisplay = new google.maps.DirectionsRenderer();
directionsDisplay.setMap(map);
directionsDisplay.setPanel(document.getElementById('directions-panel'));

function calcRoute() {
	var routeStart = start.value;
	var routeEnd = end.value;
	var request = {
		origin: routeStart,
		destination: routeEnd,
		travelMode: google.maps.TravelMode.BICYCLING
	};
	var directionsService = new google.maps.DirectionsService();
	directionsService.route(request, function(response, status) {
		if (status == google.maps.DirectionsStatus.OK) {
			directionsDisplay.setDirections(response);
		}
	});
} //end calcRoute