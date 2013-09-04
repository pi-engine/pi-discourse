define(["storage/appStorage"],function(appStorage){
    return Backbone.View.extend({
        initialize: function(){
        },
        render: function(categoryModel){
            var variables = {
                color:  categoryModel.get("color"),
                id:     categoryModel.get("id"),
                name:   categoryModel.get("name"),
                slug:   categoryModel.get("slug")
            };
            var template = _.template(appStorage.templates.categoryListTemplate, variables);
            this.$el.append( template );
        }
    })
})