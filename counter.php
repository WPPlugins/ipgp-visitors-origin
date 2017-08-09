<?php
$ipdata = filter_input(INPUT_POST, 'ipdata', FILTER_SANITIZE_SPECIAL_CHARS); 
$referer = filter_input(INPUT_POST, 'referer', FILTER_SANITIZE_SPECIAL_CHARS);
$ipdata = $_POST['ipdata'];
if(!$ipdata) die('no data sent');
require('../../../wp-load.php');


echo $ipdata;


	$api = json_decode($ipdata);
        	$api = $api->Details;
        	$ip = $api->ip;
        	//print_r($api); die();
          	//print_r($api);
			//      echo $api->Country; die();  						
        						
        	if($api->country){					
                	$wpdb->query("INSERT INTO ". $wpdb->prefix ."IPGPVO_ip_date(ip,country) VALUES('".$ip."','".$api->country."')") or die(mysql_error());
                    $wpdb->query("INSERT INTO ". $wpdb->prefix ."IPGPVO_ip_list(ip,hits,country,city,isp,lat,lng,referer) VALUES('".$ip."','1','".$api->country."','".$api->city."','".$api->isp."','".$api->lat."','".$api->long."','".$referer."')") or die(mysql_error());
            }


?>