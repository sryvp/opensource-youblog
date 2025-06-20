<?php
function getUsers() {
    return json_decode(file_get_contents(USERS_JSON), true) ?? [];
}

function saveUsers($users) {
    file_put_contents(USERS_JSON, json_encode($users, JSON_PRETTY_PRINT));
}

function getPosts() {
    return json_decode(file_get_contents(POSTS_JSON), true) ?? [];
}

function savePosts($posts) {
    file_put_contents(POSTS_JSON, json_encode($posts, JSON_PRETTY_PRINT));
}

function getUserByUsername($username) {
    $users = getUsers();
    foreach ($users as $user) {
        if ($user['username'] === $username) {
            return $user;
        }
    }
    return null;
}

function isLoggedIn() {
    return isset($_SESSION['user']);
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

function extractHashtags($text) {
    preg_match_all('/#(\w+)/', $text, $matches);
    return $matches[1] ?? [];
}

function getPostsByHashtag($tag) {
    $posts = getPosts();
    return array_filter($posts, function($post) use ($tag) {
        return in_array(strtolower($tag), array_map('strtolower', $post['hashtags']));
    });
}

function getPostsByUser($username) {
    $posts = getPosts();
    return array_filter($posts, function($post) use ($username) {
        return $post['username'] === $username;
    });
}
// Add these functions to count comments and likes for a user
function countUserComments($username) {
    $posts = getPosts();
    $count = 0;
    foreach ($posts as $post) {
        if ($post['username'] === $username) {
            $count += count($post['comments']);
        }
    }
    return $count;
}

function countUserLikes($username) {
    $posts = getPosts();
    $count = 0;
    foreach ($posts as $post) {
        if ($post['username'] === $username) {
            $count += count($post['likes']);
        }
    }
    return $count;
}
?>