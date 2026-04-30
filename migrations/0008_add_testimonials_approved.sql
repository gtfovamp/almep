-- Migration: Add approved field to testimonials
-- Created: 2026-04-28

ALTER TABLE testimonials ADD COLUMN approved INTEGER DEFAULT 0;

CREATE INDEX IF NOT EXISTS idx_testimonials_approved ON testimonials(approved);
