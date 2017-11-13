
<!DOCTYPE html>
<html>
  <head>
    <title>How to show duration trip in google map api</title>
    <meta name="viewport" content="initial-scale=1.0">
    <meta charset="utf-8">
    <style>
      html, body {
        height: 90%;
        margin: 0;
        padding: 0;
      }
      #map {
        height: 100%;
      }
    </style>
  </head>
  <body>
    <div id="map"></div>
    <input id="start" placeholder="start" value="Lausanne Gare">
    <input id="end" placeholder="end" value="Chemin de Bellerive 32, 1007 Lausanne">
    <input type="button" onclick="submit_form()" value="Calculate Route">
    <script>
      var directionsDisplayDriving;
      var directionsDisplayWalking;
      var infowindowDriving;
      var infowindowWalking;
      var directionsService;
      var map;

      // returns a polyline.  Depending on the travelMode
      function getPolylineOptions(travelMode) {
        switch(travelMode) {
          default:
          case 'DRIVING':
            return {
              strokeColor: '#808080',   // grey'ish
              strokeOpacity: 1.0,
              strokeWeight: 3
            };
            break;
          case 'WALKING':
            // Define a symbol using SVG path notation, with an opacity of 1.
            var lineSymbol = {
              path: 'M 0,-1 0,1',
              strokeOpacity: 1,
              scale: 3
            };
            // Create the polyline, passing the symbol in the 'icons' property.
            // Give the line an opacity of 0.
            // Repeat the symbol at intervals of 20 pixels to create the dashed effect.
            return {
              strokeColor: '#0099ff',
              strokeOpacity: 0,
              strokeWeight: 3,
              icons: [{
                icon: lineSymbol,
                offset: '0',
                repeat: '15px'
              }]
            };
            break;
        }
      }

      function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
          center: {lat: 46.5138527, lng: 6.6260286},  // Lausanne
          zoom: 12
        });
        directionsService = new google.maps.DirectionsService();
      }

    // reads the inputs and calculates the route
    function submit_form() {
      // remove previous routes
      if(directionsDisplayDriving) {
        directionsDisplayDriving.setMap(null);
        directionsDisplayDriving = null;
      }
      if(directionsDisplayWalking) {
        directionsDisplayWalking.setMap(null);
        directionsDisplayWalking = null;
      }
      // calculate the route, both Driving and Walking
      calcRoute(
        document.getElementById('start').value,
        document.getElementById('end').value,
        'DRIVING',
        function(display) {
          // we put an infoWindow, 20% along the Driving route, and display the total length and duration in the content.
          directionsDisplayDriving = display;
          var point = distanceAlongPath(display, null, .2);
          var content = 'Driving - total distance: ' + getTotalDistance(display) + 'm <br/> total duration: ' + getTotalDuration(display) +'s';
          if(infowindowDriving) {
            infowindowDriving.setMap(null);
          }
          infowindowDriving = new google.maps.InfoWindow({
            content: content,
            map: map,
            position: point
          });
        }
      );

      calcRoute(
        document.getElementById('start').value,
        document.getElementById('end').value,
        'WALKING',
        function(display) {
          // we put an infoWindow, 40% along the Walking route, and display the total length and duration in the content.
          directionsDisplayWalking = display;
          var point = distanceAlongPath(display, null, .4);
          var content = 'Walking - total distance: ' + getTotalDistance(display) + 'm <br/> total duration: ' + getTotalDuration(display) +'s';
          if(infowindowWalking) {
            infowindowWalking.setMap(null);
          }
          infowindowWalking = new google.maps.InfoWindow({
            content: content,
            map: map,
            position: point
          });
        }
      );

      ////absolute (in meter)
      //var point = distanceAlongPath(directionsDisplay, 100000);
      // as a ratio (0 to 1)  of the route
      //var point = distanceAlongPath(directionsDisplay, null, 0.3);  // at 30% from the origin
    }

    function calcRoute(start, end, travelMode, onReady) {
      // alert(travelMode);
      var mode = google.maps.TravelMode[travelMode];
      var request = {
        origin: start,
        destination: end,
        travelMode: mode
      };
      directionsService.route(request, function (response, status) {
        if (status == google.maps.DirectionsStatus.OK) {
          var polylineOptions = getPolylineOptions(travelMode);
          var directionsDisplay = new google.maps.DirectionsRenderer({
            suppressMarkers: true,
            map: map,
            polylineOptions: polylineOptions,
            preserveViewport: false
          });
          directionsDisplay.setDirections(response);
          if(typeof onReady == 'function') {
            onReady(directionsDisplay);
          }
        }
        else {
          console.log('status: ' + status);
        }
      });
    }

    function getTotalDuration(directionsDisplay) {
      var directionsResult = directionsDisplay.getDirections();
      var route = directionsResult.routes[0];
      var totalDuration = 0;
      var legs = route.legs;
      for(var i=0; i<legs.length; ++i) {
        totalDuration += legs[i].duration.value;
      }
      return totalDuration;
    }

    function getTotalDistance(directionsDisplay) {
      var directionsResult = directionsDisplay.getDirections();
      var route = directionsResult.routes[0];
      var totalDistance = 0;
      var legs = route.legs;
      for(var i=0; i<legs.length; ++i) {
        totalDistance += legs[i].distance.value;
      }
      return totalDistance;
    }
    //  Returns a point along a route; at a requested distance ( either absolute (in meter) or as a ratio (0 to 1)  of the route)
    //     example : you have a random route ( 100km long), and you want to put a marker, 30km from the origin.
    //     we add the distances of the waypoints and stop when we reach the requested total length.
    //     nothing stops you from making it even more precise by interpolling.
    // the function returns a location (LatLng) along the route
    function distanceAlongPath(directionsDisplay, distanceFromOrigin, ratioFromOrigin) {
      var directionsResult = directionsDisplay.getDirections();
      var route = directionsResult.routes[0];
      var totalDistance = getTotalDistance(directionsDisplay);
      var tempDistanceSum = 0;
      var dist = 0;

      if(ratioFromOrigin) {
        distanceFromOrigin = ratioFromOrigin * totalDistance;
      }

      // we prepare the object
      var result = new Object();
      result.routes = new Array();
      result.routes[0] = route;
      for(var i in result.routes[0].overview_path) {
        if (i>0) {
          dist = google.maps.geometry.spherical.computeDistanceBetween (result.routes[0].overview_path[i], result.routes[0].overview_path[i - 1]);
        }
        tempDistanceSum += dist;
        if (tempDistanceSum > distanceFromOrigin) {
          return result.routes[0].overview_path[i];
        }
        // console.log(dist+' '+tempDistanceSum);
      }
    }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDB1yDGD5NadqGwVEkx2-cA8n4Zc99Wrj0&callback=initMap&libraries=geometry" async defer></script>
  </body>
</html>
