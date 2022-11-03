import {AbstractView} from "module/SinglePageApp/AbstractView";
import CSS from "../../css/login.scss";
import {LoginBodyRequest} from "../model/LoginBodyRequest";
import qrcodeScanIcon from '../../img/qr-code.inline.svg';
import {JsonResponseStatus} from "lib/http/model/JsonResponse";
import I8n from "i8n/I8n";
import "ui/component/error/errorDialog";


export class LoginView extends AbstractView {


    constructor(domContainer,spa) {
        super(domContainer, spa);
        this.codeReader = null;
        this.bottomSheet = null;
        this.bottomSheetElement = null;
        /**
         * @type {App}
         */
        this.lang = I8n.get("app");
    }

    init(jsonData)
    {
        super.init(jsonData);
        this.domContainer.html(`<div class="${CSS.signInDescription}">${this.lang.login.label.signInDescription}</div>
        <div class="${CSS.signInEmailForm}">
            <input class="${CSS.signInEmailField}" type="text" placeholder="${this.lang.login.input.emailPlaceholder}">
                <button class="${CSS.signInEmailBtn}">${this.lang.login.button.signIn}</button>
        </div>

        <div class="${CSS.qrcodeSignIn}">
            <div class="${CSS.qrcodeSignInDescription}">${this.lang.login.label.signInQrCodeDescription}</div>
            <div class="${CSS.qrcodeScan}">${qrcodeScanIcon}</div>

        </div>
        <div class="${CSS.bottomSheet}">
            <div class="${CSS.scanDesc}">${this.lang.login.label.scanfindScanCode}</div>
            <div class="${CSS.videoScanContainer}" data-qrcode-url="${this.spa.apiBaseUrl+this.spa.apiEndpoints.scan}"></div>
        </div>`);


    }

    onPageLoad() {
        super.onPageLoad();

        this.bottomSheetElement = $(`.${CSS.bottomSheet}`).first();
        this.bottomSheet = this.bottomSheetElement.bottomSheet({closeBtn:true,scrollFix:false,canResize:false,height:{min:'70vh',max:'70vh'}, onClose: () => {
                if (this.codeReader)
                {
                    this.codeReader.stop();
                }
            }});


    }


    run() {
        $(`.${CSS.qrcodeScan}`).click( (evt) =>
        {
            this.bottomSheet.show();
            let videoContainer = this.bottomSheetElement.find(`.${CSS.videoScanContainer}`);
            videoContainer.QRCodeScanner({withAjax:false,autoStart:true},() => {}, (qrdata) => {

                let payload = new LoginBodyRequest();
                payload.qrcode = qrdata;
                this.spa.ajax(
                    this.spa.apiBaseUrl+this.spa.apiEndpoints.scan + window.location.search,
                    payload

                ).then(                    /**
                 * @param {JsonResponse} response
                 */
                (response) => {
                    /**
                     * @type {LoginViewResponseStatus}
                     */
                    let responseStatus = response.status;
                    switch (response.status)
                    {
                        case LoginViewResponseStatus.RESPONSE_INVALID_QRCODE():
                            this.bottomSheet.hide();
                            $('body').ErrorDialog('Invalid QR COde',"Invalid QRcode",'',{timeoutMS:2000,showCloseBtn: true, autoHide: false});
                            break;
                        case JsonResponseStatus.OK():
                            this.spa.onPageLoad(response);
                            break;
                    }
                });


                this.codeReader.stop();
            }).then((result) => {
                this.codeReader = result;
            });
        });
    }

    /**
     *
     * @param {JsonResponse} ajaxData
     */
    isValid(ajaxData) {
        if (ajaxData.pageType == null) return true;
    }


}
export class LoginViewResponseStatus extends JsonResponseStatus
{
    static RESPONSE_INVALID_QRCODE()
    {
        return 'INVALID_QRCODE';
    }
}