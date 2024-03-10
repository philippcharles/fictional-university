<?php

// ========== CUSTOM API ENDPOINTS FOR "LIKE" ACTIONS =================
// Route = Basically rest api url

add_action('rest_api_init', 'universityLikeRoutes');

function universityLikeRoutes() {
  register_rest_route('university/v1', 'manageLike', array(
    'methods' => 'POST',
    'callback' => 'createLike'
  ));
  
  register_rest_route('university/v1', 'manageLike', array(
    'methods' => "DELETE",
    'callback' => 'deleteLike'
  ));
}

function createLike($data) {
  
    if(is_user_logged_in()) {
      $professor = sanitize_text_field($data['professorId']);

      $existQuery= new WP_Query(array(
        'author' => get_current_user_id(),
        'post_type' => 'like',
        'meta_query' => array(
          array(
            'key' => 'liked_professor_id',
            'compare' => '=',
            'value' => $professor
          )
        )
      ));

      // Logic for Restriction for each user account to be able to create only 1 like per professor
      if ($existQuery->found_posts == 0 AND get_post_type($professor) == 'professor') {
        return wp_insert_post(array(
            'post_type' => 'like',
            'post_status' => 'publish',
            'post_title' => '2nd PHP Test',
            'meta_input' => array(
                'liked_professor_id' => $professor
            ) 
        ));  
         //return 'Thanks for trying to create a like';
      } else {
        die("Invalid professor id");
      }  
    } else {
        die("Only logged in users can create a like.");
    }
  
}

function deleteLike($data) {
  $likeId = sanitize_text_field($data['like']);
  
  if (get_current_user_id() == get_post_field('post_author', $likeId) AND get_post_type($likeId) == 'like') {
    wp_delete_post($likeId, true);
    return 'Congrats, like deleted successfully.';
  } else {
    die("You do not have permission to delete that.");
  }

  //return 'Thanks for trying to delete a like';  
}