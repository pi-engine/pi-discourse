define(['storage/appStorage'], function(appStorage){
   return Backbone.View.extend({
        initialize: function(){
        },

        events: {
        },

        render: function(){
            var variables = this.getVariables(this.model);
//                console.log(this.id);

            var template = _.template( appStorage.templates.postReferenceTemplate, variables );
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
                userInfo:                   appStorage.users.get(postModel.get("user_id"))
            };
        }
    })
});