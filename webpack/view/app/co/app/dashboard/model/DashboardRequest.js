import {ProfileHeaderPaneRequest} from "../../model/ProfileHeaderPaneRequest";

/**
 * @typedef {import('../../model/ProfileHeaderPaneRequest').ProfileHeaderPaneRequest}
 */
export class DashboardRequest {
    constructor() {
        /**
         *
         * @type {int|null}
         */
        this.feedRequest = null;
        ProfileHeaderPaneRequest.assign(this);
    }
}