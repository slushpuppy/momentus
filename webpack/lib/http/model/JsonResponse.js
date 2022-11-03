
export class JsonResponseStatus {

    static OK()
    {
        return 'OK';
    }
    static Error()
    {
        return 'Error';
    }

    static InvalidParam()
    {
        return 'INVALID_PARAM';
    }
    static NoPermission()
    {
        return 'NO_PERMISSION';
    }

    static ServerError()
    {
        return 'SERVER_ERROR';
    }

    static IdNotFound()
    {
        return 'ID_NOT_FOUND';
    }

}




export class JsonResponse
{
    constructor() {
        this.data = null;
        /**
         *
         * @type {JsonResponseStatus}
         */
        this.status = JsonResponseStatus.OK()
        this.error = null;
        this.error_uri = null;
        this.error_description = null;
        this.apiEndpoints = {};
        this.pageType = null;
    }
}

