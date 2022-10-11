<?php
require_once dirname(__FILE__) . "/php/functions.php";
require_once dirname(__FILE__) . "/php/shared.php";
require_once dirname(__FILE__) . "/php/send_code.php";
?>

<?php
// Resend verification code
if (isset($_GET['resend_code'])) {
    $code = gen_code();
    send_code($_SESSION['email'], 'Verify Your Email', $code);
    $_SESSION['code'] = $code;
    header('Location: ?resent');
}
?>

<?php
// Redirect user to Feed if logged in
// if (isset($_SESSION['user_id']) && $_SESSION['user_id']) {
//     header('Location: ./index.php');
// }
?>
<?php if (isset($_SESSION['email'])) { ?>
    <?= show_partial("header", ["page_title" => "ChatMa8 - Verify your email"]) ?>
    <div class="ve-wrapper">
        <p class="success"><?= isset($_GET['resent']) ? 'Verification code resent...' : '' ?></p>
        <p class="error"><?= isset($_GET['invalid_code']) ? 'Invalid Verification Code...' : '' ?></p>
        <h1>Verify your email</h1>
        <p>Enter the 6 digit code sent to your email address <b>(<?= $_SESSION['email'] ?? null ?>)</b></p>
        <form action="php/actions.php?verify_email" method="POST">
            <input type="text" name="code">
            <input type="submit" name="ve_btn" value="Verify Email">
            <a href="?resend_code" style="color: black;">Resend code</a>
        </form>
    </div>
<?php } ?>

<?= show_partial("footer") ?>