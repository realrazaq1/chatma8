<?php
require_once dirname(__FILE__) . "/php/functions.php";
require_once dirname(__FILE__) . "/php/shared.php";
?>

<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
} else {
    $loggedin = $_SESSION['user_id'];
    $current_user = get_user($_SESSION['user_id']);
} ?>

<?php
if (isset($_GET['u'])) {
    $user = get_user($_GET['u']);
    if ($user) {
        $posts = get_user_post($user['id']);
        $is_friend = is_friend($user['id']); // user & current user are friends
        $fr_sent = fr_sent($user['id']); // current user has sent FR to user
        $fr_received = fr_received($user['id']); // current user received FR from user
        $friends_id = get_friends($user['id']);
        $friends = [];
        foreach ($friends_id as $id) {
            array_push($friends, get_user($id));
        }
    }
}

?>

<?php
if ($user && $loggedin) { ?>
    <?= show_partial("header", ["page_title" => "ChatMa8 - Profile"]); ?>
    <?= show_partial("nav");
    ?>
    <div class="feed-wrapper">
        <!-- LEFT SIDE BAR -->
        <aside class="left-sidebar">
            <div class="ls-nav">
                <div class="ls-nav-item">
                    <i class="fa-solid fa-rss"></i>
                    <span>Feed</span>
                </div>
                <div class="ls-nav-item">
                    <i class="fas fa-message"></i>
                    <span>Chats</span>
                </div>
                <div class="ls-nav-item">
                    <i class="fa-solid fa-circle-play"></i>
                    <span>Videos</span>
                </div>
                <div class="ls-nav-item">
                    <i class="fas fa-bookmark"></i> <span>Bookmarks</span>
                </div>
                <div class="ls-nav-item">
                    <i class="fas fa-question"></i>
                    <span>Questions</span>
                </div>
                <div class="ls-nav-item">
                    <i class="fas fa-briefcase"></i>
                    <span>Jobs</span>
                </div>
                <div class="ls-nav-item">
                    <i class="fa-solid fa-calendar-days"></i>
                    <span>Events</span>
                </div>
                <div class="ls-nav-item">
                    <i class="fas fa-book"></i>
                    <span>Courses</span>
                </div>
        </aside>
        <main class="main-feed">
            <div class="profile-info">
                <!-- profile cover & profile pic -->
                <div class="profile-cover" style="--cover-url: url('../img/cover/<?= $user['cover_pic'] ?>')">
                    <div class="pp">
                        <img src="<?= dirname($_SERVER['SERVER_ADDR']) ?>/assets/img/profile/<?= $user['profile_pic'] ?>" alt="Post Profile Picture">
                    </div>
                </div>
                <!-- profile info text -->
                <div class="profile-text">
                    <h1><?= $user['firstname'] ?> <?= $user['lastname'] ?></h1>
                    <span>@<?= $user['username'] ?></span>
                </div>
                <!-- Show add friend & unfriend btns if it's not the current user -->
                <?php if ($user['username'] != $current_user['username']) { ?>

                    <?php if ($fr_sent) { ?>
                        <button class="fr-sent" data-user-id="<?= $user['id'] ?>">Friend Request Sent <i class="fas fa-check"></i></button>
                    <?php } else if ($fr_received) { ?>
                        <div class="accept-reject-friend">
                            <button class="accept-friend" data-user-id="<?= $user['id'] ?>">Accept Friend <i class="fas fa-user-check"></i></button>
                            <button class="reject-friend" data-user-id="<?= $user['id'] ?>">Reject Friend <i class="fas fa-user-xmark"></i></button>
                        </div>
                    <?php } else if (!$is_friend) { ?>
                        <button class="addfriend-btn" data-user-id="<?= $user['id'] ?>">Add Friend <i class="fas fa-user-plus"></i></button>
                    <?php } else if ($is_friend) { ?>
                        <button class="unfriend" data-user-id="<?= $user['id'] ?>">Unfriend <i class="fas fa-user-minus"></i></button>
                    <?php } ?>
                <?php } else if ($user['username'] == $current_user['username']) { ?>
                    <a href="editprofile.php" class="edit-profile-btn">Edit Profile <i class="fas fa-user-pen"></i></a>
                <?php } ?>
            </div>
            <?php
            foreach ($posts as $post) { ?>

                <section class="post">
                    <div class="poster-info-section">
                        <i class="fa-solid fa-ellipsis-vertical post-option"></i>
                        <a href="profile.php?u=<?= $post['username'] ?>" class="poster-info-link">
                            <div class="poster-info">
                                <img src="<?= dirname($_SERVER['SERVER_ADDR']) ?>/assets/img/profile/<?= $post['profile_pic'] ?>" alt="Post Profile Picture">
                                <span><?= $post['firstname'] ?> <?= $post['lastname'] ?></span>
                            </div>
                        </a>

                        <div class="dot"></div>
                        <span class="post-time"><?= date('D, d-m-Y', strtotime($post['created_at']))                                                ?></span>
                        <span class="post-time"><i class="fas fa-clock"></i> <?= date('g:i A', strtotime($post['created_at']))                                                ?></span>

                    </div>
                    <div class="post-text">
                        <p><?= $post['text'] ?></p>
                    </div>
                    <div class="post-img">
                        <?php if ($post['media_type'] == 'image') { ?>
                            <img src="./assets/posts/<?= $post['media'] ?>">
                        <?php } else if ($post['media_type'] == 'video') { ?>
                            <video src="./assets/posts/<?= $post['media'] ?>" controls></video>
                        <?php  }
                        ?>
                    </div>
                    <div class="post-actions">
                        <!-- unlkie btn -->
                        <label class="unlike-btn" id="unlike<?= $post['id'] ?>" data-post-id="<?= $post['id'] ?>" style="display: <?= post_liked($post['id']) ? 'inline' : 'none' ?> ;"><i class="fa-solid fa-heart"></i></label>
                        <!-- like btn -->
                        <label id="like<?= $post['id'] ?>" class="like-btn" data-post-id="<?= $post['id'] ?>" style="display: <?= post_liked($post['id']) ? 'none' : 'inline' ?> ;">
                            <i class="fa-regular fa-heart"></i>
                        </label>
                        <label for="comment<?= $post['id'] ?>" id="comment-btn"><i class="fa-regular fa-comment"></i></label>
                        <label id="share-btn"><i class="fa-regular fa-share-from-square"></i></label>

                    </div>
                    <div class="post-stats">
                        <span class="like-stat">
                            <span id="like-count<?= $post['id'] ?>"><?= count(get_likes($post['id'])) ?></span>
                            <span>likes</span>
                        </span>
                        <span>|</span>
                        <span class="comment-stat">
                            <span id="comment-count<?= $post['id'] ?>"><?= count(get_comments($post['id'])) ?></span>
                            <span>comments</span>
                        </span>
                        <!-- <span class="share-stat">2 shares</span> -->
                    </div>
                    <div class="comment-box">
                        <div class="comment-img">
                            <img src="<?= dirname($_SERVER['SERVER_ADDR']) ?>/assets/img/profile/<?= $current_user['profile_pic'] ?>" alt="User Profile Picture">
                        </div>
                        <textarea class="comment" id="comment<?= $post['id'] ?>" cols="20" rows="1" placeholder="Add a comment..."></textarea>
                        <button class="comment-btn" data-post-id="<?= $post['id'] ?>">Post</button>
                    </div>
                    <section class="comments-section" id="comments-section<?= $post['id'] ?>" data-post-id="<?= $post['id'] ?>">
                    </section>
                </section>


            <?php } ?>
        </main>
        <!-- RIGHT SIDE BAR -->
        <aside class="right-sidebar">
            <div class="profileDetailsWrapper">
                <h1>Profile Info</h1>
                <div class="profileDetails">
                    <div>
                        <h4>First Name:</h4>
                        <span><?= ucwords($user['firstname']) ?></span>
                    </div>
                    <div>
                        <h4>Last Name:</h4>
                        <span><?= ucwords($user['lastname']) ?></span>
                    </div>
                    <div>
                        <h4>Username:</h4>
                        <span><?= $user['username'] ?></span>
                    </div>
                    <div>
                        <h4>Relationship:</h4>
                        <span><?= ucwords($user['relationship']) ?></span>
                    </div>
                    <div>
                        <h4>From:</h4>
                        <span><?= ucwords($user['state']) ?></span>
                    </div>
                    <div>
                        <h4>Resides In:</h4>
                        <span><?= ucwords($user['location']) ?></span>
                    </div>
                </div>
            </div>
            <div class="friends">
                <h1>Friends
                    <?php if (count($friends) > 0) { ?>
                        (<span class="friends-count"><?= count($friends) ?></span>)
                    <?php } ?>
                </h1>
                <div class="friendsWrapper">
                    <?php foreach ($friends as $friend) { ?>

                        <div class="friendWrapper">
                            <div class="info">
                                <a href="./profile.php?u=<?= $friend['username'] ?>">
                                    <img src="./assets/img/profile/<?= $friend['profile_pic'] ?>" alt="">
                                </a>
                                <a href="./profile.php?u=<?= $friend['username'] ?>">
                                    <span class="name"><?= ucwords($friend['firstname']) ?> <?= ucwords($friend['lastname']) ?></span>
                                </a>
                            </div>
                            <?php if (!is_friend($friend['id']) && $loggedin != $friend['id']) { ?>

                                <button class="addfriend-icon" data-user-id="<?= $friend['id'] ?>"><i class="fas fa-user-plus"></i></button>
                            <?php } ?>
                        </div>

                    <?php } ?>
                </div>
        </aside>
    <?php } ?>
    <?= show_partial("footer") ?>