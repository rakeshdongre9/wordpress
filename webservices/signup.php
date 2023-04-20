<?php
   ///wp-webservices/signup.php?username=admin&password=placeapart-2020&email=admin@gmail.com
   require ('../wp-config.php');
   $username = sanitize_text_field($_REQUEST['username']);
   $email = sanitize_email($_REQUEST['email']);
   $password = sanitize_text_field($_REQUEST['password']);
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
       http_response_code(200);
       header('Content-Type: application/json');
       $user = get_userdata($user_id);
       $token = wp_generate_auth_cookie($user_id, 1209600); // 2 weeks
       $data = array("ID" => $user->data->ID, "user_login" => $user->data->user_login, "user_email" => $user->data->user_email, "user_registered" => $user->data->user_registered);
       $output["result"] = $data;
       $output["status"] = 1;
       $output["message"] = "Registration Successful.";
       $output["token"] = $token;
       echo json_encode($output);
   }
?>
