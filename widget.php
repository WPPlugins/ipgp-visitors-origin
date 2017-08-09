<?php

class RandomPostWidget extends WP_Widget
{
  function __construct()
  {
    $widget_ops = array('classname' => 'RandomPostWidget', 'description' => 'Top 5 countries' );
    //$this->WP_Widget('RandomPostWidget', 'IPGP Visitors origin', $widget_ops);
    parent::__construct('RandomPostWidget', 'IPGP Visitors origin', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
    $title = $instance['title'];
?>
  <p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    return $instance;
  }
 
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
 
    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
 
    if (!empty($title))
      echo $before_title . $title . $after_title;
 
	
	global $wpdb;	
	$wpdb->query("select count(*) from ". $wpdb->prefix ."IPGPVO_ip_date into @total");
	$fields = $wpdb->get_results("SELECT   country,(COUNT(country)/@total*100) AS c FROM ". $wpdb->prefix ."IPGPVO_ip_date WHERE date > DATE_SUB(NOW(), INTERVAL 1 MONTH) GROUP by country LIMIT 0,5");

	foreach($fields as $f)
		{
			echo $f->country." -";
			echo number_format($f->c,1)."%<br/>";			
		}

    echo $after_widget;
  }
 
}
add_action( 'widgets_init', create_function('', 'return register_widget("RandomPostWidget");') );?>