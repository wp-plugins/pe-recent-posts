<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * Plugin Name: PE Recent Posts
 * Plugin URI: http://pixelemu.com
 * Description: Simple Slider for Posts
 * Version: 1.0.0
 * Author: pixelemu.com
 * Author URI: http://www.pixelemu.com
 * Text Domain: pe-recent-posts
 * License: GPLv2 or later
 */
 

// excerpt limit - BEGIN
if ( ! function_exists( 'get_excerpt_plugin' ) ) {
	function get_excerpt_plugin($count){
	  global $post;
	  $permalink = get_permalink($post->ID);
	  $excerpt = get_the_excerpt();
	  $excerpt = strip_tags($excerpt);
	  $excerpt = substr($excerpt, 0, $count);
	  $excerpt = '<div class="excerpt-text">'.$excerpt.'...</div><a class="readmore" href="'.$permalink.'">'.__('Read more', 'pe-recent-posts').'</a>';
	  return $excerpt;
	}
}
// excerpt limit - END

 
if(!class_exists('PE_Recent_Posts_Plugin')){
    class PE_Recent_Posts_Plugin extends WP_Widget {

        function PE_Recent_Posts_Plugin(){
            $options_widget = array( 'classname' => 'PE_Recent_Posts', 'description' => __('Show recent posts.', 'pe-recent-posts'));
            $this->WP_Widget( 'pe_recent_posts', __('PE Recent Posts', 'pe-recent-posts'), $options_widget );
        }

        function widget($args,  $setup)
        {
            extract($args);
			$count_posts = wp_count_posts('post');
			$number_of_posts = $setup['number_of_posts'];
			$posts_in_row = $setup['posts_in_row'];
			$unique_id = $this->id;
			if ($number_of_posts > $count_posts->publish){
				$number_of_posts = $count_posts->publish;
			}
			$order_posts = $setup['order_posts'];
			$order_direction = $setup['order_direction'];
            $title_widget = apply_filters('widget_title', $setup['title']);
			
			
            if ( empty($title_widget) )
                $title_widget = false;
			
            echo $before_widget;
            echo '<h2 class="widgettitle">';
            echo $title_widget;
            echo '</h2>';
			$desc_limit = $setup['desc_limit'];
			$image_alignment = $setup['image_alignment'];
			$image_size = $setup['image_size'];
			$category_id = $setup['category_id'];
			?>
			<div id="myCarousel<?php echo $unique_id; ?>" class="pe-recent-posts-outer carousel slide">
				<div class="carousel-inner image-<?php echo $image_alignment; ?>">
						<?php 
							$loop = new WP_Query(array('post_type' => 'post', 'posts_per_page' => ''.$number_of_posts.'', 'orderby'=> ''.$order_posts.'', 'order' => ''.$order_direction.'', 'cat' => $category_id)); 
							$counter = 0;
						?>
						<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
								<?php 
								$counter++;
								if ($posts_in_row == 1){ 
									if ($counter == 1){ ?>
										<div class="item active">
									<?php } else { ?>
										<div class="item">
									<?php }?>
									
								<?php } else{
									if (($counter % $posts_in_row == 1)){
											if ($counter == 1){ ?>
												<div class="item active">
											<?php } else { ?>
												<div class="item">
											<?php } ?>
									<?php }
								} ?>
								<ul class="thumbnails">
									<li>
										<?php if ($image_alignment=='bottom') { ?>
										<div class="caption fadeInUp animated <?php if ( has_post_thumbnail()){ echo 'image-on'; } ?>">
											<h5><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>	
											<?php echo get_excerpt_plugin($desc_limit); ?>
										</div> 
										<?php } ?>
										<?php if ( has_post_thumbnail()){ ?>
											<?php 
												echo the_post_thumbnail($image_size);
											 ?>
										<?php } ?>
										<?php if ($image_alignment!='bottom') { ?>
										<div class="caption fadeInUp animated <?php if ( has_post_thumbnail()){ echo 'image-on'; } ?>">
											<h5><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>	
											<?php echo get_excerpt_plugin($desc_limit); ?>
										</div> 
										<?php } ?>
									</li>
								</ul>
								<?php if (($counter % $posts_in_row) == 0){ ?>
									</div>
								<?php } ?> 	
						<?php endwhile; ?>
						<?php if ((($counter % $posts_in_row) != 0) && ($counter >= $posts_in_row)){ ?>
							</div>
						<?php } ?> 
						<?php wp_reset_query(); ?>
			</div>
			<?php 
			if($counter < $posts_in_row){ ?>
			</div>	
			<?php } ?>
	        <?php $counter2 = 0; ?>
		        <ol class="carousel-indicators">
		        	<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
		        		<?php $counter2++; ?>
		        	<?php if (($counter2 % $posts_in_row == 1) || $posts_in_row == 1){
		        		if ($counter2 == 1){ ?>
	        			<li data-target="#myCarousel<?php echo $unique_id; ?>" data-slide-to="0" class="active"></li>
					<?php } else { ?>
						<li data-target="#myCarousel<?php echo $unique_id; ?>" data-slide-to="<?php echo ($counter2 -1)/$posts_in_row; ?>"></li>
					<?php } ?>	
				<?php } ?>
	            <?php endwhile; ?>
	        	</ol>  
		</div>
		<?php
            echo $after_widget;
        }

        //Admin Form

        function form($setup)
        {
            $setup = wp_parse_args( (array) $setup, array('title' => __('MISC Posts', 'pe-recent-posts'),
                'number_of_posts' => '9',
                'posts_in_row' => '3',
                'order_posts' => 'Date',
                'order_direction' => 'DESC',
                'title' => __('PE Recent Posts', 'pe-recent-posts'),
                'desc_limit' => '55',
                'image_alignment' => 'left',
                'image_size' => 'thumbnail',
                'category_id' => '' ) );
				
			$title_widget= esc_attr($setup['title']);
			$number_of_posts = $setup['number_of_posts'];
            $order_posts = $setup['order_posts'];
			$desc_limit = $setup['desc_limit'];
			$image_alignment = $setup['image_alignment'];
			$image_size = $setup['image_size'];
			$category_id = $setup['category_id'];
			$posts_in_row = $setup['posts_in_row'];
            ?>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'pe-recent-posts'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title_widget; ?>" />
            </p>
            <p>
            	<label for="<?php echo $this->get_field_id('category_id'); ?>"><?php _e('Category (empty categories are not displayed)', 'pe-recent-posts'); ?></label>
				<select name="<?php echo $this->get_field_name('category_id'); ?>" id="<?php echo $this->get_field_id('category_id'); ?>">
				 <option value=""><?php _e('All Categories', 'pe-recent-posts'); ?></option> 
				 <?php 
				    $values = array(
				      'orderby' => 'name',
				      'order' => 'ASC',
				      'taxonomy' => 'category'
				     );
				  $categories = get_categories($values); 
				  foreach ($categories as $category) { ?>
				    <option value="<?php echo $category->cat_ID; ?>"<?php selected( $setup['category_id'], $category->cat_ID ); ?>><?php echo $category->cat_name; ?></option>	
				  	<?php } ?>
				</select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('posts_in_row'); ?>"><?php _e('Number of slides in column', 'pe-recent-posts'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('posts_in_row'); ?>" name="<?php echo $this->get_field_name('posts_in_row'); ?>" type="text" value="<?php echo $posts_in_row; ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('number_of_posts'); ?>"><?php _e('Number of posts', 'pe-recent-posts'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('number_of_posts'); ?>" name="<?php echo $this->get_field_name('number_of_posts'); ?>" type="text" value="<?php echo $number_of_posts; ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('order_direction'); ?>"><?php _e('Order Direction', 'pe-recent-posts'); ?></label>
                <select name="<?php echo $this->get_field_name('order_direction'); ?>" id="<?php echo $this->get_field_id('order_direction'); ?>">
                    <option value="ASC"<?php selected( $setup['order_direction'], 'ASC' ); ?>><?php _e('ASC', 'pe-recent-posts'); ?></option>
                    <option value="DESC"<?php selected( $setup['order_direction'], 'DESC' ); ?>><?php _e('DESC', 'pe-recent-posts'); ?></option>
                </select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('order_posts'); ?>"><?php _e('Ordering', 'pe-recent-posts'); ?></label>
                <select name="<?php echo $this->get_field_name('order_posts'); ?>" id="<?php echo $this->get_field_id('order_posts'); ?>">
                    <option value="date"<?php selected( $setup['order_posts'], 'date' ); ?>><?php _e('Date', 'pe-recent-posts'); ?></option>
                    <option value="title"<?php selected( $setup['order_posts'], 'title' ); ?>><?php _e('Title', 'pe-recent-posts'); ?></option>
                    <option value="comment_count"<?php selected( $setup['order_posts'], 'comment_count' ); ?>><?php _e('Most commented', 'pe-recent-posts'); ?></option>
                </select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('desc_limit'); ?>"><?php _e('Description Limit (chars)', 'pe-recent-posts'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('desc_limit'); ?>" name="<?php echo $this->get_field_name('desc_limit'); ?>" type="text" value="<?php echo $desc_limit; ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('image_alignment'); ?>"><?php _e('Image Alignment', 'pe-recent-posts'); ?></label>
                <select name="<?php echo $this->get_field_name('image_alignment'); ?>" id="<?php echo $this->get_field_id('image_alignment'); ?>">
                    <option value="left"<?php selected( $setup['image_alignment'], 'left' ); ?>><?php _e('left', 'pe-recent-posts'); ?></option>
                    <option value="right"<?php selected( $setup['image_alignment'], 'right' ); ?>><?php _e('right', 'pe-recent-posts'); ?></option>
                    <option value="top"<?php selected( $setup['image_alignment'], 'top' ); ?>><?php _e('top', 'pe-recent-posts'); ?></option>
                    <option value="bottom"<?php selected( $setup['image_alignment'], 'bottom' ); ?>><?php _e('bottom', 'pe-recent-posts'); ?></option>
                </select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('image_size'); ?>"><?php _e('Image Size', 'pe-recent-posts'); ?></label>
                <select name="<?php echo $this->get_field_name('image_size'); ?>" id="<?php echo $this->get_field_id('image_size'); ?>">
                    <option value="thumbnail"<?php selected( $setup['image_size'], 'thumbnail' ); ?>><?php _e('thumbnail', 'pe-recent-posts'); ?></option>
                    <option value="medium"<?php selected( $setup['image_size'], 'medium' ); ?>><?php _e('medium', 'pe-recent-posts'); ?></option>
                    <option value="large"<?php selected( $setup['image_size'], 'large' ); ?>><?php _e('large', 'pe-recent-posts'); ?></option>
                </select>
            </p>
        <?php
        }

        //Update widget

        function update($new_setup, $old_setup)
        {
            $setup=$old_setup;
            $setup['title'] = strip_tags($new_setup['title']);
			$setup['number_of_posts']  = $new_setup['number_of_posts'];
			$setup['posts_in_row']  = $new_setup['posts_in_row'];
			$setup['order_posts']  = $new_setup['order_posts'];
			$setup['order_direction']  = $new_setup['order_direction'];
			$setup['desc_limit']  = strip_tags($new_setup['desc_limit']);
			$setup['image_alignment']  = $new_setup['image_alignment'];
			$setup['image_size']  = $new_setup['image_size'];
			$setup['category_id']  = $new_setup['category_id'];
            return $setup;
        }
    }
}

//add CSS
function pe_recent_posts_css() {
	wp_enqueue_style( 'bootstrap.min', plugins_url().'/pe-recent-posts/css/bootstrap.min.css' ); 
	wp_enqueue_style( 'animate', plugins_url().'/pe-recent-posts/css/animate.css' ); 
	wp_enqueue_style( 'pe-recent-posts', plugins_url().'/pe-recent-posts/css/pe-recent-posts.css' ); 
}
add_action( 'wp_enqueue_scripts', 'pe_recent_posts_css' );

//add JS
function pe_recent_posts_js()
{
	wp_enqueue_script('jquery');
	wp_enqueue_script( 'bootstrap.min', plugins_url() . '/pe-recent-posts/js/bootstrap.min.js', array('jquery'), '3.2.0', false );
	wp_enqueue_script( 'pe-recent-posts', plugins_url() . '/pe-recent-posts/js/pe-recent-posts.js', array('jquery'), '1.0.0', false );
}
add_action( 'wp_enqueue_scripts', 'pe_recent_posts_js' );

//load widget
add_action('widgets_init',
     create_function('', 'return register_widget("PE_Recent_Posts_Plugin");')
);
?>