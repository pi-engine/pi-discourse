/* << replace >>*/

define(["dis"], function(dis){
    return {
        TopicMainView: Backbone.View.extend({
            el: $("#main-outlet"),
            
//            events: {
//                "click button#btn-reply-to-topic": "showCreatePostWindow",
//                "scroll window"                  : "more"
//            },
            
            initialize: function(userData){
                this.userData = userData;
                this.render();
//                disStorage.posts.each(function(post){
//                    post.set("view", new action.PostRowView({
//                        model: post,
//                        id: "post-" + post.get('id'),
//                        attributes: {
//                            identity: post.get('id'),
//                        }
//                    }));
//                });
            },
            
            render: function(){
//        console.log(this.userData);
//                variables = this.getVariables();
                this.$el.html(_.template( disStorage.templates.userMainTemplate, this.userData));
            },
                    
            getVariables: function(){
        return {};
//                return variables = {
//                    title:          topicModel.get("title"),
//                    id:             topicModel.get("id"),
//                    like_count:     topicModel.get("like_count"),
//                    posts_count:    topicModel.get("posts_count"),
//                    time_created:   topicModel.get("time_created"),
//                    category_id:    topicModel.get("category_id"),
//                    views:          topicModel.get("views"),
//                };
            },
            
//            showCreatePostWindow: function(){
//                if($("#reply-control").length === 0) {
//                    action.postCreateView = action.postCreateView || new action.PostCreateView();
//                    action.postCreateView.model = disStorage.currentTopic;
//                    action.postCreateView.render();
//                }
//            },
        }),

        run: function(id){
            console.log('running user.js');
            require([
                "text!../template/user-main-template.bhtml", 
//                "text!../template/post-row-template.bhtml",
//                "text!../template/post-create-template.bhtml",
//                "text!../template/post-reply-container-template.bhtml",
//                "text!../template/post-reply-row-template.bhtml",
//                "text!../template/post-reference-template.bhtml"
            ], 
            function(template1, template2, template3, template4, template5, template6 ){
                disStorage.templates.userMainTemplate           = $(template1).html();
                disStorage.templates.postRowTemplate            = $(template2).html();
                disStorage.templates.postCreateTemplate         = $(template3).html();
                disStorage.templates.postReplyContainerTemplate = $(template4).html();
                disStorage.templates.postReplyRowTemplate       = $(template5).html();
                disStorage.templates.postReferenceTemplate      = $(template6).html();
 
                
                var userData = PreloadStore.data.userData;
                
                
                
                $("#main-outlet").empty();
                new action.TopicMainView(userData);
            });
        },
    }
});