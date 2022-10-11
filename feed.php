<?php
require_once dirname(__FILE__) . "/php/functions.php";
require_once dirname(__FILE__) . "/php/shared.php";

?>

<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
} else {
    $user_data = get_user($_SESSION['user_id']);
    extract($user_data);
    $posts = filter_posts();
}

?>

<?php

// check if user is blocked
if ($acct_status == 'blocked') {
    header('Location: blocked.php');
} ?>

<?php if (isset($_SESSION['user_id']) && $acct_status == 'active') { ?>
    <?= show_partial("header", ["page_title" => "ChatMa8 - Feed"]); ?>
    <?= show_partial("nav"); ?>
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
            <!-- POST UPLOAD STARTS -->
            <section class="post-upload">
                <div class="cancel"><i class="fas fa-x"></i></div>
                <!-- POST UPLOAD IMAGE/VIDEO PREVIEW -->
                <div class="feedback">
                </div>
                <!-- POST UPLOAD INPUTS START -->
                <textarea name="text" id="text" cols="30" rows="1" placeholder="Share your thoughts <?= ucwords($username) ?>!"></textarea>
                <input type="file" name="file" id="file" hidden>
                <div class="upload-preview">
                </div>
                <div class="attmt">
                    <button id="add-file"><i class="fa-solid fa-photo-film"></i> Photo/Video</button>
                </div>
                <input type="submit" value="Post" name="post_btn" id="post_btn">
                <!-- POST UPLOAD INPUTS END -->
            </section>
            <!-- POST UPLOAD ENDS -->
            <!-- USERS POST STARTS -->
            <?php
            foreach ($posts as $post) { ?>

                <section class="post">
                    <div class="poster-info-section">
                        <i class="fa-solid fa-ellipsis-vertical post-option"></i>
                        <!-- <i class="fa-solid fa-ellipsis-vertical"></i> -->
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
                            <img src="<?= dirname($_SERVER['SERVER_ADDR']) ?>/assets/img/profile/<?= $profile_pic ?>" alt="User Profile Picture">
                        </div>
                        <textarea class="comment" id="comment<?= $post['id'] ?>" cols="20" rows="1" placeholder="Add a comment..."></textarea>
                        <button class="comment-btn" data-post-id="<?= $post['id'] ?>">Post</button>
                    </div>
                    <section class="comments-section" id="comments-section<?= $post['id'] ?>" data-post-id="<?= $post['id'] ?>">
                    </section>
                </section>


            <?php } ?>
            <!-- USERS POST ENDS -->
        </main>
        <!-- RIGHT SIDE BAR -->
        <aside class="right-sidebar">
            <div class="ads">
                <img class="ad-img" src="./assets/img/get-more-naira.gif" alt="">
                <div class="friends">
                    <h1>Suggested Friends</h1>
                    <div class="friendsWrapper">
                        <?php foreach (get_suggestions() as $friend) { ?>
                            <div class="friendWrapper">
                                <div class="info">
                                    <a href="./profile.php?u=<?= $friend['username'] ?>"><img src="./assets/img/profile/<?= $friend['profile_pic'] ?>" alt=""></a>
                                    <a href="./profile.php?u=<?= $friend['username'] ?>">
                                        <span class="name"><?= ucwords($friend['firstname']) ?> <?= ucwords($friend['lastname']) ?></span>
                                    </a>
                                </div>
                                <?php if (!is_friend($friend['id']) && $user_data['id'] != $friend['id']) { ?>

                                    <button class="addfriend-icon" data-user-id="<?= $friend['id'] ?>"><i class="fas fa-user-plus"></i></button>
                                <?php } ?>
                            </div>

                        <?php } ?>
                    </div>
                </div>
                <img class="ad-img" src="./assets/img/ad1.png" alt="">
            </div>

        </aside>
        <script>
            document.querySelector('#add-file').addEventListener('click', (e) => {
                e.preventDefault()
                document.querySelector('#file').click();
            });
        </script>
    </div>

<?php } ?>

<?= show_partial("footer") ?>