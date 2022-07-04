<?php

class U_Pizza {

    protected static $instance = null;

    public static function instance() {

        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;

    }

    public function __construct() {
        echo 'U pizza plugin';
    }

}