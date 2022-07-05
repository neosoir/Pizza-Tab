(function ($) {
  
    // Functions to add Principal image
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

    // Functions to add Secondary image
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

    // Principal function to manipulate Groups and components.
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
    // Add group image
    .on('click', '.group-image', function() {
        uploadGroupImage( $(this) );
    })
    // Add components images
    .on('click', '.component-image', function() {
        uploadComponentImage( $(this) );
    })
    // Add new group
    .on("click", ".add-group", function () {
        let index = $(".wc-metabox").length + 1;
        const template = wp.template("pizza-group");
        const dataGroup = {
            index: index,
            id: {
                name: `pizza_data[${index}][id]`,
            },
            name: {
                name: `pizza_data[${index}][group_name]`,
            },
            image: {
                name: `pizza_data[${index}][image]`,
                value: U_PIZZA_DATA.url + "images/placeholder.svg",
            },
            imageId: {
                name: `pizza_data[${index}][imageId]`,
            },
        };
        $(".wc-metaboxes").append(template(dataGroup));
    })
    // Remove group
    .on("click", ".remove-group", function () {
        const answer = confirm("Do you want to remove group?");

        if (answer) {
            let groupContainer = $(this).closest(".wc-metabox");

            $(groupContainer).remove();
        }
        $(".wc-metabox").each(function (index, el) {
            let _index = index + 1;

            $(this)
            .find("input, textarea")
            .prop("name", function (i, val) {
                let fieldName = val.replace(
                /pizza_data[[0-9]+\]/g,
                "pizza_data[" + _index + "]"
                );

                return fieldName;
            });
            $(this).find('h3 > input[name$="[id]"]').val(_index);
            $(this).closest(".wc-metabox").attr("data-index", _index);
        });

    })
    // Add Component
    .on("click", ".add-component", function () {
        let indexGroup = $(this).closest(".wc-metabox").attr("data-index");
        // let indexComponent = $(".group-component").length + 1;
        let indexComponent =
            Math.max.apply(
            null,
            $("#u-pizza-settings")
                .find(".remove-component")
                .map(function () {
                return $(this).attr("data-id");
                })
                .get()
            ) + 1;
        console.log(indexComponent);
        const template = wp.template("pizza-component");
        const dataComponent = {

            index: indexComponent,
            id: {
                name: `pizza_data[${indexGroup}][components][${indexComponent}][id]`,
            },
            name: {
                name: `pizza_data[${indexGroup}][components][${indexComponent}][name]`,
                value: "New component",
            },
            price: {
                name: `pizza_data[${indexGroup}][components][${indexComponent}][price]`,
                value: 0,
            },
            weight: {
                name: `pizza_data[${indexGroup}][components][${indexComponent}][weight]`,
            },
            description: {
                name: `pizza_data[${indexGroup}][components][${indexComponent}][description]`,
            },
            image: {
                name: `pizza_data[${indexGroup}][components][${indexComponent}][image]`,
                value: U_PIZZA_DATA.url + "images/placeholder.svg",
            },
                imageId: {
                name: `pizza_data[${indexGroup}][components][${indexComponent}][imageId]`,
            },
            meta: {
                name: `pizza_data[${indexGroup}][components][${indexComponent}][meta]`,
            },

        };
        $(this)
            .parent()
            .siblings(".group-components")
            .append(template(dataComponent));
    })
    // Remove Component
    .on("click", ".remove-component", function () {
        const answer = confirm("Do you want remove component?");

        if (answer) {
            let componentContainer = $(this).closest(".group-component");
            $(componentContainer).remove();
        }

        $(".group-component").each(function (index, el) {
            let _index = index + 1;
            $(this)
                .find("input, textarea")
                .prop("name", function (i, val) {
                let fieldName = val.replace(
                    /\[components][[0-9]+\]/g,
                    "[components][" + _index + "]"
                );
                return fieldName;
            });
            $(this).find('input[name$="[id]"]').val(_index);
            $(this).find(".remove-component").attr("data-id", _index);
        });
    });

})(jQuery);
