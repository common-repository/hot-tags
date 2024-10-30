<?php
/*
Plugin Name: Hot Tags
Plugin URI: http://movilcrunch.com/hot-tags/
Description: Your most used tags in the last 7 days (or any other interval you choose). See plugin in action in http://movilcrunch.com/
Author: Teofilo Israel Vizcaino Rodriguez
Version: 1.0
Author URI: http://movilcrunch.com/
Demo URI: http://movilcrunch.com/
*/ 
 
class HotTags extends WP_Widget {
    
    function HotTags() {
        //Constructor
        $widget_ops = array('classname' => 'HotTags', 'description' => __('Your most used tags in the last 7 days (or any other interval you choose)'));
        $this->WP_Widget('HotTags', 'Hot Tags', $widget_ops);
    }
    
    /**
     * Prints the most popular tags in the last days specified by $num
     */
    function ti_hot_tags($num,$tags){
        
        global $wpdb;
        $querystr = "SELECT count($wpdb->terms.term_id) AS tagsCount, $wpdb->terms.name, $wpdb->terms.term_id as id FROM $wpdb->posts w

        INNER JOIN $wpdb->term_relationships ON (w.ID = $wpdb->term_relationships.object_id) INNER JOIN $wpdb->term_taxonomy ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id) INNER JOIN $wpdb->terms ON ($wpdb->term_taxonomy.term_id = $wpdb->terms.term_id)
        
        WHERE w.post_status = 'publish' AND w.post_type = 'post' AND w.post_date > (SELECT DATE_SUB(NOW(), INTERVAL '$num' DAY)) AND $wpdb->term_taxonomy.taxonomy = 'post_tag'

        GROUP BY $wpdb->terms.term_id

        ORDER BY tagsCount DESC LIMIT $tags";

        $results = $wpdb->get_results($querystr, OBJECT);
        echo '<ul class="hot-tags-menu">';
        foreach($results as $result){            
            echo '<li>' . '<a href="' . get_tag_link($result->id) . '" title="'.$result->tagsCount. ' posts">'. $result->name . '</a>'.'</li>';
        }
        echo '</ul>';
        
    }
    
	/**
	 * Prints widget for user
	 */    
    function widget($args, $instance) {
        extract($args, EXTR_SKIP);
    
        $days = $instance['days'];
        $tags = $instance['tags'];
        
        $title = apply_filters('widget_title', $instance['title']);
     
        $widget_id = $args['widget_id'];
        
        //do_action( 'TB_RenderWidget',$before_widget,$after_widget,$title,$days,$before_title,
          //                          $after_title);
        $this->renderHot($before_widget,$after_widget,$title,$days,$tags,$before_title,$after_title);
  
    }
    
    function renderHot($before_widget,$after_widget,$title,$days,$tags,$before_title,$after_title){
        //echo $before_widget;
      
        if ( !empty( $title ) ) { 
            echo $before_title . $title . $after_title; 
        };

       HotTags::ti_hot_tags($days,$tags);
        
       //echo $after_widget;
    }
    
    /**
     * Saves the widget
     */
    function update($new_instance, $old_instance) {
    
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['days'] = absint(strip_tags($new_instance['days']));
        $instance['tags'] = absint(strip_tags($new_instance['tags']));
        
        return $instance;
    }
    
    /**
     * Widget form for backend
     */
    function form($instance) {
        $instance = wp_parse_args( (array) $instance, array( 'title' => 'Hot Tags', 'days' => 7, 'tags'=> 5) );
        $title = strip_tags($instance['title']);
        $days = absint($instance['days']);
        $tags = absint($instance['tags']);
    ?>
    
    <p><label for="<?php echo $this->get_field_id('title'); ?>">
        <?php echo esc_html__('Title'); ?>: 
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" 
        type="text" value="<?php echo attribute_escape($title); ?>" />
    </label></p>
    
    <p><label for="<?php echo $this->get_field_id('days'); ?>">
        <?php echo esc_html__('Number of days'); ?>: 
        <input class="widefat" id="<?php echo $this->get_field_id('days'); ?>" name="<?php echo $this->get_field_name('days'); ?>" 
        type="text" value="<?php echo attribute_escape($days); ?>" />
    </label></p>
    
    <p><label for="<?php echo $this->get_field_id('tags'); ?>">
        <?php echo esc_html__('Number of tags to display'); ?>: 
        <input class="widefat" id="<?php echo $this->get_field_id('tags'); ?>" name="<?php echo $this->get_field_name('tags'); ?>" 
        type="text" value="<?php echo attribute_escape($tags); ?>" />
    </label></p>    
            
    <?php
    }
}

/**
 * Use this function if you want to use the plugin directly from the code
 */
function ti_hot_tags($num,$tags){
    HotTags::ti_hot_tags($num,$tags);
}
   
add_action( 'widgets_init', create_function('', 'return register_widget("HotTags");') );
//add_action( 'TB_RenderWidget', array('HotTags', 'renderHot'),12,6);   

?>