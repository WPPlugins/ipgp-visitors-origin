<?php 
	
	 global $wpdb;
	 
	 delete_option('acces_key');
	
	$wpdb->query('DROP TABLE '. $wpdb->prefix .'IPGPVO_ip_list');
	$wpdb->query('DROP TABLE '. $wpdb->prefix .'IPGPVO_ip_date');
	
?>