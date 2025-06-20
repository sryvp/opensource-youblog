<?php
require_once 'config.php';

if (!isLoggedIn() || !isset($_POST['post_id'])) {
    redirect('index.php');
}

$postId = $_POST['post_id'];
$username = $_SESSION['user']['username'];

$posts = getPosts();
foreach ($posts as &$post) {
    if ($post['id'] === $postId) {
        // Check if user already liked
        $likeIndex = array_search($username, $post['likes']);
        if ($likeIndex === false) {
            $post['likes'][] = $username;
        } else {
            array_splice($post['likes'], $likeIndex, 1);
        }
        break;
    }
}

savePosts($posts);

// Redirect back to previous page
$referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
redirect($referer);
?>