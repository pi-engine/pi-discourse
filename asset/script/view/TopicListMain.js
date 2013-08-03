define([
    "storage/appStorage",
    "view/TopicListContainer", 
    "view/TopicCreate"
],
function(appStorage, TopicListContainerView, TopicCreateView){
    return Backbone.View.extend({
        el: $("#main-outlet"),

        events: {
            "click #btn-create-new-topic":  "showCreateTopicWindow"
        },

        initialize: function(){
            this.render();
            var topicListContainerView = new TopicListContainerView();
        },

        render: function(){
            var template = _.template( appStorage.templates.topicListMainTemplate );
            this.$el.append( template );
        },

        showCreateTopicWindow: function() {
            categoryController = require('controller/category');
            if($("#reply-control").length === 0) {
                categoryController.topicCreateView = categoryController.topicCreateView || new TopicCreateView();
                categoryController.topicCreateView.render();
                CKEDITOR.replace( 'editor1' );
            }
        }
    })
})