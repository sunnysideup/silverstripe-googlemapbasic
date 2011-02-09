/*
THANK YOU Marcel Nogueira d' Eurydice FOR THE INSPIRATION!
TO DECLARE USING Requirements::customScript IN PHP
var GoogleMapBasicInfoWindow = '';
var GoogleMapBasicAddress = '';
*/

jQuery(document).ready(
	function () {
		GoogleMapBasic.init();
	}
);
jQuery(window).unload(function() {GUnload();});

var GoogleMapBasic = {

	point: null,

	map: null,

	init: function() {
		if (GBrowserIsCompatible()) {
			GoogleMapBasic.map = new GMap2(document.getElementById("GoogleMapBasic"));
			GoogleMapBasic.map.setUIToDefault();
			//get address
			geocoder = new GClientGeocoder();
			geocoder.getLatLng(
				GoogleMapBasicAddress,
				function(point){
					if(!point){
						alert("address not found!!!");
					}
					else {
						GoogleMapBasic.map.setCenter(point, 15);
						GoogleMapBasic.createMarker(point, GoogleMapBasicInfoWindow);
					}
				}
			);
		}
	},

	createMarker: function (point,html) {
		var marker = new GMarker(point);
		GEvent.addListener(marker, "click", function() {
			marker.openInfoWindowHtml(html);
		});
		GoogleMapBasic.map.addOverlay(marker);
		return marker;
	}
}

