import {AbstractView} from "module/SinglePageApp/AbstractView";
import CSS from "../../css/login.scss";
import 'ui/component/error/errorDialog'
import I8n from "i8n/I8n";

let $ = require('jquery');


export class FeedView extends AbstractView {
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
        this.lang = I8n.get('app');


    }

    /**
     *
     * @param {ScanResponse} jsonData
     */
    init(jsonData)
    {
        super.init(jsonData);
        let pinDesc = this.lang.login.label.pinValidateDescription;



        this.domContainer.html(``);


    }

    onPageLoad() {
        this.domContainer.loadingOverlay().hide();
    }


    run() {


    }

    /**
     *
     * @param {JsonResponse} ajaxData
     */
    isValid(ajaxData) {
    }
}