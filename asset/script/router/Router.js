/* << replace >>*/

define([],function(){
    return Backbone.Router.extend({
        routes: {
            ""          : "categoryList",
            "c"         : "categoryList",
            "c/:id"     : "category",
            "t/:id"     : "topic",
            "u/:id"     : "user"
        },
        categoryList: function(){
            console.log( "Going to category list" );
//            $("#category-filter").children('.active').removeClass('active');
//            $("#topic-filter-category").addClass('active');
            require(["controller/categoryList"], function(controller) {
                controller.run();
            });
        },
        category: function(id){
            console.log( "Going to category " + id );
//            $("#category-filter").children('.active').removeClass('active');
            require(["controller/category"], function(controller) {
                controller.run(id);
            });
        },
        topic: function(id){
            // Note the variable in the route definition being passed in here
            console.log( "Going to topic " + id );
            
            require(["controller/topic"], function(controller) {
                controller.run(id);
            });
        },
        user: function(id){
            // Note the variable in the route definition being passed in here
            console.log( "Going to user " + id );
            
            require(["controller/user"], function(controller) {
                controller.run(id);
            });
        },
        defaultRoute: function(url) {
            // just url argument and parse params out
        }
    })}
)