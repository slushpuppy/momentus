import 'lib/http/ajax_retry'
import {AppJsonBodyRequest} from "../../view/app/co/app/model/AppJsonBodyRequest";
import {Http} from "lib/http/core/Http";
import {ApiEndPointModel} from "../../view/app/co/black/fms/service/model/ApiEndPointModel";
import {JsonResponse,JsonResponseStatus} from "../../lib/http/model/JsonResponse";
import I8n from "i8n/I8n";
import cancelIcon from "ui/assets/cancel.inline.svg";
import cloudOfflineIcon from "ui/assets/cloud-connection.inline.svg";
import permissionIcon from "ui/assets/no-permission.inline.svg";
import {RefreshPageErrorDialogButton} from "ui/component/error/errorDialogButton/RefreshPageErrorDialogButton";

let $ = require('jquery');




export class SinglePageApp {

    /**
     * @param dom
     * @param {LocalApiLoader} apiLoader
     * @param {ViewController} updateControllerFn
     */
    constructor(dom,apiLoader,updateControllerFn) {
        this.domContainer = dom;
        this._initEvents();


        /**
         * @type {ViewController}
         */
        this.viewController = updateControllerFn;


        this.apiEndpoints = apiLoader.endpoints;

        this.apiBaseUrl = apiLoader.baseApiUrl;
        /**
         *
         * @type {App}
         */
        this.appLang = I8n.get('app');

        this.dataModel = null;

    }


    _initEvents() {
        this.domContainer.on("click","a", (evt) => {
            let target =  $(evt.currentTarget);
            let attr = target.attr('data-spa-ignore')
            if (typeof attr === typeof undefined) {
                evt.preventDefault();
                this.controller.onPageLoadStart(target);
                let ahref = target.attr('href');
                let pat = /^https?:\/\/|^\/\//i;
                if (!pat.test(ahref))
                {
                    ahref = this.apiBaseUrl + ahref;
                }
                this.loadPage(ahref);
            }

        });
        window.onpopstate = (e) =>{
            if(e.state){

                this.viewController.run(e.state);
            } else {
                this.viewController.run({});

            }
        };
    }

    onPageLoad(data)
    {
        if (data.href)
        {
            window.history.pushState(data,"", data.href);

        } else {
            window.history.pushState(data,"");

        }
        this.viewController.run(data);
    }

    /**
     * @param link
     * @param payload
     * @return {Promise<unknown>}
     */
    ajax(link,payload)
    {
       return new Promise((resolve, reject) => {
           $.ajax({
               url: link,
               method: "POST",
               data: JSON.stringify(payload),
               dataType: 'text',
               xhrFields: { withCredentials: true }
           }).done(
               /**
                * @param {JsonResponse} data
                */
               (data) => {

                   let jsonData = Http.JSONStrToObject(data,null);

                   switch(jsonData.status)
                   {
                       case JsonResponseStatus.Error():
                       case JsonResponseStatus.InvalidParam():
                       case JsonResponseStatus.NoPermission():
                       case JsonResponseStatus.ServerError():
                           this.domContainer.ErrorDialog(cancelIcon,this.appLang.network.error.serverError,[new RefreshPageErrorDialogButton()]);
                           reject(jsonData);
                           break;

                       case JsonResponseStatus.OK():
                           resolve(jsonData);
                           break;
                   }


               }).fail(
               /**
                * @param {XMLHttpRequest} xhr
                * @param status
                * @param error
                */
               (xhr, status, error)=>{
                   if (xhr.readyState === 4 && xhr.status <= 200)
                   {
                       this.viewController.onOffline();
                       this.domContainer.ErrorDialog(cloudOfflineIcon,this.appLang.network.error.connectivityError,[new RefreshPageErrorDialogButton()]);

                       reject({});
                   }
               });
        });

    }


    loadPage(link, payload)
    {
        this.ajax(link,payload).then((data) => {
            this.onPageLoad(data);
        });
    }

    copyDefinedModel(src, dst)
    {
        Object.assign(dst, src);
        Object.keys(dst).forEach(function (key) {
            if(src[key] === undefined) dst[key] = null;
        });
    }

}


