/*
THANK YOU Marcel Nogueira d' Eurydice FOR THE INSPIRATION!
TO DECLARE USING Requirements::customScript IN PHP
*/

jQuery(document).ready(
	function () {
		GoogleMapBasic.init();
	}
);
jQuery(window).unload(function() {GUnload();});

var GoogleMapBasic = {

	zoomLevel: 14,
		SET_ZoomLevel: function(v) {this.zoomLevel = v;},

	infoWindow: "I live here",
		SET_InfoWindow: function(v) {this.infoWindow = v;},

	address: "The Beehive, Wellington, New Zealand",
		SET_Address: function(v) {this.address = v;},

	map: null,

	init: function() {
		if (GBrowserIsCompatible()) {
			GoogleMapBasic.map = new GMap2(document.getElementById("GoogleMapBasic"));
			GoogleMapBasic.map.setUIToDefault();
			//get address
			geocoder = new GClientGeocoder();
			geocoder.getLatLng(
				GoogleMapBasic.address,
				function(point){
					if(!point){
						alert("address "+GoogleMapBasic.address+" not found!!!");
					}
					else {
						GoogleMapBasic.map.setCenter(point, GoogleMapBasic.zoomLevel);
						GoogleMapBasic.createMarker(point, GoogleMapBasic.infoWindow);
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
		marker.openInfoWindowHtml(html);
		return marker;
	}
}

