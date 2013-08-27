define([
    "storage/appStorage", 
    "model/User", 
    "model/Topic", 
    "model/Post", 
    "collection/PostCollection", 
    "view/TopicMain",
    "text!template/topic-main-template.bhtml", 
    "text!template/post-row-template.bhtml", 
    "text!template/post-create-template.bhtml", 
    "text!template/post-reply-container-template.bhtml", 
    "text!template/post-reply-row-template.bhtml", 
    "text!template/post-reference-template.bhtml"
], 
function(appStorage, User, Topic, Post, PostCollection, TopicMainView, 
    template1, template2, template3, template4, template5, template6){
    return {
        page: null,
        
        postCreateView: null,
        
        getReferenedPost: function(referd_post_id, post_id){
            console.log(post_id);
            $.ajax({
                url: "/discourse/post/" + referd_post_id,
                type: 'GET',
                async: false,
                success: function(data){
                    data = JSON.parse(data);
                    if(!data.err_msg) {
                        var User = require('model/User');
                        var Post = require('model/Post');
                        var appStorage = require('storage/appStorage');
                        
                        appStorage.users.add(new User(data.user));

                        var postModel = new Post(data.post);
                        appStorage.posts.add(postModel);
                        
                        appStorage.posts.get(post_id).get('view').displayReference(postModel);
                    } else {
                        console.log(data.err_msg);
                    }
                }
            });
        },
        
        getReplyPost: function(post_id){
            $.ajax({
                url: "/discourse/postReply/" + post_id + "/0/50",
                type: 'GET',
                async: false,
                success: function(data){
                    data = JSON.parse(data);
                    if(!data.err_msg) {
                        var replyPostModels = [];
                        var User = require('model/User');
                        var Post = require('model/Post');
                        var appStorage = require('storage/appStorage');
                        
                        
                        _.each(data.users, function(user){
                            appStorage.posts.add(new User(user));
                        });
                        _.each(data.posts, function(post){
                            var currentPost = new Post(post);
                            appStorage.posts.add(currentPost);
                            replyPostModels.push(currentPost);
                        });
                        
                        appStorage.posts.get(post_id).get('replyContainerView').displayReplies(replyPostModels, post_id);
                    } else {
                        console.log(data.err_msg);
                    }
                }
            });
        },
        
        more: function() {
            console.log('request data');
            $.ajax({
                url: '/discourse/post/' + appStorage.currentTopic.get('id') + '/' + (require('controller/topic').page * 20) + '/20',
                type: 'GET',
                async: false,
                success: function(data){
                    data = JSON.parse(data);
                    if (typeof data.err_msg !== 'undefined') {
                        console.log(data.err_msg);
                        $(window).unbind('scroll');
                        return;
                    }
                    console.log(data);
                    
                    var User = require('model/User');
                    var Post = require('model/Post');
                    var PostRowView = require('view/PostRow');
                    
                    _.each(data.users, function(user){
                        appStorage.users.add(new User(user));
                    });
                    
                    _.each(data.posts, function(post){
                        var currentPost = new Post(post);
                        
                        currentPost.set("view", new PostRowView({
                            model: currentPost,
                            id: "post-" + currentPost.get('id'),
                            attributes: {
                                identity: currentPost.get('id'),
                            }
                        }));
                        
                        appStorage.posts.add(currentPost);

                    });

                    require('controller/topic').page++;
                }
            });
        },
        
        run: function(id){
            console.log('running topic.js');
            
            appStorage.templates.topicMainTemplate          = $(template1).html();
            appStorage.templates.postRowTemplate            = $(template2).html();
            appStorage.templates.postCreateTemplate         = $(template3).html();
            appStorage.templates.postReplyContainerTemplate = $(template4).html();
            appStorage.templates.postReplyRowTemplate       = $(template5).html();
            appStorage.templates.postReferenceTemplate      = $(template6).html();

            /**
             * prepare data
             */
            if (PreloadStore.data) {
                appStorage.currentTopic = new Topic(PreloadStore.data.topic);
                appStorage.posts        = new PostCollection;

                _.each(PreloadStore.data.postsAndUsers.users, function(user){
                    appStorage.users.add(new User(user));
                });
                _.each(PreloadStore.data.postsAndUsers.posts, function(post){
                    appStorage.posts.add(new Post(post));
                });
                PreloadStore.data = null;
            } else {
                console.log('request for data');
                $.ajax({
                    url: '/discourse/t/' + id + '.json',
                    type: 'GET',
                    async: false,
                    success: function(data){
                        var appStorage      = require('storage/appStorage');
                        var User            = require('model/User');
                        var Topic           = require('model/Topic');
                        var Post            = require('model/Post');
                        var PostCollection  = require('collection/PostCollection');
                        
                        var data = JSON.parse(data);
//                            console.log(data);

                        appStorage.currentTopic = new Topic(data.topic);
                        if ("undefined" === typeof appStorage.posts) {
                            appStorage.posts = new PostCollection;
                        } else {
                            appStorage.posts.reset();
                        }
                        _.each(data.postsAndUsers.users, function(user){
                            appStorage.users.add(new User(user));
                        });
                        _.each(data.postsAndUsers.posts, function(post){
                            appStorage.posts.add(new Post(post));
                        });
                    }
                });
            }

            /**
             * render
             */
            $("#main-outlet").empty();
            new TopicMainView();

            this.page = 1;
            
            $(window).unbind('scroll');
            $(window).bind('scroll',function (){ 
                if($(window).scrollTop()+$(window).height()>=$(document).height()){ 
                    require('controller/topic').more();
                }
            });

            /**
             * locate the post if specified
             */
        }
    };
});