let context = { currentTaskId: 0 };

let lastDelay = [];
let delay = (function () {
    let timer = 0;
    return function (callback, time) {
        if (Array.isArray(callback)) {
            if (callback.length > 0) {
                callback = callback.pop();
                lastDelay = [];
            } else {
                callback = false;
            }
        }
        clearTimeout(timer);
        if (callback !== false) timer = setTimeout(callback, time);
    }
})();

let downloadFile = function(data, fileName) {
    let blob = new Blob([data], {type: 'application/octet-binary'});
    let link = document.createElement('a');
    link.href = window.URL.createObjectURL(blob);
    link.download = fileName;
    link.click();
}