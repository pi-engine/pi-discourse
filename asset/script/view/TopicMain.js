define([
    "storage/appStorage", 
    "view/PostRow",
    "view/PostCreate"
],function(appStorage, PostRowView, PostCreateView){
   return Backbone.View.extend({
        el: $("#main-outlet"),

        events: {
            "click button#btn-reply-to-topic": "showCreatePostWindow",
//                "scroll window"                  : "more"
        },

        initialize: function(){
            this.render();
            appStorage.posts.each(function(post){
                post.set("view", new PostRowView({
                    model: post,
                    id: "post-" + post.get('id'),
                    attributes: {
                        identity: post.get('id'),
                    }
                }));
            });
        },

        render: function(){
            var variables = this.getVariables(appStorage.currentTopic);
            this.$el.append(_.template( appStorage.templates.topicMainTemplate, variables));

//                window.action.page = 1;
//                $(window).bind('scroll',function (){ 
//                    if($(window).scrollTop()+$(window).height()>=$(document).height()){ 
//                        this.more();
//                    }
//                });
        },

        getVariables: function(topicModel){
            return variables = {
                title:          topicModel.get("title"),
                id:             topicModel.get("id"),
                like_count:     topicModel.get("like_count"),
                posts_count:    topicModel.get("posts_count"),
                time_created:   topicModel.get("time_created"),
                category_id:    topicModel.get("category_id"),
                views:          topicModel.get("views"),
            };
        },

        showCreatePostWindow: function(){
            if($("#reply-control").length === 0) {
                var topicController = require('controller/topic');
                topicController.postCreateView = topicController.postCreateView || new PostCreateView();
                topicController.postCreateView.model = appStorage.currentTopic;
                topicController.postCreateView.render();
            }
        },
    })
});