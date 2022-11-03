import {AbstractView} from "module/SinglePageApp/AbstractView";
import CSS from "../css/profilePaneHeader.scss";
import {defaultAvatar} from "../img/profile-avatar.svg";
import 'ui/component/error/errorDialog'

let $ = require('jquery');


export class ProfileHeaderPane extends AbstractView {
    /**
     *
     * @param {jQuery} domContainer
     * @param {SinglePageApp} spa
     */
    constructor(domContainer,spa) {
        super(domContainer, spa);

        /**
         * @type {baseApp}
         */
        this.lang = this.spa.lang;

        this.domContainer.loadingOverlay().show();


    }

    /**
     *
     * @param {ProfileHeaderPaneResponse} jsonData
     */
    init(jsonData)
    {
        super.init(jsonData);
        let avatarUrl = defaultAvatar;
        this.domContainer.html(`<div class="${CSS.avatar}"><img src="${avatarUrl}" /></div><div class="${CSS.infoPane}"><div class="${CSS.displayName}">${jsonData.profileHeaderPane.displayName}</div><div class="${CSS.desc}">${this.lang.profile.label.taskCount(jsonData.profileHeaderPane.taskCount)}</div></div>
    `);


    }

    onPageLoad() {
        this.domContainer.loadingOverlay().hide();

    }


    run() {

    }

    /**
     *
     * @param {ProfileHeaderPaneResponse} ajaxData
     */
    isValid(ajaxData) {
        if (ajaxData.profileHeaderPane) return true;
    }
}