app.directive('flightWidget', function() {
    return {
        restrict: 'E',
        replace: true,
        scope: {
            'show' : '=',
            'done' : '=',
            'data' : '='
        },
        templateUrl: 'js/angular/templates/flight-widget.html',
        link: function(scope, elem, attr) {
            scope.allFlights = false;
            scope.showAllFlights = function() {
                scope.allFlights = scope.allFlights === false ? true : false;
            }
        }
    }
});

app.directive('hotelWidget', function() {
    return {
        restrict: 'E',
        replace: true,
        scope: {
            'show' : '=',
            'done' : '=',
            'data' : '='
        },
        templateUrl: 'js/angular/templates/hotel-widget.html',
        link: function(scope, elem, attr) {
            scope.allHotels = false;
            scope.showAllHotels = function() {
                scope.allHotels = scope.allHotels === false ? true : false;
            }
        }
    }
});

app.directive('routeWidget', ['uiGmapGoogleMapApi', 'uiGmapIsReady', function(uiGmapGoogleMapApi, uiGmapIsReady) {
    return {
        restrict: 'E',
        replace: true,
        scope: {
            'show' : '=',
            'done' : '=',
            'data' : '=',
        },
        templateUrl: 'js/angular/templates/route-widget.html',
        link: function(scope, el, attr) {
            scope.$watch('show', function() {
                if (scope.show === true) {
                    uiGmapGoogleMapApi.then(function(maps) {
                        scope.done = true;
                        var airportCoordinates = scope.data.airportCoordinates;
                        var hotelCoordinates   = scope.data.hotelCoordinates;
                        var center = hotelCoordinates.split(',')

                        scope.map = {
                            center: { latitude: center[0], longitude: center[1] },
                            zoom: 14,
                            control: {}
                        };

                        var directionsDisplay = new maps.DirectionsRenderer();

                        uiGmapIsReady.promise().then(function() {
                            directionsDisplay.setMap(scope.map.control.getGMap());
                            // directionsDisplay.setPanel(document.getElementById('directions'));
                        })

                        var directionsService = new maps.DirectionsService();
                        var request = {
                            origin: airportCoordinates,
                            destination: hotelCoordinates,
                            travelMode: maps.TravelMode.DRIVING
                          };
                          directionsService.route(request, function(response, status) {
                            if (status == maps.DirectionsStatus.OK) {
                              directionsDisplay.setDirections(response);
                            }
                          });
                    });
                }
            });
        }
    }
}]);

app.directive('venueWidget', function() {
    return {
        restrict: 'E',
        replace: true,
        scope: {
            'show' : '=',
            'done' : '=',
            'data' : '=',
        },
        templateUrl: 'js/angular/templates/venue-widget.html'
    }
});

app.directive('flightView', function() {
    return {
        restrict: 'E',
        replace: true,
        scope: {
            airlineName: '@',
            flightInfo: '=',
            facilities: '=',
            price: '='
        },
        templateUrl: 'js/angular/templates/flight-view.html',
        link: function(scope, el, attr) {
            scope.$watch('airlineName', function() {
                if (scope.airlineName !== '') {
                    scope.airlineImage = 'img/airline/' + scope.airlineName.toLowerCase() +'.png'
                }
            });
        }
    }
});

app.directive('hotelView', function() {
    return {
        restrict: 'E',
        replace: true,
        scope: {
            data: '='
        },
        templateUrl: 'js/angular/templates/hotel-view.html',
        link: function(scope, el, attr) {
            scope.$watch('data', function() {
                if (scope.data !== undefined) {
                    var stars = [];
                    for (var i = 1; i <= scope.data.stars; i++) {
                        stars.push(i);
                    }
                    scope.stars = stars;
                }
            });
        }
    }
});