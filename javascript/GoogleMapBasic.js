/*
THANK YOU Marcel Nogueira d' Eurydice FOR THE INSPIRATION!
*/

jQuery(document).ready(
	function () {
		GoogleMapBasic.init();
	}
);

var GoogleMapBasic = {

	zoomLevel: 14,
		SET_zoomLevel: function(v) {GoogleMapBasic.zoomLevel = v;},

	infoWindowContent: "I live here",
		SET_infoWindowContent: function(v) {GoogleMapBasic.infoWindowContent = v;},

	address: "The Beehive, Wellington, New Zealand",
		SET_address: function(v) {GoogleMapBasic.address = v;},

	title: "Click me",
		SET_title: function(v) {GoogleMapBasic.title = v;},

	map: null,
	options: null,
	marker: null,
	marker: null,
	infoWindowObject: null,
	options: null,
	location: null,

	init: function() {
		var geocoder;
		var results;
		geocoder = new google.maps.Geocoder();
		geocoder.geocode(
			{'address': GoogleMapBasic.address},
			function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					GoogleMapBasic.location = results[0].geometry.location
					//we have to do this now after the address is found!
					GoogleMapBasic.createMap();
				}
				else {
					alert("Geocode was not successful for the following reason: " + status);
				}
			}
		);
	},
	createMap: function(){
		GoogleMapBasic.options = {
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			zoom: GoogleMapBasic.zoomLevel,
			center: GoogleMapBasic.location
		};
		GoogleMapBasic.map = new google.maps.Map(document.getElementById('GoogleMapBasic'), GoogleMapBasic.options);
		GoogleMapBasic.marker = new google.maps.Marker({
			map: GoogleMapBasic.map,
			position: GoogleMapBasic.location,
			title: GoogleMapBasic.title
		});
		GoogleMapBasic.infoWindowObject = new google.maps.InfoWindow({content: GoogleMapBasic.infoWindowContent});
		google.maps.event.addListener(GoogleMapBasic.marker, 'click', function() {GoogleMapBasic.infoWindowObject.open(GoogleMapBasic.map,GoogleMapBasic.marker);});
	}




}

