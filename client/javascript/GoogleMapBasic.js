window.kickstartGoogleMaps = () => {
  if (typeof window.GoogleMapBasicOptions === 'undefined') {
    return
  }
  for (let i = 0; i < window.GoogleMapBasicOptions.length; i++) {
    let options = window.GoogleMapBasicOptions[i]
    var map = new GoogleMapBasic(options)
    for (let key in options) {
      if (!options.hasOwnProperty(key)) {
        continue
      }
      map.setVar(key, options[key])
    }
    map.init()
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
      // Note: check against null/undefined, not truthiness, so lat/lng of 0 still work.
      let hasCoords =
        mapObject.lat !== null && mapObject.lat !== undefined && mapObject.lat !== 0 &&
        mapObject.lng !== null && mapObject.lng !== undefined && mapObject.lng !== 0

      if (hasCoords) {
        mapObject.location = { lat: mapObject.lat, lng: mapObject.lng }
        mapObject.createMap()
      } else {
        let geocoder = new google.maps.Geocoder()
        geocoder.geocode(
          { address: mapObject.address },
          function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
              mapObject.location = results[0].geometry.location
              mapObject.createMap()
            } else {
              alert(
                'Geocode was not successful for the following reason: ' + status
              )
            }
          }
        )
      }
    },

    createMap: function () {
      // The div may not be in the DOM yet on a warm-cached page load, where
      // Google's callback can fire before the parser reaches the map element.
      // Wait for it instead of throwing "Expected mapDiv ... but was passed null".
      mapObject.waitForElement(mapObject.idOfMapDiv, function (el) {
        mapObject.buildMap(el)
      })
    },

    buildMap: function (el) {
      mapObject.mapOptions = {
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        zoom: mapObject.zoomLevel,
        center: mapObject.location,
        scrollwheel: false
      }
      mapObject.map = new google.maps.Map(el, mapObject.mapOptions)
      mapObject.marker = new google.maps.Marker({
        map: mapObject.map,
        position: mapObject.location,
        title: mapObject.title
      })
      mapObject.infoWindowObject = new google.maps.InfoWindow({
        content: mapObject.infoWindowContent
      })
      google.maps.event.addListener(mapObject.marker, 'click', function () {
        mapObject.infoWindowObject.open(mapObject.map, mapObject.marker)
      })
      google.maps.event.trigger(mapObject.marker, 'click')
    },

    waitForElement: function (id, callback) {
      let el = document.getElementById(id)
      if (el) {
        callback(el)
        return
      }

      let observer = new MutationObserver(function () {
        let found = document.getElementById(id)
        if (found) {
          observer.disconnect()
          clearTimeout(timeout)
          callback(found)
        }
      })
      observer.observe(document.documentElement, {
        childList: true,
        subtree: true
      })

      // Safety net so we never observe forever.
      let timeout = setTimeout(function () {
        observer.disconnect()
        console.error(
          'GoogleMapBasic: element "' + id + '" never appeared in the DOM'
        )
      }, 10000)
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
