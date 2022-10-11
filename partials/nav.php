<?php
require_once dirname(__FILE__, 2) . "/php/functions.php";
require_once dirname(__FILE__, 2) . "/php/shared.php";

$current_user = get_user($_SESSION['user_id']);
extract($current_user);
?>

<nav>
    <div class="logo-wrapper">
        <h1 class="logo">
            <a href="feed.php"><i class="fa-sharp fa-solid fa-circle-nodes"></i> ChatMa8</a>
        </h1>
    </div>
    <div class="search-container">
        <input type="text" name="search" placeholder="Search for friends...">
    </div>
    <div class="nav-links">
        <div class="nav-icons">
            <a href="#"><i class="fas fa-user"></i></a>
            <a href="msgs.php"><i class="fas fa-envelope"></i></a>
            <a href="not.php"><i class="fas fa-bell"></i></a>
            <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i></a>
            <a href="profile.php?u=<?= $username ?>" class="user-link">
        </div>
        <div class="user-link-content">
            <img src="<?= dirname($_SERVER['SERVER_ADDR']) ?>/assets/img/profile/<?= $profile_pic ?>" alt="User Profile Picture">
            <span><?= $username ?></span>
        </div>
        </a>
    </div>
</nav>