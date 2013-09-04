/* << replace >>*/

define(['model/Category'],function(CategoryModel){
    return Backbone.Collection.extend({
        model: CategoryModel
    })
})