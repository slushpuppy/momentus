// enhance the original "$.ajax" with a retry mechanism
let $ = require('jquery');


$.ajax = (($oldAjax) => {
    // on fail, retry by creating a new Ajax deferred
    function check(a,b,c){
        var shouldRetry = b != 'success' && b != 'parsererror';
        if( shouldRetry && --this.retries > 0 )
            setTimeout(() => { $.ajax(this) }, this.retryInterval || 300);
    }

    return settings => $oldAjax(settings).always(check)
})($.ajax);

