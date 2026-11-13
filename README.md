# Class Wall

A small message board built with PHP and PostgreSQL. Visitors post messages, the site remembers their name (cookie) and counts their visits (session), and everyone sees the same wall.

## Files
- `schema.sql` - creates the `messages` table
- `db.php` - PDO connection to PostgreSQL
- `index.php` - form, search, cookie, session, and message display

## Setup
1. Create a database named `classwall` in pgAdmin (or `CREATE DATABASE classwall;` in psql).
2. Run `schema.sql` on the `classwall` database.
3. Edit the password in `db.php`.
4. Make sure the `pdo_pgsql` extension is enabled in your PHP.
   Check with: `php -m | grep pdo_pgsql`
5. Copy this folder into XAMPP's `htdocs` folder and open `http://localhost/classwall/`.

## Testing checklist
- Post a message with "Remember my name" ticked, reload, and the name is pre-filled.
- Post with the box unticked, and the name is no longer remembered.
- Search with `index.php?search=hello`.
- Open DevTools, then Application tab, then Cookies. Look for `author_name` and `PHPSESSID`.
- Open the site in two browsers: same messages, separate visit counters.
- Close the browser and reopen it: the counter starts again from 1.

## Answers

**1. What is the difference between a cookie and a session?**
A cookie is a small piece of data the website asks the browser to store, and the browser sends it back with every request to that site. It can last for days or months (like the remembered name). A session is data the server keeps for one visitor for a limited time, such as a visit counter. The browser only holds a session ID cookie (`PHPSESSID`) so the server can find the right data. The session ID cookie normally expires when the browser closes.

**2. Where is each one stored (browser or server)?**
Cookies are stored in the browser. Session data is stored on the server (only the session ID is in the browser).

**3. Why do we use prepared statements?**
Prepared statements send the SQL structure and the user's values separately, so the input is always treated as data, never as SQL code. This prevents SQL injection, where an attacker types SQL into a form field to read, change, or delete data.
