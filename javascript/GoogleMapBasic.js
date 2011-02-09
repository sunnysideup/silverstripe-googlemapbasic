/*
THANK YOU Marcel Nogueira d' Eurydice FOR THE INSPIRATION!
TO DECLARE USING Requirements::customScript IN PHP
var GoogleMapBasicInfoWindow = '';
var GoogleMapBasicAddress = '';
*/


function GoogleMapBasicInit() {
	if (GBrowserIsCompatible()) {
		var map = new GMap2(document.getElementById("GoogleMapBasic"));
		map.setUIToDefault();
		//get address
		geocoder = new GClientGeocoder();
		geocoder.getLatLng(
			GoogleMapBasicAddress,
			function(point){
				if(!point){
					alert("address not found!!!");
				}
				else {
					alert("adding point");
					map.setCenter(point, 15);
					var marker = createMarker(point, "hello");
					map.addOverlay(marker);
				}
			}
		);
	}
}
jQuery(document).ready(
	function() {
		GoogleMapBasicInit();
	}
);
jQuery(window).unload(function() {GUnload();});
