<?php

// ========== CONNECTING CUSTOM API ENDPOINTS FOR "LIKE" ACTIONS TO THIS FUNCTION FILE ==========
require get_theme_file_path('/includes/like-route.php');

// ========== CONNECTING CUSTOM SEARCH ROUTE URL ENDPOINT INLCUDES TO FUNCTION FILE ==========
require get_theme_file_path('/includes/search-route.php');

// ========== ADDING NEW CUSTOM FIELD IN REST API =========
function university_custom_rest() {
  register_rest_field('post', 'authorName', array(
    'get_callback' => function() {
      return get_the_author();
    }
  ));

  register_rest_field('note', 'userNoteCount', array(
    'get_callback' => function() {
      return count_user_posts(get_current_user_id(), 'note');
    }
  ));
}

add_action('rest_api_init', 'university_custom_rest');


function pageBanner($args = NULL) {
  if (!isset($args['title'])) {
    $args['title'] = get_the_title();
  }

  if (!isset($args['subtitle'])) {
    $args['subtitle'] = get_field('page_banner_subtitle');
  }

  if (!isset($args['photo'])) {
    if (get_field('page_banner_background_image')) {
      $args['photo'] = get_field('page_banner_background_image')['sizes']['pageBanner'];
  } else {
    $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
  }
  }

  ?>

    <div class="page-banner">
      <div 
        class="page-banner__bg-image" 
        style="background-image: url(<?php echo $args['photo']; ?>)">
      </div>
      <div class="page-banner__content container container--narrow">
          <h1 class="page-banner__title"><?php echo $args['title']; ?></h1>
      <div class="page-banner__intro">
          <p><?php echo $args['subtitle']; ?></p>
      </div>
      </div>
    </div>

<?php }

// ========= INITIALIZE WP 
function university_files() {
    wp_enqueue_script('googleMap', '//maps.googleapis.com/maps/api/js?key=AIzaSyAwH88YqXWycgsxeNB_L7_qAtmOWM8VU-M', array(), '1.0', true);
    wp_enqueue_script('main-university-js', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0', true);
    wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));
    wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));
    
    // custom url
    wp_localize_script('main-university-js', 'universityData', array(
      'root_url' => get_site_url(),
      'nonce' => wp_create_nonce('wp_rest') // a secret property that equals the randomly generate number that wp create for our logged in user session - related to DELETE type of http request
    ));


    
}

add_action('wp_enqueue_scripts', 'university_files');



// ========= ADDING NAV MENU AND MANUALLY SETING IMAGE SIZE AND WP FEATURES
function university_features() {
    register_nav_menu('headerMenuLocation',__('Header Menu Location'));
    //register_nav_menu('footerLocationOne',__('Footer Location One'));
    //register_nav_menu('footerLocationTwo',__('Footer Location Two'));
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_image_size('professorLandscape', 400, 260, true);
    //add_image_size('professorLandscape', 400, 260, array('left', 'top'));
    add_image_size('professorPortrait', 480, 650, true);
                                 //width, height, crop? no=true, yes=false
    add_image_size('pageBanner', 1500, 350, true);

}
add_action( 'init', 'university_features' );
add_action('after_set_up_theme', 'university_features');


// ========== 
function university_adjust_queries($query) {

    if (!is_admin() AND is_post_type_archive('campus') AND is_main_query()) {
      $query->set('posts_per_page', -1);
    }

    if (!is_admin() AND is_post_type_archive('program') AND is_main_query()) {
      $query->set('orderby','title');
      $query->set('order', 'ASC');
      $query->set('posts_per_page', -1);
    }

    if (!is_admin() AND is_post_type_archive('event') AND $query->is_main_query()) {
      $today = date('Y-m-d');
      $query->set('meta_key', 'event_date');
      $query->set('orderby', 'meta_value_num');
      $query->set('order', 'ASC');
      $query->set('meta_query', array(
        array(
          'key' => 'event_date',
          'compare' => '>=',
          'value' => $today,
          'type' => 'DATETIME'
        )
      ));
    }
    // === universal query ===
    //$query->set('posts_per_page', 2);
}

add_action('pre_get_posts', 'university_adjust_queries');

// ========== ADDING GOOGLE MAP API ==========
function universityMapKey ($api) {
  $api['key'] = 'AIzaSyAwH88YqXWycgsxeNB_L7_qAtmOWM8VU-M';
  return $api;
}

add_filter('acf/fields/google_map/api', 'universityMapKey');


// ========== Redirect subsriber accounts out of admin and dashboard and onto homepage =========
add_action('admin_init', 'redirectSubsToFrontend');

function redirectSubsToFrontend() {
  $ourCurrentUser = wp_get_current_user();
  if (count($ourCurrentUser->roles) === 1  AND $ourCurrentUser->roles[0] === 'subscriber') {
    wp_redirect(site_url('/'));
    exit;
  }
}

// ========== HIDE THE ADMIN BAR FOR SUBSCRIBER USER ==========
add_action('wp_loaded', 'noSubsAdminBar');

function noSubsAdminBar() {
  $ourCurrentUser = wp_get_current_user();
  if (count($ourCurrentUser->roles) === 1  AND $ourCurrentUser->roles[0] === 'subscriber') {
    show_admin_bar(false);
  }
}

// CUSTOMIZE LOGIN SCREEN
add_filter('login_headerurl', 'ourHeaderUrl');

function ourHeaderUrl() {
  return esc_url(site_url('/'));
}

// LOAD CUSTOM THEME CSS FILE
add_action('login_enqueue_scripts', 'ourLoginCSS');

function ourLoginCSS() {
    wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));
    wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));
}

// ========== CHANGE THE DEFAULT "POWERED BY WORDPRESS" IN LOGIN SCREEN ==========
add_filter('login_headertitle', 'ourLoginTitle');

function ourLoginTitle() {
  return get_bloginfo('name');
}

// Force or intercept note posts to be private
// the "2" argument represents that we want to work with 2 parameters // the "10" is just a priority number which shpuld run first
add_filter('wp_insert_post_data', 'makeNotePrivate', 10, 2);

function makeNotePrivate($data, $postarr) {
  if($data['post_type'] == 'note' AND $data['post_status'] != 'trash') {
    $data['post_status'] = "private";    
  }

  //Set to unbale to right html code or js code in the note  content and title area
  if (($data['post_type'] == 'note')) {
    $data['post_title'] = sanitize_textarea_field($data['post_title']);
    $data['post_content'] = sanitize_textarea_field($data['post_content']);

    // My Note Per-User Post Limit
    if(count_user_posts(get_current_user_id(), 'note') > 7 AND !$postarr['ID'] )  { 
      die("You have reached your note limit.");
    }
  }

  return $data;
}

// function to ignore a certain folder when migrating your website from local to live
add_filter('ai1wm_exclude_content_from_export', 'ignoreCertainFiles');

function ignoreCertainFiles($exclude_filters) {
  $exclude_filters[] = 'themes/fictional-university-theme/node_modules';
  return $exclude_filters;
}