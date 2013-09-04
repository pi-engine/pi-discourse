define(["storage/appStorage"],function(appStorage){
    return Backbone.View.extend({
        initialize: function(){
        },
        
        render: function(topicModel){
            variables = {
                id:             topicModel.get("id"),
                title:          topicModel.get("title"),
                category_id:    topicModel.get("category_id"),
                posts_count:    topicModel.get("posts_count"),
                time_created:   'created at ' + topicModel.get("time_created")
            };
            var template = _.template(appStorage.templates.categoryListTopicTemplate, variables);
            this.$el.append( template );
        }
    })
})