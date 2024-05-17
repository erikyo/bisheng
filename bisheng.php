<?php
/*
 * Plugin Name: bisheng
 * Description: Responsive Block Attributes
 * Author: Erik yo
*/

function rba_modify_block_settings($settings, $name) {
    if (isset($settings['attributes'])) {
        $settings['attributes']['mobileAttributes'] = [
            'type' => 'object',
            'default' => [],
        ];
    }

    return $settings;
}
add_filter('blocks.registerBlockType', 'rba_modify_block_settings', 10, 2);

function rba_save_post($post_id, $post, $update) {
    if ($post->post_type !== 'post' && $post->post_type !== 'page') {
        return;
    }

    $content = $post->post_content;
    $blocks = parse_blocks($content);

    foreach ($blocks as &$block) {
        if (isset($block['attrs']) && isset($block['attrs']['mobileAttributes'])) {
            update_post_meta($post_id, '_rba_mobile_attributes_' . $block['blockName'], $block['attrs']['mobileAttributes']);
        }
    }
}
add_action('save_post', 'rba_save_post', 10, 3);

function rba_render_block($block_content, $block) {
    if (isset($block['attrs']) && isset($block['attrs']['mobileAttributes'])) {
        $mobileAttributes = get_post_meta(get_the_ID(), '_rba_mobile_attributes_' . $block['blockName'], true);

        if ($mobileAttributes) {
            $block_content .= '<style>@media (max-width: 600px) { .wp-block-' . $block['blockName'] . ' { ';
            foreach ($mobileAttributes as $attr => $value) {
                $block_content .= $attr . ': ' . $value . '; ';
            }
            $block_content .= '} }</style>';
        }
    }

    return $block_content;
}
add_filter('render_block', 'rba_render_block', 10, 2);

function rba_enqueue_scripts() {
    wp_enqueue_script(
        'bisheng',
        plugin_dir_url(__FILE__) . '/build/index.js',
        ['wp-editor']
    );
}
add_action('enqueue_block_editor_assets', 'rba_enqueue_scripts');