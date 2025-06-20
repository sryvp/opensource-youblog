<?php require_once 'config.php'; 

if (isLoggedIn()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = sanitize($_POST['password']);
    
    $users = getUsers();
    foreach ($users as $user) {
        if ($user['username'] === $username && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            redirect('index.php');
        }
    }
    
    $error = "Invalid username or password";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="assets/favicon.png" type="image/x-icon">
    <title>Login - <?= SITE_CNAME ?></title>
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
                <a href="signup.php">Signup</a>
            </nav>
        </div>
    </header>

    <div class="auth-container">
        <div class="auth-box">
            <h2>Login</h2>
            <?php if (isset($error)): ?>
                <p class="error"><?= $error ?></p>
            <?php endif; ?>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
            <p>Don't have an account? <a href="signup.php">Sign up</a></p>
        </div>
    </div>
    <footer>
        <p><?= SITE_CNAME ?> &copy; <?= date('Y') ?></p>
    </footer>
</body>
</html>