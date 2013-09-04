define(["storage/appStorage"],function(appStorage){
    return Backbone.View.extend({
        initialize: function(){
            this.$el = $("#d-container");
            this.render();
        },

        render: function(){
            var template = _.template( appStorage.templates.categoryListContainerTemplate );
            this.$el.append( template );
        }
    })
})