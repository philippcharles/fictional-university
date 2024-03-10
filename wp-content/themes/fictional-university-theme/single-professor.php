<?php 

      get_header();

      while (have_posts()) {
         the_post(); 
         pageBanner();
         
         ?>


            
            <div class="container container--narrow page-section">
            
            <div class="generic-content">
                <div class="row group">
                    <div class="one-third">
                        <?php the_post_thumbnail('professorPortrait'); ?>
                    </div>
                    <div class="two-thirds">
                      <!-- ===== Query for Like =====  -->
                      <?php 

                        $likeCount = new WP_Query(array(
                          'post_type' => 'like',
                          'meta_query' => array(
                            array(
                              'key' => 'liked_professor_id',
                              'compare' => '=',
                              'value' => get_the_ID()
                            )
                          )
                        ));
                        
                        //outputs the if data exist or already liked
                        $existStatus = 'no';
                        
                        // this logic is a restriction for account user that is not logged in the like status should be blank
                        if (is_user_logged_in()) {
                          $existQuery= new WP_Query(array(
                            'author' => get_current_user_id(),
                            'post_type' => 'like',
                            'meta_query' => array(
                              array(
                                'key' => 'liked_professor_id',
                                'compare' => '=',
                                'value' => get_the_ID()
                              )
                            )
                          ));
  
                          if($existQuery->found_posts) {
                            $existStatus = 'yes';
                          }
                        }
                        
                        


                      ?>
                      <!-- data-like="<?php echo $existQuery->posts[0]->ID; ?>" -->
                      <span class="like-box" data-professor="<?php the_ID(); ?>" data-exists="<?php echo $existStatus ?>" data-like="<?php echo $existQuery->posts[0]->ID; ?>">
                        <i class="fa fa-heart-o" aria-hidden="true"></i>
                        <i class="fa fa-heart" aria-hidden="true"></i>
                        <span class="like-count"><?php echo $likeCount->found_posts; ?></span>
                      </span>
                    <?php the_content(); ?>
                    </div>
                </div>
            </div>

            <!-- ========== Established Connection Between Program post and Professor ========= -->
             <?php
               
               $relatedPrograms = get_field('related_programs');
               //print_r($relatedPrograms) => php built-in function that lets you see details 
               //& all sorts of infos inside a variable or piece of data
               
               if($relatedPrograms) {
                echo '<hr class="section-break">';
                echo '<h2 class="headline headline--medium">Subject(s) Taught</h2>';
                echo '<ul class="link-list min-list">';
                foreach($relatedPrograms as $program) { ?>
                  <li><a href="<?php echo get_the_permalink($program) ?>"><?php echo get_the_title($program) ?></a></li>
                <?php }
                echo '</ul>';
               }


              
              
             ?>

            </div>
      

      <?php }

      get_footer();
?>