/* << replace >>*/

//config requirejs
require.config({
    baseUrl: $("meta[name=requireBase]").attr('content')
});

require([
    'appConfig',
    'storage/appStorage',
    'router/Router', 
    'model/User', 
    'model/Category', 
    'collection/UserCollection', 
    'collection/CategoryCollection', 
//    'controller/header',
//    'controller/categoryList',
//    'controller/category',
//    'controller/topic',
//    'controller/user',
], 
function(config, appStorage, Router, UserModel, 
    CategoryModel, UserCollection, CategoryCollection
//    , header, categoryList, category, topic, user
) {
    
    appStorage.user         = new UserModel(PreloadStore.data.user);
    appStorage.categories   = new CategoryCollection;
    appStorage.users        = new UserCollection;
    
    _.each(PreloadStore.data.categories, function(category) {
        appStorage.categories.add(new CategoryModel(category));
    });
    appStorage.templates = {};
    
    // render header
    require(['controller/header'], function(headerController) {
        headerController.run();
    });

    var router = new Router;
    var controllerName = $("meta[name=controllerName]").attr('content');

    if (typeof window.history.pushState === 'undefined') {
        config.pjax = 0;
    }

    if (config.pjax) {
        // enable pjax
        var routeBase = config.routeBase;

        Backbone.history.start({ pushState: true, root: routeBase });

        $(document).on("click", "a[href]:not([data-bypass])", function(evt) {
            var href = { prop: $(this).prop("href"), attr: $(this).attr("href") };

            var root = location.protocol + "//" + location.host + routeBase;

            if (href.prop.slice(0, root.length) === root && evt.ctrlKey === false) {
                evt.preventDefault();
                route = href.prop.slice(root.length, href.prop.length);
                
                Backbone.history.navigate(route, true);
            }
        });
    } else {
        // if not using pjax, run route directly
        router[controllerName]();
    }
});