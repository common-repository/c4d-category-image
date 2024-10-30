(function($){
    "use strict";
    $(document).ready(function(){
        $('#c4d_category_image').on( 'click', function(e) {
            e.preventDefault();
            var self = this,
            input = $('#c4d_category_image_input'),
            fileFrame = wp.media.frames.file_frame = wp.media({
                multiple : false
            });
            fileFrame.on('select', function() {
                var attachment = fileFrame.state().get('selection').toJSON();
                var preview = '';
                var url = '';

                for( var i = 0; i < attachment.length; i++ )
                {
                    url += attachment[i].url.replace(c4d_category_image.site_url, '');
                }

                $('#c4d_category_image').addClass('selected').css({
                    'background': 'url('+c4d_category_image.site_url+url+') no-repeat center center',
                    'background-size': 'cover'
                });

                input.val(url);
            });
            fileFrame.open();
            return false;
        });
    });
})(jQuery);

