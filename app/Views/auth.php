<?php
$escape = static fn ($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in | Vitemines</title>
    <style>
        body { margin: 0; padding: 2rem 1rem; background: #f5f7f5; color: #17251d; font: 16px system-ui, sans-serif; }
        main { max-width: 720px; margin: 2rem auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem; }
        section { background: #fff; padding: 1.5rem; border-radius: 12px; }
        form { display: grid; gap: .8rem; }
        input, button { box-sizing: border-box; width: 100%; padding: .75rem; font: inherit; }
        button { border: 0; border-radius: 6px; background: #15803d; color: #fff; cursor: pointer; }
        .error { grid-column: 1 / -1; color: #b42318; }
    </style>
</head>
<body>
<main>
    <h1>Vitemines account</h1>
    <?php if ($error !== ''): ?><p class="error" role="alert"><?= $escape($error) ?></p><?php endif; ?>
    <section>
        <h2>Sign in</h2>
        <form method="post" action="<?= $escape($base_path) ?>/login">
            <input type="hidden" name="csrf_token" value="<?= $escape($csrf_token) ?>">
            <label>Email <input type="email" name="email" required autocomplete="email"></label>
            <label>Password <input type="password" name="password" required autocomplete="current-password"></label>
            <button type="submit">Sign in</button>
        </form>
    </section>
    <section>
        <h2>Create account</h2>
        <form method="post" action="<?= $escape($base_path) ?>/register">
            <input type="hidden" name="csrf_token" value="<?= $escape($csrf_token) ?>">
            <label>Name <input name="name" maxlength="100" required autocomplete="name"></label>
            <label>Email <input type="email" name="email" required autocomplete="email"></label>
            <label>Password <input type="password" name="password" minlength="8" required autocomplete="new-password"></label>
            <button type="submit">Create account</button>
        </form>
    </section>
</main>
</body>
</html>
