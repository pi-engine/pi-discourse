/* << replace >>*/

define(function(){
    return Backbone.Model.extend({
        initialize: function(){
        },
        
        like: function() {
            var status = 1;
            return $.ajax({
                url: '/discourse/postAction',
                type: 'POST',
                data: {
                    post_id: this.get('id'),
                    post_action_type_id: 2,
                    status: status
                },
                async: false,
            });
        },
                
        unlike: function() {
            var status = 0;
            return $.ajax({
                url: '/discourse/postAction',
                type: 'POST',
                data: {
                    post_id: this.get('id'),
                    post_action_type_id: 2,
                    status: status
                },
                async: false,
            });
        },
        
        bookmark: function() {
            console.log(this.get('isBookmarked'));
    
            var status;
            if (this.get('isBookmarked') == 1) {
                status = 0;
            } else {
                status = 1;
            }
            
            return $.ajax({
                url: '/discourse/postAction',
                type: 'POST',
                data: {
                    post_id: this.get('id'),
                    post_action_type_id: 1,
                    status: status
                },
                async: false,
            });
        }
        
    })
})

