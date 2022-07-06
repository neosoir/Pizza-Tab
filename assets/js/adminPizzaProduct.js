(function ($) {

    const pizzaComponets = U_PRODUCT_DATA.pizza_components;
    console.log( pizzaComponets );
    
    // Select base components.
    $("#pizza_base_components").selectWoo({
        placeholder: "Consists of components",
    })
    .on("select2:select", function(e) {
        let data = e.params.data;
        console.log(data);
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
