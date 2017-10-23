'use strict';
var app = angular.module("EasytravelApp", [
    'ngRoute',
    'ui.bootstrap',
    'frapontillo.bootstrap-switch',
    'blockUI',
    'oitozero.ngSweetAlert',
    'uiGmapgoogle-maps'
]);

// var baseUrl = '//' + window.location.host + '/easytravel/public/api/';
var baseUrl = '//' + window.location.host + '/api/';

// config
app.config(function(blockUIConfig) {
    // set default message
    blockUIConfig.message = 'Searching...';

    // do not block if URI contains "/api/". solve conflict between typeahead and blockUI
    blockUIConfig.requestFilter = function(config) {
      if(config.url.match(/\/api\//)) {
        return false;
      }
    };
});

app.config(function(uiGmapGoogleMapApiProvider) {
    uiGmapGoogleMapApiProvider.configure({
        key: 'AIzaSyAhewScrZjjMs7nfhgCLaL0Cv3CfXzurPo',
        libraries: 'weather, geometry, visualization'
    });
})
