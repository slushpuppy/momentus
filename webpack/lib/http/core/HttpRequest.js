export class HttpRequest {
    static createFromParamString(url) {
        const urlParam = new URLSearchParams(url);
        let obj = new this;

        Object.keys(obj).forEach(function(key,index) {
            if (urlParam.has(key))
            {
                obj[key] = urlParam.get(key);
            }
        });
        for (var propName in obj) {
            if (obj[propName] === null || obj[propName] === undefined) {
                delete obj[propName];
            }
        }
        return obj;
    }

    toUrlParamString()
    {
        return Object.keys(this).map((key) => {
            return encodeURIComponent(key) + '=' + encodeURIComponent(this[key])
        }).join('&');
    }

}