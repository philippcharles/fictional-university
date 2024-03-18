<?php

/*
  Plugin Name: Are You Paying Attention Quiz
  Description: Give your readers a multiple choice question.
  Version 1.0
  Author: Philipp
  Author URI: asdfghjkl
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class AreYouPayingAttention {
    function __construct() {
        add_action('init', array($this, 'adminAssets')); // register a block from within php
        //add_action('enqueue_block_editor_assets', array($this, 'adminAssets')); // init custom block  plugins in admin side using js
    }

    function adminAssets() {
        wp_register_style('quizeditcss', plugin_dir_url(__FILE__) . 'build/index.css'); // connecting the block type css to admin dashboard
        wp_register_script('ournewblocktype', plugin_dir_url(__FILE__) . 'build/index.js', array('wp-blocks', 'wp-element', 'wp-editor'));
        register_block_type('ourplugin/are-you-paying-attention', array(
            'editor_script' => 'ournewblocktype',
            'editor_style' => 'quizeditcss',
            'render_callback' => array($this, 'theHTML')
        
        ));
        //wp_enqueue_script('ournewblocktype', plugin_dir_url(__FILE__) . 'build/index.js', array('wp-blocks', 'wp-element')); // //load directly the js file from the build folder
        
    }

    function theHTML($attributes) {
        if (!is_admin()) {
            wp_enqueue_script('attentionFrontend', plugin_dir_url(__FILE__) . 'build/frontend.js', array('wp-element') );
            wp_enqueue_style('attentionFrontendStyles', plugin_dir_url(__FILE__) . 'build/frontend.css');
        }
        
        ob_start(); ?>
        <!-- return '<h2>Today the sky is boring ' .  $attributes['skyColor'] . ' and the grass is ' . $attributes['grassColor'] . ' !!!!!!!!!!</h2>'; -->
        <!-- <h3>Today the sky is 
            <?php 
            //echo esc_html($attributes['skyColor']) 
            ?> 
            anddthe grass is
            <?php 
            //echo esc_html($attributes['grassColor'])  
            ?>
        </h3> -->
        <div class="paying-attention-update-me"><pre style="display:none;"><?php echo wp_json_encode($attributes) ?></pre></div>

        <?php return ob_get_clean();
    }



}

$areYouPayingAttention = new AreYouPayingAttention();