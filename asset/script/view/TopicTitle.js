define([
    "storage/appStorage",
    "view/TopicTitleEdit"
],
function(appStorage, TopicTitleEditView){
    return Backbone.View.extend({
        topic: null,

        events: {
            "click a#topic-title-edit-button": "editTitle",
        },

        initialize: function(topic){
            this.$el = $("#topic-title-container"),
            this.render();
        },

        render: function(){
            //this.topic.toJSON();
            var variables = appStorage.currentTopic.toJSON();
            var template = _.template( appStorage.templates.topicTitleTemplate, variables );
            this.$el.empty();
            this.$el.append( template );
        },

        editTitle: function(){
            appStorage.currentTopic.get("titleEditView").render();
        },
    })
})