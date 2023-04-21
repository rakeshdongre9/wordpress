<?php
    //https://technorizen.com/_angotech_homol1/wp-webservices/login.php?username=admin&password=placeapart-2020&android_device_id=android_device_id&ios_device_id=ios_device_id
    require ('../wp-config.php');
    $username = sanitize_text_field($_REQUEST['username']);
    $password = sanitize_text_field($_REQUEST['password']);
    $check_by_email = "false";
    // check if the entered username is an email address
    if (is_email($username)) {
        $user = get_user_by('email', $username);
        $username = $user->user_login;
        $check_by_email = "true";
    }
    if ($username == "") {
        http_response_code(401); // Unauthorized
        header('Content-Type: application/json');
        $output["result"] = array();
        $output["status"] = 0;
        if ($check_by_email == "true") {
            $output["message"] = "Email not exist.";
        } else {
            $output["message"] = "Username not exist.";
        }
        $output["token"] = "";
        echo json_encode($output);
        exit;
    }
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
    
    
    // update user meta for android_device_id and ios_device_id if not empty
    if (!empty($_REQUEST['android_device_id'])) {
        update_user_meta($user->data->ID, 'android_device_id', sanitize_text_field($_REQUEST['android_device_id']));
    }
    
    if (!empty($_REQUEST['ios_device_id'])) {
        update_user_meta($user->data->ID, 'ios_device_id', sanitize_text_field($_REQUEST['ios_device_id']));
    }
    
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
    $output["message"] = "Login Successful.";
    $output["token"] = $token;
    echo json_encode($output);
?>
