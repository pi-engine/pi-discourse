/* << replace >>*/

define(function(){
    return Backbone.Model.extend({
        initialize: function(user){
//            console.log(user);
            if('undefined' === typeof user.name) {
                user.name = 'Anonymous';
            }
        },
        defaults: {
        }
    })
})