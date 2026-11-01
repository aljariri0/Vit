-- The Nature Made probiotic has no matching image in public/assets/products.
-- Keep historical order lines, but detach them from the product before removal.
START TRANSACTION;

UPDATE order_items oi
INNER JOIN products p ON p.id = oi.product_id
SET oi.product_id = NULL
WHERE p.brand_id = 1
  AND p.category_id = 5
  AND p.name = 'Daily Probiotic 1 Billion CFU';

DELETE r
FROM reviews r
INNER JOIN products p ON p.id = r.product_id
WHERE p.brand_id = 1
  AND p.category_id = 5
  AND p.name = 'Daily Probiotic 1 Billion CFU';

DELETE FROM products
WHERE brand_id = 1
  AND category_id = 5
  AND name = 'Daily Probiotic 1 Billion CFU';

COMMIT;
