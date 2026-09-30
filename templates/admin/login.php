<?= render('admin/_head', ['title' => 'Sign in']) ?>
<body class="admin admin--login">
<main class="login-card">
    <img src="<?= e(site('logo')) ?>" alt="<?= e(site('name')) ?>" class="login-logo">
    <h1>Admin sign in</h1>
    <?php if (!empty($error)): ?><p class="msg msg--err" role="alert"><?= e($error) ?></p><?php endif; ?>
    <form method="post" action="<?= ADMIN_PATH ?>" autocomplete="on">
        <input type="hidden" name="action" value="login">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <label>Username <input name="username" autocomplete="username" required autofocus></label>
        <label>Password <input name="password" type="password" autocomplete="current-password" required></label>
        <button class="btn" type="submit">Sign in</button>
    </form>
</main>
</body>
</html>
