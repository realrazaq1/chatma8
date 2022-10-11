<?php

require_once 'functions.php';
require_once 'shared.php';
require_once 'send_code.php';

// register user
if (isset($_GET['reg']) && isset($_POST['reg-btn'])) {
    $errors = validate_reg_form($_POST);
    if (!$errors) {
        if (reg_user($_POST)) {
            // send verification to user's email
            $code = gen_code();
            send_code($_POST['email'], 'Verify Your Email', $code);
            $_SESSION['code'] = $code;
            $_SESSION['email'] = $_POST['email'];

            // redirect to verify email page
            header('Location: ../verify_email.php');
        }
    } else {
        // add errors to session & redirect to reg page
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header('Location: ../?reg');
    }
}

// login user
if (isset($_GET['login']) && isset($_POST['login-btn'])) {
    $errors = validate_login_form($_POST);
    if (!$errors) {
        // check if user exist
        if (!check_user($_POST)) {
            $_SESSION['errors']['login'] = 'Invalid Credentials';
            header('Location: ../?login');
        } else {
            // check if email is not verified
            if (!check_user($_POST)['email_verified']) {
                extract(check_user($_POST));
                $code = gen_code();
                send_code($email, 'Verify Your Email', $code);
                $_SESSION['code'] = $code;
                $_SESSION['email'] = $email;
                header('Location: ../verify_email.php');
            }
            // check if user is blocked by admin
            elseif (check_user($_POST)['acct_status'] == 'blocked') {
                $_SESSION['user_id'] = check_user($_POST)['id'];
                header('Location: ../blocked.php');
            } else {
                // FINALLY LOG THEM IN
                $_SESSION['user_id'] = true;
                $_SESSION['user_id'] = check_user($_POST)['id'];
                header('Location: ../feed.php');
            }
        }
    } else {

        // add errors to session & redirect to reg page
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header('Location: ../?login');
    }
}

// verify email
if (isset($_GET['verify_email']) && isset($_POST['ve_btn'])) {
    if (verify_code($_POST['code'])) {
        // update email verifed status
        verify_email($_SESSION['email']);
        header('Location: ../?login&verified');
    } else {
        header('Location: ../verify_email.php?invalid_code');
    }
}

// EDIT PROFILE 
if (isset($_GET['editprofile']) && isset($_POST['update-profile-btn'])) {
    echo "<pre>";
    $profile_pic = $_FILES['profile_pic'];
    $error = validate_img($profile_pic);
    if ($error) {
        echo $error;
    }
    print_r($profile_pic);
}
