<!DOCTYPE html>
<html ng-app="EasytravelApp">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>EasyTravel - The Easiest Way to Travel!</title>
        <link rel="stylesheet" href="lib/bootstrap/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="lib/font-awesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="lib/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css">
        <link rel="stylesheet" href="lib/angular-block-ui/dist/angular-block-ui.min.css"/>
        <link rel="stylesheet" href="lib/sweetalert/dist/sweetalert.css"/>
        <link rel="stylesheet" href="css/custom.css">
    </head>
    <body>
        <nav class="navbar navbar-custom">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="#">
                    <img alt="Brand" src="img/easytravel-logo.png">
                    </a>
                </div>
            </div>
        </nav>
        <div class="container-fluid">
            <div class="row">
                <aside class="col-md-4">
                    <form name="searchForm" novalidate>
                        <div id="searchWidget" class="panel panel-primary" ng-controller="SearchController" block-ui="searchWidget">
                            <div class="panel-body">
                                <ul class="nav nav-tabs nav-justified">
                                    <li role="presentation" ng-click="selectTab('flight')" ng-class="{ active:tabSelected('flight') }"><a href="#"><i class="fa fa-lg fa-plane"></i></a></li>
                                    <li role="presentation" ng-click="selectTab('hotel')" ng-class="{ active:tabSelected('hotel') }"><a href="#"><i class="fa fa-lg fa-building"></i></a></li>
                                </ul>
                                <div ng-show="tabSelected('flight')" ng-include="'./partials/flights.html'"></div>
                                <div ng-show="tabSelected('hotel')" ng-include="'./partials/hotels.html'"></div>
                                <button class="btn btn-primary btn-block" ng-click="submit()">Submit</button>
                            </div>
                        </div>
                    </form>
                </aside>
                <main class="col-md-8" ng-controller="MainController">
                    <div class="row" ng-show="budget.show">
                        <div class="col-md-12">
                            <p class="pull-right">Total Budget Required : <span class="badge" style="font-size: 14px">IDR {{ budget.value | number}}</span></p>
                        </div>
                    </div>
                    <flight-widget
                        show="fw.show"
                        done="fw.done"
                        data="fw.data">
                    </flight-widget>
                    <hotel-widget
                        show="hw.show"
                        done="hw.done"
                        data="hw.data">
                    </hotel-widget>
                    <route-widget
                        show="rw.show"
                        show="rw.done"
                        data="rw.data">
                    </route-widget>
                    <venue-widget
                        show="vw.show"
                        done="vw.done"
                        data="vw.data">
                    </venue-widget>
                </main>
            </div>
        </div>
        <script src="lib/jquery/dist/jquery.min.js"></script>
        <script src="lib/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="lib/bootstrap-switch/dist/js/bootstrap-switch.min.js"></script>
        <script src="lib/angular/angular.min.js"></script>
        <script src="lib/angular-bootstrap/ui-bootstrap.min.js"></script>
        <script src="lib/angular-bootstrap/ui-bootstrap-tpls.min.js"></script>
        <script src="lib/angular-bootstrap-switch/dist/angular-bootstrap-switch.min.js"></script>
        <script src="lib/angular-route/angular-route.min.js"></script>
        <script src="lib/angular-block-ui/dist/angular-block-ui.min.js"></script>
        <script src="lib/sweetalert/dist/sweetalert.min.js"></script>
        <script src="lib/ngSweetAlert/SweetAlert.min.js"></script>
        <script src='lib/lodash/lodash.min.js'></script>
        <script src='lib/angular-simple-logger/dist/angular-simple-logger.min.js'></script>
        <script src='lib/angular-google-maps/dist/angular-google-maps.min.js'></script>
        <script src="js/angular/app.js"></script>
        <script src="js/angular/controllers.js"></script>
        <script src="js/angular/directives.js"></script>
        <script src="js/angular/services.js"></script>
    </body>
</html>