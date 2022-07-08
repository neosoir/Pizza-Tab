(function ($) {

    // Formate the product data in product container.
    const dataComponents = JSON.parse(
        $('.pizza_components_wrapper').attr('data-pizza')
    );

    // Get the product price.
    let initialPrice    = $('.pizza_components_wrapper').attr('data-price');
    const inputBase     = $('input[name=u-pizza-base]');

    //
    if ( ( $('form.variations_form').length === 0 ) && ($('form.cart').length > 0 )  ) {

        // Calculate the product price depending the components quantity.
        $(".component-buttons").on("click", ".plus, .minus", function () {
            calculate();
        });

        // Remove component (is is avalible).
        $('#remove-component .pizza-components-item').on('click', function () {
            calculateComponentsRemove( $(this) );
        });

        // handle fancybox for floors
        $("#pizza-floor-button").on("click", function (e) {
            e.preventDefault();
            $.fancybox.open({
                src: "#u-pizza-floors-fancybox",
                type: "inline",
                touch: false,
                opts: {
                    afterShow: function (instance, current) {
                    // console.info("done!");
                    let floorFancy = $(document.body).find(
                        "#u-pizza-floors-fancybox"
                    );
                        if (window.matchMedia("(min-width: 768px)").matches) {
                            if (floorFancy.height() > window.innerHeight - 100) {
                                floorFancy.css("border-width", "0");
                                $(".pizza-floors-block", floorFancy).slimScroll({
                                    height: window.innerHeight - 100,
                                    railVisible: true,
                                    alwaysVisible: true,
                                    size: "6px",
                                    color: "#FF0329",
                                    railColor: "#EAEAEA",
                                    railOpacity: 1,
                                    wheelStep: 5,
                                });
                            }
                        } 
                        else {
                            $(".pizza-floors-block", floorFancy).slick({
                            slidesToShow: 4,
                            infinite: false,
                            arrows: false,
                            responsive: [
                                {
                                breakpoint: 500,
                                settings: {
                                    slidesToShow: 3,
                                },
                                },
                                {
                                breakpoint: 380,
                                settings: {
                                    slidesToShow: 2,
                                },
                                },
                            ],
                            });
                        }
                    },
                },
            });
            templateUFloors();
            $(".choose-floor-button").on("click", function (e) {
                e.preventDefault();
                $.fancybox.close();
            });
        });
        
        // handle fancybox for sides
        $("#pizza-sides-button").on("click", function (e) {
            e.preventDefault();
            $.fancybox.open({
                src: "#u-pizza-sides-fancybox",
                type: "inline",
                touch: false,
                opts: {
                    afterShow: function (instance, current) {
                        // console.info("done!");
                        let sideFancy = $(document.body).find("#u-pizza-sides-fancybox");
                        if (window.matchMedia("(min-width: 768px)").matches) {
                            if (sideFancy.height() > window.innerHeight - 100) {
                                sideFancy.css("border-width", "0");
                                $(".pizza-floors-block", sideFancy).slimScroll({
                                    height: window.innerHeight - 100,
                                    railVisible: true,
                                    alwaysVisible: true,
                                    size: "6px",
                                    color: "#FF0329",
                                    railColor: "#EAEAEA",
                                    railOpacity: 1,
                                    wheelStep: 5,
                                });
                            }
                        } 
                        else {
                            $(".pizza-floors-block", sideFancy).slick({
                                slidesToShow: 4,
                                infinite: false,
                                arrows: false,
                                responsive: [
                                    {
                                        breakpoint: 500,
                                        settings: {
                                            slidesToShow: 3,
                                        },
                                    },
                                    {
                                        breakpoint: 380,
                                        settings: {
                                            slidesToShow: 2,
                                        },
                                    },
                                ],
                            });
                        }
                    },
                },
            });
            templateUSides();
            $(".choose-floor-button").on("click", function (e) {
                e.preventDefault();
                $.fancybox.close();
            });
        });

        //Calculate function.
        const calculate = () => {
            let sum = parseFloat( initialPrice );

            if ( ( dataComponents.pizza.price_inc ) &&  ( $('.pizza-components-block').length ) ) {
                const inputBaseValue = JSON.parse(inputBase.val());
                let priceToExclude = 0;
                Object.values(dataComponents.pizza.base).map((component) => {
                  inputBaseValue.map((c) => {
                    let key = Object.keys(c)[0];
      
                    if (key === component.id && !c[key]) {
                      priceToExclude += parseFloat(component.price);
                    }
                  });
                });
                sum = sum - priceToExclude;
            }
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

        // Remove componet function.
        const calculateComponentsRemove = (el) => {
            if ( ! el.find( ".u-remove-component" ).length ) return;
            
            let componentId = el.attr( "data-component-id" );
            const inputBaseValue = JSON.parse( inputBase.val() );
            let modiFiedData = inputBaseValue.map((c) => {
                let key = Object.keys(c)[0];
                return c.hasOwnProperty(componentId) ? {[key]: !c[componentId]} : c; //was true? now false.
            });
            inputBase.val(JSON.stringify(modiFiedData));
            refreshClasses(modiFiedData);
            calculate();
        };

        // Refresh 
        const refreshClasses = (data) => {
            $("#remove-component .pizza-components-item").each(function () {
                $(this).removeClass("active");
            });

            data.forEach((c) => {
                let key = Object.keys(c)[0];
                !c[key] &&
                $(`[data-component-id=${key}]`)
                    .closest(".pizza-components-item")
                    .addClass("active");
            });
        };

    }

})(jQuery);