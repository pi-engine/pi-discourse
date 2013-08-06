/* << replace >>*/

//var requireBase = $("meta[name=requireBase]").attr('content');

//config requirejs
require.config({
    baseUrl: $("meta[name=requireBase]").attr('content')
});


require([
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
function(appStorage, Router, UserModel, 
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
    
    
    pjax = 1;

    if (typeof window.history.pushState === 'undefined') {
        pjax = 0;
    }

    if (pjax) {
        //enable pjax
        router = new Router;
        
        var rootBase = '/discourse/';
        
        // Trigger the initial route and enable HTML5 History API support, set the
        // root folder to '/' by default.  Change in app.js.
        Backbone.history.start({ pushState: true, root: rootBase, silent: true });

        // All navigation that is relative should be passed through the navigate
        // method, to be processed by the router. If the link has a `data-bypass`
        // attribute, bypass the delegation completely.
        $(document).on("click", "a[href]:not([data-bypass])", function(evt) {
            // Get the absolute anchor href.
            var href = { prop: $(this).prop("href"), attr: $(this).attr("href") };
            // Get the absolute root.

            var root = location.protocol + "//" + location.host + rootBase;

            // Ensure the root is part of the anchor href, meaning it's relative.
            if (href.prop.slice(0, root.length) === root && evt.ctrlKey === false) {
                // Stop the default event to ensure the link will not cause a page
                // refresh.
                evt.preventDefault();
                route = href.prop.slice(root.length, href.prop.length);
                // `Backbone.history.navigate` is sufficient for all Routers and will
                // trigger the correct events. The Router's internal `navigate` method
                // calls this anyways.  The fragment is sliced from the root.
    //            Backbone.history.start({ pushState: true, root: dis.app.root });
                Backbone.history.navigate(route, true);
    //            Backbone.history.stop();
            }
        });
    }
    
    //load current page script file
    var controllerName = $("meta[name=controllerName]").attr('content');

    require(['controller/header', 'controller/' + controllerName], function(headerController, contentController) {
        headerController.run();
        contentController.run();
    });
});