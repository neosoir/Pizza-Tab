(function ($) {

    const pizzaComponents   = U_PRODUCT_DATA.pizza_components;
    let symbol              = U_PRODUCT_DATA.wc_symbol,
        pricePosition       = U_PRODUCT_DATA.price_position,
        wcDecimals          = U_PRODUCT_DATA.decimals || 2;
    const u_wc_price = (price) => {
        //recreating wc_price() function
        switch (pricePosition) {
            case "left":
                return `${symbol}${price.toFixed(wcDecimals)}`;
            case "right":
                return `${price.toFixed(wcDecimals)}${symbol}`;
            case "left_space":
                return `${symbol} ${price.toFixed(wcDecimals)}`;
            case "right_space":
                return `${price.toFixed(wcDecimals)} ${symbol}`;
        }
    };
    
    // Select base components.
    $("#pizza_base_components").selectWoo({
        placeholder: "Consists of components",
    })
    .on("select2:select", function (e) {
        let data = e.params.data;
        let foundComponent = pizzaComponents.find(
            (component) => component.id == data.id
        );
        if ( ! foundComponent ) {
            return;
        }
        const template = wp.template("pizza-component");
        const dataComponent = {
            index: data.id,
            id: {
            name: `pizza_base[${data.id}][id]`,
            value: data.id,
            },
            name: {
            name: `pizza_base[${data.id}][name]`,
            value: foundComponent.name,
            },
            price: {
            name: `pizza_base[${data.id}][price]`,
            value: u_wc_price(parseFloat(foundComponent.price)),
            raw: foundComponent.price,
            },
            weight: {
            name: `pizza_base[${data.id}][weight]`,
            value: foundComponent.weight,
            },

            image: {
            name: `pizza_base[${data.id}][image]`,
            value: foundComponent.image,
            },
            visible: {
            name: `pizza_base[${data.id}][visible]`,
            value: foundComponent.visible,
            },
            required: {
            name: `pizza_base[${data.id}][required]`,
            value: foundComponent.required,
            },
        };
        $("#pizza_consists_block").append( template( dataComponent ) );
    })
    .on("select2:unselect", function (e) {
        let data = e.params.data;
        $(`#pizza_consists_block .group-component[data-id=${data.id}]`).remove();
    });
    // Select extra components.
    $("#pizza_extra_components").selectWoo({
        placeholder: "Extra of components",
    });
    // Principal function to manipulate Groups and components.
    $("#pizza_block_1").on("click", ".edit-component", function () {
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
    });

})(jQuery);
