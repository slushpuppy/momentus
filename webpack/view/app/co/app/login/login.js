import CSS from '../css/login.scss';
import Env from 'bootloader';
import I8n from "i8n/I8n";
import 'ui/component/loadingOverlay/loadingOverlay';
import 'ui/component/bottomSheet/bottomSheet';
import 'ui/component/QRCode/QRCodeScanner';
import 'module/SinglePageApp/LocalApiLoader';
import 'module/PWA/init';
import 'lib/http/ajax_retry';

import {LocalApiLoader} from "module/SinglePageApp/LocalApiLoader";
import {LoginBodyRequest} from "./model/LoginBodyRequest";
import {JsonReponseStatus, SinglePageApp} from "module/SinglePageApp/SinglePageApp";
import {ViewController} from "module/SinglePageApp/ViewController";
import {LoginView} from "./view/LoginView";
import {PinView} from "./view/PinView";
import {JsonResponse, JsonResponseStatus} from "../../../../../lib/http/model/JsonResponse";


let loader = LocalApiLoader.Init();

let $ = require('jquery');

let logo = $('[data-company-logo]').attr('data-company-logo');


$('body').loadingOverlay().show();


I8n.load([I8n.getAllNamespaces().app]).then(
    () => {

        /**
         *
         * @type {App}
         */
        let lang = I8n.get('app');

        let contentDom =  $('#content');

        contentDom.html(
            `<div class="${CSS.companyLogo}"><img alt="logo" src="${logo}"></div>
<div class="${CSS.contentContainer}">

</div>

    `
        );

        let contentContainer = $(`.${CSS.contentContainer}`);


        let controller = new ViewController();

        let spa = new SinglePageApp(contentDom,loader,controller);

        controller.addRoute(new LoginView(contentContainer,spa));
        controller.addRoute(new PinView(contentContainer,spa));

        $('body').loadingOverlay().hide();


        let iniJson = new JsonResponse();
        iniJson.apiEndpoints = loader.endpoints;

        spa.onPageLoad(iniJson);




},(error) => {

});

