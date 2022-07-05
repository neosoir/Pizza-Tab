<?php

class U_Pizza_Product_Simple extends \WC_Product_Simple {

    public function __construnct( $product )
    {
        $this->product_type = 'u_pizza';
        parent::__construnct( $product );
    }

    public function get_type()
    {
        return 'u_pizza';
    }

    public function is_purchasable()
    {
        return true;
    }

    public function is_virtual()
    {
        return false;
    }

    public function is_sold_individually()
    {
        return apply_filters('woocommerce_is_sold_individually', true === $this->get_sold_individually(), $this);
    }
}