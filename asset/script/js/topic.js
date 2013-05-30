/* << replace >>*/

define(["dis"], function(dis){
    return {
        TopicMainView: Backbone.View.extend({
            el: $("#main-outlet"),
            
            events: {
                "click #btn-reply-to-topic":    "showCreatePostWindow"
            },
            
            initialize: function(){
                this.render();
                _.each(disStorage.posts.models, function(post){
                    var postRowView = new action.PostRowView({ model: post });
                });
            },
            
            render: function(){
                variables = this.getVariables(disStorage.currentTopic);
                var template = _.template( disStorage.templates.topicMainTemplate, variables );
                this.$el.append( template );
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
            }
        }),
        
        PostRowView: Backbone.View.extend({
            initialize: function(){
                this.$el = $("#post-row-container"),
                this.render();
                $("#reply-to-post-" + this.model.id).bind("click", this.showReplyWindow);
                $("#post-" + this.model.id + "-reply-toggler").bind("click", this.toggleReply);
                $("#post-" + this.model.id + "-reference-toggler").bind("click", this.toggleReference);
            },
            
            render: function(){
                variables = this.getVariables(this.model);

                var template = _.template( disStorage.templates.postRowTemplate, variables );
                this.$el.append( template );
            },
            
            toggleReference: function(){
                var post_id = this.getAttribute('value');
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
                                
                                new action.PostRefernceView( {
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
                    
            toggleReply: function(){
                if($("#post-" + this.value + "-reply-container").length === 0) {
                    new action.PostReplyContainerView( {
                        el: $("#post-" + this.value + "-body"), 
                        model: disStorage.posts.get(this.value)
                    }).render();
                } else {
                    $("#post-" + this.value + "-reply-container").remove();
                    $("#post-" + this.value + "-body").addClass('bottom-round');
                    
                }
            },
            
            showReplyWindow: function(){
                if($("#reply-control").length === 0) {
                    action.postCreateView = action.postCreateView || new action.PostCreateView();
                    action.postCreateView.model = disStorage.posts.get(this.value);
                    action.postCreateView.render();
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
                    userInfo:                   disStorage.users.get(postModel.get("user_id")),
//                    replyToUserInfo:            disStorage.users.get(disStorage.posts.get(postModel.get("reply_to_post_id")).get('user_id'))
                };
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
    //                            reply_to_post_id: 1,
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
                    url: "/discourse/post/" + this.id + "/0/50",
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
                            console.log(data);
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
        
        run: function(){
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
                disStorage.templates.topicMainTemplate          = $(template1).html();
                disStorage.templates.postRowTemplate            = $(template2).html();
                disStorage.templates.postCreateTemplate         = $(template3).html();
                disStorage.templates.postReplyContainerTemplate = $(template4).html();
                disStorage.templates.postReplyRowTemplate       = $(template5).html();
                disStorage.templates.postReferenceTemplate      = $(template6).html();

                disStorage.currentTopic = new dis.Topic(PreloadStore.data.topic);
                disStorage.posts        = new dis.Posts;
                
                _.each(PreloadStore.data.postsAndUsers.users, function(user){
                    disStorage.users.add(new dis.User(user));
                });
                _.each(PreloadStore.data.postsAndUsers.posts, function(post){
                    disStorage.posts.add(new dis.Post(post));
                });
                PreloadStore.data = null;
                
                new action.TopicMainView();
            });
        }
    };
});