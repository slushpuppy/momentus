import {ErrorDialogButton} from "../ErrorDialogButton";
import CSS from '../css/style.scss';

import I8n from 'i8n/I8n';

export class CloseErrorDialogButton extends ErrorDialogButton {
    constructor() {
        super(I8n.get("app").ui.component.errorDialog.button.close,CloseErrorDialogButton.onClick);
    }

    /**
     * @param {Event} evt
     */
    static onClick(evt)
    {
        /**
         * @type {jQuery}
         */
        let element = $(evt.target);
        element.parents(`.${CSS.containerErrorDialog}`).remove();
    }
}