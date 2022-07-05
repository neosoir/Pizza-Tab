<?php
?>

<div id="u_pizza_product_data" class="panel wc-metaboxes-wrapper hidden wocommerce_options_panel">
    Pizza product
</div>

<script>
    if ( jQuery('#_u_pizza').is(':checked') ) {
        jQuery('.show_if_u_pizza').show(); 
    }
    else {
        jQuery('.show_if_u_pizza').hide(); 
    }
    jQuery('#_u_pizza').on('change', function() {
        if ( jQuery(this).is(':checked') ) {
            jQuery('.show_if_u_pizza').show(); 
        }
        else {
            jQuery('.show_if_u_pizza').hide(); 
        } 
    });

</script>