<?php 
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$currentUser = $_SESSION['user'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $users = getUsers();
    
    // Find current user in array
    foreach ($users as &$user) {
        if ($user['username'] === $currentUser['username']) {
            // Update bio
            if (isset($_POST['bio'])) {
                $user['bio'] = sanitize($_POST['bio']);
            }
            
            // Handle password change
            if (!empty($_POST['current_password']) && !empty($_POST['new_password'])) {
                if (!password_verify($_POST['current_password'], $user['password'])) {
                    $error = "Current password is incorrect";
                } elseif ($_POST['new_password'] !== $_POST['confirm_password']) {
                    $error = "New passwords don't match";
                } else {
                    $user['password'] = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                }
            }
            
            // Handle avatar upload
            if (!empty($_FILES['avatar']['name'])) {
                $uploadDir = 'uploads/avatars/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $filename = uniqid() . '.' . pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
                $targetFile = $uploadDir . $filename;
                
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFile)) {
                    // Delete old avatar if not default
                    if ($user['avatar'] !== 'assets/default-avatar.png') {
                        @unlink($user['avatar']);
                    }
                    $user['avatar'] = $targetFile;
                }
            }
            
            // Update session
            $_SESSION['user'] = $user;
            break;
        }
    }
    
    if (empty($error)) {
        saveUsers($users);
        redirect('profile.php?user=' . $currentUser['username']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Setup - <?= SITE_CNAME ?></title>
    <link rel="shortcut icon" href="assets/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="header-content">
<a href="index.php" class="logo-link">
    <div class="logo">
        <img src="<?= LOGO_PATH ?>" alt="<?= SITE_NAME ?>">
        <span><?= SITE_NAME ?></span>
    </div>
</a>
            <nav>
                <a href="profile.php?user=<?= $currentUser['username'] ?>">Back to Profile</a>
                <a href="index.php">Home</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="setup-container">
            <h1>Profile Settings</h1>
            
            <?php if (!empty($error)): ?>
                <div class="error-message"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="avatar">Profile Picture</label>
                    <div class="current-avatar">
                        <img src="<?= $currentUser['avatar'] ?>" alt="Current avatar">
                    </div>
                    <input type="file" name="avatar" id="avatar" accept="image/*">
                </div>
                
                <div class="form-group">
                    <label for="bio">Bio</label>
                    <textarea name="bio" id="bio" placeholder="Tell about yourself"><?= $currentUser['bio'] ?></textarea>
                </div>
                
                <div class="password-change">
                    <h3>Change Password</h3>
                    
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" name="current_password" id="current_password">
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" name="new_password" id="new_password">
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" name="confirm_password" id="confirm_password">
                    </div>
                </div>
                
                <button type="submit" class="save-btn">Save Changes</button>
            </form>
        </div>
    </div>
</body>
</html>