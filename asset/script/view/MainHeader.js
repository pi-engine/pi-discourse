/* << replace >>*/

define([
    'storage/appStorage', 
    'view/UnreadNotificationRow'
],
function(appStorage, UnreadNotificationRowView){
    return Backbone.View.extend({
        el: $("#main-header"),

        events: {
            'click #user-notifications' : 'showNotification',
            'click #site-map'           : 'showSiteMap',
            'click #search'             : 'showSearchBox'
        },

        initialize: function(){
            this.render();
        },

        render: function(){
            var variables = this.prepareVariables();
            var template = _.template( appStorage.templates.mainHeaderTemplate, variables );
            this.$el.append( template );
        },

        prepareVariables: function(){
            return {
                user:           appStorage.user,
                all_categories: appStorage.categories,
                appStorage:     appStorage
            };
        },

        showNotification: function(){
            var icon = $("#user-notifications");
            if (!icon.hasClass("active")) {
                _.each($(".dropdown"),function(dom){
                    $(dom).removeClass("active");
                });
                _.each($(".d-dropdown"),function(dom){
                    $(dom).css('display','none');
                });
                require('controller/header').getNotification();
                icon.addClass("active");
                $("#notifications-dropdown").css('display','block');

            } else {
                icon.removeClass("active");
                $("#notifications-dropdown").css('display','none');
            }

        },

        showSiteMap: function(){
            var icon = $("#site-map");
            if (!icon.hasClass("active")) {
                _.each($(".dropdown"),function(dom){
                    $(dom).removeClass("active");
                });
                _.each($(".d-dropdown"),function(dom){
                    $(dom).css('display','none');
                });
                icon.addClass("active");
                $("#site-map-dropdown").css('display','block');
            } else {
                icon.removeClass("active");
                $("#site-map-dropdown").css('display','none');
            }
        },

        showSearchBox: function(){
            var icon = $("#search");
            if (!icon.hasClass("active")) {
                _.each($(".dropdown"),function(dom){
                    $(dom).removeClass("active");
                });
                _.each($(".d-dropdown"),function(dom){
                    $(dom).css('display','none');
                });
                icon.addClass("active");
                $("#search-dropdown").css('display','block');
            } else {
                icon.removeClass("active");
                $("#search-dropdown").css('display','none');
            }
        },

        renderNotification: function(data){
            $("#unread-notification-container").empty();
            if(!data.err_msg){
                if (data.count > 0) {
                    $("#unread-notifications-count").css('display', 'block');
                    $("#unread-notifications-count").html(data.count);
                    _.each(data.notifications, function(notification){
                        var tmp_view = new UnreadNotificationRowView(notification);
                    });
                } else {
                    $("#unread-notifications-count").css('display', 'none');
                    $("#unread-notifications-count").html(0);
                    $("#unread-notification-container").append($("<li>no unread notification</li>"))
                }
            } else {
                console.log(data);
            }
        },
    })
})



