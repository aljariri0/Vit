<?php $registering = ($auth_mode ?? 'login') === 'register'; ?>
<section class="account-entry" aria-labelledby="account-title">
  <div class="account-art">
    <p class="eyebrow">SHIFA / WELLNESS, YOUR WAY</p>
    <h2>A little care.<br><span>A better everyday.</span></h2>
    <p>Your favourite essentials.<br>Your own feel-good routine.</p>
    <div class="account-bottle"><div class="account-orbit" aria-hidden="true"></div><img src="<?= $e($base) ?>/assets/products/Solgar/Omega%20and%20Fish%20Oil/Solgar-Triple-Strength-Omega-3-950mg.png" alt="Solgar Omega-3 vitamins"><span class="softgel" aria-hidden="true"></span></div>
    <span class="account-art-caption">SMALL RITUALS. BETTER DAYS.</span>
  </div>
  <div class="account-form-side">
    <div class="account-form-wrap">
      <a class="account-back" href="<?= $e($base) ?>/">← Back to Shifa</a>
      <p class="eyebrow"><?= $registering ? 'YOUR WELLNESS STARTS HERE' : 'WELCOME BACK' ?></p>
      <h1 id="account-title"><?= $registering ? 'Create your account' : 'Good to see you.' ?></h1>
      <p class="account-intro"><?= $registering ? 'A little space for all your feel-good favourites.' : 'Sign in to follow your orders and pick up where you left off.' ?></p>
      <?php if ($error !== ''): ?><div class="account-error" role="alert"><?= $e($error) ?></div><?php endif; ?>
      <a class="google-button" href="<?= $e($base) ?>/auth/google?mode=<?= $registering ? 'register' : 'login' ?>"><svg width="20" height="20" viewBox="0 0 48 48" aria-hidden="true"><path fill="#4285F4" d="M43.6 24.5c0-1.4-.1-2.8-.4-4.1H24v7.8h11a9.4 9.4 0 0 1-4.1 6.2v5.1h6.6c3.9-3.6 6.1-8.8 6.1-15z"/><path fill="#34A853" d="M24 44c5.5 0 10.1-1.8 13.5-4.5l-6.6-5.1c-1.8 1.2-4.1 1.9-6.9 1.9-5.3 0-9.8-3.6-11.4-8.4H5.8v5.3A20.4 20.4 0 0 0 24 44z"/><path fill="#FBBC05" d="M12.6 27.9a12.2 12.2 0 0 1 0-7.8v-5.3H5.8a20 20 0 0 0 0 18.4z"/><path fill="#EA4335" d="M24 11.7c3 0 5.6 1 7.7 3l5.8-5.8A19.4 19.4 0 0 0 24 4 20.4 20.4 0 0 0 5.8 14.8l6.8 5.3c1.6-4.8 6.1-8.4 11.4-8.4z"/></svg>Continue with Google</a>
      <div class="account-divider"><span>or continue with email</span></div>
      <form class="account-form" method="post" action="<?= $e($base) ?>/<?= $registering ? 'register' : 'login' ?>">
        <input type="hidden" name="csrf_token" value="<?= $e($csrf_token) ?>">
        <?php if ($registering): ?><label for="account-name">Full name</label><input id="account-name" name="name" required maxlength="100" autocomplete="name" value="<?= $e($old_name ?? '') ?>" placeholder="Your name"><?php endif; ?>
        <label for="account-email">Email address</label><input id="account-email" type="email" name="email" required maxlength="150" autocomplete="email" value="<?= $e($old_email ?? '') ?>" placeholder="you@example.com">
        <label for="account-password"><?= $registering ? 'Create password' : 'Password' ?></label><div class="account-password"><input id="account-password" type="password" name="password" required <?= $registering ? 'minlength="8"' : '' ?> autocomplete="<?= $registering ? 'new-password' : 'current-password' ?>" placeholder="<?= $registering ? 'At least 8 characters' : 'Enter your password' ?>"><button type="button" data-toggle-password="account-password" aria-label="Show password" aria-pressed="false">Show</button></div>
        <?php if ($registering): ?><label for="account-confirm">Confirm password</label><div class="account-password"><input id="account-confirm" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password" placeholder="Enter your password again"><button type="button" data-toggle-password="account-confirm" aria-label="Show confirmation password" aria-pressed="false">Show</button></div><?php endif; ?>
        <button class="account-submit" type="submit"><?= $registering ? 'Create account' : 'Sign in' ?> <span aria-hidden="true">↗</span></button>
      </form>
      <p class="account-switch"><?= $registering ? 'Already have an account?' : 'New to Shifa?' ?> <a href="<?= $e($base) ?>/<?= $registering ? 'login' : 'register' ?>"><?= $registering ? 'Sign in' : 'Create an account' ?></a></p>
    </div>
  </div>
</section>
