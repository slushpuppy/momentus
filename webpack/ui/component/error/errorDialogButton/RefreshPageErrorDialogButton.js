import {ErrorDialogButton} from "../ErrorDialogButton";
import CSS from '../css/style.scss';

import I8n from 'i8n/I8n';

export class RefreshPageErrorDialogButton extends ErrorDialogButton {
    constructor() {
        super(I8n.get("app").ui.component.errorDialog.button.refresh,RefreshPageErrorDialogButton.onClick);
    }

    /**
     * @param {Event} evt
     */
    static onClick(evt)
    {

    }
}