<?php
require_once 'config.php';
require_once 'shared.php';
// show page
function show_partial($file_name, $data = null)
{
    require_once dirname(__FILE__, 2) . "/partials/{$file_name}.php";
}
// validate registeration form
function validate_reg_form($form)
{
    extract($form);
    $errors = [];

    // validate firstname
    if (empty($firstname)) {
        $errors['firstname'] = 'First Name is required';
    }

    // validate lastname
    if (empty($lastname)) {
        $errors['lastname'] = 'Last Name is required';
    }

    // validate username
    if (empty($username)) {
        $errors['username'] = 'Username is required';
    } elseif (username_exist($username)) {
        $errors['username'] = 'Username is aleady registered';
    }

    // validate email
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email address';
    } elseif (email_exist($email)) {
        $errors['email'] = 'Email address is aleady registered';
    }

    // validate password
    if (empty($password)) {
        $errors['password'] = 'Password is required';
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'Password should be atleast 6 chars';
    } elseif (strlen($password) > 40) {
        $errors['password'] = 'Password should be atmost 40 chars';
    }

    if (empty($gender)) {
        $errors['gender'] = 'Gender is required';
    }

    return $errors;
}

function validate_login_form($form)
{
    extract($form);
    $errors = [];

    // validate username/email
    if (empty($username_email)) {
        $errors['username_email'] = 'Username/Email is required';
    }

    // validate password
    if (empty($login_password)) {
        $errors['login_password'] = 'Password is required';
    }

    return $errors;
}

// display error
function show_error($field)
{
    return $_SESSION['errors'][$field] ?? null;
}

// display previously submitted form data
function show_formdata($field)
{
    return $_SESSION['form_data'][$field] ?? null;
}

// check user
function check_user($data)
{
    extract($data);
    global $db;
    $login_password = md5($login_password);

    $query = "SELECT * FROM users WHERE (username = :username_email || email = :username_email) && password = :password";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username_email', $username_email);
    $stmt->bindParam(':password', $login_password);
    $stmt->execute();
    return $stmt->fetch();
}

// register user
function reg_user($data)
{
    extract($data);
    $firstname = trim($firstname);
    $lastname = trim($lastname);
    $username = trim($username);
    $email = trim($email);

    global $db;
    $password = md5($password);

    $query = "INSERT INTO users
            (firstname, lastname, username, email, password, gender) 
            VALUES(:firstname, :lastname, :username, :email, :password, :gender)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':firstname', $firstname);
    $stmt->bindParam(':lastname', $lastname);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':gender', $gender);
    return $stmt->execute();
}

// check if email is already registered
function email_exist($email)
{
    global $db;

    $query = "SELECT email FROM users WHERE (email = :email)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    return $stmt->fetch();
}

// check if username is already registered
function username_exist($username)
{
    global $db;

    $query = "SELECT username FROM users WHERE (username = :username)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    return $stmt->fetch();
}

// generate verification code
function gen_code()
{
    return rand(111111, 999999);
}

// verify code
function verify_code($code)
{
    return $code == $_SESSION['code'] ? true : false;
}


// update email verification status
function verify_email($email)
{
    global $db;

    $query = "UPDATE users SET email_verified = true WHERE email = :email";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email);
    return $stmt->execute();
}

// get single user
function get_user($id_uname)
{
    global $db;

    $query = "SELECT * FROM users WHERE id = :id_uname || username = :id_uname";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id_uname', $id_uname);
    $stmt->execute();
    return $stmt->fetch();
}

// get single user
function get_users()
{
    global $db;

    $query = "SELECT * FROM users LIMIT 0,8";
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll();
}

// get single user
function get_suggestions()
{
    $current_user = $_SESSION['user_id'];
    $suggeted_users = [];
    foreach (get_users() as $user) {
        if (!is_friend($user['id']) && $user['id'] != $current_user && !fr_sent($user['id'])) {
            array_push($suggeted_users, $user);
        }
    }

    return $suggeted_users;
}

// validate file
function validate_file($file)
{
    extract($file);
    $max_size = 30; // mb
    $file_size = round($size / (1000 * 1000), 2); // file size (mb)
    $file_type = explode('/', $type)[0];

    $error = '';
    if ($file_size > $max_size) {
        $error = "File should be atmost $max_size MB";
    }
    if ($file_type != 'image' && $file_type != 'video') {
        $error = "You can only upload photo or video";
    }

    return $error;
}

// validate file
function validate_img($file)
{
    extract($file);
    $max_size = 10; // mb
    $file_size = round($size / (1000 * 1000), 2); // file size (mb)
    $file_type = explode('/', $type)[0];

    $error = '';
    if ($file_size > $max_size) {
        $error = "File should be atmost $max_size MB";
    }
    if ($file_type != 'image') {
        $error = "You can only upload image";
    }

    return $error;
}
// save post to db
function save_post($data)
{
    global $db;
    extract($data);
    $text = trim(htmlspecialchars($text));
    $query = "INSERT INTO posts (text, media, media_type, user_id)
            VALUES (:text, :media, :media_type, :user_id)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':text', $text);
    $stmt->bindParam(':media', $media);
    $stmt->bindParam(':media_type', $media_type);
    $stmt->bindParam(':user_id', $user_id);
    return $stmt->execute();
}


// get all posts
function get_post()
{
    global $db;

    $query = "SELECT 
                p.id,
                p.user_id, 
                p.text, 
                p.media,
                p.media_type, 
                p.created_at, 
                p.updated_at,
                u.firstname,
                u.lastname,
                u.username,
                u.profile_pic                
            FROM posts p
            JOIN users u
             ON p.user_id = u.id
            ORDER BY updated_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll();
}

// get all posts by user
function get_user_post($user_id)
{
    global $db;

    $query = "SELECT 
                p.id,
                p.user_id, 
                p.text, 
                p.media,
                p.media_type, 
                p.created_at, 
                p.updated_at,
                u.firstname,
                u.lastname,
                u.username,
                u.profile_pic                
            FROM posts p
            JOIN users u
             ON p.user_id = u.id
            WHERE user_id = :user_id
            ORDER BY updated_at DESC";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetchAll();
}

// like post
function like_post($post_id)
{
    global $db;
    $current_user = $_SESSION['user_id'];

    $query = "INSERT INTO likes (post_id, user_id)
              VALUES (:post_id, :user_id)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':post_id', $post_id);
    $stmt->bindParam(':user_id', $current_user);
    return $stmt->execute();
}

// check like status
function post_liked($post_id)
{
    global $db;
    $current_user = $_SESSION['user_id'];

    $query = "SELECT * FROM likes WHERE post_id = :post_id && user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':post_id', $post_id);
    $stmt->bindParam(':user_id', $current_user);
    $stmt->execute();
    return $stmt->fetch();
}

// check like status
function unlike_post($post_id)
{
    global $db;
    $current_user = $_SESSION['user_id'];

    $query = "DELETE FROM likes WHERE post_id = :post_id && user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':post_id', $post_id);
    $stmt->bindParam(':user_id', $current_user);
    return $stmt->execute();
}

// get post likes
function get_likes($post_id)
{
    global $db;

    $query = "SELECT * FROM likes WHERE post_id = :post_id ";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':post_id', $post_id);
    $stmt->execute();
    return $stmt->fetchAll();
}

// comment on post
function comment($data)
{
    global $db;
    extract($data);
    $text = trim(htmlspecialchars($text));
    $current_user = $_SESSION['user_id'];

    $query = "INSERT INTO comments (post_id, user_id, text)
              VALUES (:post_id, :user_id, :text)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':post_id', $post_id);
    $stmt->bindParam(':user_id', $current_user);
    $stmt->bindParam(':text', $text);
    return $stmt->execute();
}

// get post comments
function get_comments($post_id)
{
    global $db;

    $query = "SELECT * FROM comments WHERE post_id = :post_id 
             ORDER BY updated_at DESC ";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':post_id', $post_id);
    $stmt->execute();
    return $stmt->fetchAll();
}

// get followers
function get_followers($id)
{
    global $db;

    $query = "SELECT follower_id FROM follow_list WHERE user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $id);
    $stmt->execute();
    return $stmt->fetchAll();
}

// get followings
function get_followings($id)
{
    global $db;

    $query = "SELECT user_id FROM follow_list WHERE follower_id = :follower_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':follower_id', $id);
    $stmt->execute();
    return $stmt->fetchAll();
}

// filter posts
function filter_posts()
{
    $list = get_post();
    $filter_list = array();
    $current_user = $_SESSION['user_id'];

    foreach ($list as $post) {
        if (check_follow_status($post['user_id']) || $post['user_id'] == $current_user) {
            array_push($filter_list, $post);
        }
    }

    return $filter_list;
}

// check follow status
function check_follow_status($user_id)
{
    global $db;
    $current_user = $_SESSION['user_id'];
    $query = "SELECT * FROM follow_list WHERE follower_id = :follower_id && user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':follower_id', $current_user);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetch();
}

// add friend
function add_friend($user_id)
{
    global $db;
    $current_user = $_SESSION['user_id'];

    $query = "INSERT INTO friends(sender_id, receiver_id) 
              VALUES(:sender_id, :receiver_id)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':sender_id', $current_user);
    $stmt->bindParam(':receiver_id', $user_id);
    return $stmt->execute();
}

// accept friend request
function accept_friend($user_id)
{
    global $db;
    $current_user = $_SESSION['user_id'];

    $query = "UPDATE friends SET is_friend = true, status = 'accepted'
             WHERE sender_id = :user_id  && receiver_id = :current_user";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':current_user', $current_user);
    return $stmt->execute();
}

// reject friend request
function reject_friend($user_id)
{
    global $db;
    $current_user = $_SESSION['user_id'];

    $query = "DELETE FROM friends
             WHERE sender_id = :user_id  && receiver_id = :current_user";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':current_user', $current_user);
    return $stmt->execute();
}

// unfriend a user
function unfriend($user_id)
{
    global $db;
    $current_user = $_SESSION['user_id'];

    $query = "DELETE FROM friends
             WHERE (sender_id = :user_id  && receiver_id = :current_user) || (sender_id = :current_user  && receiver_id = :user_id)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':current_user', $current_user);
    return $stmt->execute();
}

// unfollow user
function unfollow_user($user_id)
{
    global $db;
    $current_user = $_SESSION['user_id'];

    $query = "DELETE FROM follow_list WHERE follower_id = :follower_id && user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':follower_id', $current_user);
    $stmt->bindParam(':user_id', $user_id);
    return $stmt->execute();
}

// check if current user is friend with user
function is_friend($user_id)
{
    global $db;
    $current_user = $_SESSION['user_id'];

    $query = "SELECT * FROM friends 
              WHERE (
                sender_id = :current_user && 
                receiver_id = :user_id && 
                is_friend = true) || 
                (sender_id = :user_id && 
                receiver_id = :current_user && 
                is_friend = true)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':current_user', $current_user);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetch();
}

// check if friend request sent
function fr_sent($user_id)
{
    global $db;
    $current_user = $_SESSION['user_id'];

    $query = "SELECT * FROM friends 
              WHERE (
                sender_id = :current_user && 
                receiver_id = :user_id && 
                status = 'pending')";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':current_user', $current_user);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetch();
}

function fr_received($user_id)
{
    global $db;
    $current_user = $_SESSION['user_id'];

    $query = "SELECT * FROM friends 
              WHERE (
                sender_id = :user_id && 
                receiver_id = :current_user && 
                status = 'pending')";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':current_user', $current_user);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetch();
}

// get friend requests sent to current user
function get_fr()
{
    global $db;
    $current_user = $_SESSION['user_id'];

    $query = "SELECT * FROM friends 
              WHERE (
                receiver_id = :current_user 
                status = 'pending')";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':current_user', $current_user);
    $stmt->execute();
    return $stmt->fetch();
}

// get all friends of user
function get_friends($user_id)
{
    global $db;
    $friends_ids = [];

    $query = "SELECT * FROM friends 
              WHERE (
                sender_id = :user_id && 
                is_friend = true) || 
                (receiver_id = :user_id &&  
                is_friend = true)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $friends = $stmt->fetchAll();

    foreach ($friends as $friend) {
        if ($friend['sender_id'] != $user_id) {
            array_push($friends_ids, $friend['sender_id']);
        } else if ($friend['receiver_id'] != $user_id) {
            array_push($friends_ids, $friend['receiver_id']);
        }
    }
    return $friends_ids;
}
