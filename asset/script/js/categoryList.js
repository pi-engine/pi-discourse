/* << replace >>*/

define(["dis"], function(dis, templates){
    return {
        CategoryListMainView: Backbone.View.extend({
            el: $("#main-outlet"),
            
            events: {
                "click #btn-create-new-category":  "showCreateCategoryWindow"
            },
            
            initialize: function(){
                this.render();
                var categoryListContainerView = new action.CategoryListContainerView();
                
                var i = 0;
                _.each(disStorage.categoryTopics, function(topics){
                    var categoryModel = categories.get(topics.category_id);
                    categoryModel.set('topics', new dis.Topics(topics.topics));
                    var categoryListViewFirst = new action.CategoryListView({ el: $("#category_container_first") });
                    var categoryListView = new action.CategoryListView({ el: $("#category_container") });
                    if(i % 2 === 0) {
                        categoryListViewFirst.render(categoryModel);
                    } else {
                        categoryListView.render(categoryModel);
                    }
                    i++;

                    _.each(categoryModel.get("topics").models, function(topicModel){
                        var categoryListTopicView = new action.CategoryListTopicView({ el: $("#category_topic_table_" + topicModel.get('category_id')) });
                        categoryListTopicView.render(topicModel);

                    });
                });
            },
            
            render: function(){
                var template = _.template( disStorage.templates.categoryListMainTemplate );
                this.$el.append( template );
            },
            
            showCreateCategoryWindow: function(){
                if($("#discourse-modal").length === 0) {
                    $("body").append('<div id="modal" class="modal-backdrop  in"></div>');
                    action.categoryCreateView = action.categoryCreateView || new action.CategoryCreateView();
                    action.categoryCreateView.render();
                }
            }
        }),
        
        CategoryListContainerView: Backbone.View.extend({
            initialize: function(){
                this.$el = $("#d-container");
                this.render();
            },
                    
            render: function(){
                var template = _.template( disStorage.templates.categoryListContainerTemplate );
                this.$el.append( template );
            }
        }),
        
        CategoryListView: Backbone.View.extend({
            initialize: function(){
            },
            render: function(categoryModel){
                variables = {
                    color:  categoryModel.get("color"),
                    id:     categoryModel.get("id"),
                    name:   categoryModel.get("name"),
                    slug:   categoryModel.get("slug")
                };
                var template = _.template(disStorage.templates.categoryListTemplate, variables);
                this.$el.append( template );
            }
        }),
        
        CategoryListTopicView: Backbone.View.extend({
            initialize: function(){
            },
            render: function(topicModel){
                variables = {
                    id:             topicModel.get("id"),
                    title:          topicModel.get("title"),
                    category_id:    topicModel.get("category_id"),
                    posts_count:    topicModel.get("posts_count"),
                    time_created:   'created at ' + topicModel.get("time_created")
                };
                var template = _.template(disStorage.templates.categoryListTopicTemplate, variables);
                this.$el.append( template );
            }
        }),
        
        CategoryCreateView: Backbone.View.extend({
            el: $("#main-outlet"),
            
            initialize: function(){
            },
                    
            events: {
                "click #close-create-window"        : "closeCreateWindow",
                "click #btn-category-create-submit" : "submit"
            },
            
            closeCreateWindow: function(){
                $("#modal").remove();
                $("#discourse-modal").remove();
            },
            
            submit: function(){
                var category_name               = $("#new-category-name").val();
                var category_description        = $("#new-category-description").val();
                var category_background_color   = $("#new-category-background-color").val();
                var category_foreground_color   = $("#new-category-foreground-color").val();

                if(category_name.length !== 0 
                    && category_description.length !== 0
                    && category_background_color.match('^[0-9a-fA-F]{6}$') !== null
                    && category_foreground_color.match('^[0-9a-fA-F]{6}$') !== null
                ) {
                    $.ajax({
                        url: '/discourse/category',
                        type: 'POST',
                        data: {
                            name: category_name,
                            color: category_background_color,
                            slug: category_description,
                        },
                        async: false,
                        success: function(data){
                            data = JSON.parse(data);
                            if(!data.err_msg) {
                                window.location.reload();
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
                var template = _.template( disStorage.templates.categoryCreateTemplate );
                this.$el.append( template );
            }
        }),
        
        run: function(){
            console.log('running categoryList.js');
            require([
                "text!../template/category-list-container-template.bhtml", 
                "text!../template/category-list-template.bhtml",
                "text!../template/category-list-topic-template.bhtml",
                "text!../template/category-list-main-template.bhtml",
                "text!../template/category-create-template.bhtml",
            ], 
            function(template1, template2, template3, template4, template5){
                disStorage.templates.categoryListContainerTemplate = $(template1).html();
                disStorage.templates.categoryListTemplate          = $(template2).html();
                disStorage.templates.categoryListTopicTemplate     = $(template3).html();
                disStorage.templates.categoryListMainTemplate      = $(template4).html();
                disStorage.templates.categoryCreateTemplate        = $(template5).html();
                
                if(PreloadStore.data) {
                    disStorage.categoryTopics = PreloadStore.data.categoryTopics;
                    PreloadStore.data = null;
                } else {
                    console.log('request for data');
                    $.ajax({
                        url: '/discourse/c.json',
                        type: 'GET',
                        async: false,
                        success: function(data){
                            data = JSON.parse(data);
                            disStorage.categoryTopics = data;
                        }
                    });
                }
                
                $("#main-outlet").empty();
                var categoryListMainView = new action.CategoryListMainView();
            });
        },
    };
});
