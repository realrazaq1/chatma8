<?php
require_once dirname(__FILE__) . "/php/functions.php";
require_once dirname(__FILE__) . "/php/shared.php";

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
} else {
    $current_user = get_user($_SESSION['user_id']);
}

?>

<?= show_partial("header", ["page_title" => "ChatMa8 - Edit Profile"]); ?>
<?= show_partial("nav") ?>


<div class="edit-profile-container">
    <div class="profile-cover" style="--cover-url: url('../img/cover/<?= $current_user['cover_pic'] ?>')">
        <div class="pp">
            <img src="<?= dirname($_SERVER['SERVER_ADDR']) ?>/assets/img/profile/<?= $current_user['profile_pic'] ?>" alt="Post Profile Picture">
            <label for="profile_pic" class="edit-pp-icon">
                <i class="fas fa-pen"></i>
            </label>
        </div>
        <label for="cover_pic" class="edit-cp-icon">
            <i class="fas fa-pen"></i>
        </label>
    </div>
    <div class="edit-profile-content">
        <h2>Edit Profile</h2>
        <form action="./php/actions.php?editprofile" method="POST" enctype="multipart/form-data" class="edit-profile-form">
            <div class="files">
                <div>
                    <label for="cover_pic">Change cover picture</label>
                    <input type="file" name="cover_pic" id="cover_pic">
                </div>
                <div>
                    <label for="profile_pic">Change profile picture</label>
                    <input type="file" name="profile_pic" id="profile_pic">
                </div>
            </div>
            <div class="fields">
                <div>
                    <label for="firstname">First Name</label>
                    <input type="text" name="firstname" id="firstname" value="<?= $current_user['firstname'] ?>">
                </div>
                <div>
                    <label for="lastname">Last Name</label>
                    <input type="text" name="lastname" id="lastname" value="<?= $current_user['lastname'] ?>">
                </div>
                <div>
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" value="<?= $current_user['username'] ?>">
                </div>
                <div>
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" value="<?= $current_user['email'] ?>" disabled>
                </div>
                <div>
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender">
                        <option value="male" <?= $current_user['gender'] == 'male' ? 'selected' : ''  ?>>Male</option>
                        <option value="female" <?= $current_user['gender'] == 'female' ? 'selected' : ''  ?>>Female</option>
                        <option value="others" <?= $current_user['gender'] == 'others' ? 'selected' : ''  ?>>Others</option>
                    </select>
                </div>
                <div>
                    <label for="relationship">Relationship</label>
                    <select id="relationship" name="relationship">
                        <option value="single" <?= $current_user['relationship'] == 'single' ? 'selected' : ''  ?>>Single</option>
                        <option value="married" <?= $current_user['relationship'] == 'married' ? 'selected' : ''  ?>>Married</option>
                        <option value="divorced" <?= $current_user['relationship'] == 'divorced' ? 'selected' : ''  ?>>Divorced</option>
                        <option value="separated" <?= $current_user['relationship'] == 'separated' ? 'selected' : ''  ?>>Separated</option>
                        <option value="complicated" <?= $current_user['relationship'] == 'complicated' ? 'selected' : ''  ?>>Complicated</option>
                    </select>
                </div>
                <div>
                    <label for="state">From</label>
                    <input type="text" name="state" id="state" value="<?= $current_user['state'] ?>">
                </div>
                <div>
                    <label for="location">Resides In</label>
                    <input type="text" name="location" id="location" value="<?= $current_user['location'] ?>">
                </div>
                <div>
                    <label for="old_pass">Old Password</label>
                    <input type="password" name="old_pass" id="old_pass">
                </div>
                <div>
                    <label for="new_pass">New Password</label>
                    <input type="password" name="new_pass" id="new_pass">
                </div>
            </div>
            <input type="submit" name="update-profile-btn" value="Update Profile" id="update-profile-btn">
        </form>
    </div>
</div>

<?= show_partial("footer") ?>