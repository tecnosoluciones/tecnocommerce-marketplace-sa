<?php
/**
 * Runs only when the plugin is activated.
 * @since 0.1.0
 */

function scd_admin_notice_scd_activation_hook() {

	/* Create transient data */
	set_transient( 'scd-admin-notice', true, 0 );
}




/* Add admin notice */
add_action( 'admin_notices', 'scd_admin_notice_scd_notice' );


/**
 * Admin Notice on Activation.
 * @since 0.1.0
 */
function scd_admin_notice_scd_notice(){

	/* Check transient */
	
	if( get_transient( 'scd-admin-notice' ) ){

//$wcfmmarketplace=wcfm_is_marketplace();


$pathscdwcfm = ABSPATH . 'wp-content/plugins/scd_wcfm_marketplace/index.php';

$pathscddokan = ABSPATH . 'wp-content/plugins/scd_dokan_marketplace/index.php';

$pathscdwcmp = ABSPATH . 'wp-content/plugins/scd_wcmp_marketplace/index.php';

$pathscdwcv = ABSPATH . 'wp-content/plugins/scd_wcv_marketplace/index.php';

if((is_plugin_active('wc-multivendor-marketplace/wc-multivendor-marketplace.php')) && ( ! file_exists( $pathscdwcfm ))){

$scd_free_icon= plugins_url('images/scd_free_icon.jpg', __FILE__ );
     ?>
<style>
body {font-family: Arial, Helvetica, sans-serif;}

/* The Modal (background) */
.modal {
display: none; /* Hidden by default */
position: fixed; /* Stay in place */
z-index: 1; /* Sit on top */
padding-top: 100px; /* Location of the box */
left: 0;
top: 0;
width: 100%; /* Full width */
height: 100%; /* Full height */
overflow: auto; /* Enable scroll if needed */
background-color: rgb(0,0,0); /* Fallback color */
background-color: rgba(0,0,0,0.4); /* Black w/ opacity */

}

/* Modal Content */
.modal-content {
background-color: #fefefe;
margin: auto;
padding: 20px;
border: 1px solid #888;
width: 50%;
border-radius:25px;

left: 10%;
top: 80%;

}

/* The Close Button */
.close {
color: #aaaaaa;
float: right;
font-size: 28px;
font-weight: bold;
}

.close:hover,
.close:focus {
color: #000;
text-decoration: none;
cursor: pointer;
}
</style>

<body>

<!-- The Modal -->
<div id="myModal" class="modal">

<!-- Modal content -->
<div class="modal-content">
<span class="close">&times;</span>
<p style="font-size:18px"><img src="<?php echo $scd_free_icon; ?>"><strong>&emsp;SMART CURRENCY DETECTOR </strong>.</p>
<p style="font-size:16px"> Thank you for installing the free variant of our end2end currencies handler plugin SCD.</p>
<p style="font-size:16px"> This variant has limited features  when you use <strong style="color:red; font-weight: bold;"> WCFM marketplace </strong>in your store. </p>
<p style="font-size:16px"> <strong style="font-weight:bold"> You can  subscribe for 14 days trial  to SCD premium variant  for WCFM  under this link <a href="https://gajelabs.com/" target="_blank"> https://www.gajelabs.com/14-day-free-trial/ </a>. So to get full features. </strong></p>
<p style="font-size:16px"> <strong style="color:red; font-weight: bold;"> SCD premium variant for WCFM </strong> extends vendor dashboard to allow each vendor to define the default currency in which the want to enter their products prices. </p> 

</div>

</div>

<script>

function myfunction() {
// jQuery('#myModal').modal('toggle')

// Get the modal
var modal = document.getElementById("myModal");

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 
//btn.onclick = function() {
modal.style.display = "block";
//}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
if (event.target == modal) {
modal.style.display = "none";
}
}

}
window.onload = myfunction;
</script>

</body>

<?php

} elseif((is_plugin_active('dokan-lite/dokan.php')) && ( ! file_exists( $pathscddokan ))){




  $scd_free_icon= plugins_url('images/scd_free_icon.jpg', __FILE__ );
  ?>
<style>
body {font-family: Arial, Helvetica, sans-serif;}

/* The Modal (background) */
.modal {
display: none; /* Hidden by default */
position: fixed; /* Stay in place */
z-index: 1; /* Sit on top */
padding-top: 100px; /* Location of the box */
left: 0;
top: 0;
width: 100%; /* Full width */
height: 100%; /* Full height */
overflow: auto; /* Enable scroll if needed */
background-color: rgb(0,0,0); /* Fallback color */
background-color: rgba(0,0,0,0.4); /* Black w/ opacity */

}

/* Modal Content */
.modal-content {
background-color: #fefefe;
margin: auto;
padding: 20px;
border: 1px solid #888;
width: 50%;
border-radius:25px;

left: 10%;
top: 80%;

}

/* The Close Button */
.close {
color: #aaaaaa;
float: right;
font-size: 28px;
font-weight: bold;
}

.close:hover,
.close:focus {
color: #000;
text-decoration: none;
cursor: pointer;
}
</style>

<body>

<!-- The Modal -->
<div id="myModal" class="modal">

<!-- Modal content -->
<div class="modal-content">
<span class="close">&times;</span>
<p style="font-size:18px"><img src="<?php echo $scd_free_icon; ?>"><strong>&emsp;SMART CURRENCY DETECTOR </strong>.</p>
<p style="font-size:16px"> Thank you for installing the free variant of our end2end currencies handler plugin SCD.</p>
<p style="font-size:16px"> This variant has limited features  when you use <strong style="color:red; font-weight: bold;"> DOKAN marketplace </strong>in your store. </p>
<p style="font-size:16px"> <strong style="font-weight:bold"> You can  subscribe for 14 days trial  to SCD premium variant  for DOKAN  under this link <a href="https://gajelabs.com/" target="_blank"> https://www.gajelabs.com/14-day-free-trial/ </a>. So to get full features. </strong></p>
<p style="font-size:16px"> <strong style="color:red; font-weight: bold;"> SCD premium variant for DOKAN </strong> extends vendor dashboard to allow each vendor to define the default currency in which the want to enter their products prices. </p> 

</div>

</div>

<script>

function myfunction() {
// jQuery('#myModal').modal('toggle')

// Get the modal
var modal = document.getElementById("myModal");

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 
//btn.onclick = function() {
modal.style.display = "block";
//}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
if (event.target == modal) {
modal.style.display = "none";
}
}

}
window.onload = myfunction;
</script>

</body>

<?php


}

elseif((is_plugin_active('dc-woocommerce-multi-vendor/dc_product_vendor.php')) && ( ! file_exists( $pathscdwcmp ))){




  $scd_free_icon= plugins_url('images/scd_free_icon.jpg', __FILE__ );
  ?>
<style>
body {font-family: Arial, Helvetica, sans-serif;}

/* The Modal (background) */
.modal {
display: none; /* Hidden by default */
position: fixed; /* Stay in place */
z-index: 1; /* Sit on top */
padding-top: 100px; /* Location of the box */
left: 0;
top: 0;
width: 100%; /* Full width */
height: 100%; /* Full height */
overflow: auto; /* Enable scroll if needed */
background-color: rgb(0,0,0); /* Fallback color */
background-color: rgba(0,0,0,0.4); /* Black w/ opacity */

}

/* Modal Content */
.modal-content {
background-color: #fefefe;
margin: auto;
padding: 20px;
border: 1px solid #888;
width: 50%;
border-radius:25px;

left: 10%;
top: 80%;

}

/* The Close Button */
.close {
color: #aaaaaa;
float: right;
font-size: 28px;
font-weight: bold;
}

.close:hover,
.close:focus {
color: #000;
text-decoration: none;
cursor: pointer;
}
</style>

<body>

<!-- The Modal -->
<div id="myModal" class="modal">

<!-- Modal content -->
<div class="modal-content">
<span class="close">&times;</span>
<p style="font-size:18px"><img src="<?php echo $scd_free_icon; ?>"><strong>&emsp;SMART CURRENCY DETECTOR </strong>.</p>
<p style="font-size:16px"> Thank you for installing the free variant of our end2end currencies handler plugin SCD.</p>
<p style="font-size:16px"> This variant has limited features  when you use <strong style="color:red; font-weight: bold;"> WCMP marketplace </strong>in your store. </p>
<p style="font-size:16px"> <strong style="font-weight:bold"> You can  subscribe for 14 days trial  to SCD premium variant  for WCMP  under this link <a href="https://gajelabs.com/" target="_blank"> https://www.gajelabs.com/14-day-free-trial/ </a>. So to get full features. </strong></p>
<p style="font-size:16px"> <strong style="color:red; font-weight: bold;"> SCD premium variant for WCMP </strong> extends vendor dashboard to allow each vendor to define the default currency in which the want to enter their products prices. </p> 

</div>

</div>

<script>

function myfunction() {
// jQuery('#myModal').modal('toggle')

// Get the modal
var modal = document.getElementById("myModal");

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 
//btn.onclick = function() {
modal.style.display = "block";
//}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
if (event.target == modal) {
modal.style.display = "none";
}
}

}
window.onload = myfunction;
</script>

</body>

<?php


}

elseif((is_plugin_active('wc-vendors/class-wc-vendors.php')) && ( ! file_exists( $pathscdwcv ))){




  $scd_free_icon= plugins_url('images/scd_free_icon.jpg', __FILE__ );
  ?>
<style>
body {font-family: Arial, Helvetica, sans-serif;}

/* The Modal (background) */
.modal {
display: none; /* Hidden by default */
position: fixed; /* Stay in place */
z-index: 1; /* Sit on top */
padding-top: 100px; /* Location of the box */
left: 0;
top: 0;
width: 100%; /* Full width */
height: 100%; /* Full height */
overflow: auto; /* Enable scroll if needed */
background-color: rgb(0,0,0); /* Fallback color */
background-color: rgba(0,0,0,0.4); /* Black w/ opacity */

}

/* Modal Content */
.modal-content {
background-color: #fefefe;
margin: auto;
padding: 20px;
border: 1px solid #888;
width: 50%;
border-radius:25px;

left: 10%;
top: 80%;

}

/* The Close Button */
.close {
color: #aaaaaa;
float: right;
font-size: 28px;
font-weight: bold;
}

.close:hover,
.close:focus {
color: #000;
text-decoration: none;
cursor: pointer;
}
</style>

<body>

<!-- The Modal -->
<div id="myModal" class="modal">

<!-- Modal content -->
<div class="modal-content">
<span class="close">&times;</span>
<p style="font-size:18px"><img src="<?php echo $scd_free_icon; ?>"><strong>&emsp;SMART CURRENCY DETECTOR </strong>.</p>
<p style="font-size:16px"> Thank you for installing the free variant of our end2end currencies handler plugin SCD.</p>
<p style="font-size:16px"> This variant has limited features  when you use <strong style="color:red; font-weight: bold;"> WC-Vendor marketplace </strong>in your store. </p>
<p style="font-size:16px"> <strong style="font-weight:bold"> You can  subscribe for 14 days trial  to SCD premium variant  for WC-Vendor  under this link <a href="https://gajelabs.com/" target="_blank"> https://www.gajelabs.com/14-day-free-trial/ </a>. So to get full features. </strong></p>
<p style="font-size:16px"> <strong style="color:red; font-weight: bold;"> SCD premium variant for WC-Vendor </strong> extends vendor dashboard to allow each vendor to define the default currency in which the want to enter their products prices. </p> 

</div>

</div>

<script>

function myfunction() {
// jQuery('#myModal').modal('toggle')

// Get the modal
var modal = document.getElementById("myModal");

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 
//btn.onclick = function() {
modal.style.display = "block";
//}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
if (event.target == modal) {
modal.style.display = "none";
}
}

}
window.onload = myfunction;
</script>

</body>

<?php


}

		delete_transient( 'scd-admin-notice' );
	}
}






 add_action('admin_enqueue_scripts', 'scd_feedback_insert_adminScripts', 1);

add_action( 'wp_ajax_scd_feedback', 'scd_feedback' );

function scd_feedback_insert_adminScripts($hook) {

    if( 'plugins.php' == $hook  )            

	{

	    wp_enqueue_style('wp-jquery-ui-dialog');
		
        wp_enqueue_script('jquery');

        wp_enqueue_script('jquery-ui-dialog');       

        add_action( 'admin_footer', 'scd_feedback_javascript' );            
   }
}

function scd_feedback() {

   
		//php mailer variables
	  $to = 'support@gajelabs.com';
	  	  
	  $subject = "SCD Free 4.7.9 desactivated";
	  
	  $headers = 'From customer site ';
	  
	  $message = 'Administrators Email </br>';
	  
	  $blogusers = get_users('role=Administrator');
	  
	  foreach ($blogusers as $user) {
		  
			$email.=$user->user_email."<span> ; </span>" ;
			
	   } 
	   

	 $data = array(

		  'answer' => $_POST["answer"],

		  'plugin_name' => $_POST["opinfo"],

		  'other_info' => $_POST["oinfo"],

      'comment_plugin_did_ not_work' => $_POST["cpodw"],

      'comment_limited_features' => $_POST["colf"],
      
      'email_courant' => $_POST["emailcourant"],
      
      'hote' => $_SERVER['HTTP_HOST'],
		  );
	 
	$fields = $data;
	

	     
  foreach($fields as $key=>$value) { 
    if ($value != "") {
      $fields_string .= $key.'='.$value."\n";
   }
   }

	 
	$messages = $email. "\n" . $fields_string; 

  //echo($message);
	//Here put your Validation and send mail
	
	 wp_mail($to, $subject, strip_tags($messages), $headers);

	 wp_die(); // this is required to terminate immediately and return a proper response
 }

 //fonction pour tester la validiter du mail
      
 function validation($emailcourant)
 {
    // Validate email
  if (filter_var($emailcourant, FILTER_VALIDATE_EMAIL)) {
        return true;
  } else {
        return false;
  }

}

function scd_feedback_javascript() { ?>

	     <script type="text/javascript" >

	

	 $ = jQuery.noConflict()

	 $( window ).load(function() {

	         document.querySelector('[data-slug="scd-smart-currency-detector"] .deactivate a').addEventListener('click', function(event){

	             event.preventDefault()

	             var urlRedirect = document.querySelector('[data-slug="scd-smart-currency-detector"] .deactivate a').getAttribute('href');

	             $ = jQuery.noConflict()

	             

	             $('<div title="SCD - QUICK FEEDBACK"><div style="padding:10px;">'+

	                '<style type="text/css">.scdreasonblock { margin-top:8px; }</style>'+
                                  
                    '<h3><strong>If you have a moment, please let us know why you are deactivating:</strong></h3>'+

	                '<form id="scdfeedbackform">'+ 

	                

	                '<div class="scdreasonblock"><input type="radio" name="scdm_reason" onclick="scd_update_reason(this);" value="plugin-no-work"> The plugin didn\'t work<br />'+

	                 '<div id="scdm_nowork" style="margin-left:25px;display:none;padding:10px;border:1px dotted gray;color:#660000"><strong>We can help!</strong> We offer <strong>free support</strong> for this plugin. Feel free to open a support ticket at <a href="https://wordpress.org/support/users/gajelabs1/topics/"><strong>https://wordpress.org/support/users/gajelabs</strong></a> and we will be happy to help.<br /><p style="font-size:15px; color:#03224c">Comments</p><textarea rows = "3" cols = "80" name = "scdm_cpodw" id="scdm_cpodw" placeholder="Please write here..."></textarea></div></div>'+

	               
				   '<div class="scdreasonblock"><input type="radio" name="scdm_reason" onclick="scd_update_reason(this);" value="limited-features"> Limited features <br />'+

	               '<div id="scdm_limited" style="margin-left:25px;display:none;padding:10px;border:1px dotted gray;color:#660000"><strong>If you are currently using SCD FREE version please</strong> Download for free <strong> SCD premium 14 days trial </strong> <a href="https://gajelabs.com/14-day-free-trial"><strong>here</strong></a> or get our products <a href="https://gajelabs.com/our-products/"><strong>here</strong></a> to enjoy all our features<br /><p style="font-size:15px; color:#03224c">Please give us the maketplace of your site and additional Comments</p><textarea rows = "3" cols = "80" id="scdm_colf" name = "scdm_colf" placeholder="Please write here... "></textarea></div></div>'+	                 
                                                                                                                                         
	                '<div class="scdreasonblock"><input type="radio" name="scdm_reason" onclick="scd_update_reason(this);" value="temporary-deactivation"> It\'s a temporary deactivation. I\'m just debugging an issue.<br /></div>'+

	                '<div class="scdreasonblock"><input type="radio" name="scdm_reason" onclick="scd_update_reason(this);" value="conflict-plugin"> I have a conflict with another plugin<br />'+

	                '<div id="scdm_otherplugin" style="margin-left:25px;display:none;"><input type="text" name="scdm_otherpinfo" id="scdm_otherpinfo" placeholder="What\'s the plugin name?" style="width:100%"></div></div>'+

	                '<div class="scdreasonblock"><input type="radio" name="scdm_reason" onclick="scd_update_reason(this);" value="other"> Other<br />'+

	                '<div id="scdm_other" style="margin-left:25px;display:none;font-weight:bold;">Kindly tell us the reason so we can improve.<br /><textarea rows = "3" cols = "65" id="scdm_otherinfo" name = "scdm_otherinfo" placeholder="Please write here... "></textarea></div></div>'+

                  '<h3 id="emailcourant">Your email: <input type="email" id="email" placeholder="Enter your email" name="emailcourant" style="width=80%;"></h3>'+

                  '<h3 id="emailvalid" style="margin-left:25px; display:none; position:center; color:red;"><center> Please insert a valid email</center></h3>'+
                
                  '<h3 id="commentnull" style="margin-left:25px; display:none; position:center; color:red;"><center> Please insert a valid Comment</center></h3>'+
                

	                '</form>'+               

	                  '</div></div>'

	                 ).dialog({

	                   width:'700',

	                   dialogClass: 'wp-dialog',

	                   modal: true,

	                   close: function(event, ui)

	                   {

	                       $(this).dialog("close");

	                       $(this).remove();

	                   },

	                   closeOnEscape: true,

	                   buttons: [

	                       {

	                         id: 'scddeactivatebtn',

	                         text: "Skip & Deactivate", 

	                         click: function() {                               

	                                    var answer = $("input[name='scdm_reason']:checked").val();
                                      var emailcourant = $("input[name='emailcourant']").val();
                                      var cpodw = document.getElementById('scdm_cpodw').value;
                                      var colf = document.getElementById('scdm_colf').value;
                                      var opinfo = $("input[name='scdm_otherpinfo']").val();
                                      var oinfo = document.getElementById('scdm_otherinfo').value;

	                                  if (answer == undefined || answer == '' || answer == '-') 
	                                        window.location.href = urlRedirect;  
	                                  else if ((answer == 'plugin-no-work') && (cpodw == '')){     
                                         commentnull.style.display ="block";
                                    }
                                    else if ((answer == 'conflict-plugin') && (opinfo == '')){    
                                         commentnull.style.display ="block";
                                    }
                                    else if ((answer == 'other') && (oinfo == '')){    
                                         commentnull.style.display ="block";
                                    }
                                    else if ((answer == 'limited-features') && (colf == '')){    
                                         commentnull.style.display ="block";
                                    }
                                    else if (answer == 'temporary-deactivation'){    
                                          window.location.href = urlRedirect; 
                                    }

                                    else if (emailcourant == undefined || emailcourant == '' || emailcourant == '_'){
                                      emailvalid.style.display = "block";
                                        
                                    }
                                    else if (!emailcourant.match(/[a-z0-9_\-\.]+@[a-z0-9_\-\.]+\.[a-z]+/i)) {
                                      emailvalid.style.display = "block";
                                     
                                    }
                                    else 
	                                    {
                                          
	                                        var opinfo = $("input[name='scdm_otherpinfo']").val();

	                                        var oinfo = $("input[name='scdm_otherinfo']").val();

                                          var cpodw = document.getElementById('scdm_cpodw').value;

                                          //var colf = $("input[name='scdm_colf']").val();
                                          var colf = document.getElementById('scdm_colf').value;
                                         //var colf1 = $.trim($("#scdm_colf").val());
                                         
                                          var emailcourant = $("input[name='emailcourant']").val();

	                                        var data = {
	                                                'action': 'scd_feedback',

	                                                'answer': $("input[name='scdm_reason']:checked").val(),

	                                             'opinfo': $("input[name='scdm_otherpinfo']").val(),

	                                             //'oinfo': $("input[name='scdm_otherinfo']").val(),
                                                'oinfo':document.getElementById('scdm_otherinfo').value,
                                                
                                               'cpodw': document.getElementById('scdm_cpodw').value,

                                               'colf': document.getElementById('scdm_colf').value,
                                              
                                               'emailcourant': $("input[name='emailcourant']").val(),

                                              };                                       

	                                        $.post(ajaxurl, data, function(response) {

	                                            window.location.href = urlRedirect;                                         

	                                        });    

	                                    }                                   

	                                  }                            

	                       },

	                       {

	                         text: "We can help: Support Service", 

	                         click: function() {

	                                    window.open('https://wordpress.org/support/users/gajelabs1/topics/');

	                                      $(this).dialog("close");

	                                  }                            

	                       },

	                       {

	                         text: "Cancel", 

	                         "class": 'button button-primary button-close',

	                         click: function() {

	                                      $(this).dialog("close");

	                                  }                            

	                       }

	                   ]

	               });

	         })

	 });

	

	 function scd_update_reason(field)

	 {

	     document.getElementById("scddeactivatebtn").value = 'Submit & Deactivate';

	     document.getElementById("scddeactivatebtn").innerHTML = '<span class="ui-button-text">Submit &amp; Deactivate</span>';

	     document.getElementById("scdm_other").style.display = 'none';

	     document.getElementById("scdm_otherplugin").style.display = 'none';

	     document.getElementById("scdm_nowork").style.display = 'none';
		 
		 document.getElementById("scdm_limited").style.display = 'none';

	     if (field.value == 'other')

	     {
            document.getElementById("scdm_other").style.display = '';
            document.getElementById("emailcourant").style.display = "block"; 
            document.getElementById("emailvalid").style.display = "none";
            document.getElementById("commentnull").style.display ="none";       
	     }    

	     else if (field.value == 'conflict-plugin'){

	         document.getElementById("scdm_otherplugin").style.display = '';
           document.getElementById("emailcourant").style.display = "block";
           document.getElementById("emailvalid").style.display = "none";
           document.getElementById("commentnull").style.display ="none";
          }
       else if (field.value == 'limited-features'){

          document.getElementById("scdm_limited").style.display = ''; 
          document.getElementById("emailcourant").style.display = "block";	
          document.getElementById("emailvalid").style.display = "none";	
          document.getElementById("commentnull").style.display ="none";
        }
	     else if (field.value == 'plugin-no-work'){

	         document.getElementById("scdm_nowork").style.display = ''; 
           document.getElementById("emailcourant").style.display = "block";
           document.getElementById("emailvalid").style.display = "none";
           document.getElementById("commentnull").style.display ="none";
          }
      else if (field.value == 'temporary-deactivation')
           
           document.getElementById("emailcourant").style.display = "none";
           document.getElementById("emailvalid").style.display = "none";
           document.getElementById("commentnull").style.display ="none";
         //  window.location.href = urlRedirect;  

	 }

	

	     </script> <?php

	 }
	 
	function plugin_activated()
	{

	  global $current_user;
	  
      get_currentuserinfo();
	  
      //$current_user->user_login;
      $user_email=$current_user->user_email;
    
      $firstname=$current_user->user_firstname;
	  
	  $sender_email="support@gajelabs.com";
	  
      //$current_user->user_lastname;
      //$current_user->display_name;
      //$current_user->ID ;


	  
	  $subject = "Smart-Currency-Detector 4.7.9 activation";
	  
	  
	  $headers_admin = 'From: '. $user_email . "\r\n" .
                   'Reply-To: ' . $user_email . "\r\n";
	  $headers_admin .= "MIME-Version: 1.0\r\n";
	  $headers_admin .= "Content-Type: text/html; charset=UTF-8\r\n";
	  
	  $message = 'new customer website - ' . $_SERVER['HTTP_HOST'] . ' - customer email - ' . $user_email . ' - customer name - ' . $firstname ;
	  
	
	  
	 if(!get_option('scd_activation_notif_sent')){
		 
		//wp_mail($to_user, $subject, $message, $headers_user);

		wp_mail($sender_email, $subject, $message, $headers_admin);
		 
		update_option('scd_activation_notif_sent','yes');
	 } 
	}

      		 	function plugin_deactivated()
	{

	  global $current_user;
	  
      get_currentuserinfo();
	  
      //$current_user->user_login;
      $user_email=$current_user->user_email;
    
      $firstname=$current_user->user_firstname;
	  
	  $sender_email="support@gajelabs.com";
	  
      //$current_user->user_lastname;
      //$current_user->display_name;
      //$current_user->ID ;


	  
	  $subject = "Smart-Currency-Detector 4.7.9 Deactivation";
	  
	  
	  $headers_admin = 'From: '. $user_email . "\r\n" .
                   'Reply-To: ' . $user_email . "\r\n";
	  $headers_admin .= "MIME-Version: 1.0\r\n";
	  $headers_admin .= "Content-Type: text/html; charset=UTF-8\r\n";
	  
	  $message = 'new customer website - ' . $_SERVER['HTTP_HOST'] . ' - customer email - ' . $user_email . ' - customer name - ' . $firstname ;
	
	  
	 if(!get_option('scd_deactivation_notif_sent')){
		 
        wp_mail($sender_email, $subject, $message, $headers_admin);
		 
		update_option('scd_deactivation_notif_sent','yes');
	 }
	 
	}
        
?>