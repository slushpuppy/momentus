import CSS from './css/dashboard.scss';
import CSSProfilePane from './../css/profilePaneHeader.scss';
import Env from 'bootloader';
import I8n from "i8n/I8n";
import 'ui/component/loadingOverlay/loadingOverlay';
import 'ui/component/bottomSheet/bottomSheet';
import 'module/SinglePageApp/LocalApiLoader';
import 'lib/http/ajax_retry';

import {LocalApiLoader} from "module/SinglePageApp/LocalApiLoader";
import {SinglePageApp} from "module/SinglePageApp/SinglePageApp";
import {ViewController} from "module/SinglePageApp/ViewController";
import {ProfileHeaderPane} from "view/app/co/app/view/ProfileHeaderPane";
import {FeedView} from "./view/FeedView";
import {DashboardRequest} from "./model/DashboardRequest";


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
            `<div class="${CSSProfilePane.profileHeader}"></div><div class="${CSS.dashboardFeed}"></div> `
        );

        let contentContainer = $(`.${CSS.dashboardFeed}`);


        let controller = new ViewController();

        let spa = new SinglePageApp(contentDom,loader,controller);

        controller.addRoute(new ProfileHeaderPane($(`.${CSSProfilePane.profileHeader}`).first(),spa));
        controller.addRoute(new FeedView(contentContainer,spa));

        let dashboardRequest = new DashboardRequest();
        dashboardRequest.feedRequest = 1;
        dashboardRequest.profileHeaderPaneRequest = 1;


       $('body').loadingOverlay().hide();

        spa.ajax(spa.apiBaseUrl + loader.initialEndpoint + window.location.search,dashboardRequest).then((data) => {
            this.onPageLoad(data);
        });




    },(error) => {

    });

