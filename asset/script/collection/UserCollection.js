/* << replace >>*/

define(['model/User'],function(UserModel){
    return Backbone.Collection.extend({
        model: UserModel
    })
})

