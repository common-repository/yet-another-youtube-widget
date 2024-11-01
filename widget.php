<?php

/**
 * @package youtube-widget
 * @author Marc Boivin
 * @version 0.1.1
 */
/*
Plugin Name: Yet another Youtube Widget (but better)
Plugin URI: http://mboivin.com
Description: Fetch and show Youtube videos
Author: Marc Boivin
Version: 0.1.1
*/

// Add function to widgets_init that'll load our widget.
add_action('widgets_init', 'youtubewidget_load_widgets');
	

// Register our widget.
function youtubewidget_load_widgets() {
	register_widget('youtubewidget');
}

class youtubewidget extends WP_Widget {
	function youtubewidget() {
		// widget actual processes
		
		// Widget settings.
		$widget_ops = array('classname' => 'youtube-widget', 'description' => __('Display a list of youtube videos', 'youtube-widget'));

		// Widget control settings.
		$control_ops = array('width' => 300, 'height' => 350, 'id_base' => 'youtube-widget');

		// Create the widget.
		$this->WP_Widget('youtube-widget', __('Youtube Widget', 'youtube-widget'), $widget_ops, $control_ops);
	}

	function form($instance) {
		// outputs the options form on admin
		// Set up some default widget settings.
		$defaults = array('title' => __('Youtube Widget', 'youtube-widget'), 'p_width' => '145', 'p_height' => '105', 'term' => 'Laurent', 'i_width' => '105', 'i_height' => '105', 'videos' => 7);
		$instance = wp_parse_args((array) $instance, $defaults); ?>
		
		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'youtube-widget'); ?></label>
			<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" style="width: 100%;" />
		</p>

		<!-- Search query: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id('term'); ?>"><?php _e('Search Query:', 'youtube-widget'); ?></label>
			<input id="<?php echo $this->get_field_id('term'); ?>" name="<?php echo $this->get_field_name('term'); ?>" value="<?php echo $instance['term']; ?>" style="width: 100%;" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('videos'); ?>"><?php _e('Number of videos:', 'youtube-widget'); ?></label>
			<input id="<?php echo $this->get_field_id('videos'); ?>" name="<?php echo $this->get_field_name('videos'); ?>" value="<?php echo $instance['videos']; ?>" style="width: 100%;" />
		</p>

        <!-- Enable cache: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id('cache'); ?>"><?php _e('Enable cache:', 'youtube-widget'); ?>(Not implemented)</label>
			<input id="<?php echo $this->get_field_id('cache'); ?>" name="<?php echo $this->get_field_name('cache'); ?>" <?php checked( $instance['cache'], true ); ?> type="checkbox" />
		</p>
		<p>
		    <h3><?php _e('Dimensions', 'youtube-widget'); ?></h3>
		    <ul>
		        <li>
		            <label for="<?php echo $this->get_field_id('p_width'); ?>"><?php _e('Player width:', 'youtube-widget'); ?></label>
        			<input id="<?php echo $this->get_field_id('p_width'); ?>" name="<?php echo $this->get_field_name('p_width'); ?>" value="<?php echo $instance['p_width']; ?>" style="width: 100%;" />
                </li>
                <li>
		            <label for="<?php echo $this->get_field_id('p_height'); ?>"><?php _e('Player height:', 'youtube-widget'); ?></label>
        			<input id="<?php echo $this->get_field_id('p_height'); ?>" name="<?php echo $this->get_field_name('p_height'); ?>" value="<?php echo $instance['p_height']; ?>" style="width: 100%;" />
                </li>
                <li>
		            <label for="<?php echo $this->get_field_id('i_width'); ?>"><?php _e('Image width:', 'youtube-widget'); ?></label>
        			<input id="<?php echo $this->get_field_id('i_width'); ?>" name="<?php echo $this->get_field_name('i_width'); ?>" value="<?php echo $instance['i_width']; ?>" style="width: 100%;" />
                </li>
                <li>
		            <label for="<?php echo $this->get_field_id('i_height'); ?>"><?php _e('Image height:', 'youtube-widget'); ?></label>
        			<input id="<?php echo $this->get_field_id('i_height'); ?>" name="<?php echo $this->get_field_name('i_height'); ?>" value="<?php echo $instance['i_height']; ?>" style="width: 100%;" />
                </li>
		</ul>
		</p>
	<?php
	}

	function update($new_instance, $old_instance) {
        $instance = $new_instance;

		// Strip tags for title and name to remove HTML (important for text inputs).
		$instance['title'] = strip_tags($new_instance['title']);

		return $instance;
	}

	function widget($args, $instance) {
		// outputs the content of the widget
		extract($args);
		
		include_once(ABSPATH.WPINC.'/rss.php'); // path to include script
        include "youtube.class.php";

        $feed = fetch_rss('http://gdata.youtube.com/feeds/api/videos?v=2&q='. urlencode($instance['term']) ); // specify feed url
        $items = array_slice($feed->items, 0, intval($instance['videos'])); // specify first and last item

        
        echo "\r".$before_widget;
		
		// Display the widget title if one was input (before and after defined by themes).
		if ($instance['title'])
			echo "\r\t".$before_title.$instance['title'].$after_title;
		
		$is_first = true;
		if (!empty($items)) :
            foreach ($items as $item) : 
            
            $video = new YouTube($item['link']);
            if ($is_first){
                $player_id = $widget_id . '_player';
                $cont_id = $widget_id . '_cont';
                ?>
                <div id="<?php echo $cont_id; ?>">
                    You need Flash player 8+ and JavaScript enabled to view this video.
                  </div>
                
                <script type="text/javascript">

                    var params = { allowScriptAccess: "always" };
                    var atts = { id: "<?php echo $player_id; ?>" };
                    swfobject.embedSWF("http://www.youtube.com/v/<?php echo $video->getID(); ?>?enablejsapi=1&playerapiid=<?php echo $player_id; ?>", 
                                       "<?php echo $cont_id; ?>", "<?php echo $instance['p_width']; ?>", "<?php echo $instance['p_height']; ?>", "8", null, null, params, atts);

                  </script>
                  <ul>
                <?php
                //echo $video->EmbedVideo($item['link'], $instance['p_width'], $instance['p_height'] );
                $is_first = false;
            }
            
            echo '<li><a href="http://www.youtube.com/watch?v='.$video->getID().'" class="link">'.$video->ShowImg( $instance['i_width'], $instance['i_height'] ).'</a></li>';
            //echo $item['title'];


            endforeach;
            echo '</ul>';
        endif;
        
        // After widget (defined by themes).
		echo $after_widget;
	}
	
	public static function hook_style_js( ){   	
        add_action('init', 'youtubewidget::load_js');
        add_action('wp_head', 'youtubewidget::load_style');
	}
	
	public static function load_style( ){
	    echo "\r\t<link rel=\"stylesheet\" href=\"";
    	bloginfo('url');
    	echo "/wp-content/plugins/yayw/frontend.css\" type=\"text/css\" media=\"screen\" />\r";
	}
	
	public static function load_js( ){
	    //load scripts
	    wp_enqueue_script('jquery');
    	wp_enqueue_script('youtube-widget', get_bloginfo('url').'/wp-content/plugins/yayw/frontend.js' );
    	wp_enqueue_script('swfobject');
	}

}
add_action('init', 'youtubewidget::load_js');
add_action('wp_head', 'youtubewidget::load_style');
