export class ErrorDialogButton {
    constructor(btnLabel,btnEvent, btnEventExtraData = null) {
        this.label = btnLabel;
        this.event = btnEvent;
        this.eventExtraData = btnEventExtraData
    }

    static onClick()
    {
        throw 'Implement onclick in child class';
    }
}