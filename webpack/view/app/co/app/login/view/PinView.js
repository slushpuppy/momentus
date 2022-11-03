import {AbstractView} from "module/SinglePageApp/AbstractView";
import CSS from "../../css/login.scss";
import qrcodeScanIcon from "../../img/qr-code.inline.svg";
import {LoginBodyRequest} from "../model/LoginBodyRequest";
import {_LoginPageType} from "../model/_PageType";
import {_PinStatus} from "../model/ScanResponse";
import {PinBodyRequest} from "../model/PinBodyRequest";
import 'ui/component/error/errorDialog'
import I8n from "i8n/I8n";
import cancelIcon from "ui/assets/cancel.inline.svg";

let $ = require('jquery');


export class PinView extends AbstractView {
    /**
     *
     * @param {jQuery} domContainer
     * @param {SinglePageApp} spa
     */
    constructor(domContainer,spa) {
        super(domContainer, spa);
        this.codeReader = null;
        this.bottomSheet = null;
        this.bottomSheetElement = null;
        /**
         * @type {baseApp}
         */
        this.lang = I8n.get("app");



        this.pinPayload = new PinBodyRequest();

    }

    /**
     *
     * @param {ScanResponse} jsonData
     */
    init(jsonData)
    {
        super.init(jsonData);
        let pinDesc = this.lang.login.label.pinValidateDescription;

        this.pinPayload.pinSession = jsonData.pinSession;

        if (jsonData.pinStatus === _PinStatus.NEW)
        {
            pinDesc = this.lang.login.label.pinNewDescription;
        }
        this.domContainer.html(`<div class="${CSS.signInDescription}">${pinDesc}</div>
        <div class="${CSS.pinForm}">
            <input class="${CSS.pinField}" type="text" placeholder="${this.lang.login.input.pincodePlaceholder}">
                <button class="${CSS.pinSignInBtn}">${this.lang.login.button.signIn}</button>
        </div>

`);


    }

    onPageLoad() {


    }

    /**
     * @param {string} pin
     * @return {*}
     */
    isPinValid(pin)
    {
        return (pin.length === 8);
    }

    run() {
        $(`.${CSS.pinSignInBtn}`).click( (evt) =>
        {

            this.pinPayload.pin = $(`.${CSS.pinField}`).val();
            if (!this.isPinValid(this.pinPayload.pin))
            {
                this.domContainer.ErrorDialog(cancelIcon,this.lang.network.error.invalidPin,[],{showCloseBtn:true});
                return;
            }
                this.spa.ajax(
                    this.spa.apiBaseUrl+this.spa.apiEndpoints.pin + window.location.search,
                    this.pinPayload
                    ).then(
                    /**
                     *
                     * @param response
                     */
                    (response) =>
                    {
                        window.location.href = response.redirect;
                    });

            });

    }

    /**
     *
     * @param {JsonResponse} ajaxData
     */
    isValid(ajaxData) {
        if (ajaxData.pageType === _LoginPageType.PINCODE) return true;
    }
}