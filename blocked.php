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
}

if ($acct_status == 'blocked') { ?>

    <?= show_partial("header", ["page_title" => "ChatMa8 - Account Blocked"]) ?>
    <div class="ve-wrapper">

        <h1 class="error">Account Blocked</h1>
        <p>Hey <b>(<?= $username ?? null ?>)</b> your account has been blocked by Admin</p>
        <?php if (isset($_SESSION['user_id'])) { ?>
            <a href="logout.php" style="color: black;">Logout</a>

        <?php } else { ?>
            <a href="index.php" style="color: black;">Home</a>
        <?php } ?>
    </div>

    <?= show_partial("footer") ?>

<?php }
?>