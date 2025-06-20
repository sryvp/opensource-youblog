<?php require_once 'config.php'; 

if (!isLoggedIn()) {
    redirect('login.php');
}

// View single post
if (isset($_GET['id'])) {
    $posts = getPosts();
    $post = null;
    foreach ($posts as $p) {
        if ($p['id'] === $_GET['id']) {
            $post = $p;
            break;
        }
    }
    
    if (!$post) {
        die("Post not found");
    }
    
    // Handle comment submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
        $comment = [
            'id' => uniqid(),
            'username' => $_SESSION['user']['username'],
            'text' => sanitize($_POST['comment']),
            'timestamp' => time()
        ];
        
        $post['comments'][] = $comment;
        
        // Update the post in the array
        foreach ($posts as &$p) {
            if ($p['id'] === $post['id']) {
                $p = $post;
                break;
            }
        }
        
        savePosts($posts);
        redirect("post.php?id=" . $post['id']);
    }
}

// Create new post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $caption = sanitize($_POST['caption']);
    $hashtags = extractHashtags($caption);
    
    // Simple image upload handling
    $uploadDir = 'uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $filename = uniqid() . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $targetFile = $uploadDir . $filename;
    
    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        $newPost = [
            'id' => uniqid(),
            'username' => $_SESSION['user']['username'],
            'image' => $targetFile,
            'caption' => $caption,
            'hashtags' => $hashtags,
            'timestamp' => time(),
            'likes' => [],
            'comments' => []
        ];
        
        $posts = getPosts();
        $posts[] = $newPost;
        savePosts($posts);
        
        redirect('index.php');
    } else {
        $error = "Error uploading file";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/favicon.png" type="image/x-icon">
    <title><?= isset($post) ? "Post" : "New Post" ?> - <?= SITE_CNAME ?></title>
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
                <a href="index.php">Home</a>
                <a href="profile.php?user=<?= $_SESSION['user']['username'] ?>">Profile</a>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <?php if (isset($post)): ?>
            <!-- View single post -->
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
                </div>
                
                <div class="post-comments">
                    <h3>Comments</h3>
                    <?php foreach ($post['comments'] as $comment): ?>
                        <div class="comment">
                            <a href="profile.php?user=<?= $comment['username'] ?>" class="comment-user">
                                @<?= $comment['username'] ?>
                            </a>
                            <p class="comment-text"><?= nl2br($comment['text']) ?></p>
                            <span class="comment-date"><?= date('M j, Y', $comment['timestamp']) ?></span>
                        </div>
                    <?php endforeach; ?>
                    
                    <form method="POST" class="comment-form">
                        <input type="text" name="comment" placeholder="Add a comment..." required>
                        <button type="submit">Post</button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <!-- Create new post -->
            <div class="new-post">
                <h2>Create New Post</h2>
                <?php if (isset($error)): ?>
                    <p class="error"><?= $error ?></p>
                <?php endif; ?>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="image">Image</label>
                        <input type="file" name="image" id="image" accept="image/*" required>
                    </div>
                    <div class="form-group">
                        <label for="caption">Caption</label>
                        <textarea name="caption" id="caption" placeholder="Write a caption... #hashtags"></textarea>
                    </div>
                    <button type="submit">Share</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>