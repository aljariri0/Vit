ALTER TABLE reviews
    ADD COLUMN status ENUM('pending', 'approved', 'rejected')
    NOT NULL DEFAULT 'approved' AFTER comment;

ALTER TABLE payments
    ADD COLUMN gateway_metadata JSON NULL AFTER provider_transaction_id;

ALTER TABLE payments
    MODIFY method ENUM('card', 'visa', 'paypal', 'cash_on_delivery') NOT NULL;

UPDATE payments
SET method = 'card'
WHERE method = 'visa';

ALTER TABLE payments
    MODIFY method ENUM('card', 'paypal', 'cash_on_delivery') NOT NULL;
