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
            },
            
            render: function(){
//        console.log(this.userData);
//                variables = this.getVariables();
                this.$el.html(_.template( disStorage.templates.userMainTemplate, this.userData));
            },
                    
            getVariables: function(){
                return {};
            },
            
            showItems: function(e){
                console.log(e.currentTarget.value);
//                console.log("/discourse/userAction/" + disStorage.user.id + "/" + e.currentTarget.value + "/1");
                $.ajax({
                    url: "/discourse/userAction/" + disStorage.user.id + "/" + e.currentTarget.value + "/1",
                    type: 'GET',
                    async: false,
                    success: function(data){
                        data = JSON.parse(data);
                        console.log(data);
                        _.each(data, function(action){
                            
                        });
                    }
                });
            },
        }),
        
        UserActionRowView: Backbone.View.extend({
//            el: $("#main-outlet"),
            
            initialize: function(actionData){
                this.actionData = actionData;
                this.render();
            },
            
            render: function(){
//        console.log(this.actionData);
//                variables = this.getVariables();
                this.$el.html(_.template( disStorage.templates.userActionRowTemplate, this.userData));
            },
                    
            getVariables: function(){
                return {};
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
 
                
                var userData = PreloadStore.data.userData;
                userData.actionCount = PreloadStore.data.userActionCountData;
//                console.log(userData);
                
                
                $("#main-outlet").empty();
                new action.UserMainView(userData);
            });
        },
    }
});