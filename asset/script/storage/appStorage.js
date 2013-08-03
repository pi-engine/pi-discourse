define(function(){
    var useSessionStorage = false;
    if (useSessionStorage) {
        return window.sessionStorage;
    }
    return {};
});