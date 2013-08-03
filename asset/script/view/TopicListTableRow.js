define(["storage/appStorage"],function(appStorage){
    return Backbone.View.extend({  
        initialize: function(){
            this.$el = $("#topic-list-table");
            this.render();
            $("#topic-" + this.model.id + " a.icon-star").bind("click", this.star);
            appStorage.topics.get(this.model.id).on( "change", this.update, this );
        },

        render: function(){
            if(!this.model.get('newCreated')){
                variables = this.getModelVariables(this.model);
                var template = _.template( appStorage.templates.topicListTableRowTemplate, variables );
                this.$el.append( template );
            } else {
                variables = this.getModelVariables(this.model);
                var template = _.template( appStorage.templates.topicListTableRowTemplate, variables );
                if($(".icon.icon-pushpin :last").length > 0 ) {
                    $(".icon.icon-pushpin :last").parent().parent().parent().parent().after(template);
                } else {
                    $(".topic-list-item :first").before(template);
                }
            }
        },

        update: function(){
            starred = appStorage.topics.get(this.model.id).get('starred');
            if(starred === 1) {
                this.$("#topic-" + this.model.id + " a.icon-star").addClass("starred");
            } else {
                this.$("#topic-" + this.model.id + " a.icon-star").removeClass("starred");
            }
        },

        getModelVariables: function(topicModel) {
            return {
                title:                  topicModel.get("title"),
                id:                     topicModel.get("id"), 
                like_count:             topicModel.get("like_count") + " ",
                posts_count:            topicModel.get("posts_count"),
                time_created:           topicModel.get("time_created"),
                time_from_created:      topicModel.get("time_from_created"),
                time_last_posted:       topicModel.get("time_last_posted"),
                time_from_last_posted:  topicModel.get("time_from_last_posted"),
                category_id:            topicModel.get("category_id"),
                views:                  topicModel.get("views"),
                categoryInfo:           appStorage.categories.get(topicModel.get("category_id")),
                starred:                topicModel.get("starred"),
                pinned:                 topicModel.get("pinned"),
                isVisible:              topicModel.get("visible"),
                closed:                 topicModel.get("closed")
            };
        },

        star: function(){
            var categoryController = require('controller/category');
            topicId = this.parentNode.parentNode.attributes.identity.value;
            if(appStorage.topics.get(topicId).get('starred') === 1) {
                targetStatus = 0;
            } else {
                targetStatus = 1;
            }
            categoryController.star(targetStatus);
        }
    })
})