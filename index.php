<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/favicon.png" type="image/x-icon">
    <title><?= SITE_CNAME ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="header-content">
<a href="index.php" class="logo-link">
    <div class="logo">
        <img src="<?= LOGO_PATH ?>" alt="<?= SITE_CNAME ?>">
    </div>
</a>
            <div class="search">
                <form action="index.php" method="get">
                    <input type="text" name="q" placeholder="Search" value="<?= $_GET['q'] ?? '' ?>">
                    <button type="submit">🔍</button>
                </form>
            </div>
            <nav>
                <?php if (isLoggedIn()): ?>
                    <a href="post.php">New Post</a>
                    <a href="profile.php?user=<?= $_SESSION['user']['username'] ?>">Profile</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="signup.php">Signup</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="container">
        <?php
        $posts = getPosts();
        $searchQuery = $_GET['q'] ?? '';
        
        if (!empty($searchQuery)) {
            if (str_starts_with($searchQuery, '@')) {
                // Search by username
                $username = substr($searchQuery, 1);
                $userPosts = getPostsByUser($username);
                if ($userPosts) {
                    echo "<h2>Posts by @$username</h2>";
                    $posts = $userPosts;
                } else {
                    echo "<p>No posts found for @$username</p>";
                }
            } elseif (str_starts_with($searchQuery, '#')) {
                // Search by hashtag
                $tag = substr($searchQuery, 1);
                $tagPosts = getPostsByHashtag($tag);
                if ($tagPosts) {
                    echo "<h2>Posts tagged #$tag</h2>";
                    $posts = $tagPosts;
                } else {
                    echo "<p>No posts found for #$tag</p>";
                }
            } else {
                // General search
                echo "<h2>Search results for '$searchQuery'</h2>";
                $posts = array_filter($posts, function($post) use ($searchQuery) {
                    return stripos($post['caption'], $searchQuery) !== false || 
                           stripos($post['username'], $searchQuery) !== false;
                });
            }
        }
        
        if (empty($posts)) {
            echo "<p>No posts found. Be the first to post!</p>";
        } else {
            foreach (array_reverse($posts) as $post): ?>
                <div class="post">
                    <div class="post-header">
                        <a href="profile.php?user=<?= $post['username'] ?>" class="post-user">
                            @<?= $post['username'] ?>
                        </a>
                        <span class="post-date"><?= date('M j, Y', $post['timestamp']) ?></span>
                    </div>
                    <div class="post-image">
                        <img src="<?= $post['image'] ?>" alt="Post image">
                    </div>
                    <div class="post-caption">
                        <?= nl2br($post['caption']) ?>
                        <div class="post-tags">
                            <?php foreach ($post['hashtags'] as $tag): ?>
                                <a href="index.php?q=#<?= $tag ?>" class="hashtag">#<?= $tag ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="post-actions">
                        <form action="like.php" method="post" class="like-form">
                            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                            <button type="submit">❤️ <?= count($post['likes']) ?></button>
                        </form>
                        <a href="post.php?id=<?= $post['id'] ?>" class="comment-btn">💬 Comment</a>
                    </div>
                </div>
            <?php endforeach;
        }
        ?>
    </div>

    <footer>
        <p><?= SITE_CNAME ?> &copy; <?= date('Y') ?></p>
    </footer>
</body>
</html>
