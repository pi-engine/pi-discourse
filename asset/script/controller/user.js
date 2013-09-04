/* << replace >>*/

define([
    "storage/appStorage", 
    "view/UserMain", 
    "view/UserActionRow", 
    "text!template/user-main-template.bhtml", 
    "text!template/user-action-row-template.bhtml", 
], 
function(appStorage, UserMainView, UserActionRowView, template1, template2){
    return {
        run: function(id){
            console.log('running user.js');
            appStorage.templates.userMainTemplate           = $(template1).html();
            appStorage.templates.userActionRowTemplate      = $(template2).html();

            if (PreloadStore.data) {
                appStorage.targetUser = PreloadStore.data.userData;
                appStorage.targetUser.actionCount = PreloadStore.data.userActionCountData;
                PreloadStore.data = null;
            } else {
                console.log('request for data');
                $.ajax({
                    url: '/discourse/u/' + id + '.json',
                    type: 'GET',
                    async: false,
                    success: function(data){
                        var appStorage = require("storage/appStorage");
                        data = JSON.parse(data);

                        appStorage.targetUser = data.userData;
                        appStorage.targetUser.actionCount = data.userActionCountData;

                    }
                });
            }


            $("#main-outlet").empty();
            new UserMainView(appStorage.targetUser);
        }
    };
});