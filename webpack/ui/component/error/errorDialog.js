import CSS from './css/style.scss';
import I8n from "i8n/I8n";
import {ErrorDialogButton} from './ErrorDialogButton';
import {CloseErrorDialogButton} from "./errorDialogButton/CloseErrorDialogButton";


let $ = require('jquery');
class ErrorDialog {
    /**
     * @param {jQuery} evt
     */
    static getBodyEventListener(evt) {
        if(!$(evt.target).closest((`.${CSS.containerErrorDialog}`).length)) {
            $('body').find(`.${CSS.containerErrorDialog}`).remove();
        }
    }
    /**
     * @param {jQuery} evt
     */
    static getBtnCloseEventListener (evt) {
        $('body').find(`.${CSS.containerErrorDialog}`).remove();
    }



}

jQuery.fn.extend({


    /**
     * @param titleHtml
     * @param titleHtml
     * @param messageHTML
     * @param timeoutMS
     * @param autoHide
     * @param showCloseBtn
     * @param {ErrorDialogButton[]} buttonList
     */
    ErrorDialog:

        function (titleHtml,messageHTML,buttonList,{timeoutMS= 2000, autoHide = false, showCloseBtn = true} = {}) {
        let comp  = $(this);

        let existingDialog = $(this).find(`.${CSS.containerErrorDialog}`);

        if (existingDialog.length)
        {
            existingDialog.remove();
        }

        if (!buttonList)
        {
            buttonList = [];
        }
        if (showCloseBtn)
        {
            buttonList.push(new CloseErrorDialogButton());
        }


        let controlHtml = '';

        for(let i = 0; i < buttonList.length; i++)
        {
            controlHtml += `<button class="${CSS.BtnSecondary}">${buttonList[i].label}</button>`;
        }

        controlHtml = `<div class="${CSS.blockControl}">${controlHtml}</div>`;

        comp.append(`<div class="${CSS.containerErrorDialog}"><div class="${CSS.blockTitle}">${titleHtml}</div><div class="${CSS.blockMessage}">${messageHTML}</div><div class="${CSS.blockControl}">${controlHtml}</div></div>`);

        comp.find(`.${CSS.blockControl}`).children('button').each(
            /**
             * @param i
             * @param HTMLElement element
             */
            (i,ele) => {

                let button = buttonList[i];
                let element = $(ele);
                if (button.eventExtraData)
                {
                    element.click(button.eventExtraData, button.event);
                } else {
                    element.click(button.event);

                }

        });

        $('body').click(ErrorDialog.getBodyEventListener);

    }
});