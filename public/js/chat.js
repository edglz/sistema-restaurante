/*jslint browser: true*/
/*global $, jQuery, alert*/

$(function () {

    "use strict";

    $('.panel-left-inner > .panelonline').slimScroll({
        height: '100%',
        position: 'right',
        size: "5px",
        color: '#dcdcdc'

    });
    
    $('.panel-list').slimScroll({
        position: 'right'
        , size: "5px"
        , height: '100%'
        , color: '#dcdcdc'
     });
    
    var cht = function () {
            var topOffset = 205;
            var height = ((window.innerHeight > 0) ? window.innerHeight : this.screen.height) - 1;
            height = height - topOffset;
            $(".panel-list").css("height", (height) + "px");
    };
    $(window).ready(cht);
    $(window).on("resize", cht);
    
    // this is for the left-aside-fix in content area with scroll
    var chtin = function () {
            var topOffset = 270;
            var height = ((window.innerHeight > 0) ? window.innerHeight : this.screen.height) - 1;
            height = height - topOffset;
            $(".panel-left-inner").css("height", (height) + "px");
    };
    $(window).ready(chtin);
    $(window).on("resize", chtin);

    $(".open-panel").on("click", function () {
        $(".panel-left-aside").toggleClass("open-pnl");
        $(".open-panel i").toggleClass("ti-angle-left");
    });

});
