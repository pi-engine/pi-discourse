/* << replace >>*/

define([
    "dis", 
    "text!../template/main-header-template.bhtml",
    "text!../template/main-header-unread-notification-row-template.bhtml",
], 
function(dis, header_template, unread_notification_row_template){
    return {
        MainHeaderView: Backbone.View.extend({
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
                variables = this.prepareVariables();
                var template = _.template( disStorage.templates.mainHeaderTemplate, variables );
                this.$el.append( template );
            },

            prepareVariables: function(){
                return {
                    user:           disStorage.user,
                    all_categories: disStorage.categories
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
                    this.renderNotification();
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
            
            renderNotification: function(){
                $.ajax({
                    url: '/discourse/notification/' + disStorage.user.id + '/1/1',
                    type: 'GET',
                    async: false,
                    success: function(data){
                        data = JSON.parse(data);
                        $("#unread-notification-container").empty();
                        if(!data.err_msg){
                            if (data.count > 0) {
                                $("#unread-notifications-count").css('display', 'block');
                                $("#unread-notifications-count").html(data.count);
                                require(['header'], function(header){
                                    _.each(data.notifications, function(notification){
                                        var tmp_view = new header.UnreadNotificationRowView(notification);
                                    });
                                });
                            } else {
                                $("#unread-notifications-count").css('display', 'none');
                                $("#unread-notifications-count").html(0);
                                $("#unread-notification-container").append($("<li>no unread notification</li>"))
                            }
                        }
                    }
                });
            },
        }), 
        
        UnreadNotificationRowView: Backbone.View.extend({
            initialize: function(notification){
                this.$el = $("#unread-notification-container"),
                this.notification = notification;
                this.render();
            },

            render: function(){
                this.notification.data = JSON.parse(this.notification.data)
                variables = this.notification;
                var template = _.template( disStorage.templates.unreadNotificatioRowTemplate, variables );
                this.$el.append( template );
            },
        }),
        
        
        run: function(){
            console.log('running header.js');
            disStorage.templates.mainHeaderTemplate = $(header_template).html();
            disStorage.templates.unreadNotificatioRowTemplate = $(unread_notification_row_template).html();
            disStorage.notificationCount = PreloadStore.data.notificationCount;
            var mainHeaderView = new this.MainHeaderView();
//            this.bind();
        }
    };
});