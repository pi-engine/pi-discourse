define([
    "storage/appStorage",
    "view/TopicListTableRow"
],
function(appStorage, TopicListTableRowView){
    return Backbone.View.extend({
        initialize: function(){
            this.$el = $("#d-container"),
            this.render();

//                this.listenTo(appStorage.topics, 'add', this.addOne);
//                appStorage.topics.on( "add", this.aaa, topic );

            _.each(appStorage.topics.models, function(topic){
                var topicListTableRowView = new TopicListTableRowView({ model: topic });
            });
        },

        render: function(){
            var template = _.template( appStorage.templates.topicListContainerTemplate );
            this.$el.append( template );
        }
    })
})