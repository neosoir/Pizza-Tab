(function ($) {

    function calculateUPizzaVariable() {
        const dataComponents = JSON.parse(
        $(".pizza_components_wrapper").attr("data-pizza")
        );
        //inputs
        const inputBase = $("input[name=u-pizza-base]");
        const inputFloors = $("input[name=pizza-floors-data]");
        const inputSides = $("input[name=pizza-sides-data]");
        console.log(dataComponents.pizza.extra);
        const floorsEnabled = dataComponents.pizza.floors.enabled;
        const sidesEnabled = dataComponents.pizza.sides.enabled;
        const addToCartButton = $("form.cart").find(".single_add_to_cart_button");
        if ($("form.variations_form").length) {
        let $allowed = false;

        let selectedIdFloors = [
            {
            id: $(".pizza_components_wrapper").attr("data-product-id"),
            position: 1,
            },
        ];
        let selectedIdSides = [];

        const $variationForm = $("form.variations_form");
        let variationPrice = 0;
        let variationRegularPrice = 0;

        //on variation select
        $variationForm.on("show_variation", function (event, variation) {
            console.log(variation);
            variationPrice = parseFloat(variation.display_price);
            variationRegularPrice = parseFloat(variation.display_regular_price);
            if (variation.display_price !== variation.display_regular_price) {
            variationPrice = parseFloat(variation.display_price);
            variationRegularPrice = parseFloat(variation.display_regular_price);
            $(".pizza-variable-price").html(
                u_wc_price_sale(
                    parseFloat(variation.display_price),
                    parseFloat(variation.display_regular_price)
                )
            );
            } 
            else {
            variationRegularPrice = 0;
            variationPrice = parseFloat(variation.display_price);
            $(".pizza-variable-price").html(u_wc_price(variationPrice));
            }
            setTimeout(() => {
            if (addToCartButton.is(".wc-variation-selection-needed")) {
                $allowed = false;
            } 
            else {
                $allowed = true;
            }
            }, 100);
            calculate();
        });
        //on variation hide
        $variationForm.on("hide_variation", function () {
            console.log("hide");
            variationPrice = 0;
            variationRegularPrice = 0;
            setTimeout(() => {
            if (addToCartButton.is(".wc-variation-selection-needed")) {
                $allowed = false;
            }
            }, 100);

            calculate();
        });
        $(".component-buttons").on("click", ".plus, .minus", function () {
            calculate();
        });
        $("#remove-component .pizza-components-item").on("click", function () {
            calculateComponentsRemove($(this));
        });
        // handle fancybox for floors
        $("#pizza-floor-button").on("click", function (e) {
            e.preventDefault();
            if (!$allowed) {
            return;
            }
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
            if (!$allowed) {
            return;
            }
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
        //Templating Sides components
        const templateUSides = () => {
            inputSides.val(JSON.stringify(selectedIdSides));
            $(document.body)
            .on("click", ".pizza-fancybox-sides .pizza-floor-item", function () {
                let side_id = $(this).attr("data-side-id");
                let price = $(this).find(".u-pizza-price").html();
                let title = $(this).find(".u-pizza-title").text();

                let image = $(this).find("img").attr("src");

                selectedIdSides = [{id: side_id}];
                inputSides.val(JSON.stringify(selectedIdSides));
                const templateSelected = wp.template("pizza-side-selected");
                const pizzaSelectedData = {
                name: title,
                image: image,
                price: price,
                };
                $(".pizza-fancybox-sides .pizza-sides-selected__item").replaceWith(
                templateSelected(pizzaSelectedData)
                );
                calculate();
            })
            .on("click", ".u-remove-side", function (e) {
                e.preventDefault();
                const templateDefault = wp.template("pizza-side-default");
                const pizzaDefaultData = {
                name: PIZZA_FRONT_DATA.side_default_text,
                image: PIZZA_FRONT_DATA.side_default_image,
                };
                $(this)
                .closest(".pizza-floors-selected__item")
                .replaceWith(templateDefault(pizzaDefaultData));
                selectedIdSides = [];
                inputSides.val(JSON.stringify(selectedIdSides));
                calculate();
            });
        };
        //Calculation process
        const calculate = () => {
            ///return when no variation selected
            if (variationPrice === 0) {
            return;
            }
            if (!$allowed) {
            return;
            }
            let summ = variationPrice;

            if (
            dataComponents.pizza.price_inc &&
            $(".pizza-components-block").length
            ) {
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
            summ = summ - priceToExclude;
            }
            $("#add-component .pizza-components-item").each(function () {
                let val = $(this).find(".component-qty").val();
                let componentId = $(this)
                        .find(".component-buttons")
                        .attr("data-food-item");
                let componentObject = Object.values(dataComponents.pizza.extra).find(
                    (component) => component.id === componentId
                );
                if (componentObject !== undefined) {
                    summ += parseFloat(componentObject.price) * parseInt(val);
                }
            });
            /// calculating dish type
            if ($(".pizza-components-wrapper").length) {
            $(".components-item-wrapper .component-item").each(function () {
                let val = $(this).find(".component-qty").val();
                let componentId = $(this)
                .find(".component-buttons")
                .attr("data-food-item");
                let componentObject = Object.values(
                dataComponents.dish.components
                ).find((component) => component.id === componentId);
                if (componentObject !== undefined) {
                summ += parseFloat(componentObject.price) * parseInt(val);
                }
            });
            }
            // calculating dish type with tabs
            if ($(".pizza-component-tabs-wrapper").length) {
            $(".tab-components-wrapper .component-item").each(function () {
                let val = $(this).find(".component-qty").val();
                let componentId = $(this)
                .find(".component-buttons")
                .attr("data-food-item");
                let componentObject = Object.values(
                dataComponents.dish.components
                ).find((component) => component.id === componentId);
                if (componentObject !== undefined) {
                summ += parseFloat(componentObject.price) * parseInt(val);
                }
            });
            }
            if (floorsEnabled) {
            let floorsData = selectedIdFloors.filter((el, i) => i !== 0);
            floorsData.forEach((el) => {
                let priceFloor = parseFloat(
                $(`[data-floor=${el.id}]`).attr("data-floor-price")
                );
                summ += priceFloor;
            });
            }
            if (sidesEnabled) {
            if (selectedIdSides.length > 0) {
                const findSide = Object.values(
                dataComponents.pizza.sides.components
                ).find((el) => el.id == selectedIdSides[0].id);

                if (findSide) {
                summ += parseFloat(findSide.price);
                }
            }
            }
            console.log(summ);
            refreshPriceHtml(summ);
        };
        //Refresh prices
        const refreshPriceHtml = (summ) => {
            let priceContainer = $("form.variations_form").find(
            ".woocommerce-variation-price .price"
            );
            let priceFloorContainer = $(document.body).find(".floors-total-price");

            if (variationRegularPrice > 0) {
            priceContainer.html(
                u_wc_price_sale(
                summ,

                summ + (variationRegularPrice - variationPrice)
                )
            );
            if (floorsEnabled || sidesEnabled) {
                priceFloorContainer.html(u_wc_price(summ));
            }
            } else {
            priceContainer.html(u_wc_price(summ));
            if (floorsEnabled || sidesEnabled) {
                priceFloorContainer.html(u_wc_price(summ));
            }
            }
        };
        const calculateComponentsRemove = (el) => {
            if (!el.find(".u-remove-component").length) return;
            let componentId = el.attr("data-component-id");
            const inputBaseValue = JSON.parse(inputBase.val());
            let modiFiedData = inputBaseValue.map((c) => {
            let key = Object.keys(c)[0];
            return c.hasOwnProperty(componentId) ? {[key]: !c[componentId]} : c; //was true? now false.
            });
            inputBase.val(JSON.stringify(modiFiedData));
            refreshClasses(modiFiedData);
            calculate();
        };
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
    }

    if ($(".pizza_components_wrapper").length > 0) {
        calculateUPizzaVariable();
    }
})(jQuery);
