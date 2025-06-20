<?php
session_start();

// Configuration
define('SITE_NAME', '');
define('SITE_CNAME', 'YouBlog');
define('SITE_URL', 'index.php');
define('LOGO_PATH', 'assets/logotmp.png');

// File paths
define('USERS_JSON', 'data/users.json');
define('POSTS_JSON', 'data/posts.json');

// Create data directory if it doesn't exist
if (!file_exists('data')) {
    mkdir('data', 0755, true);
}

// Initialize empty JSON files if they don't exist
if (!file_exists(USERS_JSON)) {
    file_put_contents(USERS_JSON, json_encode([]));
}
if (!file_exists(POSTS_JSON)) {
    file_put_contents(POSTS_JSON, json_encode([]));
}

// Include functions
require_once 'functions.php';
?>