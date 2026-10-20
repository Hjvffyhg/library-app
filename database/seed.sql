-- Demo data for development only. Import schema.sql first.
-- No admin password is seeded. We'll implement a safe admin-creation command with authentication.

INSERT INTO categories (name, description) VALUES
('Computer Science', 'Programming, software engineering, and computing'),
('Literature', 'Fiction, poetry, and literary works'),
('History', 'Local and world history'),
('Science', 'General science and research');

INSERT INTO books (category_id, title, author, isbn, publisher, publication_year, description) VALUES
(1, 'Clean Code', 'Robert C. Martin', '9780132350884', 'Prentice Hall', 2008, 'A practical book about writing readable and maintainable code.'),
(1, 'Eloquent JavaScript', 'Marijn Haverbeke', '9781593279509', 'No Starch Press', 2018, 'An introduction to JavaScript and programming concepts.'),
(2, 'Noli Me Tangere', 'Jose Rizal', NULL, 'Various editions', 1887, 'A classic Filipino novel. Replace edition details with the copy held by your library.'),
(4, 'A Brief History of Time', 'Stephen Hawking', '9780553380163', 'Bantam', 1998, 'An introduction to cosmology for general readers.');

INSERT INTO book_copies (book_id, barcode, status, acquired_on, notes) VALUES
(1, 'LIB-BOOK-0001', 'available', '2026-01-10', 'Demo copy 1'),
(1, 'LIB-BOOK-0002', 'available', '2026-01-10', 'Demo copy 2'),
(2, 'LIB-BOOK-0003', 'available', '2026-01-10', 'Demo copy 1'),
(3, 'LIB-BOOK-0004', 'available', '2026-01-10', 'Demo copy 1'),
(4, 'LIB-BOOK-0005', 'available', '2026-01-10', 'Demo copy 1');

INSERT INTO members (member_code, first_name, last_name, email, phone, address, joined_on, status) VALUES
('LIB-2026-0001', 'Alex', 'Reyes', 'alex.reyes@example.test', '09170000001', 'Demo address - replace with sample data', '2026-01-15', 'active'),
('LIB-2026-0002', 'Jamie', 'Santos', 'jamie.santos@example.test', '09170000002', 'Demo address - replace with sample data', '2026-02-01', 'active'),
('LIB-2026-0003', 'Taylor', 'Cruz', 'taylor.cruz@example.test', '09170000003', 'Demo address - replace with sample data', '2026-03-12', 'active');

INSERT INTO settings (setting_key, setting_value, description) VALUES
('loan_period_days', '14', 'Default loan period in calendar days.'),
('max_active_loans', '5', 'Maximum number of active loans allowed per member.'),
('max_renewals', '1', 'Maximum number of renewals per loan.'),
('fine_rate_per_day', '5.00', 'Fine charged per overdue calendar day, in local currency.'),
('fine_grace_days', '0', 'Number of fine-free calendar days after due date.'),
('reservation_hold_days', '2', 'Number of days a ready reservation is held after notification.');
