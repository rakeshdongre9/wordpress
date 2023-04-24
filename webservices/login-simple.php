<?php 
   require_once('../../../wp-config.php');
   
   
   
   $email = $_REQUEST['email']; 
   
   $password = $_REQUEST['password'];
   
   
   
     $check_login = $wpdb->get_results( "SELECT  *  FROM  `dNvuK_users`  WHERE  `user_email`  =  '$email' ");
     
    
    if($check_login)
       
       {
       $login_user_array = json_decode(json_encode($check_login), true);
          $hash = $login_user_array[0]['user_pass'];
          
           //  print_r($hash);exit;
   
   
       //   $hash = '$P$BgqKaog9o0PaX8KOEd4Q3.fkqRNvL//'; // password hash from the user table
   if ( wp_check_password( $password, $hash ) ) {
       
           // $check_login[0]['dd'] = 'sd';
           $msg['result'] = $check_login[0];
           $msg["message"] = "successful";
           $msg["status"] = "1";
           header('Content-type:application/json');
           echo json_encode($msg);
           die;
   } else {
          
           $msg['result'] = (object)[];
           $msg["message"] = "Your have entered wrong email & password";
           $msg["status"] = "0";
           header('Content-type:application/json');
           echo json_encode($msg);
   }
          
       }
       else
       {
            $msg['result'] = (object)[];
           $msg["message"] = "Your have entered wrong email & password";
           $msg["status"] = "0";
           header('Content-type:application/json');
           echo json_encode($msg);
       }
   
   
   
   
   
   ?>
