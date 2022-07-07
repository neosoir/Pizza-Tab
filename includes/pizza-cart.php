<?php
class U_Pizza_Cart
{
    use Pizza_Instantiable;

    public function __construct()
    {
        add_filter('woocommerce_add_cart_item_data', [$this, 'add_item_data'], 10, 4);
        add_action('woocommerce_before_calculate_totals', [$this, 'calculate_totals'], 10, 1);

        add_action('woocommerce_before_calculate_totals', [$this, 'debug_cart'], 20, 1);

    }
    public function add_item_data($cart_item_data, $product_id, $variation_id, $quantity)
    {
        if ( u_is_pizza_product( $product_id ) ) {
            $pizza_confing          = [];
            $product_pizza_data     =  get_post_meta( $product_id, 'u_product_pizza_data', true );
            //echo "<pre>";
            //print_r($product_pizza_data);
            //echo "</pre>";
            if ( (isset( $_POST['ev_quantity'] ) ) && ( ! empty( $_POST['ev_quantity'] ) ) ) {
                foreach ( wc_clean( $_POST['ev_quantity'] ) as $componet_id => $quantity ) {
                    if ( $quantity  == 0 ) continue;
                    foreach ( $product_pizza_data['pizza']['extra'] as $componet ) {
                        if ( (int) $componet['id'] === $componet_id ) {
                            $pizza_confing['pizza']['extra'][] = [
                                'id'            => $componet['id'],
                                'name'          => $componet['name'],
                                'quantity'      => $quantity,
                                'price'         => $componet['price'],
                                'weight'        => $componet['weight'],
                                'description'   => $componet['description'],
                                'image'         => $componet['image'],
                            ];
                        }
                    }
                }
            }
            $cart_item_data['u_pizza_config']   = $pizza_confing;
        }
        return $cart_item_data;
    }

    public function calculate_totals($cart_object)
    {
        //$cart_object === WC()->cart
        foreach ( $cart_object->get_cart() as $cart_item_key => $cart_item ) {
            $product_id = $cart_item['product_id'];
            if ( u_is_pizza_product( $product_id ) ) {
                $product_pizza_data = get_post_meta( $product_id, 'u_product_pizza_data', true );
                $price              = $cart_item['data']->get_price();
                if ( isset( $cart_item['u_pizza_config']['pizza']['extra'] ) ) {
                    $pizza_extra = $product_pizza_data['pizza']['extra'];
                    foreach ($cart_item['u_pizza_config']['pizza']['extra'] as $component) {
                        foreach ( $pizza_extra as $c ) {
                            if ( (int) $component['id'] === (int) $c['id'] ) {
                                $price +=  floatval( $c['price'] ) * intval( $component['quantity'] );
                            }
                        }
                    }
                }
                $cart_item['data']->set_price( $price );
            }
        }
    }

    public function debug_cart()
    {
        $cart = WC()->cart->get_cart();
        echo "<pre>";
        print_r($cart);
        echo "</pre>";
    }

}
