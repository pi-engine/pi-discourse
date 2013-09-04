define([
    "storage/appStorage",
    "model/Topic", 
    "collection/TopicCollection", 
    "view/TopicListMain", 
    "view/TopicListContainer", 
    "view/TopicListTableRow", 
    "view/TopicCreate", 
    "text!template/topic-list-container-template.bhtml", 
    "text!template/topic-list-table-row-template.bhtml",
    "text!template/topic-create-template.bhtml",
    "text!template/topic-list-main-template.bhtml"
],
function(appStorage, Topic, TopicCollection, 
    TopicListMainView, TopicListContainerView, 
    TopicListTableRowView, TopicCreateView, 
    template1, template2, template3, template4){
    return {
        page: null,
        
        topicCreateView: null,
        
        more: function() {
            console.log('request data');
            $.ajax({
                url: '/discourse/topic/' + appStorage.currentCategory.get('id') + '/' + (this.page * 20) + '/20',
                type: 'GET',
                async: false,
                success: function(data){
                    var appStorage              = require('storage/appStorage');
                    var Topic                   = require('model/Topic');
                    var TopicListTableRowView   = require('view/TopicListTableRow');
                    data = JSON.parse(data);
                    if (typeof data.err_msg !== 'undefined') {
                        console.log(data.err_msg);
                        $(window).unbind('scroll');
                        return;
                    }
                    _.each(data, function(topic){
                        var currentTopic = new Topic(topic);
                        appStorage.topics.add(currentTopic);
                        var topicListTableRowView = new TopicListTableRowView({ model: currentTopic });
                    });
                    
                    require('controller/category').page++;
                }
            });
        },
        
        star: function(targetStatus){
            $.ajax({
                url: '/discourse/star/' + topicId,
                type: 'PUT',
                contentType : 'application/json',
                data: {
                    starred: targetStatus
                },
                async: false,
                success: function(data){
                    var appStorage = require('storage/appStorage')
                    data = JSON.parse(data);
                    if (!data.err_msg) {
                        appStorage.topics.get(data.topic_id).set('starred', data.starred);
                        appStorage.topics.get(data.topic_id).get('starred');
                    } else {
                        console.log(data.err_msg);
                    }
                }
            });
        },
        
        createTopic: function(title, content, categoryId){
            $.ajax({
                url: '/discourse/topic',
                type: 'POST',
                data: {
                    title:          title,
                    content_raw:    content,
                    category_id:    categoryId
                },
                async: false,
                success: function(data){
                    var appStorage              = require('storage/appStorage');
                    var Topic                   = require('model/Topic');
                    var TopicListTableRowView   = require('view/TopicListTableRow');
                    data = JSON.parse(data);
                    if(!data.err_msg) {
                        $("#reply-control").remove();
                        var newTopic = new Topic(data);
                        newTopic.set('newCreated', true);
                        if(newTopic.get('category_id') === appStorage.currentCategory.get('id')) {
                            appStorage.topics.add(newTopic);
                            var topicListTableRowView = new TopicListTableRowView({ model: newTopic });
                        }
                    } else {
                        console.log(data.err_msg);
                    }
                }
            });
        },
        
        run: function(id){
            console.log('running category.js');
            appStorage.templates.topicListContainerTemplate    = $(template1).html();
            appStorage.templates.topicListTableRowTemplate     = $(template2).html();
            appStorage.templates.topicCreateTemplate           = $(template3).html();
            appStorage.templates.topicListMainTemplate         = $(template4).html();

            appStorage.topics = new TopicCollection;

            if(PreloadStore.data) {
                _.each(PreloadStore.data.topics, function(topic){
                    var currentTopic = new Topic(topic);
                    appStorage.topics.add(currentTopic);
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
                        var appStorage = require('storage/appStorage');
                        var Topic = require('model/Topic');
                        appStorage.topics.reset();
                        _.each(data, function(topic){
                            var currentTopic = new Topic(topic);
                            appStorage.topics.add(currentTopic);
                        });
                    }
                });
            }

            appStorage.currentCategory = appStorage.categories.get(appStorage.topics.models[0].get('category_id'));

            $("#main-outlet").empty();

            var topicListMainView = new TopicListMainView();

//                console.log($(".topic-list-item :last"));

            this.page = 1;

            $(window).unbind('scroll');
            $(window).bind('scroll',function (){ 
                if($(window).scrollTop()+$(window).height()>=$(document).height()){ 
                    require('controller/category').more();
                }
            });
        }
    }
})