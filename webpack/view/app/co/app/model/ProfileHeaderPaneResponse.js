import {JsonResponse} from "lib/http/model/JsonResponse";


export class ProfileHeaderPaneResponse
{
    constructor() {
        this.profileHeaderPane = {
            displayName: null,
            avatar: null,
            taskCount: 0,
            notificationCount: 0,
            messageCount: 0,
            activityPoints: 0
        };

    }
    set assign(obj) {
        Object.assign(obj,new ProfileHeaderPaneResponse());
    }


}