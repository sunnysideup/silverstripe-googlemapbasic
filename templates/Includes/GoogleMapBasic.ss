<% if GoogleMapBasic %>
	<div id="GoogleMapBasic">
	<% if StaticMap %>
		<a href="$GoogleMapBasicExternalLink">
			<img src="$GoogleMapBasicStaticMapSource(300,400)" alt="$Address.ATT" width="300" height="400" />
		</a>
		<div class="staticInfoWindowContent">$InfoWindowContent</div>
		<% else %>
			$GoogleMapBasic
		<% end_if %>
	</div>
<% end_if %>


