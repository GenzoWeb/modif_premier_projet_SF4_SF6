$(document).ready(function () {
    var count = $("#menu-list > li").length;
    var transY = "";
    var baseHeight = Math.ceil($('#menu').height());

    window.onresize = function () {
        var divMenu = Math.ceil($('#menu').height());
        
        if (document.body.clientWidth) {
            if (divMenu == baseHeight) {
                divSearchReset("0s");
            }

            if (divMenu !== baseHeight) {
                divSearchDown("0s");
            }
        }
    }

    $('.navbar-toggler').click(function () {
        var divMenu = Math.ceil($('#menu').height());

        if (divMenu === baseHeight) {
            divSearchDown("0.35s");
        } 

        if ($('#menu-test button').hasClass('collapsed'))
        {
            divSearchReset("0.35s");
        }
    });

    function divSearchReset(duration) {
        transY = "translateY(0px)";
        $('#div-search').css({ "transform": transY, "transition-duration": duration });
    }

    function divSearchDown(duration) {
        if (count == 5) {
            transY = "translateY(250px)";
        }
        if (count == 6) {
            transY = "translateY(290px)";
        }
        if (count == 7) {
            transY = "translateY(330px)";
        }

        $('#div-search').css({ "transform": transY, "transition-duration": duration });
    }
})