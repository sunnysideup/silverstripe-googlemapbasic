/*
THANK YOU Marcel Nogueira d' Eurydice FOR THE INSPIRATION!
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

	getAddress: function(){
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

	},

	createMap: function ():
			var myOptions = {
				zoom: 8,
				center: new google.maps.LatLng(-34.397, 150.644),
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			this.map = new google.maps.Map(document.getElementById('map_canvas'), myOptions);
		}
		google.maps.event.addDomListener(window, 'load', initialize);
	},


}

