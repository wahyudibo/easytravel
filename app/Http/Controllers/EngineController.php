<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\Tiketdotcom;
use App\Http\Controllers\Services\Google;
use App\Http\Controllers\Services\Foursquare;
use App\Http\Models\Airports;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;

class EngineController extends Controller
{

    public function airports(Request $request)
    {

        $airports = Airports::indonesia()->select('name', 'city', 'iata', 'latitude', 'longitude')->query($request->input('q'))->get();

        echo $airports;
    }

    public function flights(Request $request)
    {
        $params = $request->all();
        preg_match('/\(([^)]+)\)/', $params['origin'], $origin);
        preg_match('/\(([^)]+)\)/', $params['destination'], $destination);
        $origin_iata = $origin[1];
        $destination_iata = $destination[1];
        $depart_date = date('Y-m-d', strtotime($params['departDate']));
        if ($params['type'] == 'RT') {
            $post_data['ret_date'] = date('Y-m-d', strtotime($params['returnDate']));
        }

        $post_data['d']      = $origin_iata;
        $post_data['a']      = $destination_iata;
        $post_data['date']   = $depart_date;
        $post_data['adult']  = $params['adults'];
        $post_data['child']  = $params['children'];
        $post_data['infant'] = $params['infants'];

        if ($params['priority'] == 'time') {
            $data['sort']   = 'departureasc';
        } else if ($params['priority'] == 'price') {
            $data['sort']   = 'priceasc';
        }

        $services = new Tiketdotcom();
        // if result not found, repeat searching for 60 seconds
        $start_mark = Carbon::now();
        while (Carbon::now()->diffInSeconds($start_mark) < 60){
            $flights = $services->flights($post_data);
            $flights = json_decode($flights, true);

            if (isset($flights['departures'])) {
                break;
            }
        }

        $response = [
            'request'  => $params,
            'response' => []
        ];

        if (!empty($flights['departures']['result'])) {
            $time = '';
            if ($params['priority'] == 'time') {
                $time = $params['departTime'];
            }

            $parsedFlight = $this->parseFlight($flights['departures']['result'], $time);
            $result['all_flights']['departure'] = $parsedFlight['result'];
            $result['rec_flight']['departure'] = $result['all_flights']['departure'][$parsedFlight['selected_key']];

            $result['all_flights']['return'] = $result['rec_flight']['return'] = [];
            if ($params['type'] == 'RT') {
                if (!empty($flights['returns']['result'])) {
                    $time = '';
                    if ($params['priority'] == 'time') {
                        $time = $params['returnTime'];
                    }

                    $parsedFlight = $this->parseFlight($flights['returns']['result'], $time);
                    $result['all_flights']['return'] = $parsedFlight['result'];
                    $result['rec_flight']['return'] = $result['all_flights']['return'][$parsedFlight['selected_key']];
                }
            }
            $response['response'] = $result;
        }

        return response()->json($response);
    }

    public function hotels(Request $request)
    {
        $params = $request->all();
        $check_in  = date('Y-m-d', strtotime($params['checkIn']));
        $check_out = date('Y-m-d', strtotime($params['checkOut']));
        $night = Carbon::parse($check_in)->diffInDays(Carbon::parse($check_out)) + 1;
        if ($params['keyword'] == '') {
            $params['keyword'] = trim(substr($params['airport'], strpos($params['airport'], ',')+1, strpos($params['airport'], '(') - (strpos($params['airport'], ',')+1)));
        }

        $post_data['q']         = $params['keyword'];
        $post_data['startdate'] = $check_in;
        $post_data['enddate']   = $check_out;
        $post_data['night']     = $night;
        $post_data['room']      = $params['rooms'];
        $post_data['adult']     = $params['adults'];
        $post_data['child']     = $params['children'];
        $post_data['minstar']   = $params['rating'];

        $coordinates = explode(',', $params['airportCoordinates']);
        if ($params['priority'] == 'near') {
            if ($params['keyword'] != '') {
                $services = new Google;
                $geocodes = $services->geocodes(urlencode($params['keyword']));
                $geocodes = json_decode($geocodes, true);
                if ($geocodes['status'] == 'OK') {
                    $location = $geocodes['results'][0]['geometry']['location'];
                    $post_data['latitude']  = $location['lat'];
                    $post_data['longitude'] = $location['lng'];
                }
            } else {
                // when no keyword is provided, get hotel which nearest to the airport
                $post_data['latitude']  = $coordinates[0];
                $post_data['longitude'] = $coordinates[1];
            }
        } else if ($params['priority'] == 'price') {
            $post_data['sort'] = 'priceasc';
        }

        $services = new Tiketdotcom();
        // if result not found, repeat searching for 60 seconds
        $start_mark = Carbon::now();
        while (Carbon::now()->diffInSeconds($start_mark) < 60){
            $hotels = $services->hotels($post_data);
            $hotels = json_decode($hotels, true);

            if (isset($hotels['results']['result'])) {
                break;
            }
        }

        $facilities = json_decode($params['facilities'], true);
        $params['facilities'] = $facilities;

        $response = [
            'request'  => $params,
            'response' => []
        ];

        if (isset($hotels['results']['result'])) {
            $parsedHotel = $this->parseHotel($hotels['results']['result'], $facilities, $coordinates);
            $result['all_hotels'] = $parsedHotel['result'];
            $result['rec_hotel']  = $result['all_hotels'][$parsedHotel['selected_key']];
            $response['response'] = $result;
        }

        return response()->json($response);
    }

    public function venues(Request $request)
    {
        $params = $request->all();
        $services = new Foursquare;
        $venues = $services->exploreVenues($params['coordinates']);
        $venues = json_decode($venues, true);

        $response = [
            'request'  => $params,
            'response' => []
        ];

        if (!empty($venues['response']['groups'][0]['items'])) {
            foreach ($venues['response']['groups'][0]['items'] as $key => $value) {
                $result['venues'][$key]['name']      = str_replace('"', '', $value['venue']['name']);
                $result['venues'][$key]['address']   = $value['venue']['location']['formattedAddress'][0];
                $result['venues'][$key]['distance']  = $value['venue']['location']['distance'];
                $result['venues'][$key]['latitude']  = $value['venue']['location']['lat'];
                $result['venues'][$key]['longitude'] = $value['venue']['location']['lng'];
                $result['venues'][$key]['category']  = $value['venue']['categories'][0]['name'];
                $result['venues'][$key]['icon']      = $value['venue']['categories'][0]['icon']['prefix'] . '44' .
                                                       $value['venue']['categories'][0]['icon']['suffix'];
            }

            $response['response'] = $result;
        }

        return response()->json($response);
    }

    private function parseFlight($flights, $time = '')
    {
        $result = [];
        $minDiffInMinutes = '';
        $time_user_exp = explode(':', $time);
        foreach ($flights as $key => $value) {
            if ($time != '') {
                $time_row_exp = explode(':', $value['simple_departure_time']);

                $depart_time_row  = Carbon::createFromTime($time_row_exp[0], $time_row_exp[1], 0);
                $depart_time_user = Carbon::createFromTime($time_user_exp[0], $time_user_exp[1], 0);

                $diffInMinutes = $depart_time_row->diffInMinutes($depart_time_user);

                if ($minDiffInMinutes == '' || $minDiffInMinutes > $diffInMinutes) {
                    $minDiffInMinutes = $diffInMinutes;
                    $selected_key = $key;
                }
            } else {
                $selected_key = 0;
            }

            $result[$key]['airline_name']  = $value['airlines_name'];
            $result[$key]['flight_number'] = $value['flight_number'];
            $result[$key]['image']         = $value['image'];
            $result[$key]['depart_time']   = $value['simple_departure_time'];
            $result[$key]['arrival_time']  = $value['simple_arrival_time'];
            $result[$key]['price'] = [
                'promo'    => $value['is_promo'],
                'adults'   => $value['price_adult'],
                'children' => $value['price_child'],
                'infants'  => $value['price_infant'],
                'total'    => $value['price_value']
            ];
            $result[$key]['facilities'] = [
                'food'        => $value['has_food'],
                'baggage'     => $value['check_in_baggage'],
                'airport_tax' => $value['airport_tax'],
            ];
            $routes = [];
            foreach ($value['flight_infos']['flight_info'] as $info) {
                $routes[] = [
                    'flight_number'  => $info['flight_number'],
                    'departure_city' => $info['departure_city'],
                    'arrival_city'   => $info['arrival_city'],
                    'depart_time'    => $info['simple_departure_time'],
                    'arrival_time'   => $info['simple_arrival_time']
                ];
            };
            $result[$key]['flight_info']['routes'] = $routes;
            $result[$key]['flight_info']['duration'] = $value['duration'];
        }

        return compact('result', 'selected_key');
    }

    private function parseHotel($hotels, $facilities, $coordinates = [])
    {
        $result = [];
        $facility_available = $selected_key = $point = 0;
        $services = new Google;
        foreach ($hotels as $index => $value) {
            $result[$index]['name']            = $value['name'];
            $result[$index]['room_available']  = $value['room_available'];
            $result[$index]['stars']           = $value['star_rating'];
            $result[$index]['image']           = $value['photo_primary'];
            $result[$index]['max_occupancies'] = $value['room_max_occupancies'];
            $result[$index]['address']         = preg_replace('/\\n/', '', $value['address']);
            $result[$index]['facilities']      = $value['room_facility_name'] == '' ? array() : explode('|', $value['room_facility_name']);
            $result[$index]['price'] = [
                'old'     => $value['oldprice'],
                'current' => $value['price'],
                'total'   => $value['total_price'] != '' ? $value['total_price'] : $value['oldprice']
            ];

            // distance from airport
            $result[$index]['distance'] = '';
            $result[$index]['duration'] = '';
            if ($value['latitude'] != '' &&  $value['longitude'] != '') {
                $hotel_coordinates   = implode(',', [$value['latitude'], $value['longitude']]);
                $airport_coordinates = implode(',', $coordinates);
                $distance = $services->distance($airport_coordinates, $hotel_coordinates);
                $distance = json_decode($distance, true);
                if ($distance['status'] == 'OK') {
                    if ($distance['rows'][0]['elements'][0]['status'] == 'OK') {
                        $result[$index]['distance'] = $distance['rows'][0]['elements'][0]['distance']['text'];
                        $result[$index]['duration'] = $distance['rows'][0]['elements'][0]['duration']['text'];
                    }
                }
                $result[$index]['coordinates'] = $hotel_coordinates;
            }

            foreach ($facilities as $key => $selected) {
                if ($selected == true) {
                    switch ($key) {
                        case 'hw' : $keyword = 'Shower';
                        break;
                        case 'tv' : $keyword = 'TV';
                        break;
                        case 'wifi' : $keyword = 'Internet';
                        break;
                        case 'ac' : $keyword = 'Air conditioning';
                        break;
                        case 'safe' : $keyword = 'Safe';
                        break;
                    }

                    if (stripos($value['room_facility_name'], $keyword)) {
                        $point++;
                    }
                }
            }

            if ($point > $facility_available) {
                $selected_key = $index;
                $facility_available = $point;
            }

        }

        return compact('result', 'selected_key');
    }

}