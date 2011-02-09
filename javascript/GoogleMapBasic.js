/*
THANK YOU Marcel Nogueira d' Eurydice FOR THE INSPIRATION!
TO DECLARE USING Requirements::customScript IN PHP
var GoogleMapBasicInfoWindow = '';
var GoogleMapBasicAddress = '';
*/


/*
SOME IDEAS FOR ADDITIONAL OPTIONS - see google map API for 1000000 more options
	var baseIcon = new GIcon(G_DEFAULT_ICON);
	baseIcon.iconSize = new GSize(60, 46);
	baseIcon.shadowSize = new GSize(60, 46);
	baseIcon.iconAnchor = new GPoint(18, 46);
	baseIcon.infoWindowAnchor = new GPoint(9, 2);
	baseIcon.image = "/themes/main/images/mycompany-icon.png";
	baseIcon.shadow = "/themes/main/images/mycompany-icon-shadow.png";
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
					map.setCenter(point, 15);
					marker = createMarker(point);
					map.addOverlay(marker);
				}
			}
		);
		function createMarker(point) {
			/* ICON ...
			var myIcon = new GIcon(); //can add baseIcon here new GIcon(baseIcon);
			markerOptions = { icon:myIcon};
			var marker = new GMarker(point, markerOptions);
			*/
			/* NO ICON */
      GEvent.addListener(marker, "click", function() {
        marker.openInfoWindowHtml(GoogleMapBasicInfoWindow);
      });
			return marker;
		}
	}
}
jQuery(document).ready(
	function() {
		GoogleMapBasicInit();
	}
);
jQuery(window).unload(function() {GUnload();});
