define(['storage/appStorage'], function(appStorage){
    return Backbone.View.extend({
        initialize: function(actionData){
            this.actionData = actionData;
            this.render();
        },

        render: function(){
            $("#user-stream").append(_.template( appStorage.templates.userActionRowTemplate, this.actionData));
        },
    })
});