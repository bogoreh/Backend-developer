CREATE DATABASE library_db;
USE library_db;

CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    isbn VARCHAR(20) UNIQUE,
    publication_year INT,
    category VARCHAR(100),
    total_copies INT DEFAULT 1,
    available_copies INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    membership_date DATE,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT,
    member_id INT,
    borrow_date DATE,
    due_date DATE,
    return_date DATE NULL,
    status ENUM('borrowed', 'returned') DEFAULT 'borrowed',
    fine_amount DECIMAL(10,2) DEFAULT 0.00,
    FOREIGN KEY (book_id) REFERENCES books(id),
    FOREIGN KEY (member_id) REFERENCES members(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample data
INSERT INTO books (title, author, isbn, publication_year, category, total_copies, available_copies) VALUES
('The Great Gatsby', 'F. Scott Fitzgerald', '9780743273565', 1925, 'Fiction', 5, 5),
('To Kill a Mockingbird', 'Harper Lee', '9780061120084', 1960, 'Fiction', 3, 3),
('1984', 'George Orwell', '9780451524935', 1949, 'Science Fiction', 4, 4);

INSERT INTO members (name, email, phone, address, membership_date) VALUES
('John Smith', 'john@example.com', '123-456-7890', '123 Main St, City', '2023-01-15'),
('Sarah Johnson', 'sarah@example.com', '123-456-7891', '456 Oak Ave, Town', '2023-02-20');