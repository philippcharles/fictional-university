<?php

/*
  Plugin Name: Our Word Filter Plugin
  Description: replaces  list of words.
  Version 1.0
  Author: Philipp
  Author URI: asdfghjkl
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class OurWordFilterPlugin {
  function __construct() {
    add_action('admin_menu', array($this, 'ourMenu')); // adding custom menu in the dashboard page
    add_action('admin_init', array($this, 'ourSettings'));// adding this action to admin_menu hook
    if (get_option('plugin_words_to_filter')) add_filter('the_content', array($this, 'filterLogic')); // filter hook for words that you want to filter
  }

  function ourSettings() {
    add_settings_section('replacement-text-section', null, null, 'word-filter-options' ); // 4 argumeents
    register_setting('replacementFields', 'replacementText');
    add_settings_field('replacement-text', 'Filtered Text', array($this, 'replacementFieldHTML'), 'word-filter-options','replacement-text-section'); // 5 args
  }

  function replacementFieldHTML() { ?>
    <input type="text" name="replacementText" value="<?php echo esc_attr(get_option('replacementText', '***')) ?>">
    <p class="description">Leave blank to simply remove the filtered words.</p>
  <?php }


  function filterLogic($content) {
    $badWords = explode(',', get_option('plugin_words_to_filter'));
    $badWordsTrimmed = array_map('trim', $badWords);
    return str_ireplace($badWordsTrimmed, esc_html(get_option('replacementText', '****')), $content);
  }

  // properties of a custome menu in dashboard page
  function ourMenu() {
    //add_menu_page('Words to Filter', 'Word Filter', 'manage_options', 'ourwordfilter', array($this, 'wordFilterPage'), plugin_dir_url(__FILE__) . 'custom.svg', 100);
    $mainPageHook = add_menu_page('Words to Filter', 'Word Filter', 'manage_options', 'ourwordfilter', array($this, 'wordFilterPage'), 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHZpZXdCb3g9IjAgMCAyMCAyMCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZmlsbC1ydWxlPSJldmVub2RkIiBjbGlwLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0xMCAyMEMxNS41MjI5IDIwIDIwIDE1LjUyMjkgMjAgMTBDMjAgNC40NzcxNCAxNS41MjI5IDAgMTAgMEM0LjQ3NzE0IDAgMCA0LjQ3NzE0IDAgMTBDMCAxNS41MjI5IDQuNDc3MTQgMjAgMTAgMjBaTTExLjk5IDcuNDQ2NjZMMTAuMDc4MSAxLjU2MjVMOC4xNjYyNiA3LjQ0NjY2SDEuOTc5MjhMNi45ODQ2NSAxMS4wODMzTDUuMDcyNzUgMTYuOTY3NEwxMC4wNzgxIDEzLjMzMDhMMTUuMDgzNSAxNi45Njc0TDEzLjE3MTYgMTEuMDgzM0wxOC4xNzcgNy40NDY2NkgxMS45OVoiIGZpbGw9IiNGRkRGOEQiLz4KPC9zdmc+', 100); // 7 arguments
    add_submenu_page('ourwordfilter', 'Words ToFilter', 'Words List', 'manage_options', 'ourwordfilter', array($this, 'wordFilterPage'), null); 
    add_submenu_page('ourwordfilter', 'Word Filter Options', 'Options', 'manage_options', 'word-filter-options', array($this, 'optionsSubpage'), null); // 6 arguments
    add_action("load-{$mainPageHook}", array($this, 'mainPageAssets')); // adding custom css for the custom admin menu page "word filter"
  }

  function mainPageAssets() {
    wp_enqueue_style('filterAdminCss', plugin_dir_url(__FILE__) . 'styles.css');
  }
  
  // // the frontend and function of the custom sub menu "option:
  function optionsSubpage() { ?>
    <div class="wrap">
        <h1>Word Filter Options</h1>
        <form action="options.php" method="POST">
            <?php
              settings_errors();
              settings_fields('replacementFields');
              do_settings_sections('word-filter-options');
              submit_button();
            ?>
        </form>
    </div>
  <?php }

  // the frontend and function of the custom page
  function wordFilterPage() { ?>
    <div class="wrap">
        <h1>Word Filter</h1>
        <?php if (isset($_POST['justsubmitted'])) $this->handleForm(); ?>
        <form action="" method="POST">
            <input type="hidden"  name="justsubmitted" value="true">
            <?php wp_nonce_field('saveFilterWords', 'ourNonce') ?>
            <label for="plugin_words_to_filter"><p>Enter a <strong>comma-separated</strong> list of words to filter from your site's content.</p></label>
            <div class="word-filter__flex-container">
                <textarea name="plugin_words_to_filter" id="plugin_words_to_filter" placeholder="bad, mean, awful, horrible">
                    <?php echo esc_textarea(get_option('plugin_words_to_filter')) ?>
                </textarea>
            </div>
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
        </form>
    </div>
  <?php }

  function handleForm() {
   if (wp_verify_nonce($_POST['ourNonce'], 'saveFilterWords') AND current_user_can('manage_options')) {
    update_option('plugin_words_to_filter', sanitize_text_field($_POST['plugin_words_to_filter'])); ?>
    <div class="updated">
        <p>Your filtered words were saved</p>
    </div>
   <?php } else { ?>
    <div class="error">
        <p>Sorry, you do not have permission to perform that action.</p>
    </div>
   <?php }
  }

}

$ourWordFilterPlugin = new OurWordFilterPlugin();