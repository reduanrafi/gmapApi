 <script>
        // Note: This example requires that you consent to location sharing when
        // prompted by your browser. If you see the error "The Geolocation service
        // failed.", it means you probably did not give permission for the browser to
        // locate you.
        var map, infoWindow;
        function initMap() {

            map = new google.maps.Map(document.getElementById('map'), {

                center:{lat:23.8103,lng:90.4125},
                zoom: 14
            });
            infoWindow = new google.maps.InfoWindow;

            // Try HTML5 geolocation.
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    var marker1 = new google.maps.Marker({
                        position:pos,
                        map:map,
                        icon:'{{asset('assets/images/user.png')}}'
                    });
                    infoWindow.setPosition(pos);
                    infoWindow.setContent('Your current position.');
                    infoWindow.open(map);
                    map.setCenter(pos);
                }, function() {
                    handleLocationError(true, infoWindow, map.getCenter());
                });
            } else {
                // Browser doesn't support Geolocation
                handleLocationError(false, infoWindow, map.getCenter());
            }
            var marker = new google.maps.Marker({
                position:{lat: parseFloat("{{$driver->lat}}"), lng: parseFloat("{{$driver->lng}}")},
                map:map,
                icon:'{{ asset('assets/images/car.png') }}'
            });
            var infowindow1 = new google.maps.InfoWindow({
                content: 'Your care'
            });

            marker.addListener('click',function () {
                infowindow1.open(map,marker);
            })

            setInterval(function() {
                $.ajax({
                    method:'get',
                    dataType:'html',
                    url: "{{route('getArea')}}",
                    success: function(result,status) {
                        var x = JSON.parse(result)
                       var location = new google.maps.LatLng(x.data.lat,x.data.lng);
                        marker.setPosition(location);
                        map.panTo(location);
                    }
                });
            },60000); // 30 seconds
            //car marker

            //user marker
            {{--var marker = new google.maps.Marker({--}}
                {{--position:{lat: parseFloat("{{$driver->lat}}"), lng: parseFloat("{{$driver->lng}}")},--}}
{{--//                position:{lat:x.data.lat,lng:x.data.lng},--}}
                {{--map:map,--}}
                {{--icon:'{{ asset('assets/images/car.png') }}'--}}
            {{--});--}}
            {{--var infowindow1 = new google.maps.InfoWindow({--}}
                {{--content: 'Your care'--}}
            {{--});--}}

            {{--marker.addListener('click',function () {--}}
                {{--infowindow1.open(map,marker);--}}
            {{--})--}}

        }



        function handleLocationError(browserHasGeolocation, infoWindow, pos) {
            infoWindow.setPosition(pos);
            infoWindow.setContent(browserHasGeolocation ?
                'Error: The Geolocation service failed.' :
                'Error: Your browser does not support geolocation.');
            infoWindow.open(map);
        }
    </script>
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyClcuDcHYH18IyQnuUlC0K2xxCTM50Jqu4&callback=initMap">
    </script>
