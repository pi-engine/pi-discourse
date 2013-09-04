define([
    "storage/appStorage", 
    "view/CategoryListContainer",
    "view/CategoryList",
    "view/CategoryListTopic",
    "view/CategoryCreate",
    "collection/TopicCollection"
],
function(appStorage, CategoryListContainerView, CategoryListView, 
    CategoryListTopicView, CategoryCreateView, 
    TopicCollection){
    return Backbone.View.extend({
        el: $("#main-outlet"),

        events: {
            "click #btn-create-new-category":  "showCreateCategoryWindow"
        },

        initialize: function(){
            this.render();
            var categoryListContainerView = new CategoryListContainerView();

            var i = 0;
            _.each(appStorage.categoryTopics, function(topics){
                var categoryModel = appStorage.categories.get(topics.category_id);
                categoryModel.set('topics', new TopicCollection(topics.topics));
                var categoryListViewFirst = new CategoryListView({ el: $("#category_container_first") });
                var categoryListView = new CategoryListView({ el: $("#category_container") });
                if(i % 2 === 0) {
                    categoryListViewFirst.render(categoryModel);
                } else {
                    categoryListView.render(categoryModel);
                }
                i++;

                _.each(categoryModel.get("topics").models, function(topicModel){
                    var categoryListTopicView = new CategoryListTopicView({ el: $("#category_topic_table_" + topicModel.get('category_id')) });
                    categoryListTopicView.render(topicModel);

                });
            });
        },

        render: function(){
            var template = _.template( appStorage.templates.categoryListMainTemplate );
            this.$el.append( template );
        },

        showCreateCategoryWindow: function(){
            var categoryListController = require('controller/categoryList');
            if($("#discourse-modal").length === 0) {
                $("body").append('<div id="modal" class="modal-backdrop  in"></div>');
                categoryListController.categoryCreateView = categoryListController.categoryCreateView || new CategoryCreateView();
                categoryListController.categoryCreateView.render();
            }
        }
    })
})