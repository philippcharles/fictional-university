<?php 

// ========== ADDING NEW CUSTOM SEARCH API ROUTE OR URL ENDPOINT==========

add_action('rest_api_init', 'universityRegisterSearch');


function universityRegisterSearch() {
    register_rest_route('university/v1', 'search', array(
        'methods' => WP_REST_Server::READABLE,  //GET, POST, PUT, DELETE
        'callback'=> 'universitySearchResults'  // functions you make
    ));
}

// ========== QUERY OF KEYWORD SEARCHING FOR MULTIPLE POST TYPE CUSTOM API URL ==========
function universitySearchResults($data) {
  $mainQuery = new WP_Query(array(
    'post_type' => array('post', 'page', 'professor', 'program', 'campus', 'event'),
    's' => sanitize_text_field($data['term'])
  ));

  $results = array(
    'generalInfo' => array(),
    'professors' => array(),
    'programs' => array(),
    'events' => array(),
    'campuses' => array(),
  );

  while($mainQuery->have_posts()) {
    $mainQuery->the_post();

    if (get_post_type() == 'post' OR get_post_type() == 'page') {
      array_push($results['generalInfo'], array(
         'title' => get_the_title(),
         'permalink' => get_the_permalink(),
         'postType' => get_post_type(),
         'authorName' => get_the_author()
      ));
    }

    if (get_post_type() == 'professor') {
      array_push($results['professors'], array(
         'title' => get_the_title(),
         'permalink' => get_the_permalink(),
         'image' => get_the_post_thumbnail_url(0, 'professorLandscape'),
      ));
    }

    //relationship between program and campuses
    if (get_post_type() == 'program') {
      $relatedCampuses = get_field('related_campus');

      if ($relatedCampuses) {
        foreach($relatedCampuses as $campus) {
          array_push($results['campuses'], array(
            'title' => get_the_title($campus),
            'permalink' => get_the_permalink($campus)
          ));
        }
      }
      

      array_push($results['programs'], array(
         'title' => get_the_title(),
         'permalink' => get_the_permalink(),
         'id' => get_the_id()
      ));
    }

    if (get_post_type() == 'campus') {
      array_push($results['campuses'], array(
         'title' => get_the_title(),
         'permalink' => get_the_permalink()
      ));
    }

    if (get_post_type() == 'event') {

      $eventDate = new DateTime(get_field('event_date'));
      $description = null;
      if(has_excerpt()) {
        $description = get_the_excerpt();
      } else {
        $description = wp_trim_words(get_the_content(), 18);
      }

      array_push($results['events'], array(
         'title' => get_the_title(),
         'permalink' => get_the_permalink(),
         'month' => $eventDate->format('M'),
         'day' => $eventDate->format('d'),
         'description' => $description
      ));
    }


    
  }

  // CUSTOM QUERY FOR SEARCH RESULTS THAT'S AWARE OF RELATIONSHIP

  if ($results['programs']) {
    $programsMetaQuery = array('relation' => 'OR');
    // logic for programs that dynamically pop on search field whatever phrase being search
    // what if there is more than 1 or 10  program with the same name like math and basic math
    // this code will programtatically work wethert there is 1 or 4 or 1o program in database
    foreach ($results['programs'] as $item) {
      array_push($programsMetaQuery, array(
        'key' => 'related_programs',
        'compare' => 'LIKE',
        'value' => '"' . $item['id'] . '"'
      ));
    }
    
    //if programs array contain multiple programs like biology, human biology and super advance marine biology
    // this code only make sense if there exactly 3 programs but what if there 4, 5 or 10 program
    $programRelationshipQuery = new WP_Query(array(
      'post_type' => array('professor', 'event'),
      'meta_query' => $programsMetaQuery
    ));
  
    while ($programRelationshipQuery->have_posts()) {
      $programRelationshipQuery->the_post();
  
      if (get_post_type() == 'professor') {
        array_push($results['professors'], array(
           'title' => get_the_title(),
           'permalink' => get_the_permalink(),
           'image' => get_the_post_thumbnail_url(0, 'professorLandscape'),
        ));
      }

      // ========== EVENTS ==========
      if (get_post_type() == 'event') {

        $eventDate = new DateTime(get_field('event_date'));
        $description = null;
        if(has_excerpt()) {
          $description = get_the_excerpt();
        } else {
          $description = wp_trim_words(get_the_content(), 18);
        }
  
        array_push($results['events'], array(
           'title' => get_the_title(),
           'permalink' => get_the_permalink(),
           'month' => $eventDate->format('M'),
           'day' => $eventDate->format('d'),
           'description' => $description
        ));
      }



    }
  
    // functon that removes duplicates from an array
    // array valies -> removes numerical key in an array api
    $results['professors'] = array_values(array_unique($results['professors'], SORT_REGULAR));
    $results['events'] = array_values(array_unique($results['events'], SORT_REGULAR));
  }
  


  return $results;

}


// ========== QUERY OF KEYWORD SEARCHING FOR PROFESSOR POST TYPE IN CUSTOME API URL ==========
// function universitySearchResults($data) {
//   $professors = new WP_Query(array(
//     'post_type' => array('post', 'page', 'professor'),
//     's' => sanitize_text_field($data['term'])
//   ));

//   $professorResults = array();

//   while($professors->have_posts()) {
//     $professors->the_post();
//     array_push($professorResults, array(
//         'title' => get_the_title(),
//         'permalink' => get_the_permalink()
//     ));
//   }

//   return $professorResults;

// }



