-- Migration: Create portfolio table
CREATE TABLE IF NOT EXISTS portfolio (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  title TEXT NOT NULL,
  title_en TEXT,
  title_az TEXT,
  year TEXT NOT NULL,
  image_url TEXT NOT NULL,
  description TEXT,
  description_en TEXT,
  description_az TEXT,
  order_index INTEGER DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_portfolio_order ON portfolio(order_index);
