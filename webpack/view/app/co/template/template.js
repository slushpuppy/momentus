import './css/template.global.scss';


let $ = require('jquery');

$(document).ready(function () {

    $('body').on('click', function (e) {
        $('[data-toggle="popover"]').each(function () {
            //the 'is' for buttons that trigger popups
            //the 'has' for icons within a button that triggers a popup
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                $(this).popover('hide');
            }
        });
    });

    //$('.toast').toast('show');
    $(document).click(function (event) {
        var clickover = $(event.target);
        var $navbar = $(".navbar-collapse");
        var _opened = $navbar.hasClass("show");
        if (_opened === true && !clickover.hasClass("navbar-toggle")) {
            $navbar.collapse('hide');
        }
    });
});
