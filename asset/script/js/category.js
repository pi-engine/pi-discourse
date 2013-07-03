/* << replace >>*/

define(["dis"], function(dis){
    return {
        TopicListMainView: Backbone.View.extend({
            el: $("#main-outlet"),
            
            events: {
                "click #btn-create-new-topic":  "showCreateTopicWindow"
            },
                    
            initialize: function(){
                this.render();
                var topicListContainerView = new action.TopicListContainerView();
            },
                    
            render: function(){
                var template = _.template( disStorage.templates.topicListMainTemplate );
                this.$el.append( template );
            },
            
            showCreateTopicWindow: function() {
                if($("#reply-control").length === 0) {
//                    var topicCreateView = new action.TopicCreateView();
                    action.topicCreateView = action.topicCreateView || new action.TopicCreateView();
                    action.topicCreateView.render();
                    CKEDITOR.replace( 'editor1' );
                }
            }
        }),
        
        TopicListContainerView: Backbone.View.extend({
            initialize: function(){
                this.$el = $("#d-container"),
                this.render();
                
//                this.listenTo(disStorage.topics, 'add', this.addOne);
//                disStorage.topics.on( "add", this.aaa, topic );
                
                _.each(disStorage.topics.models, function(topic){
                    var topicListTableRowView = new action.TopicListTableRowView({ model: topic });
                });
            },
                    
            render: function(){
                var template = _.template( disStorage.templates.topicListContainerTemplate );
                this.$el.append( template );
            }
        }),
        
//        addOne: function(topic){
//            var topicListTableRowView = new action.TopicListTableRowView({ model: topic });
//        },
        
        TopicListTableRowView: Backbone.View.extend({  
            initialize: function(){
                this.$el = $("#topic-list-table");
                this.render();
                $("#topic-" + this.model.id + " a.icon-star").bind("click", this.star);
                disStorage.topics.get(this.model.id).on( "change", this.update, this );
            },
                    
            render: function(){
                if(!this.model.get('newCreated')){
                    variables = this.getModelVariables(this.model);
                    var template = _.template( disStorage.templates.topicListTableRowTemplate, variables );
                    this.$el.append( template );
                } else {
                    variables = this.getModelVariables(this.model);
                    var template = _.template( disStorage.templates.topicListTableRowTemplate, variables );
                    if($(".icon.icon-pushpin :last").length > 0 ) {
                        $(".icon.icon-pushpin :last").parent().parent().parent().parent().after(template);
                    } else {
                        $(".topic-list-item :first").before(template);
                    }
                }
            },
            
            update: function(){
                starred = disStorage.topics.get(this.model.id).get('starred');
                if(starred === 1) {
                    this.$("#topic-" + this.model.id + " a.icon-star").addClass("starred");
                } else {
                    this.$("#topic-" + this.model.id + " a.icon-star").removeClass("starred");
                }
            },
    
            getModelVariables: function(topicModel) {
                return {
                    title:                  topicModel.get("title"),
                    id:                     topicModel.get("id"), 
                    like_count:             topicModel.get("like_count") + " ",
                    posts_count:            topicModel.get("posts_count"),
                    time_created:           topicModel.get("time_created"),
                    time_from_created:      topicModel.get("time_from_created"),
                    time_last_posted:       topicModel.get("time_last_posted"),
                    time_from_last_posted:  topicModel.get("time_from_last_posted"),
                    category_id:            topicModel.get("category_id"),
                    views:                  topicModel.get("views"),
                    categoryInfo:           disStorage.categories.get(topicModel.get("category_id")),
                    starred:                topicModel.get("starred"),
                    pinned:                 topicModel.get("pinned"),
                    isVisible:              topicModel.get("visible"),
                    closed:                 topicModel.get("closed")
                };
            },
                    
            star: function(){
                topicId = this.parentNode.parentNode.attributes.identity.value;
                if(disStorage.topics.get(topicId).get('starred') === 1) {
                    targetStatus = 0;
                } else {
                    targetStatus = 1;
                }
                $.ajax({
                    url: '/discourse/star/' + topicId,
                    type: 'PUT',
                    contentType : 'application/json',
                    data: {
                        starred: targetStatus
                    },
                    async: false,
                    success: function(data){
                        data = JSON.parse(data);
                        if(!data.err_msg){
                            disStorage.topics.get(data.topic_id).set('starred', data.starred);
                            disStorage.topics.get(data.topic_id).get('starred');
                        } else {
                            console.log(data.err_msg);
                        }
                    }
                });
            }
        }),
        
        TopicCreateView: Backbone.View.extend({
            el: $("#main"),
            
            initialize: function(){
            },
                    
            events: {
                "click #create-topic-toggler"       : "closeCreateWindow",
                "click #submit_cancel"              : "closeCreateWindow",
                "click #btn-topic-create-submit"    : "submit"
            },
            
            closeCreateWindow: function(){
                $("#reply-control").remove();
            },
            
            submit: function(){
                var title = $("#reply-title").val();
                var content = CKEDITOR.instances.editor1.getData();
                if(title.length !== 0 && content.length !== 0) {
                    $.ajax({
                        url: '/discourse/topic',
                        type: 'POST',
                        data: {
                            title: title,
                            content_raw: content,
                            category_id: disStorage.currentCategory.id
                        },
                        async: false,
                        success: function(data){
                            data = JSON.parse(data);
                            if(!data.err_msg) {
                                $("#reply-control").remove();
                                var newTopic = new dis.Topic(data);
                                newTopic.set('newCreated', true);
                                if(newTopic.get('category_id') === disStorage.currentCategory.get('id')) {
                                    disStorage.topics.add(newTopic);
                                    var topicListTableRowView = new action.TopicListTableRowView({ model: newTopic });
                                }
                            } else {
                                console.log(data.err_msg);
                            }
                        }
                    });
                } else {
                    console.log("invalid input");
                }
            },
            
            render: function(){
                var template = _.template( disStorage.templates.topicCreateTemplate );
                this.$el.append( template );
            }
        }),
        
        more: function() {
            console.log('request data');
            $.ajax({
                url: '/discourse/topic/' + disStorage.currentCategory.get('id') + '/' + (window.action.page * 20) + '/20',
                type: 'GET',
                async: false,
                success: function(data){
                    data = JSON.parse(data);
                    if (typeof data.err_msg !== 'undefined') {
                        console.log(data.err_msg);
                        $(window).unbind('scroll');
                        return;
                    }
                    _.each(data, function(topic){
                        var currentTopic = new dis.Topic(topic);
                        disStorage.topics.add(currentTopic);
                        var topicListTableRowView = new action.TopicListTableRowView({ model: currentTopic });
                    });
                    
                    window.action.page++;
                }
            });
        },
        
        run: function(id){
            console.log('running category.js');
            require([
                "text!../template/topic-list-container-template.bhtml", 
                "text!../template/topic-list-table-row-template.bhtml",
                "text!../template/topic-create-template.bhtml",
                "text!../template/topic-list-main-template.bhtml"
            ], 
            function(template1, template2, template3, template4){
                disStorage.templates.topicListContainerTemplate    = $(template1).html();
                disStorage.templates.topicListTableRowTemplate     = $(template2).html();
                disStorage.templates.topicCreateTemplate           = $(template3).html();
                disStorage.templates.topicListMainTemplate         = $(template4).html();

                disStorage.topics = new dis.Topics;

                if(PreloadStore.data) {
                    _.each(PreloadStore.data.topics, function(topic){
                        var currentTopic = new dis.Topic(topic);
                        disStorage.topics.add(currentTopic);
                    });
                    PreloadStore.data = null;
                } else {
                    console.log('request for data');
                    $.ajax({
                        url: '/discourse/c/' + id + '.json',
                        type: 'GET',
                        async: false,
                        success: function(data){
                            data = JSON.parse(data);
                            disStorage.topics.reset();
                            _.each(data, function(topic){
                                var currentTopic = new dis.Topic(topic);
                                disStorage.topics.add(currentTopic);
                            });
                        }
                    });
                }
                
                disStorage.currentCategory = disStorage.categories.get(disStorage.topics.models[0].get('category_id'));
                
                $("#main-outlet").empty();

                var topicListMainView = new action.TopicListMainView();
                
//                console.log($(".topic-list-item :last"));

                window.action.page = 1;
                
                $(window).bind('scroll',function (){ 
                    if($(window).scrollTop()+$(window).height()>=$(document).height()){ 
                        window.action.more();
                    }
                });
            });
        }
    };
});
