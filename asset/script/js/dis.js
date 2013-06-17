/* << replace >>*/

define({
    app: {
        root: '/discourse/'
    },

    Category: Backbone.Model.extend({
        initialize: function(category){
//            console.log("Build a category");
        }
    }),

    Topic: Backbone.Model.extend({
        initialize: function(){
        }
    }),

    Post: Backbone.Model.extend({
        initialize: function(){
        }
    }),

    User: Backbone.Model.extend({
        initialize: function(user){
//            console.log(user);
            if('undefined' === typeof user.name) {
                user.name = 'Anonymous';
            }
//            if(!user.name) {
//                user.name = 'Anonymous';
//            }
        },
        defaults: {
        }
    }),
    
    Topics: Backbone.Collection.extend({
//        model: this.Topic
    }),

    Categories: Backbone.Collection.extend({
        model: this.Category
    }),

    Posts: Backbone.Collection.extend({
        model: this.Post
    }),
    
    Users: Backbone.Collection.extend({
        model: this.User
    }),

    
    CategoryButtonView: Backbone.View.extend({
        initialize: function(){
        },
        render: function(categoryModel){
        console.log(dis.template.topicListContainerTemplate);
            variables = {
                name:           categoryModel.get("name"),
                id:             categoryModel.get("id"), 
                color:          categoryModel.get("color"),
                slug:           categoryModel.get("slug")
            };
//            variables = {id:1,name:"category",color:"333333",slug:""};
            var template = _.template( $("#category-button-template").html(), variables );
            this.$el.append( template );
        }
    }),

    Router: Backbone.Router.extend({
        routes: {
            ""          : "categoryList",
            "c"         : "categoryList",
            "c/:id"     : "category",
            "t/:id"     : "topic"
        },
        categoryList: function(){
            console.log( "Going to category list" );
//            $("#category-filter").children('.active').removeClass('active');
//            $("#topic-filter-category").addClass('active');
            require(["categoryList"], function(action) {
                window.action = action;
                window.action.run();
            });
        },
        category: function(id){
            console.log( "Going to category " + id );
//            $("#category-filter").children('.active').removeClass('active');
            require(["category"], function(action) {
                window.action = action;
                window.action.run(id);
            });
        },
        topic: function(id){
            // Note the variable in the route definition being passed in here
            console.log( "Going to topic " + id );
            
            require(["topic"], function(action) {
                window.action = action;
                window.action.run(id);
            });
        },
        defaultRoute: function(url) {
            // just url argument and parse params out
        }
    }),

    template: {},

    jump: function(){

    },
});