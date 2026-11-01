# Shifa · Vitemines E-commerce

A wellness shopping experience for vitamins and supplements, built with PHP and MySQL. Shifa brings product discovery, checkout, customer accounts, and store management into one application.

[GitHub repository](https://github.com/aljariri0/Vit) · [Trello board](https://trello.com/b/UdSHFspK/vitemines-e-commerce) · [Design guide](docs/design/README.md)

## Project overview

Customers can browse products by brand or category, search the catalog, manage a shopping bag, and place orders using Stripe or cash on delivery. Their account brings together profile details, order history, product photos, and order status.

Administrators manage the catalog and fulfilment from a dashboard with a full-height sidebar, product search, moderated reviews, and five Chart.js visualizations. Approved customer comments can be summarized into cached AI Review Notes.

> This documentation describes the updated **finalVit** implementation. The GitHub checkout may still contain the earlier source; sync the updated application before expecting all the features below in a fresh clone.

## Features

### Customer experience

- Landing page with product photography and wellness categories.
- Product search, brand and category filters, and paginated catalog browsing.
- Product details, available sizes, stock checks, discounts, and shopping bag controls.
- Email/password registration and sign-in, plus optional Google sign-in.
- Profile editing, password changes, and customer order history.
- Order details with product photos, quantities, shipping information, and totals.
- Stripe card checkout and cash on delivery, with prices recorded in USD.
- Shipping across Jordan's 12 governorates; free shipping above 70.52 USD.
- Star ratings and written reviews, subject to admin approval.

### Administration

- Direct dashboard redirect after admin login and a full-height navigation sidebar.
- Product creation, editing, visibility controls, and search by name, brand, or category.
- Brand/category management, customer lists, payments, and review moderation.
- Order details and controlled fulfilment status updates.
- Eight-row pagination, scrollable tables, alternating row colors, and explicit View actions.
- Five charts: daily sales, order statuses, top-selling products, category sales, and new customers.
- AI Review Notes generated from approved written reviews and cached per product.

Verified card payments move pending orders to **Processing**. Cash-on-delivery confirmation also moves an order to **Processing**. Shipping and delivery require admin updates based on actual fulfilment.

## Technology

| Area | Implementation |
| --- | --- |
| Backend | PHP, custom router and MVC-style organization |
| Database | MySQL/MariaDB through PDO and prepared statements |
| Frontend | PHP templates, JavaScript, custom CSS, Tailwind browser CDN |
| Analytics | Chart.js 4 |
| Payments | Stripe PHP library and cash on delivery |
| Authentication | PHP sessions, password hashing, Google OAuth |
| Review summaries | Server-side OpenAI integration |

No Node.js build is required to run the application. Bundled PHP libraries live in `app/Libraries/`.

## Local setup

### 1. Requirements

- PHP 8.1 or newer, with `pdo_mysql`, `curl`, and JSON support; `mbstring` is recommended.
- MySQL or MariaDB; XAMPP provides a convenient local PHP/Apache/database stack.
- Internet access for CDN assets and any enabled external integrations.

### 2. Obtain the source

```bash
git clone https://github.com/aljariri0/Vit.git
cd Vit
```

If you already have the updated `finalVit` folder, run the following commands from that folder instead. See the version note above when using a fresh clone.

### 3. Import the database

For the updated version, import `database/schema-v2.sql` into MySQL using phpMyAdmin or your database client. It creates and selects the `vitemines` database.

The earlier repository version uses `database/schema.sql`. Use the schema shipped with the version you are running. Import into a fresh development database; a schema dump is not an incremental upgrade.

For an existing database, review the applicable scripts in `database/migrations/` before applying them. Do not blindly apply every migration after importing the full schema. Run `database/migrate_currency_to_usd.php` only when upgrading an older JOD database that has not been converted.

### 4. Configure the environment

Create `.env` in the project root. If `.env.example` is present, copy it first. Use your own values:

```dotenv
DB_HOST=localhost
DB_NAME=vitemines
DB_USER=root
DB_PASSWORD=

STRIPE_SECRET_KEY=
STRIPE_PUBLISHABLE_KEY=
STRIPE_CURRENCY=usd

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback
APP_BASE_PATH=

OPENAI_API_KEY=
OPENAI_REVIEW_MODEL=
```

Set `OPENAI_REVIEW_MODEL` to a model available to your API account when enabling AI Review Notes. Keep secret keys server-side and out of source control. Check that `.env` is ignored before committing; changing ignore rules does not remove a previously tracked file.

### 5. Create an administrator

```bash
php scripts/create-admin.php
```

Follow the prompts. No shared admin password is documented or required.

### 6. Start the application

```bash
php -S 127.0.0.1:8000 -t public public/router.php
```

Open [the local store](http://127.0.0.1:8000). Admin sign-in redirects to `/admin/dashboard` in the updated version.

For Apache/XAMPP, use `public/` as the document root and enable `mod_rewrite`. If served from a subdirectory, include that path in the URL and Google callback configuration.

## Optional integrations

| Integration | Configuration | When omitted |
| --- | --- | --- |
| Stripe | Secret and publishable test keys; keep currency aligned with orders | Cash on delivery remains available |
| Google sign-in | Client ID, client secret, and exact authorized callback URL | Email/password sign-in remains available |
| AI Review Notes | API key, accessible model, and summary-cache database table | Regular review moderation remains available |

The Google callback URI must match the address used in the browser, including host, port, and any subdirectory. A `redirect_uri_mismatch` must be corrected in both the environment configuration and the OAuth client.

To generate AI Review Notes, approve written reviews, open **Admin → Products**, expand **Review Notes**, and select **Generate notes**. Only ratings and comment text are submitted; reviewer names and emails are excluded. Regenerate notes after approving new comments.

The schema and review-notes migration include clearly labeled synthetic sample reviews. Remove demo content before operating the store with real customers.

## Project structure

```text
app/
  Core/          Router, configuration, controllers, and shared services
  Models/        Database access and order/catalog rules
  Services/      Payments, product image resolution, and review summaries
  Views/         Storefront, account, checkout, and admin templates
  Libraries/     Bundled third-party PHP dependencies
database/        Schema, migrations, catalog import, and currency upgrade
public/
  index.php      Route definitions and application entry point
  router.php     PHP development-server routing
  assets/        Stylesheets, JavaScript, product images, and design assets
scripts/         Administrator creation utility
docs/design/     Design guide and Figma-importable SVG board
```

## Design and planning

- **Planning:** [Vitemines E-commerce on Trello](https://trello.com/b/UdSHFspK/vitemines-e-commerce).
- **Source:** [aljariri0/Vit on GitHub](https://github.com/aljariri0/Vit).
- **Design:** [Shifa design guide](docs/design/README.md) and [SVG design board](docs/design/shifa-design.svg).

The SVG is an editable design reference for import into Figma, not a published Figma file or a pixel-perfect export of the application. A live Figma link can be added after the design is imported and shared.

## Validation and practical notes

The updated local implementation has passed PHP syntax checks, JavaScript syntax checks, database-backed dashboard rendering, product-search checks, and order-photo resolution checks. These checks do not replace an end-to-end payment test.

Before deployment, test Stripe in test mode, verify Google callbacks, review environment and database permissions, remove demo data, and replace Tailwind's browser CDN with compiled production CSS. Dashboard sales include confirmed cash-on-delivery orders; they represent order value rather than collected cash.
