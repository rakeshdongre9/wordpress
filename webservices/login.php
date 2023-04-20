<?php
    //wp-webservices/login.php?username=admin&password=placeapart-2020
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
    $data = array("ID" => $user->data->ID, "user_login" => $user->data->user_login, "user_email" => $user->data->user_email, "user_registered" => $user->data->user_registered);
    $output["result"] = $data;
    $output["status"] = 1;
    $output["message"] = "Login Successful.";
    $output["token"] = $token;
    echo json_encode($output);
    ?>
