(function ($) {

    // Formate the product data in product container.
    const dataComponents = JSON.parse(
        $('.pizza_components_wrapper').attr('data-pizza')
    );

    // Inputs.
    const inputBase         =   $('input[name=u-pizza-base]');
    const inputFloors       =   $("input[name=pizza-floors-data]");
    const inputSides        =   $("input[name=pizza-sides-data]");

    const floorsEnabled     =   dataComponents.pizza.floors.enabled;
    const sidesEnabled      =   dataComponents.pizza.sides.enabled;
    let initialPrice        =   $('.pizza_components_wrapper').attr('data-price');

    //
    if ( ( $('form.variations_form').length === 0 ) && ($('form.cart').length > 0 )  ) {

        let selectedIdFloors = [
            {
                id: $(".pizza_components_wrapper").attr("data-product-id"),
                position: 1,
            },
        ];
        let selectedIdSides = [];

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

        //Templating floors
        const templateUFloors = () => {
            inputFloors.val(JSON.stringify(selectedIdFloors));
            $(document.body)
                .on("click", ".pizza-fancybox-floors .pizza-floor-item", function () {
                    
                    let product_id = $(this).attr("data-floor");
                    let price = $(this).find(".u-pizza-price").html();
                    let title = $(this).find(".u-pizza-title").text();
                    let image = $(this).find("img").attr("src");
                    let findElement = selectedIdFloors.findIndex(
                        (el) => el.id === product_id
                    );

                    if (findElement !== -1) {
                        return;
                    }
                    //Only three floors can be selected
                    if (selectedIdFloors.length >= 3) return;
                    //Get array of positions. Positions need for position elements in DOM
                    let positionIndexes = selectedIdFloors.map((l) => l.position);
                    //Get array with positions which doesnt exists in positionIndexes array. For example, if positionIndexes = [1,2], then templateIndexes = [3, 4, 5, 6, 7]. This is for getting next number for position
                    let templateIndexes = [1, 2, 3, 4, 5, 6, 7].filter(
                        (i) => !positionIndexes.includes(i)
                    );
                    selectedIdFloors = [
                        ...selectedIdFloors,
                        {id: product_id, position: Math.min(...templateIndexes)}, //if positionIndexes = [1,2], then templateIndexes = [3, 4, 5, 6, 7] and min will be 3
                    ];
                    inputFloors.val(JSON.stringify(selectedIdFloors));
                    // Add to floor left seccion.
                    const templateSelected = wp.template("pizza-floor-selected");
                    const pizzaSelectedData = {
                        name: title,
                        image: image,
                        product_id: product_id,
                        price: price,
                    };
                    $(".pizza-fancybox-floors .pizza-floors-selected__item")
                        .eq(Math.min(...templateIndexes) - 1)
                        .replaceWith(templateSelected(pizzaSelectedData));
                    calculate();
                })
                .on("click", ".u-remove-floor", function (e) {
                    e.preventDefault();
                    let product_id = $(this)
                        .closest(".pizza-floors-selected__item")
                        .attr("data-product-id");
                    let index = $(".pizza-floors-selected__item").index(
                        $(`[data-product-id=${product_id}]`)
                    );
                    const templateDefault = wp.template("pizza-floor-default");
                    const pizzaDefaultData = {
                    name: PIZZA_FRONT_DATA.floor_default_text.replace(
                        "%s",
                        index + 1
                    ),
                    image: PIZZA_FRONT_DATA.floor_default_image,
                    product_id: "",
                    };
                    $(this)
                        .closest(".pizza-floors-selected__item")
                        .replaceWith(templateDefault(pizzaDefaultData));
                    selectedIdFloors = selectedIdFloors.filter(
                        (el) => el.id !== product_id
                    );
                    inputFloors.val(JSON.stringify(selectedIdFloors));
                    calculate();
                });
        };

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
            let priceFloorContainer = $(document.body).find(".floors-total-price");

            priceContainer.html(u_wc_price(summ));
            if (floorsEnabled || sidesEnabled) {
                priceFloorContainer.html(u_wc_price(summ));
            }
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