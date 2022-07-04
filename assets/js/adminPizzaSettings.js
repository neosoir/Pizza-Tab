(function ($) {
  
    const uploadGroupImage = (ele) => {

        // Create a new media frame
        let frame = wp.media({
            multiple: false  // Set to true to allow multiple files to be selected
        });
    
        // When an image is selected in the media frame...
        frame.on( 'select', function() {
            let attachment = frame.state().get('selection').first().toJSON();
            ele.find("input[name$='image]']").val(
                attachment.sizes.hasOwnProperty("thumbnail") 
                ? attachment.sizes.thumbnail.url 
                : attachment.sizes.full.url
            );
            ele.find("input[name$='imageId]']").val( attachment.id );
            ele.find("img").attr( "src", attachment.sizes.full.url );
        });
    
        // Finally, open the modal on click
        frame.open();

    }

    const uploadComponentImage = (el) => {

        // Create a new media frame
        let frame = wp.media({
            multiple: false  // Set to true to allow multiple files to be selected
        });
    
        // When an image is selected in the media frame...
        frame.on( 'select', function() {
            let attachment = frame.state().get('selection').first().toJSON();
            el.find("input[name$='image]']").val(
                attachment.sizes.hasOwnProperty("thumbnail") 
                ? attachment.sizes.thumbnail.url 
                : attachment.sizes.full.url
            );
            el.find("input[name$='imageId]']").val( attachment.id );
            el.find("img").attr( "src", attachment.sizes.full.url );
        });
    
        // Finally, open the modal on click
        frame.open();

    }


    $("#u-pizza-settings").on("click", ".edit-component", function () {
        if ($(this).is(".active")) {
            $(this)
            .closest(".group-component")
            .find(".component-body-collapse")
            .slideUp();
            $(this).closest(".group-component").find(".component-body").show();
            $(this).removeClass("active");
        } else {
            $(this)
            .closest(".group-component")
            .find(".component-body-collapse")
            .slideDown();
            $(this).closest(".group-component").find(".component-body").hide();
            $(this).addClass("active");
        }
    })
    .on('click', '.group-image', function() {
        uploadGroupImage( $(this) );
    })
    .on('click', '.component-image', function() {
        uploadComponentImage( $(this) );
    });

})(jQuery);
