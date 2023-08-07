/*
THANK YOU Marcel Nogueira d' Eurydice FOR THE INSPIRATION!
*/

window.kickstartGoogleMaps = () => {
  if (typeof window.GoogleMapBasicOptions !== 'undefined') {
    for (let i = 0; i < window.GoogleMapBasicOptions.length; i++) {
      let options = window.GoogleMapBasicOptions[i]
      var map = new GoogleMapBasic(options)
      for (let key in options) {
        if (!options.hasOwnProperty(key)) {
          // The current property is not a direct property of p
          continue
        }
        map.setVar(key, options[key])
        //Do your logic with the property here
      }
      map.init()
    }
  }
}

function GoogleMapBasic (options) {
  var mapObject = {

    // to be provided
    idOfMapDiv: options.idOfMapDiv,

    zoomLevel: options.zoomLevel,

    infoWindowContent: options.infoWindowContent,

    address: options.address,

    lat: options.lat,

    lng: options.lng,

    title: options.title,

    // internal items

    map: null,

    mapOptions: null,

    marker: null,

    infoWindowObject: null,

    location: null,

    init: function () {
      if (mapObject.lat && mapObject.lng) {
        mapObject.location = { lat: mapObject.lat, lng: mapObject.lng }
        mapObject.createMap()
      } else {
        let geocoder = new google.maps.Geocoder()
        geocoder.geocode(
          { address: mapObject.address },
          function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
              mapObject.location = results[0].geometry.location
              // we have to do this now after the address is found!
              mapObject.createMap()
            } else {
              alert('Geocode was not successful for the following reason: ' + status)
            }
          }
        )
      }
    },

    createMap: function () {
      mapObject.mapOptions = {
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        zoom: mapObject.zoomLevel,
        center: mapObject.location,
        scrollwheel: false
      }
      mapObject.map = new google.maps.Map(
        document.getElementById(mapObject.idOfMapDiv),
        mapObject.mapOptions
      )
      mapObject.marker = new google.maps.Marker(
        {
          map: mapObject.map,
          position: mapObject.location,
          title: mapObject.title
        }
      )
      mapObject.infoWindowObject = new google.maps.InfoWindow({ content: mapObject.infoWindowContent })
      google.maps.event.addListener(mapObject.marker, 'click', function () { mapObject.infoWindowObject.open(mapObject.map, mapObject.marker) })
      google.maps.event.trigger(mapObject.marker, 'click')
    }
  }

  // Expose public API
  return {
    getVar: function (variableName) {
      if (mapObject.hasOwnProperty(variableName)) {
        return mapObject[variableName]
      }
    },
    setVar: function (variableName, value) {
      mapObject[variableName] = value
      return this
    },
    init: function () {
      mapObject.init()
      return this
    }
  }
}
