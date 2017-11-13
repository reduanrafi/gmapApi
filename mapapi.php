<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Map API Testing</title>
    <style media="screen">
      #map{
        height: 400px;
        width: 100%;
      }
    </style>
  </head>
  <body>
  <div id="map"></div>
    <script>
    var lanlat = {lat:23.8759,lng:90.3795};
        function initMap(){
          var option = {
            center:lanlat,
            zoom:10
          }
          var map = new google.maps.Map(document.getElementById('map'),option);
          // var marker = new google.maps.Marker({
          //   position:lanlat,
          //   map:map,
          //   icon:'map.jpg'
          // });
          addMarker({coord:lanlat});
          addMarker({
            coord:{lat:23.8859,lng:90.3795},
            iconImage:'map.jpg'
          });
          function addMarker(props){
            var marker= new google.maps.Marker({

              position:props.coord,
              map:map,

            });
            if (props.iconImage) {
              marker.setIcon(props.iconImage)
            }
            // else {
            //   marker.setIcon('r.jpeg')
            // }
//var distance = google.maps.geometry.spherical.computeDistanceBetween(new google.maps.LatLng(latitude1, longitude1), new google.maps.LatLng(latitude2, longitude2));       


          }
        }


    </script>
  <script async defer
  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDB1yDGD5NadqGwVEkx2-cA8n4Zc99Wrj0&callback=initMap">
  </script>
  </body>
</html>
