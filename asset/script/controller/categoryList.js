/* << replace >>*/

define([
    "storage/appStorage",
    "view/CategoryListMain",
    "view/CategoryListContainer",
    "view/CategoryListTopic",
    "view/CategoryList",
    "view/CategoryCreate",
    "text!template/category-list-container-template.bhtml", 
    "text!template/category-list-template.bhtml",
    "text!template/category-list-topic-template.bhtml",
    "text!template/category-list-main-template.bhtml",
    "text!template/category-create-template.bhtml",
], 
function(appStorage, CategoryListMainView, CategoryListContainerView, 
    CategoryListTopicView, CategoryListView, CategoryCreateView, 
    template1, template2, template3, template4, template5){
    return {
        categoryCreateView: null,
        
        createCategory: function(name, background_color, description){
            $.ajax({
                url: '/discourse/category',
                type: 'POST',
                data: {
                    name: name,
                    color: background_color,
                    slug: description
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
        },
        
        run: function(){
            console.log('running categoryList.js');
            appStorage.templates.categoryListContainerTemplate = $(template1).html();
            appStorage.templates.categoryListTemplate          = $(template2).html();
            appStorage.templates.categoryListTopicTemplate     = $(template3).html();
            appStorage.templates.categoryListMainTemplate      = $(template4).html();
            appStorage.templates.categoryCreateTemplate        = $(template5).html();

            if(PreloadStore.data) {
                appStorage.categoryTopics = PreloadStore.data.categoryTopics;
                PreloadStore.data = null;
            } else {
                console.log('request for data');
                $.ajax({
                    url: '/discourse/c.json',
                    type: 'GET',
                    async: false,
                    success: function(data){
                        data = JSON.parse(data);
                        require('storage/appStorage').categoryTopics = data;
                    }
                });
            }

            $("#main-outlet").empty();
            var categoryListMainView = new CategoryListMainView();
            
        }
    }
})