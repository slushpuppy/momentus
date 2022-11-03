export class LocalApiLoader {
    constructor() {
        this.endpoints = null;
        this.baseApiUrl = null;
        this.initialEndpoint = null;

    }

    /**
     * @return {LocalApiLoader}
     * @constructor
     */
    static Init() {
        let ret = new LocalApiLoader();
        if ($('[data-base-api-url]').length)
            ret.baseApiUrl = $('[data-base-api-url]').first().attr('data-base-api-url');
        if ( $('[data-initial-endpoint]').length)
            ret.initialEndpoint = $('[data-initial-endpoint]').first().attr('data-initial-endpoint');
        if ($('[data-api-endpoints]').length)
            ret.endpoints = JSON.parse($('[data-api-endpoints]').first().attr('data-api-endpoints'));
        return ret;
    }
}
