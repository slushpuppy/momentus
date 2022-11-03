import {JsonResponse} from "lib/http/model/JsonResponse";

export class ScanResponse extends JsonResponse
{
    constructor() {
        super();
        this.redirect = null;
        /**
         *
         * @type {_PinStatus}
         */
        this.pinStatus = null;
        this.pinSession = null;
    }


}

export const _PinStatus = {
    "NEW":"newPin",
    "VALIDATE":"validatePin"

};
Object.freeze(_PinStatus);