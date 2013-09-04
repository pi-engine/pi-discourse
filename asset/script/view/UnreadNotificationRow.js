define(["storage/appStorage"],function(appStorage){
    return Backbone.View.extend({
        initialize: function(notification){
            this.$el = $("#unread-notification-container"),
            this.notification = notification;
            this.render();
        },

        render: function(){
            this.notification.data = JSON.parse(this.notification.data)
            var variables = this.notification;
            var template = _.template( appStorage.templates.unreadNotificatioRowTemplate, variables );
            this.$el.append( template );
        },
    })
})