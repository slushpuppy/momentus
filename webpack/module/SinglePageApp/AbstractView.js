import {HttpRequest} from "lib/http/core/HttpRequest";
import 'ui/component/loadingOverlay/loadingOverlay';

export class AbstractView {
    /**
     *
     * @param {jQuery} appContainer
     * @param {SinglePageApp} spa;
     */
    constructor(appContainer,spa) {
        this.pageModel = null;
        this.domContainer = appContainer;


        this.spa = spa;
        let className = this.getParamClassReference();
        this.param = className.createFromParamString(window.location.search);


    }


    updateDataModel(pageModel) {

        this.pageModel = pageModel;
        if (pageModel.endpoints !== null && !$.isEmptyObject(pageModel.apiEndPoints))
        {
            this.spa.apiLoaders = pageModel.apiLoaders;
        }
    }




    _currentDate()
    {
        let date = new Date();
        const nth = function(d) {
            if (d > 3 && d < 21) return 'th';
            switch (d % 10) {
                case 1:  return "st";
                case 2:  return "nd";
                case 3:  return "rd";
                default: return "th";
            }
        }
        const month = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"][date.getMonth()];
        let hours = date.getHours();
        let minutes = date.getMinutes();
        let ampm = hours >= 12 ? 'pm' : 'am';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        minutes = minutes < 10 ? '0'+minutes : minutes;
        let strTime = hours + ':' + minutes + ' ' + ampm;
        return `${date.getDate()}<sup>${nth(date.getDate())}</sup> ${month} ${date.getFullYear()} ${strTime}`;

    }


    run()
    {
        throw 'Implement run in child class';

    }

    /**
     */
    onOffline() {

    }


    isValid(ajaxData)
    {
        throw 'Implement isValid in child class';

    }

    init(jsonData)
    {
        if (jsonData.apiEndpoints)
        {
            this.spa.apiEndpoints = jsonData.apiEndpoints;
        }

    }


    onPageLoad() {
        this.domContainer.loadingOverlay().hide();
    }

    onPageUnload() {

    }

    /**
     * @return {HttpRequest}
     */
    getParamClassReference()
    {
        return HttpRequest;
    }


}
