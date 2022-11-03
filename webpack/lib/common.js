let $ = require('jquery');
$.debounce  = (callback, wait) => {
    let timeout = null
    return (...args) => {
        const next = () => callback(...args)
        clearTimeout(timeout);

        timeout = setTimeout(next, wait);
    }
}
$.fn.extend({
    hasAttr: function (attr) {
        let attribVal = $(this).attr(attr);
        return (attribVal !== undefined) && (attribVal !== false);
    },
    isChecked: function (attr) {
        let attribVal = $(this).prop(attr);
        return (attribVal !== undefined) && (attribVal !== false);
    }
});

