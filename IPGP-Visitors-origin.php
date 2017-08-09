<?php
/*
Plugin Name: IPGP Visitors Origin 
Plugin URI: http://www.ipgp.net
Description: Find information about ip address.
Author: Lucian Apostol
Version: 1.4
Author URI: http://www.ipgp.net
*/
//error_reporting(0);

require "widget.php";

add_action("admin_menu", "IPGPVO_menus");
add_action('wp_head', 'IPGPVO_counter');
add_shortcode( 'ipgp-report', 'ipgp_report_public' );
add_action( 'admin_init', 'IPGPVO_admin_init' );


function IPGPVO_admin_init() {
       /* Register our stylesheet. */
       wp_register_style( 'IPGPVO_stylesheet', plugins_url('styles.css', __FILE__) );
   }


function IPGPVO_isBot()
{
    /* This function will check whether the visitor is a search engine robot */
 
    $bots = array("Teoma", "froogle", "Gigabot", "inktomi",
    "looksmart", "URL_Spider_SQL", "Firefly", "NationalDirectory",
    "Ask Jeeves", "TECNOSEEK", "InfoSeek", "WebFindBot", "girafabot",
    "crawler", "www.galaxy.com", "Googlebot", "Scooter", "Slurp",
    "msnbot", "appie", "WebBug", "Spade", "ZyBorg", "rabaz",
    "Baiduspider", "Feedfetcher-Google", "TechnoratiSnoop", "Rankivabot",
    "Mediapartners-Google", "Sogou web spider", "WebAlta Crawler","TweetmemeBot",
    "Butterfly","Twitturls","Me.dium","Twiceler");
 
    foreach($bots as $bot)
    {
            if(strpos($_SERVER['HTTP_USER_AGENT'], $bot) !== false)
                return true;
    }
 
    return false;
}



function IPGPVO_menus() {
     $page = add_menu_page("IPGP Visitor Origin", "IPGP Visitor Origin", 0, "ipgp-visitor-origin", "ipgp_report");
	
	 add_action( 'admin_print_styles-' . $page, 'IPGPVO_admin_styles' );
}

function IPGPVO_admin_styles() {
       /*
        * It will be called only on your plugin admin page, enqueue our stylesheet here
        */
       wp_enqueue_style( 'IPGPVO_stylesheet' );
}


function IPGPVO_counter(){
	global $wpdb;
	$ip=$_SERVER['REMOTE_ADDR'];
	//$ip="123.123.123.123";	
	//$ip="81.12.171.90";
    $wpdb->query("SELECT * FROM ". $wpdb->prefix ."IPGPVO_ip_list WHERE ip='".$ip."'");
	
	if($wpdb->num_rows>0) $wpdb->query("UPDATE `". $wpdb->prefix ."IPGPVO_ip_list` SET hits=(hits+1) WHERE ip='".$ip."'");
	else{
		
		if(!IPGPVO_isBot()) {
			
			echo '<script type="text/javascript">var ipgp_userip = "'. $ip .'"; var ipgp_returnurl = "'. plugins_url('counter.php', __FILE__) .'"; var ipgp_referer = "'. $_SERVER["HTTP_REFERER"] .'"; var ipgp_apikey = "'. get_option('acces_key') .'";</script>
			<script type="text/javascript" src="'. plugins_url('counter.js', __FILE__) .'" ></script>';

        /*	$api_key=get_option('acces_key');
        	$file = "http://www.ipgp.net/api/json/".$ip."/".$api_key."";
        	//echo $file;
        	$json = file_get_contents($file);
        	$json = substr($json, 9);
        	$json = substr($json, 0, -2);
        	$api = json_decode($json);
        	$api = $api->Details;
        	//print_r($api); die();
        	$referer=$_SERVER["HTTP_REFERER"];
        	//print_r($api);
			//      echo $api->Country; die();  						
        						
        	if(!IPGPVO_isBot() && $api->country){					
                	$wpdb->query("INSERT INTO ". $wpdb->prefix ."IPGPVO_ip_date(ip,country) VALUES('".$ip."','".$api->country."')") or die(mysql_error());
                    $wpdb->query("INSERT INTO ". $wpdb->prefix ."IPGPVO_ip_list(ip,hits,country,city,isp,lat,lng,referer) VALUES('".$ip."','1','".$api->country."','".$api->city."','".$api->isp."','".$api->lat."','".$api->long."','".$referer."')") or die(mysql_error());
            } */
            
         }   
            
        }
}


function ipgp_report(){
	
	$access_key = get_option('acces_key');
	
	
		if(isset($_POST['action'])){
			if($_POST['action']=='add_key'){
				
			delete_option('acces_key');
			add_option('acces_key',$_POST['key']);
			echo'<meta http-equiv="refresh" content="0;URL='.$_SERVER['PHP_SELF'].'?page=ipgp-visitor-origin">';
			
			
			}
		}		
	
	
	
	
	if(!get_option('acces_key')){
		
		
				echo '
				<h1>IPGP Visitors Origin Plugin</h1>
				<p>This plugin will give you information about your website visitors.</p>
				<p>You will be able to see a map with country origin of your visitors.</p>
				<br />
				<p>The plugin retrieve the data from IPGP IP Address Lookup. To be able to make queries, you have to get your own API key from: <a href="http://www.ipgp.net/get-api-key/">http://www.ipgp.net/get-api-key/</a></p>
				<br/>
				<p>If you already have an API key, enter it into the text field below and click Save.</p>
				<br/><br/>
				<form  method="post" action="'.$_SERVER['PHP_SELF'].'?page=ipgp-visitor-origin">
						
						<input type="hidden" name="action" value="add_key"/>
						API Key: <input type="text" name="key" value="'. $access_key .'" /><input type="submit"  value="Save" />
					  </form>';
			
	}
	else 
		{
			
			global $wpdb;
		
		if(isset($_GET['p']) and $_GET['p']!=0)$page=(int)$_GET['p'];
		else $page=1;
		
		$table_name = $wpdb->prefix ."IPGPVO_ip_list";
		$limit=20;
		$wpdb->get_results( "SELECT * FROM ".$table_name);
		$total=$wpdb->num_rows;
		
		$pages=round($total/$limit);
		$current=($page-1)*$limit;
		$fields = $wpdb->get_results( "SELECT * FROM ".$table_name." ORDER BY id DESC LIMIT $current,$limit");
		
		echo"
		<h1>IPGP Visitors Origin Plugin</h1>
		<br/><p>Here you will see a list with all your latest visitors to your website. </p>
		
		";
		
		if(!$fields) { echo "<p>Please allow some visitors to be recorded until you start seeing data here</p>"; }
		
		echo "
		<br/><br/><br /><table><tr>";
		echo"<td>Ip</td>";
	echo"	<td>Hits</td>
		<td>Country </td>
		<td>City</td>
		<td>ISP</td>
		<td>Referer</td>
		</tr>";
		
		$var=array();
		
				foreach($fields as $f)
					{	
						
						 
						$lat=$f->lat;
						$lng=$f->lng;
						if($lat!='' and $lng!='')$var[]=array('lat'=>$lat,'lng'=>$lng);
						echo "<tr>";
						echo"<td>".$f->ip."</td>";
						echo"	<td>".$f->hits."</td>
							<td>".$f->country."</td>
							<td>".$f->city."</td>
							<td>".$f->isp."</td>
							<td>".$f->referer."</td>
							</tr>";
					}
		
		
		
		echo "</table><br/>";

				if($_GET['p']>1)echo "<a href='".$_SERVER['PHP_SELF']."?page=ipgp-visitor-origin&p=".($_GET['p']-1) ."'>Prev </a>";
				
				for($i=1;$i<=$pages;$i++)
echo "<a href='".$_SERVER['PHP_SELF']."?page=ipgp-visitor-origin&p=".$i."'> ".$i." </a>";
								
				if($_GET['p']<$pages)echo "<a href='".$_SERVER['PHP_SELF']."?page=ipgp-visitor-origin&p=".($_GET['p']+1) ."'> Next</a>><br/><br/>";

				$t=$wpdb->get_results("SELECT  DISTINCT country   FROM ". $wpdb->prefix ."IPGPVO_ip_list ")or die(mysql_error());
				 

echo' 
  <script src="http://maps.google.com/maps/api/js?sensor=false" 
          type="text/javascript"></script>

  <div id="map" style="width: 900px; height: 600px;"></div>

  <script type="text/javascript">
    var locations = [';
                    
                    $first=1;
				 foreach($t as $r)
					{
					    
						$v=$wpdb->get_results("SELECT COUNT(country) as nr ,lat,lng,country FROM ". $wpdb->prefix ."IPGPVO_ip_list WHERE `country`='".$r->country."'")or die(mysql_error());
						foreach($v as $c)
							if($c->nr!='' and $c->lat!='' and $c->lng!='')
							     {
							         if($first==0)echo",";
                                     echo "['".$c->country."-".$c->nr." visitors','".$c->lat."','".$c->lng."']";
                                 
                                 }
					        
                            $first=0;
					}

    
	echo'];

    var map = new google.maps.Map(document.getElementById(\'map\'), {
      zoom: 3,
      center: new google.maps.LatLng(46.694667,25.015869),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var infowindow = new google.maps.InfoWindow();

    var marker, i;

    for (i = 0; i < locations.length; i++) {  
      marker = new google.maps.Marker({
        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
        map: map
      });

      google.maps.event.addListener(marker, \'click\', (function(marker, i) {
        return function() {
          infowindow.setContent(locations[i][0]);
          infowindow.open(map, marker);
        }
      })(marker, i));
    }
  </script>
';	
				
				
				
			echo '				<br /><br/><br/>
				<form  method="post" action="'.$_SERVER['PHP_SELF'].'?page=ipgp-visitor-origin">
						
						<input type="hidden" name="action" value="add_key"/>
						API Key: <input type="text" name="key" value="'. $access_key .'" /><input type="submit"  value="Save" />
					  </form>';
				
			

		}
}


function ipgp_report_public(){
	
			
			global $wpdb;
		
		if(isset($_GET['p']) and $_GET['p']!=0)$page=(int)$_GET['p'];
		else $page=1;
		
		$table_name = $wpdb->prefix ."IPGPVO_ip_list";
		$limit=20;
		$wpdb->get_results( "SELECT * FROM ".$table_name);
		$total=$wpdb->num_rows;
		
		$pages=round($total/$limit);
		$current=($page-1)*$limit;
		$fields = $wpdb->get_results( "SELECT * FROM ".$table_name." ORDER BY id DESC LIMIT $current,$limit");
		
		echo"<br/><br/><table ><tr>";

	echo"	<td>Hits</td>
		<td>Country </td>
		<td>City</td>
		<td>ISP</td></tr>";
		
		$var=array();
		
				foreach($fields as $f)
					{	
						
						 
						$lat=$f->lat;
						$lng=$f->lng;
						$var[]=array('lat'=>$lat,'lng'=>$lng);
						echo "<tr>";
						if($ip==1)echo"<td>".$f->ip."</td>";
						echo"	<td>".$f->hits."</td>
							<td>".$f->country."</td>
							<td>".$f->city."</td>
							<td>".$f->isp."</td>
							
							</tr>";
					}
		
		
		
		echo "</table><br/><br/>";

				if($_GET['p']>1)echo "<a href='".$_SERVER['PHP_SELF']."?page=ipgp&p=".($_GET['p']-1) ."'>Prev </a>";
				
				for($i=1;$i<=$pages;$i++)
echo "<a href='".$_SERVER['PHP_SELF']."?page=ipgp-visitor-origin&p=".$i."'> ".$i." </a>";
								
				if($_GET['p']<$pages)echo "<a href='".$_SERVER['PHP_SELF']."?page=ipgp-visitor-origin&p=".($_GET['p']+1) ."'> Next</a>";

				$t=$wpdb->get_results("SELECT  DISTINCT city   FROM ". $wpdb->prefix ."IPGPVO_ip_list ")or die(mysql_error());
				 

echo' 
  <script src="http://maps.google.com/maps/api/js?sensor=false" 
          type="text/javascript"></script>

  <div id="map" style="width: 600px; height: 400px;"></div>

  <script type="text/javascript">
    var locations = [';
				 foreach($t as $r)
					{
						$v=$wpdb->get_results("SELECT COUNT(city) as nr ,lat,lng,city FROM ". $wpdb->prefix ."IPGPVO_ip_list WHERE `city`='".$r->city."'")or die(mysql_error());
						foreach($v as $c)
							if($c->nr!='' and $c->lat!='' and $c->lng!='')
							echo "['".$c->city."-".$c->nr." visitors',".$c->lat.",".$c->lng."]";
					
					}

    
	echo'];

    var map = new google.maps.Map(document.getElementById(\'map\'), {
      zoom: 4,
      center: new google.maps.LatLng(46.694667,25.015869),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var infowindow = new google.maps.InfoWindow();

    var marker, i;

    for (i = 0; i < locations.length; i++) {  
      marker = new google.maps.Marker({
        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
        map: map
      });

      google.maps.event.addListener(marker, \'click\', (function(marker, i) {
        return function() {
          infowindow.setContent(locations[i][0]);
          infowindow.open(map, marker);
        }
      })(marker, i));
    }
  </script>
';	
				
		
}



register_activation_hook(__FILE__,'IPGPVO_install');
register_deactivation_hook(__FILE__,'IPGPVO_uninstall');




function IPGPVO_install() {
  global $wpdb;
  require_once(ABSPATH . 'wp-admin/includes/upgrade.php'); 
  
  $num = 0;
  $it_tables[$num]['table_name'] =  $wpdb->prefix .'IPGPVO_ip_list';
  $it_tables[$num]['table_sql'] = "CREATE TABLE ". $wpdb->prefix ."IPGPVO_ip_list(
  	id mediumint(9) NOT NULL AUTO_INCREMENT,
	  ip text NOT NULL,
	  hits int NOT NULL,
	  country text NOT NULL,
	  city text NOT NULL,
	  isp text NOT NULL,
	  lat text NOT NULL,
	  lng text NOT NULL,
	  referer text NOT NULL,
	  UNIQUE KEY id (id)
	) ";  

  $num++;


$it_tables[$num]['table_name'] =  $wpdb->prefix .'IPGPVO_ip_date';
  $it_tables[$num]['table_sql'] = "CREATE TABLE ". $wpdb->prefix ."IPGPVO_ip_date(
  	id mediumint(9) NOT NULL AUTO_INCREMENT,
	  ip text NOT NULL,
	  country text NOT NULL,
	  date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	  
	  UNIQUE KEY id (id)
	) ";    	



  foreach($it_tables as $it_table) {
    if(!$wpdb->get_var("SHOW TABLES LIKE '{$it_table['table_name']}'")) {
      $wpdb->query($it_table['table_sql']) or die(mysql_error());
		}
  }
}


function IPGPVO_uninstall() {
  global $wpdb;
  
	delete_option('acces_key');
	
	$wpdb->query('DROP TABLE '. $wpdb->prefix .'IPGPVO_ip_list');
	$wpdb->query('DROP TABLE '. $wpdb->prefix .'IPGPVO_ip_date');
  
  }

?>
