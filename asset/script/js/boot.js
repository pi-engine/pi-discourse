/* << replace >>*/

pjax = false;
var scripts = document.getElementsByTagName("script");

//config requirejs
require.config({
    baseUrl: '/asset' + scripts[scripts.length-1].src.match(/\/module.*?\//)[0] + 'script/js/'
});

//load dis.js and prepare PreloadStore data
require(["dis"], function(dis) {
    disStorage = {};
    
    disStorage.user         = new dis.User(PreloadStore.data.user);
    disStorage.categories   = new dis.Categories;
    disStorage.users        = new dis.Users;
    
    _.each(PreloadStore.data.categories, function(category) {
        disStorage.categories.add(new dis.Category(category));
    });
    disStorage.templates = {};
//    disStorage.topics = {};
    
    
    user        = new dis.User(PreloadStore.data.user);
    categories  = new dis.Categories;
    
    _.each(PreloadStore.data.categories, function(category) {
        categories.add(new dis.Category(category));
    });
    
    if(pjax) {
        //enable pjax
        var dis_router = new dis.Router;

        // Trigger the initial route and enable HTML5 History API support, set the
        // root folder to '/' by default.  Change in app.js.

        Backbone.history.start({ pushState: true, root: dis.app.root, silent: true });

        // All navigation that is relative should be passed through the navigate
        // method, to be processed by the router. If the link has a `data-bypass`
        // attribute, bypass the delegation completely.
        $(document).on("click", "a[href]:not([data-bypass])", function(evt) {
            // Get the absolute anchor href.
            var href = { prop: $(this).prop("href"), attr: $(this).attr("href") };
            // Get the absolute root.

            var root = location.protocol + "//" + location.host + dis.app.root;

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
    var actionName = $("meta[name=actionName]").attr('content');

    require(['dis', 'header', actionName], function(dis, header, action) {
        window.action = action;
        header.run();
        window.action.run();
    //    Backbone.history.start({ pushState: true, root: dis.app.root });
    });
});