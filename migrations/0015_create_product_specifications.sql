-- Migration: Create product_specifications table
-- Created: 2026-04-28

CREATE TABLE IF NOT EXISTS product_specifications (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  product_id INTEGER NOT NULL,
  key TEXT NOT NULL,
  key_en TEXT,
  key_az TEXT,
  value TEXT NOT NULL,
  value_en TEXT,
  value_az TEXT,
  order_index INTEGER DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_product_specifications_product ON product_specifications(product_id);
CREATE INDEX IF NOT EXISTS idx_product_specifications_order ON product_specifications(order_index);
