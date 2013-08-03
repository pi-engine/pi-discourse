define(["storage/appStorage"],function(appStorage){
    return Backbone.View.extend({
        el: $("#main-outlet"),

        initialize: function(){
        },

        events: {
            "click #close-create-window"        : "closeCreateWindow",
            "click #btn-category-create-submit" : "submit"
        },

        closeCreateWindow: function(){
            $("#modal").remove();
            $("#discourse-modal").remove();
        },

        submit: function(){
            var category_name               = $("#new-category-name").val();
            var category_description        = $("#new-category-description").val();
            var category_background_color   = $("#new-category-background-color").val();
            var category_foreground_color   = $("#new-category-foreground-color").val();

            if(category_name.length !== 0 
                && category_description.length !== 0
                && category_background_color.match('^[0-9a-fA-F]{6}$') !== null
                && category_foreground_color.match('^[0-9a-fA-F]{6}$') !== null
            ) {
                require('controller/categoryList')
                    .createCategory(category_name, category_background_color, category_description);
            } else {
                console.log("invalid input");
            }
        },

        render: function(){
            var template = _.template( appStorage.templates.categoryCreateTemplate );
            this.$el.append( template );
        }
    })
})