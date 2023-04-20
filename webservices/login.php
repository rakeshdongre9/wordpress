<?php
   //wp-webservices/login.php?username=admin&password=placeapart-2020
   require ('../wp-config.php');
   $username = $_REQUEST['username'];
   $password = $_REQUEST['password'];
   $user = wp_authenticate($username, $password);
   if (is_wp_error($user)) {
       http_response_code(401); // Unauthorized
       header('Content-Type: application/json');
       $output["result"] = array();
       $output["status"] = 0;
       $output["message"] = strip_tags($user->get_error_message());
       $output["token"] = "";
       echo json_encode($output);
       exit;
   }
   http_response_code(200);
   header('Content-Type: application/json');
   $token = wp_generate_auth_cookie($user->ID, 1209600); // 2 weeks
   $data = array("ID" => $user->data->ID, "user_login" => $user->data->user_login, "user_email" => $user->data->user_email, "user_registered" => $user->data->user_registered);
   $output["result"] = $data;
   $output["status"] = 1;
   $output["message"] = "Login Successful.";
   $output["token"] = $token;
   echo json_encode($output);
?>
