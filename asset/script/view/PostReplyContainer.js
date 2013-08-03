define([
    'storage/appStorage',
    'view/PostReplyRow',
], 
function(appStorage, PostReplyRowView){
   return Backbone.View.extend({
        initialize: function(){
            this.id = this.model.get("id");
        },

        events: {
        },

        render: function(){
            $("#post-" + this.id + "-body").removeClass('bottom-round');
            var template = _.template( appStorage.templates.postReplyContainerTemplate, {id: this.id,} );
            this.$el.after( template );
        },
        
        displayReplies: function(replyPostModels, post_id){
            _.each(replyPostModels, function(postModel){
                var postReplyRowView = new PostReplyRowView({ 
                    el: $("#post-" + post_id + "-reply-container"),
                    model: postModel
                });
            });
        },
    })
});