define(["storage/appStorage"],function(appStorage){
    return Backbone.View.extend({
        el: $("#main"),

        initialize: function(){
        },

        events: {
            "click #edit-post-toggler"          : "closeEditWindow",
            "click #submit_cancel"              : "closeEditWindow",
            "click #btn-post-edit-submit"       : "submit"
        },

        submit: function(){
            this.variables = this.getVariables(this.model);
            this.variables.content = CKEDITOR.instances.editor3.getData();

            if(variables.content.length !== 0) {
                $.ajax({
                    url: '/discourse/post/' + this.variables.post_id,
                    type: 'PUT',
                    data: {
                        raw:        this.variables.content,
//                        id:         this.variables.post_id,
                    },
                    async: false,
                    success: function(data){
                        var data = JSON.parse(data);
                        //console.log(data);
                        if(!data.err_msg) {
                            $("#reply-control").remove();
                            window.location.reload();
                        } else {
                            console.log(data.err_msg);
                        }
                    }
                });
            } else {
                console.log("invalid input");
            }
        },

        closeEditWindow: function(){
            $("#reply-control").remove();
        },

        render: function(){
            this.variables = this.getVariables(this.model);
            var template = _.template( appStorage.templates.postEditTemplate, this.variables );
            this.$el.append( template );
            CKEDITOR.replace( 'editor3' );
            CKEDITOR.instances.editor3.setData(this.model.get("raw"));
        },

        getVariables: function(postModel){
            return variables = {
                post_id:        postModel.get("id"),
                topic_id:       postModel.get("topic_id"),
                post_number:    postModel.get("post_number"),
                userInfo:       appStorage.users.get(postModel.get("user_id")),
            };
        },
    })
});