	apidata = { key: ipgp_apikey, ip: ipgp_userip };

	jQuery.ajax({
         type: "GET",
         url: "http://www.ipgp.net/api/json/index.php",
         data: apidata,
         cache: false,
         success: function(returned){
         	
         	//console.log('succes');   
         	//console.log(returned); 
         	returned = returned.substring(9);
         	returned = returned.substring(0, returned.length - 2);
         	ipdata = jQuery.parseJSON(returned);
         	//console.log(ipdata.Details.country);
         	
         	
				senddata = { ipdata: returned, referer: ipgp_referer };

				jQuery.ajax({
        		  type: "POST",
         	  url: ipgp_returnurl,
        		  data: senddata,
        		  cache: false,
         	  success: function(returned){
         	
         	//console.log('succes');   
         	console.log(returned); 
                    
  			
	
                   
     				}
     
  				 });         	
         	
         	
         	
         	
         	
     
	
                   
     		}
     
   });