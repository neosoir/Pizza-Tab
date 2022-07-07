(function ($) {
    const dataComponents = JSON.parse(
        $('.pizza_components_wrapper').attr('data-pizza')
    );

    let initialPrice = $('.pizza_components_wrapper').attr('data-price');
    console.log(initialPrice);

    if ( ( $('form.variations_form').length === 0 ) && ($('form.cart').length > 0 )  ) {

        $(".component-buttons").on("click", ".plus, .minus", function () {
            calculate();
        });

        //Calculation process
        const calculate = () => {
            let sum = parseFloat( initialPrice );
            $('#add-component .pizza-components-item').each( function() {
                let val = $(this).find('.component-qty').val();
                let componentId = $(this)
                    .find('.component-buttons')
                    .attr('data-food-item');
                    let componentObject = Object.values( dataComponents.pizza.extra ).find(
                        (component) => component.id === componentId
                    );
                    if ( componentObject !== undefined ) {
                        sum += parseFloat( componentObject.price ) * parseInt( val )
                    }
            });
            console.log(sum);
            refreshPriceHtml(sum);
        }
        //Refresh prices
        const refreshPriceHtml = (summ) => {
            let priceContainer = $(".product").find(".price");
            //let priceFloorContainer = $(document.body).find(".floors-total-price");

            priceContainer.html(u_wc_price(summ));
            //if (floorsEnabled || sidesEnabled) {
            //priceFloorContainer.html(u_wc_price(summ));
            //}
        };


    }

    console.log(dataComponents);
})(jQuery);