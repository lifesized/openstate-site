<?php

  function openstate_enqueue_scripts() {
      wp_enqueue_script(
        'slidejs',
        get_stylesheet_directory_uri() . '/scripts/slides.min.jquery.js',
        array('jquery')
      );
      wp_enqueue_script(
        'openstatejs',
        get_stylesheet_directory_uri() . '/scripts/openstate.js',
        array('slidejs'),
        false,
        true
      );                
  }    
  add_action('wp_enqueue_scripts', 'openstate_enqueue_scripts');

  // Add custom post type for announcements
  add_action( 'init', 'create_my_post_types' );

  function create_my_post_types() {
  	register_post_type( 'announcement', 
  		array(
  			'labels' => array(
  				'name' => 'Announcements',
  				'singular_name' => 'Announcement'
  			),
        'supports' => array(  
          'title',
          'excerpt'
        ),
        'taxonomies' => array(
          'category'
        ),
  			'public' => true,
        'menu_position' => 5,
        'hierarchical' => false
  		)
  	);
  }
  
  // Add announcements to top of sidebar
  function openstate_abovemainasides()  {  
    $args = array( 
      'post_type' => array(
        'announcement',
        'post'
      ),
      'category_name' => 'events',
      'posts_per_page' => 3 );
    $loop = new WP_Query( $args );
    ?>
    <div class="aside main-aside">
  		<ul class="xoxo">
  			<li id="announcements" class="widgetcontainer widget_announcement">
          <span id="announcement_icon"></span><span class="widgettitle">Announcements</span></br>
          <div class="slides_container">
            <?php 
              while ( $loop->have_posts() ) : $loop->the_post();
                echo '<div>';
                echo '<h4 class=\'announcement-title\'>';
                the_title();
                echo '</h4>';
                the_excerpt();
                echo '</div>';
              endwhile;
            ?>
          </div>
      </li>
  		</ul>
    </div>
    <?php
  } 
  add_action('thematic_abovemainasides','openstate_abovemainasides');
  
  // Add mission statements to header
  function openstate_belowheader() {
    if(is_home()){
      $args = array( 
        'post_type' => array(
          'announcement',
          'post'
        ),
        'category_name' => 'mission_statements',
        'posts_per_page' => 3 );
      $loop = new WP_Query( $args );
      ?>
      <div class='statements'>
        <div class="slides_container">
          <?php
            while ( $loop->have_posts() ) : $loop->the_post();
              echo '<div>';
              kd_mfi_the_featured_image( 'statement-head', 'post', 'full' ) || kd_mfi_the_featured_image( 'statement-head', 'announcement', 'full' );
              echo '<div>';
              echo '<h3 class=\'statement-title\'>';
              the_title();
              echo '</h3>';
              the_excerpt();
              echo '</div>';
              echo '</div>';
            endwhile;
          ?>
        </div>
      </div>
      <?php
    }
  }
  add_action('thematic_belowheader','openstate_belowheader');
  
  // Show excerpt instead of full posts on front page
  function openstate_thematic_content($post) {
  	if (is_home() || is_front_page()) {
  	    $post = 'excerpt';
  	}
  	return apply_filters('openstate_thematic_content', $post);
  }
  add_filter('thematic_content', 'openstate_thematic_content');
  
  // Filter author and seperators from post-meta block
  function openstate_thematic_postmeta_entrydate() {
	
    $entrydate .= '<span class="entry-date"><abbr class="published" title="';
    $entrydate .= get_the_time(thematic_time_title()) . '">';
    $entrydate .= get_the_time(thematic_time_display());
    $entrydate .= '</abbr></span>';
	    
    return apply_filters('thematic_post_meta_entrydate', $entrydate);  
  }   
  function openstate_thematic_postheader_postmeta($postmeta) {
 
    if(is_single()){
      
  	  $postmeta;
 
    }
    else {
      
      $postmeta = '<div class="entry-meta">';
      $postmeta .= openstate_thematic_postmeta_entrydate();
      $postmeta .= '<span class="cat-list">';
      $postmeta .= get_the_category_list(', ');
      $postmeta .= '</span>';
      $postmeta .= "</div><!-- .entry-meta -->\n";
      
    }
    
    return apply_filters('openstate_thematic_postheader_postmeta',$postmeta);     
  }    
  add_filter('thematic_postheader_postmeta','openstate_thematic_postheader_postmeta');
  
  // Add avatar to author link
  function childtheme_override_postmeta_authorlink(){
		global $authordata;
    
      $author_avatar = '<span class="post-author" >';
      $author_avatar .= get_avatar( get_the_author_meta('ID'), 32 );
      $author_avatar .= '</span>';  
	
	    $author_prep = '<span class="meta-prep meta-prep-author">' . __('Posted by', 'thematic') . ' </span>';
	    
	    if ( thematic_is_custom_post_type() && !current_theme_supports( 'thematic_support_post_type_author_link' ) ) {
	    	$author_info  = '<span class="vcard"><span class="fn nickname">';
	    	$author_info .= get_the_author_meta( 'display_name' ) ;
	    	$author_info .= '</span></span>';
	    } else {
	    	$author_info  = '<span class="author vcard">';
	    	$author_info .= sprintf('<a class="url fn n" href="%s" title="%s">%s</a>',
	    							get_author_posts_url( $authordata->ID, $authordata->user_nicename ),
									/* translators: author name */
	    							sprintf( esc_attr__( 'View all posts by %s', 'thematic' ), get_the_author_meta( 'display_name' ) ),
	    							get_the_author_meta( 'display_name' ));
	    	$author_info .= '</span>';
	    }
	    
	    $author_credit = $author_avatar . $author_prep . $author_info ;
	    
	    return apply_filters('thematic_postmeta_authorlink', $author_credit);
  }
  
  // Increase post thumbnail image thumbnail size
  function hdo_thematic_post_thumb_size() {
      return apply_filters('hdo_thematic_post_thumb_size', array(260, 260));
  }
  add_filter('thematic_post_thumb_size','hdo_thematic_post_thumb_size');
  
  // Add featured image for single post header
  $singleposthead = array(
          'id' => 'single-post-head',
          'post_type' => 'post',
          'labels' => array(
              'name'      => 'Singel Post Head Image',
              'set'       => 'Set image (620x410)',
              'remove'    => 'Remove image',
              'use'       => 'Use as post head',
          )
  );

  // Add featured image for single post header
  $statementhead_post = array(
          'id' => 'statement-head',
          'post_type' => 'post',
          'labels' => array(
              'name'      => 'Mission Statement Head Image',
              'set'       => 'Set image (660x310)',
              'remove'    => 'Remove image',
              'use'       => 'Use as mission statement image',
          )
  );
  
  // Add featured image for single post header
  $statementhead_announcement = array(
          'id' => 'statement-head',
          'post_type' => 'announcement',
          'labels' => array(
              'name'      => 'Mission Statement Head Image',
              'set'       => 'Set image (660x310)',
              'remove'    => 'Remove image',
              'use'       => 'Use as mission statement image',
          )
  );
  
  new kdMultipleFeaturedImages( $singleposthead );
  new kdMultipleFeaturedImages( $statementhead_post );
  new kdMultipleFeaturedImages( $statementhead_announcement );
  
  // Add featured image to single posts
  function openstate_thematic_postheader_posttitle($posttitle){
    
    if(is_single()){
      $image = kd_mfi_the_featured_image( 'single-post-head', 'post', 'full' );
      $posttitle = $image . $posttitle; 
    }
    
    return apply_filters('openstate_thematic_postheader_posttitle', $posttitle);
  }
  add_filter('thematic_postheader_posttitle','openstate_thematic_postheader_posttitle');
  
?>