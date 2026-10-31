# Shifa

Shifa is a PHP and MySQL wellness shop. It uses the existing schema in `database/schema.sql`, Tailwind CSS utilities, and the local Shifa stylesheet for the storefront and dashboards.

## Run locally

1. For a fresh database, import `database/schema.sql`. For an older database, apply the matching migration in `database/migrations/` once instead; do not run it after the fresh schema, because both add the same columns.
2. Copy `.env.example` to `.env` and set the database credentials. Add your Stripe test keys before trying card checkout.
3. Create the first admin with `php scripts/create-admin.php`.
4. Start the site from the repository root:

   ```sh
   php -S 127.0.0.1:8000 -t public public/router.php
   ```

5. Open `http://127.0.0.1:8000`. Apache deployments should enable `mod_rewrite` and use `public/` as the document root.

Admin pages require an account whose session role is `admin`. Customer sign-up and sign-in use the existing account tables.

For an existing database, apply `database/migrations/20261031_payment_and_review_alignment.sql` only if these changes have not already been applied. Do not run it after importing the fresh schema or after manually running the two `ALTER TABLE` statements; that would add the columns twice.

Tailwind is loaded with its browser CDN because this project has no Node build setup; that needs internet access in the browser. For a production deployment, switch to Tailwind's CLI build.
