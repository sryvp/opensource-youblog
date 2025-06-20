<?php require_once 'config.php'; 

if (isLoggedIn()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = password_hash(sanitize($_POST['password']), PASSWORD_DEFAULT);
    $email = sanitize($_POST['email']);
    
    $users = getUsers();
    
    // Check if username exists
    foreach ($users as $user) {
        if ($user['username'] === $username) {
            $error = "Username already exists";
            break;
        }
    }
    
    if (!isset($error)) {
        $newUser = [
            'id' => uniqid(),
            'username' => $username,
            'password' => $password,
            'email' => $email,
            'joined' => time(),
            'bio' => '',
            'avatar' => 'assets/default-avatar.png'
        ];
        
        $users[] = $newUser;
        saveUsers($users);
        
        $_SESSION['user'] = $newUser;
        redirect('index.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="assets/favicon.png" type="image/x-icon">
    <title>Signup - <?= SITE_CNAME ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo">
        <img src="<?= LOGO_PATH ?>" alt="<?= SITE_CNAME ?>">
    </div>
            <nav>
                <a href="index.php">Home</a>
                <a href="login.php">Login</a>
            </nav>
        </div>
    </header>

    <div class="auth-container">
        <div class="auth-box">
            <h2>Create Account</h2>
            <?php if (isset($error)): ?>
                <p class="error"><?= $error ?></p>
            <?php endif; ?>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Sign Up</button>
            </form>
            <p>Already have an account? <a href="login.php">Log in</a></p>
        </div>
    </div>
    <footer>
        <p><?= SITE_CNAME ?> &copy; <?= date('Y') ?></p>
    </footer>
</body>
</html>