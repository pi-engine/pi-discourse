/* << replace >>*/

define(["dis", "text!../template/main-header-template.bhtml"], function(dis, header_template){
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
                    $("#site-map-dropdown").css('display','none')
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
                    $("#search-dropdown").css('display','none')
                }
            },
        }),
        
        
        
        run: function(){
            console.log('running header.js');
            disStorage.templates.mainHeaderTemplate = $(header_template).html();
            
            var mainHeaderView = new this.MainHeaderView();
            
//            this.bind();
        }
    };
});