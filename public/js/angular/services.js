app.service('Airports', ['$http', function($http) {
    return function(q) {
        return $http.get(baseUrl + 'airports', {
            params: {
                'q': q
            }
        })
        .success(function(data, status) {
            if (status == 200) {
                return data;
            }
        })
        .error(function(error) {
            return error;
        });
    }
}]);

app.service('Flights', ['$http', function($http) {
    return function(data) {

        var departTime = (data.departTime.getHours() < 10 ? '0' + data.departTime.getHours() : data.departTime.getHours()) + ':' +
                          (data.departTime.getMinutes() < 10 ? '0' + data.departTime.getMinutes() : data.departTime.getMinutes());
        var returnTime = (data.returnTime.getHours() < 10 ? '0' + data.returnTime.getHours() : data.returnTime.getHours()) + ':' +
                          (data.returnTime.getMinutes() < 10 ? '0' + data.returnTime.getMinutes() : data.returnTime.getMinutes());
        var departDate = (data.departDate.getDate() < 10 ? '0' + data.departDate.getDate() : data.departDate.getDate()) + '-' +
                          (data.departDate.getMonth()+1 < 10 ? '0' + data.departDate.getMonth()+1 : data.departDate.getMonth()+1) + '-' +
                          data.departDate.getFullYear();
        var returnDate = data.type !== 'OW' ? (data.returnDate.getDate() < 10 ? '0' + data.returnDate.getDate() : data.returnDate.getDate()) + '-' +
                          (data.returnDate.getMonth()+1 < 10 ? '0' + data.returnDate.getMonth()+1 : data.returnDate.getMonth()+1) + '-' +
                          data.returnDate.getFullYear() : undefined;

        return $http.get(baseUrl + 'flights', {
            params: {
                'origin' : data.origin,
                'destination' : data.destination,
                'priority' : data.priority,
                'type' : data.type,
                'adults' : data.adults,
                'children' : data.children,
                'infants' : data.infants,
                'departDate' : departDate,
                'departTime' : departTime,
                'returnDate' : returnDate,
                'returnTime' : returnTime,
                'airportCoordinates' : data.airportCoordinates
            }
        })
        .success(function(data, status) {
            if (status == 200) {
                return data;
            }
        })
        .error(function(error) {
            return error;
        });
    }
}]);

app.service('Hotels', ['$http', function($http) {
    return function(data) {

        var checkIn  = (data.checkIn.getDate() < 10 ? '0' + data.checkIn.getDate() : data.checkIn.getDate()) + '-' +
                        (data.checkIn.getMonth()+1 < 10 ? '0' + data.checkIn.getMonth()+1 : data.checkIn.getMonth()+1) + '-' +
                        data.checkIn.getFullYear();
        var checkOut =  (data.checkOut.getDate() < 10 ? '0' + data.checkOut.getDate() : data.checkOut.getDate()) + '-' +
                        (data.checkOut.getMonth()+1 < 10 ? '0' + data.checkOut.getMonth()+1 : data.checkOut.getMonth()+1) + '-' +
                        data.checkOut.getFullYear();

        return $http.get(baseUrl + 'hotels', {
            params: {
                'keyword' : data.keyword === undefined ? '' : data.keyword,
                'airport' : data.airport === undefined ? '' : data.airport,
                'airportCoordinates' : data.airportCoordinates,
                'rooms' : data.rooms,
                'priority' : data.priority,
                'rating' : data.rating,
                'facilities' : data.facilities,
                'adults' : data.adults,
                'children' : data.children,
                'checkIn' : checkIn,
                'checkOut' : checkOut
            }
        })
        .success(function(data, status) {
            if (status == 200) {
                return data;
            }
        })
        .error(function(error) {
            return error;
        });
    }
}]);

app.service('Venues', ['$http', function($http) {
    return function(coordinates) {
        return $http.get(baseUrl + 'venues', {
            params: {
                'coordinates': coordinates
            }
        })
        .success(function(data, status) {
            if (status == 200) {
                return data;
            }
        })
        .error(function(error) {
            return error;
        });
    }
}]);

app.service('FormValidation', ['SweetAlert', function(SweetAlert) {
    return function(flight, hotel) {

        var message = null;

        // validation
        if (flight.origin === undefined ||
            flight.destination === undefined ||
            flight.departDate === undefined ||
            hotel.checkIn === undefined ||
            hotel.checkOut === undefined ||
            hotel.rating === undefined ||
            hotel.rooms === undefined ||
            (flight.type === 'RT' && flight.returnDate === undefined)) {

            message = "The parameters required are not complete";
        }

        if ((flight.adults + flight.children) > 4) {
            message = "Maximum number of passengers (4) is reached";
        }

        if (flight.infants > flight.adults) {
            message = "The number of infants cannot exceeds the number of adults";
        }

        if (hotel.checkIn < flight.departDate) {
            message = "The check in date cannot precedes depart date";
        }

        if (flight.type == "RT") {
            if (hotel.checkOut < flight.returnDate) {
                message = "The check in date cannot precedes depart date";
            }
        }

        if (message !== null) {
            SweetAlert.swal("Whoops", message, "error");
            throw new Error(message);
        }
    }
}]);