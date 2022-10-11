
<?php
require_once 'functions.php';
require_once 'shared.php';

// save post
if (isset($_GET['post_upload'])) {
    $id = $_SESSION['user_id'];
    $text = trim($_POST['text']);

    if ($_FILES) {
        extract($_FILES['file']);
        $name = time() . $name;
        $file_type = explode('/', $type)[0];
        $target_dir = dirname(__FILE__, 2) . '/assets/posts/' . $name;
        $error = validate_file($_FILES['file']);

        $data = [
            'text' => $text,
            'media' => $name ?? null,
            'media_type' => $file_type ?? null,
            'user_id' => $id,
        ];

        if ($error) {
            echo json_encode(['error' => $error]);
        } else if (empty($text)) {
            echo json_encode(["error" => 'Post cannot be empty']);
        } else {
            try {
                move_uploaded_file($tmp_name, $target_dir);
                save_post($data);
                echo json_encode(["msg" => "post uploaded"]);
            } catch (Exception $e) {
                echo json_encode(["error" => "something went wrong"]);
            }
        }
    } else {
        if (empty($text)) {
            echo json_encode(["error" => 'Post cannot be empty']);
        } else {
            $data = [
                'text' => $text,
                'media' => '',
                'media_type' => '',
                'user_id' => $id,
            ];

            try {
                save_post($data);
                echo json_encode(["msg" => "post uploaded"]);
            } catch (Exception $e) {
                echo json_encode(["error" => "something went wrong"]);
            }
        }
    }
}

// like post
if (isset($_GET['like_post']) && isset($_POST['post_id'])) {
    $post_id = $_POST['post_id'];
    try {
        like_post($post_id);
        echo json_encode(['msg' => 'post liked']);
    } catch (Exception $e) {
        echo json_encode(["error" => "something went wrong"]);
    }
}

// unlike post
if (isset($_GET['unlike_post']) && isset($_POST['post_id'])) {
    $post_id = $_POST['post_id'];

    try {
        unlike_post($post_id);
        echo json_encode(['msg' => 'post unliked']);
    } catch (Exception $e) {
        echo json_encode(["error" => "something went wrong"]);
    }
}

// comment on post
if (isset($_GET['comment']) && isset($_POST['text'])) {

    if (empty($_POST['text'])) {
        echo json_encode(["error" => 'Comment cannot be empty']);
    } else {
        try {
            comment($_POST);
            echo json_encode(['msg' => 'comment sent']);
        } catch (Exception $e) {
            echo json_encode(["error" => "something went wrong"]);
        }
    }
}

// sync comments
if (isset($_GET['sync_comments'])) {
    $post_id = $_POST['post_id'];
    $comments = get_comments($post_id);
    $new_comments = [];
    foreach ($comments as $comment) {
        $c_author = get_user($comment['user_id']);
        extract($c_author);
        $ca = [
            'firstname' => $firstname,
            'lastname' => $lastname,
            'profile_pic' => $profile_pic,
            'username' => $username
        ];
        $comment = array_merge($comment, $ca);
        array_push($new_comments, $comment);
    }
    echo json_encode($new_comments);
}


// add friend
if (isset($_GET['addfriend']) && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    try {
        add_friend($user_id);
        echo json_encode(['msg' => 'friend request sent']);
    } catch (Exception $e) {
        echo json_encode(["error" => "something went wrong"]);
    }
}

// accept friend request
if (isset($_GET['acceptfriend']) && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    try {
        accept_friend($user_id);
        echo json_encode(['msg' => 'friend request accepted']);
    } catch (Exception $e) {
        echo json_encode(["error" => "something went wrong"]);
    }
}

// reject friend request
if (isset($_GET['rejectfriend']) && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    try {
        reject_friend($user_id);
        echo json_encode(['msg' => 'friend request rejected']);
    } catch (Exception $e) {
        echo json_encode(["error" => "something went wrong"]);
    }
}

// unfriend a user
if (isset($_GET['unfriend']) && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    try {
        unfriend($user_id);
        echo json_encode(['msg' => 'user unfriended']);
    } catch (Exception $e) {
        echo json_encode(["error" => "something went wrong"]);
    }
}

// unfollow userr
if (isset($_GET['unfollow']) && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    try {
        unfollow_user($user_id);
        echo json_encode(['msg' => 'user unfollowed']);
    } catch (Exception $e) {
        echo json_encode(["error" => "something went wrong"]);
    }
}
