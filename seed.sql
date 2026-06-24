-- ============================================================
-- ConsumerConnect RSA 
-- Run AFTER schema.sql
-- ============================================================

USE consumerconnect_rsa;

-- ─────────────────────────────────────────
-- DEMO USERS (password for all: Demo@1234)
-- Hash: $2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
-- ─────────────────────────────────────────
INSERT INTO users (role_id, first_name, last_name, email, phone, password_hash, province, city, status, verified_at) VALUES
  (2, 'Admin',   'User',     'admin2@consumerconnect.co.za',  '+27110001111', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Gauteng',       'Johannesburg', 'active', NOW()),
  (3, 'Moddie',  'Mokoena',  'mod@consumerconnect.co.za',     '+27110002222', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Western Cape',  'Cape Town',    'active', NOW()),
  (4, 'Sipho',   'Dlamini',  'sipho@example.co.za',           '+27721234567', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'KwaZulu-Natal', 'Durban',       'active', NOW()),
  (4, 'Thandi',  'Nkosi',    'thandi@example.co.za',          '+27731234567', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Gauteng',       'Pretoria',     'active', NOW()),
  (5, 'Lebo',    'Sithole',  'lebo@example.co.za',            '+27741234567', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Western Cape',  'Cape Town',    'active', NOW()),
  (5, 'Andile',  'Mthembu',  'andile@example.co.za',          '+27751234567', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Gauteng',       'Sandton',      'active', NOW()),
  (4, 'Priya',   'Pillay',   'priya@example.co.za',           '+27761234567', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'KwaZulu-Natal', 'Durban',       'active', NOW()),
  (5, 'Zanele',  'Moyo',     'zanele@example.co.za',          '+27771234567', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Limpopo',       'Polokwane',    'active', NOW());

-- ─────────────────────────────────────────
-- PRODUCTS  (seller_id references users above)
-- Sellers: Sipho=3, Thandi=4, Priya=7
-- ─────────────────────────────────────────
INSERT INTO products (seller_id, category_id, title, slug, description, price, stock, condition_type, province, city, status) VALUES
  (3, 1, 'Apple iPhone 14 Pro 256GB Space Black',          'apple-iphone-14-pro-256gb',     'Excellent condition, 9 months old. Includes original box, charger and earphones. No scratches on screen.', 14999.00, 1,  'used',        'KwaZulu-Natal','Durban',       'active'),
  (3, 1, 'MacBook Air M2 8GB 256GB Silver',                'macbook-air-m2-8gb',            'Barely used MacBook Air M2 chip. Battery health 98%. Perfect for students and professionals.',             18500.00, 1,  'used',        'KwaZulu-Natal','Durban',       'active'),
  (4, 1, 'Samsung 65" QLED 4K Smart TV 2023',              'samsung-65-qled-4k',            'Brand new sealed in box. Full manufacturer warranty. Can deliver within Gauteng at extra cost.',           12999.00, 3,  'new',         'Gauteng',      'Pretoria',     'active'),
  (4, 1, 'Sony WH-1000XM5 Wireless Headphones Black',      'sony-wh1000xm5-black',          'Sealed box. Industry-leading noise cancellation. 30 hours battery life. Perfect holiday gift.',             4299.00, 5,  'new',         'Gauteng',      'Pretoria',     'active'),
  (7, 2, 'Nike Air Max 270 React Mens Size 10',            'nike-air-max-270-size-10',      'Worn twice, like new. White/Black colourway. Original box included. Bought for R2800, selling R1400.',      1400.00, 1,  'used',        'KwaZulu-Natal','Durban',       'active'),
  (7, 2, 'Formal Maxi Dress Navy Blue Size 12',            'formal-maxi-dress-navy-s12',    'Beautiful evening dress, worn once to a wedding. Dry cleaned and in perfect condition. Zara original.',      650.00, 1,  'used',        'KwaZulu-Natal','Durban',       'active'),
  (3, 3, '3-Seater Leather Sofa Dark Brown',               '3-seater-leather-sofa',         'Genuine leather sofa in very good condition. Minor wear on armrests. Selling due to moving house.',         4800.00, 1,  'used',        'KwaZulu-Natal','Pietermaritzburg','active'),
  (4, 4, 'Volkswagen Golf 7 1.4 TSI Comfortline 2017',     'vw-golf-7-2017',                '62,000km. Full service history at VW dealer. One owner. White. No accidents. Roadworthy certificate included.',189900.00,1, 'used',      'Gauteng',      'Johannesburg', 'active'),
  (7, 1, 'Canon EOS 750D DSLR Camera + 18-55mm Lens',      'canon-eos-750d-kit',            'Great beginner/intermediate DSLR. 7500 shutter count (very low). Kit lens included. Bag and extra battery.', 5500.00, 1,  'used',        'KwaZulu-Natal','Durban',       'active'),
  (4, 6, 'Trek Marlin 5 Mountain Bike Large Frame 2022',   'trek-marlin-5-large-2022',      'Used for 6 months on trail rides. Excellent condition. Hydraulic disc brakes. 29" wheels.',                 9200.00, 1,  'used',        'Gauteng',      'Centurion',    'active'),
  (3, 7, 'Rich Dad Poor Dad + Think and Grow Rich (2 Books)','rich-dad-think-grow-pack',    'Both books in great condition. Life-changing reads. Selling as a pair. Great for entrepreneurs.',              180.00, 4,  'used',        'KwaZulu-Natal','Durban',       'active'),
  (7, 2, 'Woolworths Puffer Jacket Womens Medium Charcoal', 'woolworths-puffer-jacket-m',   'Worn twice. Very warm. Original Woolworths tag still attached. Perfect for Cape Town winters.',               450.00, 1,  'used',        'KwaZulu-Natal','Durban',       'active');

-- ─────────────────────────────────────────
-- PRODUCT IMAGES  (using seed upload filenames)
-- ─────────────────────────────────────────
INSERT INTO product_images (product_id, filename, is_primary, sort_order) VALUES
  (1,  'seed-iphone.jpg',    1, 0),  -- Apple iPhone 14 Pro
  (2,  'seed-macbook.jpg',   1, 0),  -- MacBook Air M2
  (3,  'seed-samsung.jpg',   1, 0),  -- Samsung 65" QLED TV
  (4,  'seed-sony.jpg',      1, 0),  -- Sony WH-1000XM5 Headphones
  (5,  'seed-nikes.jpg',     1, 0),  -- Nike Air Max 270
  (6,  'seed-dress.jpg',     1, 0),  -- Formal Maxi Dress
  (7,  'seed-sofa.jpg',      1, 0),  -- 3-Seater Leather Sofa
  (8,  'seed-car.jpg',       1, 0),  -- Volkswagen Golf 7
  (9,  'seed-camera.jpg',    1, 0),  -- Canon EOS 750D
  (10, 'seed-bike.jpg',      1, 0),  -- Trek Marlin 5 Mountain Bike
  (11, 'seed-book.jpg',      1, 0),  -- Rich Dad Poor Dad + Think and Grow Rich
  (12, 'seed-jacket.jpg',    1, 0);  -- Woolworths Puffer Jacket

-- ─────────────────────────────────────────
-- DEMO ORDERS
-- ─────────────────────────────────────────
INSERT INTO orders (buyer_id, total_amount, status, payment_method, payment_ref, shipping_addr) VALUES
  (5, 14999.00, 'delivered', 'eft',        'EFT-20240101-001', '12 Main Road, Cape Town, 8001'),
  (6, 22799.00, 'paid',      'card',       'CARD-20240115-002','45 Jan Smuts Ave, Sandton, 2196'),
  (8, 4299.00,  'shipped',   'eft',        'EFT-20240120-003', '3 Church Street, Polokwane, 0700'),
  (5, 1400.00,  'pending',   'eft',        NULL,               '12 Main Road, Cape Town, 8001'),
  (6, 189900.00,'paid',      'eft',        'EFT-20240201-005', '45 Jan Smuts Ave, Sandton, 2196');

INSERT INTO order_items (order_id, product_id, seller_id, quantity, unit_price) VALUES
  (1, 1,  3, 1, 14999.00),
  (2, 2,  3, 1, 18500.00),
  (2, 4,  4, 1,  4299.00),
  (3, 4,  4, 1,  4299.00),
  (4, 5,  7, 1,  1400.00),
  (5, 8,  4, 1, 189900.00);

-- ─────────────────────────────────────────
-- REVIEWS
-- ─────────────────────────────────────────
INSERT INTO reviews (product_id, reviewer_id, rating, comment) VALUES
  (1, 5, 5, 'Exactly as described! Fast communication, smooth transaction. iPhone is in perfect condition.'),
  (2, 6, 5, 'Great seller, very honest about condition. MacBook works flawlessly.'),
  (4, 8, 4, 'Headphones arrived safely, great packaging. Sound quality is outstanding.'),
  (5, 5, 5, 'Barely worn as promised. Arrived quickly. Very happy!'),
  (7, 6, 4, 'Sofa is beautiful. Only minor wear as described. Great value for money.');