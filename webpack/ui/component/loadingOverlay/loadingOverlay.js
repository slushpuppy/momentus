import Env from 'bootloader';
import ImageLoading from './assets/loading.gif';
import CSS from './css/style.module.scss';


let $ = require('jquery');

jQuery.fn.extend({
    loadingOverlay : function ()
    {
        return {
            show: () => {
                $(this).addClass(CSS.loadingContainer);
                $(this).append(
                    `<div class="${CSS.loadingOverlay}"></div>
<div class="${CSS.loadingImage}"></div>`
                );
            },
            hide: () => {
                $(this).removeClass(CSS.loadingContainer);
                $(this).find(`.${CSS.loadingOverlay}`).remove();
            }
        }

    }
});

