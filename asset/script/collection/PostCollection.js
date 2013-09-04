/* << replace >>*/

define(['model/Post'],function(PostModel){
    return Backbone.Collection.extend({
        model: PostModel
    })
})