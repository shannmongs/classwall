-- Step 1: create the database first (run this while connected to "postgres"):
--   CREATE DATABASE classwall;
-- Then connect to classwall and run the statement below.

CREATE TABLE IF NOT EXISTS messages (
    id         SERIAL PRIMARY KEY,
    author     VARCHAR(100) NOT NULL,
    body       TEXT         NOT NULL,
    created_at TIMESTAMP    NOT NULL DEFAULT NOW()
);
