define([
    "storage/appStorage", 
    "view/PostCreate", 
    "view/PostReference", 
    "view/PostReplyContainer", 
],function(appStorage, PostCreateView, PostReferenceView, PostReplyContainerView){
   return Backbone.View.extend({
        className: "ember-view topic-post clearfix regular",

        initialize: function(){
            this.render();
        },

        events: {
            "click button[name='reply-post']":          "showReplyWindow",
            "click button[name='post-reply-toggler']":  "toggleReply",
            "click a[name='post-reference-toggler']":   "toggleReference",
            "click button[name='btn-bookmark']":        "bookmark",
            "click button[name='btn-like']":            "like",
            "click a[name='post-unlike']":              "unlike"
        },

        showReplyWindow: function(e){
            if ($("#reply-control").length === 0) {
                var topicController = require('controller/topic');
                topicController.postCreateView = topicController.postCreateView || new PostCreateView();
                topicController.postCreateView.model = appStorage.posts.get(e.currentTarget.value);
                topicController.postCreateView.render();
            }
        },
        render: function(){
            var variables = this.getVariables(this.model);
            this.$el.html(_.template(appStorage.templates.postRowTemplate, variables));
            $("#post-row-container").append(this.$el);
        },

        toggleReference: function(e){
            var post_id = e.currentTarget.getAttribute('value');
            var referd_post_id = appStorage.posts.get(post_id).get('reply_to_post_id')

            if($("#post-" + post_id + "-reference-container").children().length === 0) {
                require('controller/topic').getReferenedPost(referd_post_id, post_id);
                $("#post-" + post_id).addClass('replies-above');
            } else {
                $("#post-" + post_id + "-reference-container").empty();
                $("#post-" + post_id).removeClass('replies-above');

            }
        },
        
        displayReference: function(postModel){
            new PostReferenceView({
                el: $("#post-" + this.model.get('id') + "-reference-container"), 
                model: postModel
            }).render();
        },
        
        toggleReply: function(e){
            var id = e.currentTarget.value;
            var post = appStorage.posts.get(id);
            if($("#post-" + id + "-reply-container").length === 0) {
                if (typeof post.get("replyContainerView") === "undefined") {
                    appStorage.posts.get(id).set(
                        "replyContainerView", 
                        new PostReplyContainerView({
                            el: $("#post-" + id + "-body"),
                            model: post
                        })
                    );
                    post.get("replyContainerView").render();
                    require('controller/topic').getReplyPost(id);
                } else {
                    post.get("replyContainerView").$el = $("#post-" + id + "-body");
                    post.get("replyContainerView").model = post;
                    post.get("replyContainerView").render();
                    require('controller/topic').getReplyPost(id);
                }
            } else {
                $("#post-" + id + "-reply-container").remove();
                $("#post-" + id + "-body").addClass('bottom-round');
            }
        },
        
        
        
        getVariables: function(postModel){
            return variables = {
                id:                         postModel.get("id"),
                topic_id:                   postModel.get("topic_id"),
                user_id:                    postModel.get("user_id"),
                raw:                        postModel.get("raw"),
                reply_count:                postModel.get("reply_count"),
                like_count:                 postModel.get("like_count"),
                time_updated:               postModel.get("time_updated"),
                time_created:               postModel.get("time_created"),
                cooked:                     postModel.get("cooked"),
                reply_to_post_id:           postModel.get("reply_to_post_id"),
                time_from_created:          postModel.get("time_from_created"),
                is_liked:                   postModel.get("isLiked"),
                is_bookmarked:              postModel.get("isBookmarked"),
                userInfo:                   appStorage.users.get(postModel.get("user_id")),
                appStorage:                 appStorage,
//                    replyToUserInfo:            appStorage.users.get(appStorageappStorage.posts.get(postModel.get("reply_to_post_id")).get('user_id'))
            };
        },

        like: function(){
            this.model.like().done(function(data){
                data = JSON.parse(data);
                if( typeof data.postAction.post_action_type_id !== 'undefined') {
                    require('storage/appStorage').posts.get(data.post.id).set('isLiked', 1);
                    $("button#btn-like-" + data.postAction.post_id).css('display', 'none');
                    $("span#post-like-count-" + data.postAction.post_id).html(data.post.like_count);
                    $("a#post-unlike-" + data.postAction.post_id).css('display', 'inline');
                    $("div#like-status-" + data.postAction.post_id).css('display', 'inline');
                }
            });
        },

        unlike: function(){
            this.model.unlike().done(function(data){
                data = JSON.parse(data);
                console.log(data);
                if( typeof data.postAction.post_action_type_id === 'undefined') {
                    require('storage/appStorage').posts.get(data.post.id).set('isLiked', 0);

                    $("button#btn-like-" + data.postAction.post_id).css('display', 'inline');
                    $("a#post-unlike-" + data.postAction.post_id).css('display', 'none');
                    $("span#post-like-count-" + data.postAction.post_id).html(data.post.like_count);
                    if (data.post.like_count > 0) {
                        $("div#like-status-" + data.postAction.post_id).css('display', 'inline');
                    } else {
                        $("div#like-status-" + data.postAction.post_id).css('display', 'none');
                    }
                }
            });
        },

        bookmark: function(){
            console.log(this.model);
            this.model.bookmark().done(function(data){
                data = JSON.parse(data);
                if( typeof data.postAction.post_action_type_id === 'undefined') {
                    require('storage/appStorage').posts.get(data.post.id).set('isBookmarked', 0);
                    $("button[name='btn-bookmark'][value=" + data.postAction.post_id + "] i").removeClass().addClass('icon-bookmark-empty');
                } else {
                    require('storage/appStorage').posts.get(data.post.id).set('isBookmarked', 1);
                    $("button[name='btn-bookmark'][value=" + data.postAction.post_id + "] i").removeClass().addClass('icon-bookmark');
                }
            });
        }
    })
});