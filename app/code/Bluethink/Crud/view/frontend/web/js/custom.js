// alert("This is from custom js.");
define([
    'jquery'
], function ($) {
    'use strict';

    alert("This is alert");
});


define(['jquery','domReady!','customJs'],function($,dm,customJs){
    alert("second file js");
});
