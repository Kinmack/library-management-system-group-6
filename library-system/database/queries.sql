USE library_database;

-- Catalog search: set this value to a title, author, or category fragment.
-- In the PHP application, bind the search term with a prepared statement.
SET @catalog_search = '';

SELECT
    b.book_id,
    b.isbn,
    b.title,
    a.author_name,
    c.cat_name AS category,
    b.publisher,
    b.year_published,
    b.total_copies,
    b.available_copies
FROM books AS b
JOIN author AS a ON a.author_id = b.author_id
JOIN categories AS c ON c.category_id = b.category_id
WHERE b.title LIKE CONCAT('%', @catalog_search, '%')
   OR b.isbn LIKE CONCAT('%', @catalog_search, '%')
   OR a.author_name LIKE CONCAT('%', @catalog_search, '%')
   OR c.cat_name LIKE CONCAT('%', @catalog_search, '%')
ORDER BY b.title;

-- Loan register with the member and book, including a derived loan status.
-- Status options: all, open, returned, or overdue. Open includes overdue loans.
SET @member_search = '';
SET @loan_status = 'all';

SELECT
    l.loan_id,
    b.title,
    b.isbn,
    m.full_name AS member_name,
    m.member_number,
    l.date_issued,
    l.due_date,
    l.date_returned,
    CASE
        WHEN l.date_returned IS NOT NULL THEN 'Returned'
        WHEN l.due_date < CURRENT_DATE THEN 'Overdue'
        ELSE 'On loan'
    END AS loan_status
FROM loans AS l
JOIN books AS b ON b.book_id = l.book_id
JOIN members AS m ON m.member_id = l.members_id
WHERE m.full_name LIKE CONCAT('%', @member_search, '%')
  AND (
      @loan_status = 'all'
      OR (@loan_status = 'open' AND l.date_returned IS NULL)
      OR (@loan_status = 'returned' AND l.date_returned IS NOT NULL)
      OR (@loan_status = 'overdue' AND l.date_returned IS NULL AND l.due_date < CURRENT_DATE)
  )
ORDER BY
    (l.date_returned IS NULL) DESC,
    l.due_date ASC,
    l.loan_id DESC;

-- Overdue loans that have not yet been returned.
SELECT
    l.loan_id,
    b.title,
    b.isbn,
    m.full_name AS member_name,
    m.member_number,
    m.email,
    l.date_issued,
    l.due_date,
    DATEDIFF(CURRENT_DATE, l.due_date) AS days_overdue
FROM loans AS l
JOIN books AS b ON b.book_id = l.book_id
JOIN members AS m ON m.member_id = l.members_id
WHERE l.date_returned IS NULL
  AND l.due_date < CURRENT_DATE
ORDER BY l.due_date, m.full_name;

-- Aggregate report: loan counts per category, including categories with no loans.
SELECT
    c.category_id,
    c.cat_name AS category,
    COUNT(l.loan_id) AS total_loans,
    SUM(CASE WHEN l.loan_id IS NOT NULL AND l.date_returned IS NULL THEN 1 ELSE 0 END)
        AS currently_on_loan
FROM categories AS c
LEFT JOIN books AS b ON b.category_id = c.category_id
LEFT JOIN loans AS l ON l.book_id = b.book_id
GROUP BY c.category_id, c.cat_name
ORDER BY total_loans DESC, c.cat_name;

-- Inventory summary by category, with a grand total row at the bottom.
SELECT category, titles, total_copies, available_copies, on_loan, pct_available
FROM (
    SELECT
        0 AS sort_order,
        c.cat_name AS category,
        COUNT(b.book_id) AS titles,
        COALESCE(SUM(b.total_copies), 0) AS total_copies,
        COALESCE(SUM(b.available_copies), 0) AS available_copies,
        COALESCE(SUM(b.total_copies - b.available_copies), 0) AS on_loan,
        COALESCE(ROUND(100 * SUM(b.available_copies) / NULLIF(SUM(b.total_copies), 0), 1), 0) AS pct_available
    FROM categories AS c
    LEFT JOIN books AS b ON b.category_id = c.category_id
    GROUP BY c.category_id, c.cat_name

    UNION ALL

    SELECT
        1,
        'All categories',
        COUNT(*),
        COALESCE(SUM(total_copies), 0),
        COALESCE(SUM(available_copies), 0),
        COALESCE(SUM(total_copies - available_copies), 0),
        COALESCE(ROUND(100 * SUM(available_copies) / NULLIF(SUM(total_copies), 0), 1), 0)
    FROM books
) AS summary
ORDER BY sort_order, category;
