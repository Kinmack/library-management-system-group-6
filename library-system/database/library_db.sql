
CREATE DATABASE IF NOT EXISTS library_database
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE library_database;

CREATE TABLE IF NOT EXISTS author (
    author_id INT AUTO_INCREMENT PRIMARY KEY,
    author_name VARCHAR(100) NOT NULL,
    country VARCHAR(100) NOT NULL DEFAULT 'Unknown'
);

CREATE TABLE IF NOT EXISTS categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    cat_name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
);

CREATE TABLE IF NOT EXISTS books (
    book_id INT AUTO_INCREMENT PRIMARY KEY,
    isbn VARCHAR(20) NOT NULL UNIQUE,
    title VARCHAR(200) NOT NULL,
    author_id INT NOT NULL,
    category_id INT NOT NULL,
    publisher VARCHAR(150),
    year_published YEAR,
    total_copies INT NOT NULL DEFAULT 1 CHECK (total_copies >= 1),
    available_copies INT NOT NULL DEFAULT 1 CHECK (available_copies >= 0),
    CONSTRAINT fk_books_author FOREIGN KEY (author_id)
        REFERENCES author(author_id) ON DELETE RESTRICT,
    CONSTRAINT fk_books_category FOREIGN KEY (category_id)
        REFERENCES categories(category_id) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS members (
    member_id INT AUTO_INCREMENT PRIMARY KEY,
    member_number VARCHAR(30) NOT NULL UNIQUE,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(30),
    status ENUM('active', 'suspended') NOT NULL DEFAULT 'active',
    date_joined DATE NOT NULL DEFAULT (CURRENT_DATE)
);

CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    role ENUM('admin', 'librarian') NOT NULL DEFAULT 'librarian'
);
CREATE TABLE IF NOT EXISTS loans (
    loan_id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    book_id INT NOT NULL,
    date_issued DATE NOT NULL DEFAULT (CURRENT_DATE),
    due_date DATE NOT NULL DEFAULT (DATE_ADD(CURRENT_DATE, INTERVAL 14 DAY)),
    date_returned DATE DEFAULT NULL,
    -- 1 while the loan is unreturned, NULL once returned (used to block duplicate active loans)
    active_flag TINYINT AS (IF(date_returned IS NULL, 1, NULL)) VIRTUAL,

    CONSTRAINT fk_loans_member FOREIGN KEY (member_id)
        REFERENCES members(member_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_loans_book FOREIGN KEY (book_id)
        REFERENCES books(book_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT chk_loans_due CHECK (due_date >= date_issued),
    CONSTRAINT chk_loans_returned CHECK (date_returned IS NULL OR date_returned >= date_issued),

    CONSTRAINT uq_loans_active UNIQUE (member_id, book_id, active_flag),
    INDEX idx_loans_due_returned (due_date, date_returned)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
