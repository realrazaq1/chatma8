<?php
require_once dirname(__FILE__) . "/php/functions.php";
require_once dirname(__FILE__) . "/php/shared.php";
?>

<?php $loggedin = isset($_SESSION['user_id']) ?>

<?php
if ((true || isset($_GET['login']) || isset($_GET['reg'])) && !$loggedin) { ?>

    <?= show_partial("header", ["page_title" => "ChatMa8 - find friends & share updates"]) ?>
    <div class="container">
        <h1 class="logo"><i class="fa-sharp fa-solid fa-circle-nodes"></i> ChatMa8</h1>
        <div class="forms-wrapper">
            <div class="form-wrapper">
                <h2 class="form-heading">Create new account</h2>
                <form method="POST" action="./php/actions.php?reg">
                    <div class="input-wrapper">
                        <label for="firstname">First Name</label>
                        <input type="text" name="firstname" id="firstname" value="<?= show_formdata('firstname') ?? null ?>">
                        <p class="error"><?= show_error('firstname') ?? null ?></p>
                    </div>
                    <div class="input-wrapper">
                        <label for="lastname">Last Name</label>
                        <input type="text" name="lastname" id="lastname" value="<?= show_formdata('lastname') ?? null ?>">
                        <p class="error"><?= show_error('lastname') ?? null ?></p>
                    </div>
                    <div class="input-wrapper">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" value="<?= show_formdata('username') ?? null ?>">
                        <p class="error"><?= show_error('username') ?? null ?></p>
                    </div>
                    <div class="input-wrapper">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" value="<?= show_formdata('email') ?? null ?>">
                        <p class="error"><?= show_error('email') ?? null ?></p>
                    </div>
                    <div class="input-wrapper">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password">
                        <p class="error"><?= show_error('password') ?? null ?></p>
                    </div>
                    <div class="input-wrapper">
                        <label for="gender">Gender</label>
                        <select name="gender" id="gender">
                            <option value="male" <?= show_formdata('gender') == 'male' ? 'selected' : '' ?>>Male</option>
                            <option value="female" <?= show_formdata('gender') == 'female' ? 'selected' : '' ?>>Female</option>
                            <option value="others" <?= show_formdata('gender') == 'others' ? 'selected' : '' ?>>Others</option>
                        </select>
                        <p class="error"><?= show_error('gender') ?? null ?></p>
                    </div>
                    <input type="submit" name="reg-btn" value="Register">
                </form>
            </div>
            <div class="form-wrapper">
                <p class="success"><?= isset($_GET['new_reg']) ? 'Registration Successful..' : '' ?></p>
                <p class="success"><?= isset($_GET['verified']) ? 'Email Verified Successfully..' : '' ?></p>
                <h2 class="form-heading">Login to your account</h2>
                <form method="POST" action="./php/actions.php?login">
                    <div class="input-wrapper">
                        <label for="username_email">Username/Email</label>
                        <p class="error"><?= show_error('login') ?? null ?></p>
                        <input type="text" name="username_email" id="username_email" value="<?= show_formdata('username_email') ?? null ?>">
                        <p class="error"><?= show_error('username_email') ?? null ?></p>
                    </div>
                    <div class="input-wrapper">
                        <label for="login_password">Password</label>
                        <input type="password" name="login_password" id="login_password">
                        <p class="error"><?= show_error('login_password') ?? null ?></p>

                    </div>
                    <input type="submit" name="login-btn" value="Login">

                </form>
            </div>
        </div>
    </div>

<?php } elseif ($loggedin) {
    header('Location: feed.php');
}
?>
<?= show_partial("footer") ?>