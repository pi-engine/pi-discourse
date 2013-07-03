/* << replace >>*/

define(["dis"], function(dis){
    return {
        TopicMainView: Backbone.View.extend({
            el: $("#main-outlet"),
            
            events: {
                "click button#btn-reply-to-topic": "showCreatePostWindow",
//                "scroll window"                  : "more"
            },
            
            initialize: function(){
                this.render();
                disStorage.posts.each(function(post){
                    post.set("view", new action.PostRowView({
                        model: post,
                        id: "post-" + post.get('id'),
                        attributes: {
                            identity: post.get('id'),
                        }
                    }));
                });
            },
            
            render: function(){
                variables = this.getVariables(disStorage.currentTopic);
                this.$el.append(_.template( disStorage.templates.topicMainTemplate, variables));
                
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
                    action.postCreateView = action.postCreateView || new action.PostCreateView();
                    action.postCreateView.model = disStorage.currentTopic;
                    action.postCreateView.render();
                }
            },
        }),
        
        PostRowView: Backbone.View.extend({
            className: "ember-view topic-post clearfix regular",
            
            initialize: function(){
                this.render();
            },
            
            events: {
                "click button[name='reply-post']": "showReplyWindow",
                "click button[name='post-reply-toggler']": "toggleReply",
                "click a[name='post-reference-toggler']": "toggleReference",
                "click button[name='btn-bookmark']": "bookmark",
                "click button[name='btn-like']": "like",
                "click a[name='post-unlike']": "unlike"
            },
            
            showReplyWindow: function(e){
                if ($("#reply-control").length === 0) {
                    action.postCreateView = action.postCreateView || new action.PostCreateView();
                    action.postCreateView.model = disStorage.posts.get(e.currentTarget.value);
                    action.postCreateView.render();
                }
            },
            render: function(){
                variables = this.getVariables(this.model);
                this.$el.html(_.template(disStorage.templates.postRowTemplate, variables));
                $("#post-row-container").append(this.$el);
            },
            
            toggleReference: function(e){
                var post_id = e.currentTarget.getAttribute('value');
                var referd_post_id = disStorage.posts.get(post_id).get('reply_to_post_id')
                
                if($("#post-" + post_id + "-reference-container").children().length === 0) {
                    $.ajax({
                        url: "/discourse/post/" + referd_post_id,
                        type: 'GET',
                        async: false,
                        success: function(data){
                            data = JSON.parse(data);
                            if(!data.err_msg) {
                                disStorage.users.add(new dis.User(data.user));

                                var postModel = new dis.Post(data.post);
                                disStorage.posts.add(postModel);
                                
                                new action.PostRefernceView({
                                    el: $("#post-" + post_id + "-reference-container"), 
                                    model: postModel
                                }).render();
                                $("#post-" + post_id).addClass('replies-above');
                            } else {
                                console.log(data.err_msg);
                            }
                        }
                    });
                } else {
                    $("#post-" + post_id + "-reference-container").empty();
                    $("#post-" + post_id).removeClass('replies-above');
                    
                }
            },
                    
            toggleReply: function(e){
                var id = e.currentTarget.value;
                var post = disStorage.posts.get(id);
                if($("#post-" + id + "-reply-container").length === 0) {
                    if (typeof post.get("replyContainerView") === "undefined") {
                        disStorage.posts.get(id).set(
                            "replyContainerView", 
                            new action.PostReplyContainerView({
                                el: $("#post-" + id + "-body"),
                                model: post
                            })
                        );
                        post.get("replyContainerView").render();
                    } else {
                        post.get("replyContainerView").$el = $("#post-" + id + "-body");
                        post.get("replyContainerView").model = post;
                        post.get("replyContainerView").render();
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
                    userInfo:                   disStorage.users.get(postModel.get("user_id")),
//                    replyToUserInfo:            disStorage.users.get(disStorage.posts.get(postModel.get("reply_to_post_id")).get('user_id'))
                };
            },
            
            like: function(e){
                var status = 1;
                $.ajax({
                    url: '/discourse/postAction',
                    type: 'POST',
                    data: {
                        post_id: e.currentTarget.value,
                        post_action_type_id: 2,
                        status: status
                    },
                    async: false,
                    success: function(data){
                        data = JSON.parse(data);
                        console.log(data);
                        if( typeof data.postAction.post_action_type_id !== 'undefined') {
                            $("button#btn-like-" + data.postAction.post_id).css('display', 'none');
                            $("span#post-like-count-" + data.postAction.post_id).html(data.post.like_count);
                            $("a#post-unlike-" + data.postAction.post_id).css('display', 'inline');
                            $("div#like-status-" + data.postAction.post_id).css('display', 'inline');
                        }
                    }
                });
            },
            
            unlike: function(e){
                var post_id = e.currentTarget.getAttribute('value');
                var status = 0;
                $.ajax({
                    url: '/discourse/postAction',
                    type: 'POST',
                    data: {
                        post_id: post_id,
                        post_action_type_id: 2,
                        status: status
                    },
                    async: false,
                    success: function(data){
                        data = JSON.parse(data);
                        console.log(data);
                        if( typeof data.postAction.post_action_type_id === 'undefined') {
                            $("button#btn-like-" + data.postAction.post_id).css('display', 'inline');
                            $("a#post-unlike-" + data.postAction.post_id).css('display', 'none');
                            $("span#post-like-count-" + data.postAction.post_id).html(data.post.like_count);
                            if (data.post.like_count > 0) {
                                $("div#like-status-" + data.postAction.post_id).css('display', 'inline');
                            } else {
                                $("div#like-status-" + data.postAction.post_id).css('display', 'none');
                            }
                        }
                    }
                });
            },
            
            bookmark: function(e){
                var status;
                if ($(e.currentTarget).find("i").hasClass('icon-bookmark')) {
                    status = 0;
                } else {
                    status = 1;
                }
                $.ajax({
                    url: '/discourse/postAction',
                    type: 'POST',
                    data: {
                        post_id: e.currentTarget.value,
                        post_action_type_id: 1,
                        status: status
                    },
                    async: false,
                    success: function(data){
                        data = JSON.parse(data);
                        if( typeof data.postAction.post_action_type_id === 'undefined') {
                            $("button[name='btn-bookmark'][value=" + data.postAction.post_id + "] i").removeClass().addClass('icon-bookmark-empty');
                        } else {
                            $("button[name='btn-bookmark'][value=" + data.postAction.post_id + "] i").removeClass().addClass('icon-bookmark');
                        }
                    }
                });
            }
        }),
        
        PostCreateView: Backbone.View.extend({
            el: $("#main"),
            
            initialize: function(){
            },
            
            events: {
                "click #create-post-toggler"        : "closeCreateWindow",
                "click #submit_cancel"              : "closeCreateWindow",
                "click #btn-post-create-submit"     : "submit"
            },
                    
            submit: function(){
                if(this.model.get('title')) {
                    this.variables = this.getTopicModelVariables(this.model);
                } else {
                    this.variables = this.getPostModelVariables(this.model);
                }
                
                this.variables.content = CKEDITOR.instances.editor2.getData();
                console.log(this.variables);
                
                if(variables.content.length !== 0) {
                    if(this.variables.title) {
                        $.ajax({
                            url: '/discourse/post',
                            type: 'POST',
                            data: {
                                raw:        this.variables.content,
                                topic_id:   this.variables.topic_id,
                            },
                            async: false,
                            success: function(data){
                                data = JSON.parse(data);
                                if(!data.err_msg) {
                                    $("#reply-control").remove();
                                    window.location.reload();
//                                    var newPost = new dis.Post(data);
//                                    newPost.set('newCreated', true);
//                                    if(newPost.get('category_id') === disStorage.currentCategory.get('id')) {
//                                        disStorage.topics.add(newTopic);
//                                        var topicListTableRowView = new action.TopicListTableRowView({ model: newTopic });
//                                    }
                                } else {
                                    console.log(data.err_msg);
                                }
                            }
                        });
                    } else {
                        $.ajax({
                            url: '/discourse/post',
                            type: 'POST',
                            data: {
                                raw:                this.variables.content,
                                topic_id:           this.variables.topic_id,
                                reply_to_post_id:   this.variables.post_id,
                            },
                            async: false,
                            success: function(data){
                                data = JSON.parse(data);
                                if(!data.err_msg) {
                                    $("#reply-control").remove();
                                    window.location.reload();
                                } else {
                                    console.log(data.err_msg);
                                }
                            }
                        });
                    }
                } else {
                    console.log("invalid input");
                }
            },
                    
            closeCreateWindow: function(){
                $("#reply-control").remove();
            },
                    
            render: function(){
                if(this.model.get('title')) {
                    this.variables = this.getTopicModelVariables(this.model);
                } else {
                    this.variables = this.getPostModelVariables(this.model);
                }
                
                var template = _.template( disStorage.templates.postCreateTemplate, this.variables );
                this.$el.append( template );
                CKEDITOR.replace( 'editor2' );
            },
                    
            getPostModelVariables: function(postModel){
                return variables = {
                    post_id:        postModel.get("id"),
                    topic_id:       postModel.get("topic_id"),
                    post_number:    postModel.get("post_number"),
                    userInfo:       disStorage.users.get(postModel.get("user_id")),
                };
            },
            
            getTopicModelVariables: function(topicModel){
                return variables = {
                    title:          topicModel.get("title"),
                    topic_id:       topicModel.get("id"),
                };
            },
        }),
        
        PostReplyContainerView: Backbone.View.extend({
            initialize: function(){
                this.id = this.model.get("id");
            },
            
            events: {
            },
                    
            render: function(){
                $("#post-" + this.id + "-body").removeClass('bottom-round');
                var template = _.template( disStorage.templates.postReplyContainerTemplate, {id: this.id,} );
                this.$el.after( template );
                
                $.ajax({
                    url: "/discourse/postReply/" + this.id + "/0/50",
                    type: 'GET',
                    async: false,
                    success: function(data){
                        data = JSON.parse(data);
                        if(!data.err_msg) {
                            _.each(data.users, function(user){
                                disStorage.posts.add(new dis.User(user));
                            });
                            _.each(data.posts, function(post){
                                var currentPost = new dis.Post(post);
                                disStorage.posts.add(currentPost);
                    
                                var postReplyRowView = new action.PostReplyRowView({ 
                                    el: $("#post-" + data.posts[0].reply_to_post_id + "-reply-container"),
                                    model: currentPost,
                                });
                            });
                        } else {
                            console.log(data.err_msg);
                        }
                    }
                });
            },
        }),

        PostReplyRowView: Backbone.View.extend({
            initialize: function(){
                this.render();
            },
            
            events: {
            },
                    
            render: function(){
                variables = this.getVariables(this.model);
                
                var template = _.template( disStorage.templates.postReplyRowTemplate, variables );
                this.$el.append( template );
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
                    reply_to_post_number:       postModel.get("reply_to_post_number"),
                    time_from_created:          postModel.get("time_from_created"),
                    userInfo:                   disStorage.users.get(postModel.get("user_id"))
                };
            }
        }),
        
        PostRefernceView: Backbone.View.extend({
            initialize: function(){
            },
            
            events: {
            },
                    
            render: function(){
                variables = this.getVariables(this.model);
//                console.log(this.id);
                
                var template = _.template( disStorage.templates.postReferenceTemplate, variables );
                this.$el.append( template );
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
                    reply_to_post_number:       postModel.get("reply_to_post_number"),
                    time_from_created:          postModel.get("time_from_created"),
                    userInfo:                   disStorage.users.get(postModel.get("user_id"))
                };
            }
        }),
        
        more: function() {
            console.log('request data');
            $.ajax({
                url: '/discourse/post/' + disStorage.currentTopic.get('id') + '/' + (window.action.page * 20) + '/20',
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
                    
                    _.each(data.users, function(user){
                        disStorage.users.add(new dis.User(user));
                    });
                    
                    _.each(data.posts, function(post){
                        var currentPost = new dis.Post(post);
                        
                        currentPost.set("view", new action.PostRowView({
                            model: currentPost,
                            id: "post-" + currentPost.get('id'),
                            attributes: {
                                identity: currentPost.get('id'),
                            }
                        }));
                        
                        disStorage.posts.add(currentPost);

                    });

                    window.action.page++;
                }
            });
        },

        run: function(id){
            console.log('running post.js');
            
            require([
                "text!../template/topic-main-template.bhtml", 
                "text!../template/post-row-template.bhtml",
                "text!../template/post-create-template.bhtml",
                "text!../template/post-reply-container-template.bhtml",
                "text!../template/post-reply-row-template.bhtml",
                "text!../template/post-reference-template.bhtml"
            ], 
            function(template1, template2, template3, template4, template5, template6 ){
                /**
                 * prepare templates
                 */
                disStorage.templates.topicMainTemplate          = $(template1).html();
                disStorage.templates.postRowTemplate            = $(template2).html();
                disStorage.templates.postCreateTemplate         = $(template3).html();
                disStorage.templates.postReplyContainerTemplate = $(template4).html();
                disStorage.templates.postReplyRowTemplate       = $(template5).html();
                disStorage.templates.postReferenceTemplate      = $(template6).html();
                
                /**
                 * prepare data
                 */
                if (PreloadStore.data) {
                    disStorage.currentTopic = new dis.Topic(PreloadStore.data.topic);
                    disStorage.posts        = new dis.Posts;

                    _.each(PreloadStore.data.postsAndUsers.users, function(user){
                        disStorage.users.add(new dis.User(user));
                    });
                    _.each(PreloadStore.data.postsAndUsers.posts, function(post){
                        disStorage.posts.add(new dis.Post(post));
                    });
                    PreloadStore.data = null;
                } else {
                    console.log('request for data');
                    $.ajax({
                        url: '/discourse/t/' + id + '.json',
                        type: 'GET',
                        async: false,
                        success: function(data){
                            data = JSON.parse(data);
//                            console.log(data);
                            
                            disStorage.currentTopic = new dis.Topic(data.topic);
                            if ("undefined" === typeof disStorage.posts) {
                                disStorage.posts = new dis.Posts;
                            } else {
                                disStorage.posts.reset();
                            }
                            _.each(data.postsAndUsers.users, function(user){
                                disStorage.users.add(new dis.User(user));
                            });
                            _.each(data.postsAndUsers.posts, function(post){
                                disStorage.posts.add(new dis.Post(post));
                            });
                        }
                    });
                }
                
                /**
                 * render
                 */
                $("#main-outlet").empty();
                new action.TopicMainView();
                
                window.action.page = 1;
                $(window).bind('scroll',function (){ 
                    if($(window).scrollTop()+$(window).height()>=$(document).height()){ 
                        window.action.more();
                    }
                });
                
                /**
                 * locate the post if specified
                 */
                
            });
        }
    };
});