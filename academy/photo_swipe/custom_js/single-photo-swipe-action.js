var openPhotoSwipeSingle = function(item) {
    var pswpElement = document.querySelectorAll('.pswp')[0];

    
    // define options (if needed)
    var options = {
			 // history & focus options are disabled on CodePen        
      	history: false,
      	focus: false,

        showAnimationDuration: 0,
        hideAnimationDuration: 0
        
    };
    
    var gallery = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options);
    gallery.init();
};

// document.getElementById('btn').onclick = openPhotoSwipeSingle;

$(document).on('click','.test-solve-box', function() {
    $img_url = $(this).data('url');
    // console.log($img_url, 'asdfasdfasdfasdfasdfasdfd');
    var img = new Image();
    img.src = $img_url;
    img.onload = function() {
        $size = [this.width*0.5, this.height*0.5];
        $item = [{
            src: $img_url,
            w: $size[0],
            h: $size[1]
        }];
        openPhotoSwipe($item);
    }
    // openPhotoSwipeSingle($img_url);
});
