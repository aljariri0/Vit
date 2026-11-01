# Shifa design reference

[Open the SVG board](shifa-design.svg).

This board documents the existing Shifa visual direction and illustrates the storefront, account orders, and admin dashboard. It is a design reference, not a functional prototype. All numbers and chart shapes on the board are illustrative.

## Import into Figma

1. Create or open a Figma Design file.
2. Drag `shifa-design.svg` onto the canvas.
3. Ungroup the imported artwork to edit text, shapes, and screen layouts.
4. Use the colors and typography below to create local styles or variables.
5. Replace illustrations with project product photos, then add Auto Layout, reusable components, and prototype interactions as needed.

A live Figma file has not been created. Importing the SVG does not automatically create components, Auto Layout, or working interactions.

## Design tokens

| Role | Value |
| --- | --- |
| Primary text | `#182A24` |
| Forest / primary action | `#0E2721` |
| Sage | `#B4C6A5` |
| Highlight | `#D8E7B5` |
| Page background | `#F7F7F2` |
| Secondary surface | `#EEE9DC` |
| Muted text | `#78817A` |
| Border | `#E5E7DF` |
| Warm accent | `#E9C8B8` |
| Body typography | DM Sans, sans-serif |
| Display typography | Fraunces or Georgia fallback |
| Spacing reference | 8, 16, 24, 32, 48, 64 px |

## Reusable patterns

- Primary, secondary, View, Edit, and destructive-action buttons, each with a visible focus state.
- Product cards with photo, name, price, and stock-aware purchase action.
- Tables with eight records per page, alternating row colors, scrolling, and explicit View labels.
- Status pills for pending, processing, shipped, delivered, cancelled, and refunded orders.
- Full-height admin sidebar with a clearly highlighted active destination.
- Dashboard cards and charts with real aggregate data, descriptive labels, and empty states.

## Main journeys

Customer: landing page → shop/search → product → bag → checkout → account orders → order details.

Admin: sign-in → dashboard → search/manage products, orders, catalog, customers, payments, or reviews.
