<?php

/*
 Plugin Name: Our Test Plugin
 Description: A truly amazing plugin.
 Version: 1.0
 Author: Philipp
 Author URI: asdfghjkl
*/

// SUPER BASIC SAMPLE PLUGIN
//  add_filter('the_content', 'addToEndOfPost');

// function addToEndOfPost($content) {
//   if(is_single() && is_main_query()) {
//     return $content . '<p>My name is Philipp.</p>';
//   } 

//   return $content;
// }

class WordCountAndTimePlugin {
  function __construct() {
    add_action('admin_menu', array($this, 'adminPage'));
    add_action('admin_init', array($this, 'settings'));
    add_filter('the_content', array($this, 'ifWrap'));

  }

  function settings() {
    //word count settings
    register_setting('wordcountplugin', 'wcp_location', array('sanitize_callback' => array($this, 'sanitizeLocation'), 'default' => '0'));
    add_settings_field('wcp_location', 'Display Location', array($this, 'locationHTML'), 'word-count-settings-page', 'wcp_first_section');

    //headline settings
    register_setting('wordcountplugin', 'wcp_headline', array('sanitize_callback' => 'sanitize_text_field', 'default' => 'Post Statistics'));
    add_settings_field('wcp_headline', 'Headline Text', array($this, 'headlineHTML'), 'word-count-settings-page', 'wcp_first_section');

    //wordcount settings
    register_setting('wordcountplugin', 'wcp_wordcount', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1'));
    add_settings_field('wcp_wordcount', 'Word Count', array($this, 'checkboxHTML'), 'word-count-settings-page', 'wcp_first_section', array('theName' => 'wcp_wordcount'));

    //charactercount settings
    register_setting('wordcountplugin', 'wcp_charactercount', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1'));
    add_settings_field('wcp_charactercount', 'Character Count', array($this, 'checkboxHTML'), 'word-count-settings-page', 'wcp_first_section', array('theName' => 'wcp_charactercount'));

    //charactercount settings
    register_setting('wordcountplugin', 'wcp_readtime', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1'));
    add_settings_field('wcp_readtime', 'Read Time', array($this, 'checkboxHTML'), 'word-count-settings-page', 'wcp_first_section', array('theName' => 'wcp_readtime'));

    


    add_settings_section('wcp_first_section', null, null, 'word-count-settings-page');
  }

  function sanitizeLocation($input) {
    if ($input != '0' AND $input != '1') {
      add_settings_error('wcp_location', 'wcp_location_error', 'Display location must be either beginning or end.');
      return get_option('wcp_location');
    }
    return $input;
  }

  function checkboxHTML($args) { ?>
    <input type="checkbox" name="<?php echo $args['theName'] ?>" value="1" <?php checked(get_option($args['theName']), '1') ?>> 
  <?php }
/*
  function readtimetHTML() { ?>
    <input type="checkbox" name="wcp_readtime" value="1" <?php checked(get_option('wcp_readtime'), '1') ?>>
  <?php }
  

  function charactercountHTML() { ?>
    <input type="checkbox" name="wcp_charactercount" value="1" <?php checked(get_option('wcp_charactercount'), '1') ?>>
  <?php }
  
  function wordcountHTML() { ?>
    <input type="checkbox" name="wcp_wordcount" value="1" <?php checked(get_option('wcp_wordcount'), '1') ?>>

  <?php }
*/

  function headlineHTML() { ?>
    <input type="text" name="wcp_headline" value="<?php echo esc_attr(get_option('wcp_headline')) ?>">
    
  <?php }


  function locationHTML() { ?>
    <select name="wcp_location">
      <option value="0" <?php selected(get_option('wcp_location'), '0') ?>>Beginning of post</option>
      <option value="1" <?php selected(get_option('wcp_location'), '1') ?>>End of post</option>
    </select>

<?php }


  function adminPage() {  
    add_options_page('Word Count Settings', 'Word Count', 'manage_options', 'word-count-settings-page', array($this, 'ourHTML')); // must have 5 args
  }
  
  function ourHTML() { ?>
    <div class="wrap">
        <h1>Word Count Settings</h1>
        <form action="options.php" method="POST">
        <?php 
         settings_fields('wordcountplugin');
         do_settings_sections('word-count-settings-page');
         submit_button();
        ?>
        </form>
    </div>
  <?php }
}

$wordCountAndTimePlugin = new WordCountAndTimePlugin();



