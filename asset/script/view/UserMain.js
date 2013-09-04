define([
    'storage/appStorage', 
    "view/UserActionRow", 
], function(appStorage, UserActionRowView){
    return Backbone.View.extend({
        el: $("#main-outlet"),

        events: {
            "click li.filter": "showItems",
//                "scroll window"                  : "more"
        },

        initialize: function(userData){
            this.userData = userData;
            this.render();
            this.showItems(5);
        },

        render: function(){
            this.$el.html(_.template( appStorage.templates.userMainTemplate, this.userData));
        },

        getVariables: function(){
            return {};
        },

        showItems: function(e){
            var actionType;
            if (typeof e !== 'object') {
                actionType = e;
            } else {
                actionType = e.currentTarget.value;
            }
            _.each($(".filter"),function(li){
                $(li).removeClass('active');
            });
            $(".filter[value=" + actionType + "]").addClass('active');
            $.ajax({
                url: "/discourse/userAction/" + appStorage.targetUser.id + "/" + actionType + "/1",
                type: 'GET',
                async: false,
                success: function(data){
                    var UserActionRowView = require('view/UserActionRow');
                    data = JSON.parse(data);
//                        console.log(data);
                    $("#user-stream").empty();
                    _.each(data, function(userAction){
                        var userActionRow = new UserActionRowView(userAction);
                    });
                }
            });
        },
    })
});