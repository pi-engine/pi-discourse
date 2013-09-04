/* << replace >>*/

define([
    "storage/appStorage", 
    "view/MainHeader",
    "text!template/main-header-template.bhtml",
    "text!template/main-header-unread-notification-row-template.bhtml",
], 
function(appStorage, MainHeaderView,
    header_template, unread_notification_row_template){
    return {
        mainHeaderView: null,
        
        getNotification: function(){
            $.ajax({
                url: '/discourse/notification/' + appStorage.user.id + '/1/1',
                type: 'GET',
                async: false,
                success: function(data){
                    data = JSON.parse(data);
                    require('controller/header').mainHeaderView.renderNotification(data);
                }
            });
        },
        
        run: function(){
            console.log('running header.js');
            appStorage.templates.mainHeaderTemplate = $(header_template).html();
            appStorage.templates.unreadNotificatioRowTemplate = $(unread_notification_row_template).html();
            appStorage.notificationCount = PreloadStore.data.notificationCount;
            this.mainHeaderView = new MainHeaderView();
//            this.bind();
        }
    };
});