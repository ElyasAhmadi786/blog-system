-- Migration script for existing blog_db installations
-- Run this ONLY if you already have blog_db set up and want to add new features.

USE blog_db;

-- Add status column to posts (published/draft)
ALTER TABLE posts
    ADD COLUMN IF NOT EXISTS status ENUM('draft', 'published') NOT NULL DEFAULT 'published';

-- Add avatar column to users
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS avatar VARCHAR(255) DEFAULT NULL;
