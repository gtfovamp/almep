-- Migration: Create news table
CREATE TABLE IF NOT EXISTS news (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  title TEXT NOT NULL,
  title_en TEXT,
  title_az TEXT,
  cover_image_url TEXT NOT NULL,
  blocks TEXT NOT NULL,
  published_at DATETIME NOT NULL,
  order_index INTEGER DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_news_order ON news(order_index);
CREATE INDEX idx_news_published ON news(published_at);
