
USE library_database;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;


INSERT INTO author (author_id, author_name, country) VALUES
(1, 'Chinua Achebe', 'Nigeria'),
(2, 'Chimamanda Ngozi Adichie', 'Nigeria'),
(3, 'Ngugi wa Thiong''o', 'Kenya'),
(4, 'Okot p''Bitek', 'Uganda'),
(5, 'Robert C. Martin', 'United States'),
(6, 'Andrew S. Tanenbaum', 'Netherlands'),
(7, 'Stephen Hawking', 'United Kingdom'),
(8, 'Yuval Noah Harari', 'Israel');

INSERT INTO categories (category_id, cat_name, description) VALUES
(1, 'Fiction', 'Imaginary stories, novels and poetry'),
(2, 'Education', 'Textbooks for learning and studying'),
(3, 'Technology', 'Computing, programming and engineering'),
(4, 'Science', 'Scientific knowledge and discoveries'),
(5, 'History', 'Historical events and accounts');

INSERT INTO books
(isbn, title, author_id, category_id, publisher, year_published, total_copies, available_copies)
VALUES
('9780385474542', 'Things Fall Apart', 1, 1, 'Heinemann', 1958, 5, 4),
('9780007200283', 'Half of a Yellow Sun', 2, 1, 'Fourth Estate', 2006, 4, 3),
('9789966466100', 'The River Between', 3, 1, 'Heinemann', 1965, 5, 4),
('9789966250001', 'Song of Lawino', 4, 1, 'East African Publishing House', 1966, 3, 3),
('9780132350884', 'Clean Code', 5, 3, 'Prentice Hall', 2008, 5, 4),
('9780133591620', 'Modern Operating Systems', 6, 3, 'Pearson', 2014, 4, 4),
('9780132126953', 'Computer Networks', 6, 2, 'Pearson', 2010, 4, 4),
('9780553380163', 'A Brief History of Time', 7, 4, 'Bantam Books', 1988, 5, 4),
('9780062316110', 'Sapiens', 8, 5, 'Harper', 2015, 4, 3),
('9780385474559', 'No Longer at Ease', 1, 1, 'Heinemann', 1960, 3, 3);

INSERT INTO members (member_id, member_number, full_name, email, phone, status, date_joined) VALUES
(1, 'MEM001', 'Aisha Nabirye', 'aisha@gmail.com', '0701000001', 'active', '2003-06-02'),
(2, 'MEM002', 'Brian Mukasa', 'brian@gmail.com', '0701000002', 'active', '2020-05-05'),
(3, 'MEM003', 'Catherine Auma', 'catherine@gmail.com', '0701000003', 'active', '2004-07-06'),
(4, 'MEM004', 'Daniel Ssekandi', 'daniel@gmail.com', '0701000004', 'active', '2008-06-28'),
(5, 'MEM005', 'Faith Nankya', 'faith@gmail.com', '0701000005', 'active', '2023-01-05'),
(6, 'MEM006', 'Isaac Opio', 'isaac@gmail.com', '0701000006', 'active', '2025-12-26'),
(7, 'MEM007', 'Joan Tumwebaze', 'joan@gmail.com', '0701000007', 'active', '2009-08-29'),
(8, 'MEM008', 'Kevin Byaruhanga', 'kevin@gmail.com', '0701000008', 'active', '2015-09-03'),
(9, 'MEM009', 'Lydia Namatovu', 'lydia@gmail.com', '0701000009', 'suspended', '2011-03-04'),
(10, 'MEM010', 'Moses Lubega', 'moses@gmail.com', '0701000010', 'active', '2014-04-12');

INSERT INTO loans (members_id, book_id, date_issued, due_date, date_returned) VALUES
(1, 1, '2026-10-12', '2026-10-26', NULL),
(2, 3, '2026-10-20', '2026-11-03', NULL),
(3, 5, '2026-10-28', '2026-11-11', NULL),
(4, 8, '2026-11-02', '2026-11-16', NULL),
(5, 2, '2026-11-04', '2026-11-18', NULL),
(6, 9, '2026-10-30', '2026-11-13', NULL),
(7, 4, '2026-09-02', '2026-09-16', '2026-09-14'),
(8, 6, '2026-09-10', '2026-09-24', '2026-09-26'),
(1, 7, '2026-09-15', '2026-09-29', '2026-09-28'),
(9, 10, '2026-08-20', '2026-09-03', '2026-09-01'),
(10, 1, '2026-10-01', '2026-10-15', '2026-10-13'),
(3, 8, '2026-10-05', '2026-10-19', '2026-10-17');

SET FOREIGN_KEY_CHECKS = 1;