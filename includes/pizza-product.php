<?php
class U_Pizza_Product
{
    private static $instances = [];
    private $data;
    public function __construct($product)
    {
        $this->data = $product;
    }
    /**
     * @return U_Pizza_Product with setting up @property $data
     */
    public static function get_product($product)
    {
        if (is_numeric($product)) {
            $product = wc_get_product($product);
        }
        if (!$product) {
            return;
        }

        if (!array_key_exists($product->get_id(), self::$instances)) {
            self::$instances[$product->get_id()] = new self($product);
        }
        return self::$instances[$product->get_id()];
    }
    /**
     * Check if price_inc checkbox enabled
     */
    public function is_price_inc()
    {
        $product_id = $this->data->get_parent_id() ? $this->data->get_parent_id() : $this->data->get_id();
        $u_product_pizza_data = get_post_meta($product_id, 'u_product_pizza_data', true);
        return $u_product_pizza_data ? $u_product_pizza_data['pizza']['price_inc'] && $this->is_base_enabled() : false;
    }
    /**
     * Get updated price for product, including base components price
     */
    public function get_price($price_value = '')
    {
        if ($price_value === '') {
            $price = $this->data->get_price();
        } else {
            $price = $price_value;
        }
        if ($this->is_price_inc()) {
            $components = $this->get_base_components();
            if ($components) {

                foreach ($components as $c) {
                    $price += (float) $c['price'];
                }
            }
        }
        return $price;
    }
    /**
     * Get base components from product meta data
     */
    public function get_base_components()
    {
        $product_id = $this->data->get_parent_id() ? $this->data->get_parent_id() : $this->data->get_id();
        $u_product_pizza_data = get_post_meta($product_id, 'u_product_pizza_data', true);

        if ($u_product_pizza_data && isset($u_product_pizza_data['pizza']['base'])) {
            return $u_product_pizza_data['pizza']['base'];
        }
        return false;
    }
    /**
     * If product is on sale
     */
    public function is_on_sale()
    {
        return $this->data->is_on_sale();
    }
    /**
     * @return WC_Product
     */
    public function get_wc_product()
    {
        return $this->data;
    }
    public function get_regular_price()
    {
        $price = $this->data->get_regular_price();
        if ($this->is_price_inc()) {
            $components = $this->get_base_components();
            if ($components) {

                foreach ($components as $c) {
                    $price += (float) $c['price'];
                }
            }
        }
        return $price;
    }
    public function get_price_suffix()
    {
        return $this->data->get_price_suffix();
    }
    /**
     * Check if pizza type enabled && base components exists in admin
     */
    public function is_base_enabled()
    {
        $product_id = $this->data->get_parent_id() ? $this->data->get_parent_id() : $this->data->get_id();
        $u_product_pizza_data = get_post_meta($product_id, 'u_product_pizza_data', true);

        if ($u_product_pizza_data && isset($u_product_pizza_data['pizza']['base'])) {
            if ($u_product_pizza_data['pizza']['enabled']) {
                return true;
            }
        }
        return false;
    }
}
