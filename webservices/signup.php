<?php

   /*
   https://technorizen.com/_angotech_homol1/wp-webservices/signup.php?username=admin&password=placeapart-2020&email=admin@gmail.com
   &country_code=+91&mobile=123456789&android_device_id=android_device_id&ios_device_id=ios_device_id
   */
   
   require ('../wp-config.php');
   $username = sanitize_text_field($_REQUEST['username']);
   $email = sanitize_email($_REQUEST['email']);
   $password = sanitize_text_field($_REQUEST['password']);
   $mobile = sanitize_text_field($_REQUEST['mobile']);
   $country_code = sanitize_text_field($_REQUEST['country_code']);
   $android_device_id = sanitize_text_field($_REQUEST['android_device_id']);
   $ios_device_id = sanitize_text_field($_REQUEST['ios_device_id']);
   
   // Check if username already exists
   if (username_exists($username) || $username == "") {
       http_response_code(401); // Unauthorized
       header('Content-Type: application/json');
       $output["result"] = array();
       $output["status"] = 0;
       if ($username == "") {
           $output["message"] = "Username required.";
       } else {
           $output["message"] = "Username already exists.";
       }
       $output["token"] = "";
       echo json_encode($output);
       exit;
   }
   // Check if email already exists
   if (email_exists($email) || $email == "") {
       http_response_code(401); // Unauthorized
       header('Content-Type: application/json');
       $output["result"] = array();
       $output["status"] = 0;
       if ($email == "") {
           $output["message"] = "Email required.";
       } else {
           $output["message"] = "Email already exists.";
       }
       $output["token"] = "";
       echo json_encode($output);
       exit;
   }
   // Validate password
   if (strlen($password) < 6 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[!@#$%^&*()\-_=+{};:<>,.?~]/', $password)) {
       http_response_code(401); // Unauthorized
       header('Content-Type: application/json');
       $output["result"] = array();
       $output["status"] = 0;
       $output["message"] = "Password must be at least 6 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.";
       $output["token"] = "";
       echo json_encode($output);
       exit;
   }
   $user_data = array('user_login' => $username, 'user_email' => $email, 'user_pass' => $password,);
   $user_id = wp_insert_user($user_data);
   if (is_wp_error($user_id)) {
       http_response_code(401); // Unauthorized
       header('Content-Type: application/json');
       $output["result"] = array();
       $output["status"] = 0;
       $output["message"] = "Failed to register user.";
       $output["token"] = "";
       echo json_encode($output);
       exit;
   } else {
       // Update user meta data
       update_user_meta($user_id, 'mobile', $mobile);
  // add user meta data
   update_user_meta($user_id, 'country_code', $country_code);
   update_user_meta($user_id, 'mobile', $mobile);
   update_user_meta($user_id, 'android_device_id', $android_device_id);
   update_user_meta($user_id, 'ios_device_id', $ios_device_id);

   if (is_wp_error($user_id)) {
       http_response_code(401); // Unauthorized
       header('Content-Type: application/json');
       $output["result"] = array();
       $output["status"] = 0;
       $output["message"] = "Failed to register user.";
       $output["token"] = "";
       echo json_encode($output);
       exit;
   } else {
       http_response_code(200);
       header('Content-Type: application/json');
       $user = get_userdata($user_id);
       $token = wp_generate_auth_cookie($user_id, 1209600); // 2 weeks
       
       $user_meta = get_user_meta($user->data->ID);

    $data = array("ID" => $user->data->ID, "user_login" => $user->data->user_login, "user_email" => $user->data->user_email, 
    "user_registered" => $user->data->user_registered,
    
    "country_code" =>  ($user_meta['country_code'][0] == NULL)?"":$user_meta['country_code'][0],
    "mobile" => ($user_meta['mobile'][0] == NULL)?"":$user_meta['mobile'][0],
    "android_device_id" => ($user_meta['android_device_id'][0] == NULL)?"":$user_meta['android_device_id'][0],
    "ios_device_id" => ($user_meta['ios_device_id'][0] == NULL)?"":$user_meta['ios_device_id'][0],

    
    );
       
       $output["result"] = $data;
       $output["status"] = 1;
       $output["message"] = "Registration Successful.";
       $output["token"] = $token;
       echo json_encode($output);
   }
   }
?>
