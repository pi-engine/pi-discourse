define(["storage/appStorage"],function(appStorage){
   return Backbone.View.extend({
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
//                                    if(newPost.get('category_id') === appStorage.currentCategory.get('id')) {
//                                        appStorage.topics.add(newTopic);
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

            var template = _.template( appStorage.templates.postCreateTemplate, this.variables );
            this.$el.append( template );
            CKEDITOR.replace( 'editor2' );
        },

        getPostModelVariables: function(postModel){
            return variables = {
                post_id:        postModel.get("id"),
                topic_id:       postModel.get("topic_id"),
                post_number:    postModel.get("post_number"),
                userInfo:       appStorage.users.get(postModel.get("user_id")),
            };
        },

        getTopicModelVariables: function(topicModel){
            return variables = {
                title:          topicModel.get("title"),
                topic_id:       topicModel.get("id"),
            };
        },
    })
});