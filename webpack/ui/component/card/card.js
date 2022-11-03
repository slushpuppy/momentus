import CSS from './css/style.module.scss';
import Env from 'bootloader';

let $ = require('jquery');

jQuery.fn.extend({
    card: async function (header,content) {
        let comp  = $(this);
        comp.attr('data-ui-component-card','');
        comp.append(`<div class="${CSS.header}">${header}</div><div class="${CSS.content}">${content}</div>`);

    }
});