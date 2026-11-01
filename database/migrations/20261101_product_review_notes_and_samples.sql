-- Adds cached admin Review Notes and three clearly marked demo reviews per product.
-- Safe to run more than once: sample reviewers and their product reviews are deduplicated.
CREATE TABLE IF NOT EXISTS product_review_summaries (
    product_id INT(10) UNSIGNED NOT NULL,
    summary_text TEXT NOT NULL,
    review_count INT(10) UNSIGNED NOT NULL DEFAULT 0,
    generated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (product_id),
    CONSTRAINT fk_product_review_summaries_product
        FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO users (name, email, password_hash) VALUES
('Sample Reviewer One', 'sample-reviewer-one@example.test', '!disabled-demo-account!'),
('Sample Reviewer Two', 'sample-reviewer-two@example.test', '!disabled-demo-account!'),
('Sample Reviewer Three', 'sample-reviewer-three@example.test', '!disabled-demo-account!');

INSERT INTO reviews (product_id, user_id, star_rating, comment, status, created_at)
SELECT p.id, u.id, sample.star_rating,
       CONCAT('[Sample review] ', p.name, CASE sample.star_rating
           WHEN 1 THEN CASE MOD(p.id, 4)
               WHEN 0 THEN ' arrived later than expected and the packaging was damaged.'
               WHEN 1 THEN ' was difficult to open, and delivery took longer than expected.'
               WHEN 2 THEN ' did not match what I expected from the listing.'
               ELSE ' arrived in a damaged box and was disappointing.'
           END
           WHEN 3 THEN CASE MOD(p.id, 4)
               WHEN 0 THEN ' was acceptable overall, but the product details could be clearer.'
               WHEN 1 THEN ' is okay, though delivery and packaging were average.'
               WHEN 2 THEN ' met basic expectations, but I am unsure I would order it again.'
               ELSE ' was neither great nor poor; the product listing could include more details.'
           END
           ELSE CASE MOD(p.id, 4)
               WHEN 0 THEN ' arrived in secure packaging and matched the listing.'
               WHEN 1 THEN ' was easy to order, and the package arrived in good condition.'
               WHEN 2 THEN ' was a positive purchase, with clear product details and prompt delivery.'
               ELSE ' arrived as described, and the overall shopping experience was smooth.'
           END
       END),
       'approved', UTC_TIMESTAMP() - INTERVAL sample.days_ago DAY
FROM products p
CROSS JOIN (
    SELECT 'sample-reviewer-one@example.test' AS reviewer_email, 1 AS star_rating, 24 AS days_ago
    UNION ALL
    SELECT 'sample-reviewer-two@example.test', 3, 17
    UNION ALL
    SELECT 'sample-reviewer-three@example.test', 5, 9
) AS sample
JOIN users u ON u.email = sample.reviewer_email
WHERE NOT EXISTS (
    SELECT 1 FROM reviews existing
    WHERE existing.product_id = p.id AND existing.user_id = u.id
);

-- Refresh sample reviews in case this migration was already run with earlier demo text.
UPDATE reviews r
JOIN users u ON u.id = r.user_id
JOIN products p ON p.id = r.product_id
SET r.star_rating = CASE u.email
        WHEN 'sample-reviewer-one@example.test' THEN 1
        WHEN 'sample-reviewer-two@example.test' THEN 3
        WHEN 'sample-reviewer-three@example.test' THEN 5
    END,
    r.comment = CONCAT('[Sample review] ', p.name, CASE u.email
        WHEN 'sample-reviewer-one@example.test' THEN CASE MOD(p.id, 4)
            WHEN 0 THEN ' arrived later than expected and the packaging was damaged.'
            WHEN 1 THEN ' was difficult to open, and delivery took longer than expected.'
            WHEN 2 THEN ' did not match what I expected from the listing.'
            ELSE ' arrived in a damaged box and was disappointing.'
        END
        WHEN 'sample-reviewer-two@example.test' THEN CASE MOD(p.id, 4)
            WHEN 0 THEN ' was acceptable overall, but the product details could be clearer.'
            WHEN 1 THEN ' is okay, though delivery and packaging were average.'
            WHEN 2 THEN ' met basic expectations, but I am unsure I would order it again.'
            ELSE ' was neither great nor poor; the product listing could include more details.'
        END
        WHEN 'sample-reviewer-three@example.test' THEN CASE MOD(p.id, 4)
            WHEN 0 THEN ' arrived in secure packaging and matched the listing.'
            WHEN 1 THEN ' was easy to order, and the package arrived in good condition.'
            WHEN 2 THEN ' was a positive purchase, with clear product details and prompt delivery.'
            ELSE ' arrived as described, and the overall shopping experience was smooth.'
        END
    END)
WHERE u.email IN (
    'sample-reviewer-one@example.test',
    'sample-reviewer-two@example.test',
    'sample-reviewer-three@example.test'
);
