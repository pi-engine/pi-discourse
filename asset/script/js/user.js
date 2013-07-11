/* << replace >>*/

define(["dis"], function(dis){
    return {
        UserMainView: Backbone.View.extend({
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
                this.$el.html(_.template( disStorage.templates.userMainTemplate, this.userData));
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
                    url: "/discourse/userAction/" + disStorage.targetUser.id + "/" + actionType + "/1",
                    type: 'GET',
                    async: false,
                    success: function(data){
                        data = JSON.parse(data);
//                        console.log(data);
                        $("#user-stream").empty();
                        _.each(data, function(userAction){
                            var userActionRow = new action.UserActionRowView(userAction);
                        });
                    }
                });
            },
        }),
        
        UserActionRowView: Backbone.View.extend({
            initialize: function(actionData){
                this.actionData = actionData;
                this.render();
            },
            
            render: function(){
                $("#user-stream").append(_.template( disStorage.templates.userActionRowTemplate, this.actionData));
            },
        }),

        run: function(id){
            console.log('running user.js');
            require([
                "text!../template/user-main-template.bhtml", 
                "text!../template/user-action-row-template.bhtml",
//                "text!../template/post-create-template.bhtml",
//                "text!../template/post-reply-container-template.bhtml",
//                "text!../template/post-reply-row-template.bhtml",
//                "text!../template/post-reference-template.bhtml"
            ], 
            function(template1, template2, template3, template4, template5, template6 ){
                disStorage.templates.userMainTemplate           = $(template1).html();
                disStorage.templates.userActionRowTemplate      = $(template2).html();
//                disStorage.templates.postCreateTemplate         = $(template3).html();
//                disStorage.templates.postReplyContainerTemplate = $(template4).html();
//                disStorage.templates.postReplyRowTemplate       = $(template5).html();
//                disStorage.templates.postReferenceTemplate      = $(template6).html();
 

                disStorage.targetUser = PreloadStore.data.userData;
                disStorage.targetUser.actionCount = PreloadStore.data.userActionCountData;

                
                
                $("#main-outlet").empty();
                new action.UserMainView(disStorage.targetUser);
            });
        },
    }
});