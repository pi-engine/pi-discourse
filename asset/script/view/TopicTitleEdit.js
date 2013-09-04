define([
    "storage/appStorage",
    "model/Topic"
],
function(appStorage, Topic){
    return Backbone.View.extend({
        events: {
            "click button#edit-title-submit": "submitEdit",
            "click button#edit-title-cancel": "cancelEdit",
        },

        initialize: function(topic){
            this.$el = $("#topic-title-container"),
            this.render();
        },

        render: function(){
            //this.topic.toJSON();
            var variables = appStorage.currentTopic.toJSON();
            var template = _.template( appStorage.templates.topicTitleEditTemplate, variables );
            this.$el.empty();
            this.$el.append( template );
        },

        submitEdit: function(){
            console.log($("#edit-title").val());
            $.ajax({
                url: '/discourse/topic/' + appStorage.currentTopic.get('id'),
                type: 'PUT',
                async: false,
                data: {
                    'title': $("#edit-title").val()
                },
                success: function(data){
                    var data = JSON.parse(data);
                    console.log(data);

                    if (typeof data.err_msg !== "undefined") {
                        console.log(data.err_msg);
                    } else {
                        var appStorage = require('storage/appStorage');
                        var Topic = require("model/Topic");
                        appStorage.currentTopic.set(data);
                        appStorage.currentTopic.get("titleView").render();
                    }
                }
            });
        },

        cancelEdit: function(){
            appStorage.currentTopic.get("titleView").render();
        }
    })
})