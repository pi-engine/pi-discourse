define(["storage/appStorage"],function(appStorage){
    return Backbone.View.extend({
        el: $("#main"),

        initialize: function(){
        },

        events: {
            "click #create-topic-toggler"       : "closeCreateWindow",
            "click #submit_cancel"              : "closeCreateWindow",
            "click #btn-topic-create-submit"    : "submit"
        },

        closeCreateWindow: function(){
            $("#reply-control").remove();
        },

        submit: function(){
            var categoryController = require('controller/category');
            var title = $("#reply-title").val();
            var content = CKEDITOR.instances.editor1.getData();
            if(title.length !== 0 && content.length !== 0) {
                categoryController.createTopic(title, content, appStorage.currentCategory.id);
            } else {
                console.log("invalid input");
            }
        },

        render: function(){
            var template = _.template( appStorage.templates.topicCreateTemplate );
            this.$el.append( template );
        }
    })
})