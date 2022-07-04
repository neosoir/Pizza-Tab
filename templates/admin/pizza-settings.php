<?php
wp_enqueue_style('woocommerce_admin_styles');
wp_enqueue_script('wc-admin-meta-boxes');
$pizza_data = get_option('u_pizza_data') ? get_option('u_pizza_data') : u_pizza_get_default_data();
$pizza_components = array_merge(...wp_list_pluck($pizza_data, 'components'));
$pizza_product_data = get_post_meta(101, 'u_product_pizza_data', true);

echo "<pre>";
print_r($pizza_data);
//print_r(u_flatten_array(wp_list_pluck($pizza_data, 'components')));
echo '</pre>';


?>
<div id="u-pizza-settings">
    <?php
    woocommerce_wp_checkbox([
        'id' => 'pizza_tipps', // also as name attr
        'label' => __('Enable tipps'),
        'value' => get_option('pizza_tipps'),
        'description' => __('Show tippy icon in every element that have description content'),
        'desc_tip' => true
    ]);
    ?>
    <div class="wc-metaboxes-wrapper">
        <div class="wc-metaboxes">
            <?php foreach ($pizza_data as $group) : ?>
                <div class="wc-metabox closed" data-index="<?= esc_attr($group['id']) ?>">
                    <h3>
                        <button type="button" class="remove-group button"><?=esc_html_e('Remove group', 'u-pizza') ?></button>
                        <strong><?= esc_html($group['group_name']) ?></strong>
                        <div class="handlediv"></div>
                        <input type="hidden" name="pizza_data[<?= esc_attr($group['id']) ?>][id]" value="<?= esc_attr($group['id']) ?>">
                    </h3>
                    <div class="wc-metabox-content">
                        <div class="group-header">
                            <div class="form-group">
                                <label for=""><?= esc_html_e('Group name', 'u-pizza') ?></label>
                                <input type="text" name="pizza_data[<?= esc_attr($group['id']) ?>][group_name]" value="<?= esc_attr($group['group_name']) ?>">
                            </div>
                            <div class="form-group form-group-image">
                                <label for=""><?= esc_html_e('Group image', 'u-pizza') ?></label>
                                <div class="group-image">
                                    <img src="<?= esc_attr($group['image']) ?>" alt="">
                                    <input type="hidden" name="pizza_data[<?= esc_attr($group['id']) ?>][image]" value="<?= esc_attr($group['image']) ?>">

                                    <input type="hidden" name="pizza_data[<?= esc_attr($group['id']) ?>][imageId]" value="<?= esc_attr($group['imageId']) ?>">
                                </div>
                            </div>
                        </div>
                        <div class="group-components">
                            <?php foreach ($group['components'] as $component) : ?>
                                <div class="group-component">
                                    <div class="component-header">
                                        <div class="component-details">
                                            <span><?= esc_html($component['name']) ?></span>
                                            <span><?= wc_price($component['price']) ?></span>
                                            <input type="hidden" name="pizza_data[<?= esc_attr($group['id']) ?>][components][<?= esc_attr($component['id']) ?>][id]" value="<?= esc_attr($component['id']) ?>">
                                        </div>
                                        <div class="component-actions">
                                            <span class="dashicons dashicons-edit edit-component"></span>
                                            <span class="dashicons dashicons-trash remove-component" data-id="<?= esc_attr($component['id']) ?>"></span>
                                        </div>
                                    </div>
                                    <div class="component-body">
                                        <div class="component-img">
                                            <img src="<?= esc_attr(wp_get_attachment_image_url($component['imageId'], 'medium')) ?>" alt="">
                                            <!-- <img src="<?= esc_attr($component['image']) ?>" alt=""> -->
                                        </div>
                                    </div>
                                    <div class="component-body-collapse">
                                        <div class="form-group-full component-name">
                                            <label for=""><?= esc_html_e('Name', 'u-pizza') ?></label>
                                            <input type="text" name="pizza_data[<?= esc_attr($group['id']); ?>][components][<?=esc_attr($component['id']) ?>][name]" value="<?= esc_attr($component['name']) ?>">
                                        </div>
                                        <div class="form-group-full">
                                            <label for=""><?= esc_html_e('Price', 'u-pizza') ?></label>
                                            <input type="text" name="pizza_data[<?= esc_attr($group['id']) ?>][components][<?= esc_attr($component['id']) ?>][price]" value="<?= esc_attr($component['price']) ?>">
                                        </div>
                                        <div class="form-group-full">
                                            <label for=""><?= esc_html_e('Weight', 'u-pizza') ?></label>
                                            <input type="text" name="pizza_data[<?= esc_attr($group['id']) ?>][components][<?= esc_attr($component['id']) ?>][weight]" value="<?= esc_attr($component['weight']) ?>">
                                        </div>
                                        <div class="form-group-full component-image">
                                            <img src="<?= esc_url(wp_get_attachment_image_url($component['imageId'], 'medium')) ?>" alt="">
                                            <!-- <img src="<?= esc_attr($component['image']) ?>" alt=""> -->
                                            <input type="hidden" name="pizza_data[<?= esc_attr($group['id']) ?>][components][<?= esc_attr($component['id']) ?>][image]" value="<?= esc_attr($component['image']) ?>">
                                            <input type="hidden" name="pizza_data[<?= esc_attr($group['id']) ?>][components][<?= esc_attr($component['id']) ?>][imageId]" value="<?= esc_attr($component['imageId']) ?>">
                                        </div>
                                        <div class="form-group-full">
                                            <textarea name="pizza_data[<?= esc_attr($group['id']); ?>][components][<?= esc_attr($component['id']); ?>][description]" id="" cols="30" rows="10"><?= esc_attr(trim($component['description'])) ?></textarea>
                                        </div>
                                        <div class="form-group-full">
                                            <input type="checkbox" name="pizza_data[<?= esc_attr($group['id']) ?>][components][<?= esc_attr($component['id']) ?>][meta]" <?= checked($component['meta'], 1) ?>>
                                            <label for=""><?= esc_html_e('Use as meta', 'u-pizza') ?></label>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="toolbar">
                            <button type="button" class="button add-component">
                                <?= esc_html_e('Add component', 'u-pizza') ?>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="toolbar">
            <button type="button" class="button add-group">
                <?= esc_html_e('Add group', 'u-pizza') ?>
            </button>
        </div>
    </div>


    <?php wp_nonce_field('u_pizza_woo_settings', '_pizzanonce'); ?>
</div>

<!-- <script type="text/html" id="tmpl-pizza-group">
    <div class="wc-metabox closed" data-index="{{{data.index}}}">
        <h3>
            <button type="button" class="remove-group button"><?php esc_html_e('Remove group', 'u-pizza'); ?></button>
            <strong></strong>
            <div class="handlediv"></div>
            <input type="hidden" name="{{{data.id.name}}}" value="{{{data.index}}}">

        </h3>
        <div class="wc-metabox-content">
            <div class="group-header">
                <div class="form-group">
                    <label for=""><?php esc_html_e('Group name', 'u-pizza'); ?></label>
                    <input type="text" name="{{{data.name.name}}}">
                </div>
                <div class="form-group form-group-image">
                    <label for=""><?php esc_html_e('Group image', 'u-pizza'); ?></label>
                    <div class="group-image">
                        <img src="{{{data.image.value}}}" alt="">
                        <input type="hidden" name="{{{data.image.name}}}">

                        <input type="hidden" name="{{{data.imageId.name}}}">
                    </div>
                </div>
            </div>
            <div class="group-components">

            </div>
            <div class="toolbar">
                <button type="button" class="button add-component"><?php esc_html_e('Add component', 'u-pizza'); ?></button>
            </div>
        </div>
    </div>
</script>

<script type="text/html" id="tmpl-pizza-component">
    <div class="group-component">
        <div class="component-header">
            <div class="component-details">
                <span>{{{data.name.value}}}</span>
                <span>{{{data.price.value}}}</span>
                <input type="hidden" name="{{{data.id.name}}}" value="{{{data.index}}}">

            </div>
            <div class="component-actions">
                <span class="dashicons dashicons-edit edit-component"></span>
                <span class="dashicons dashicons-trash remove-component" data-id="{{{data.index}}}"></span>

            </div>
        </div>
        <div class="component-body">
            <div class="component-img">

                <img src="{{{data.image.value}}}" alt="">
            </div>
        </div>
        <div class="component-body-collapse">
            <div class="form-group-full component-name">
                <label for=""><?php esc_html_e('Name', 'u-pizza'); ?></label>
                <input type="text" name="{{{data.name.name}}}" value="">
            </div>
            <div class="form-group-full">
                <label for=""><?php esc_html_e('Price', 'u-pizza'); ?></label>
                <input type="text" name="{{{data.price.name}}}" value="{{{data.price.value}}}">
            </div>
            <div class="form-group-full">
                <label for=""><?php esc_html_e('Weight', 'u-pizza'); ?></label>
                <input type="text" name="{{{data.weight.name}}}" value="">
            </div>
            <div class="form-group-full component-image">

                <img src="{{{data.image.value}}}" alt="">
                <input type="hidden" name="{{{data.image.name}}}" value="{{{data.image.value}}}">
                <input type="hidden" name="{{{data.imageId.name}}}" value="">

            </div>
            <div class="form-group-full">
                <textarea name="{{{data.description.name}}}" id="" cols="30" rows="10"></textarea>
            </div>
            <div class="form-group-full">

                <input type="checkbox" name="{{{data.meta.name}}}">
                <label for=""><?php esc_html_e('Use as meta', 'u-pizza'); ?></label>
            </div>
        </div>
    </div>
</script> -->