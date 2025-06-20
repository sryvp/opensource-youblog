<?php require_once 'config.php'; 

$profileUser = isset($_GET['user']) ? getUserByUsername($_GET['user']) : null;

if (!$profileUser) {
    header("HTTP/1.0 404 Not Found");
    die("User not found");
}

$userPosts = getPostsByUser($profileUser['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="assets/favicon.png" type="image/x-icon">
    <title>@<?= $profileUser['username'] ?> - <?= SITE_CNAME ?></title>
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
            <div class="search">
                <form action="index.php" method="get">
                    <input type="text" name="q" placeholder="Search @users or #tags">
                    <button type="submit">🔍</button>
                </form>
            </div>
            <nav>
                <?php if (isLoggedIn()): ?>
                    <a href="post.php">New Post</a>
                    <a href="profile.php?user=<?= $_SESSION['user']['username'] ?>">Profile</a>
                    <a href="profile-setup.php" class="settings-btn">Set-up profile</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="signup.php">Signup</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="profile-container">
        <div class="profile-header">
            <img src="<?= $profileUser['avatar'] ?>" alt="Profile picture" class="profile-avatar">
            <div class="profile-info">
                <h1>@<?= $profileUser['username'] ?></h1>
                <p class="profile-bio"><?= nl2br($profileUser['bio']) ?></p>
<div class="profile-stats">
    <span><?= count($userPosts) ?> posts</span>
    <span><?= countUserComments($profileUser['username']) ?> comments</span>
    <span><?= countUserLikes($profileUser['username']) ?> likes</span>
</div>
            </div>
        </div>

        <div class="profile-posts">
            <h2>Posts</h2>
            <?php if (empty($userPosts)): ?>
                <p>No posts yet</p>
            <?php else: ?>
                <div class="posts-grid">
                    <?php foreach (array_reverse($userPosts) as $post): ?>
                        <div class="post-thumbnail">
                            <a href="post.php?id=<?= $post['id'] ?>">
                                <img src="<?= $post['image'] ?>" alt="Post thumbnail">
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <footer>
        <p><?= SITE_CNAME ?> &copy; <?= date('Y') ?></p>
    </footer>
</body>
</html>