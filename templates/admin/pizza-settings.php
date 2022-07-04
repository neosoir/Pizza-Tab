<?php

$data = u_pizza_get_default_data();

echo "<pre>";
print_r( $data );
echo "</pre>";

?>
<div id="u-pizza-settings">

    <?php wp_nonce_field('u_pizza_settings', '_pizzanonce') ?>
</div>