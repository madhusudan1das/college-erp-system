<?php

$db_host = '127.0.0.1';
$db_user = 'root';
$db_pass = '';
$db_name = 'college_erp';

try {
    $dbh = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h3>Updating database with new modules...</h3>\n";

    // 1. Study Materials
    $dbh->exec("CREATE TABLE IF NOT EXISTS study_materials (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        file_path VARCHAR(255) NOT NULL,
        subject_id INT NOT NULL,
        faculty_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
        FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE CASCADE
    )");
    echo "1. Table 'study_materials' created.<br>\n";

    // 2. Leave Applications
    $dbh->exec("CREATE TABLE IF NOT EXISTS leave_applications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        reason TEXT NOT NULL,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        comments TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    echo "2. Table 'leave_applications' created.<br>\n";

    // 3. Complaints
    $dbh->exec("CREATE TABLE IF NOT EXISTS complaints (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        status ENUM('pending', 'resolved') DEFAULT 'pending',
        resolution TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    echo "3. Table 'complaints' created.<br>\n";

    // 4. Messages (Chat)
    $dbh->exec("CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sender_id INT NOT NULL,
        receiver_id INT NOT NULL,
        message_text TEXT NOT NULL,
        is_read BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    echo "4. Table 'messages' created.<br>\n";

    // 5. Forum Topics
    $dbh->exec("CREATE TABLE IF NOT EXISTS forum_topics (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        content TEXT NOT NULL,
        user_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    echo "5. Table 'forum_topics' created.<br>\n";

    // 6. Forum Replies
    $dbh->exec("CREATE TABLE IF NOT EXISTS forum_replies (
        id INT AUTO_INCREMENT PRIMARY KEY,
        topic_id INT NOT NULL,
        reply_text TEXT NOT NULL,
        user_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (topic_id) REFERENCES forum_topics(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    echo "6. Table 'forum_replies' created.<br>\n";

    // 7. Fees Ledger
    $dbh->exec("CREATE TABLE IF NOT EXISTS fees (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        status ENUM('paid', 'unpaid') DEFAULT 'unpaid',
        paid_at DATETIME NULL,
        transaction_no VARCHAR(100) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
    )");
    echo "7. Table 'fees' created.<br>\n";

    // 8. Scholarships
    $dbh->exec("CREATE TABLE IF NOT EXISTS scholarships (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        amount DECIMAL(10,2) NOT NULL,
        student_id INT NOT NULL,
        status ENUM('applied', 'approved', 'rejected') DEFAULT 'applied',
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
    )");
    echo "8. Table 'scholarships' created.<br>\n";

    // 9. Salaries (Faculty Payroll)
    $dbh->exec("CREATE TABLE IF NOT EXISTS salaries (
        id INT AUTO_INCREMENT PRIMARY KEY,
        faculty_id INT NOT NULL,
        base_salary DECIMAL(10,2) NOT NULL,
        bonuses DECIMAL(10,2) DEFAULT 0.00,
        deductions DECIMAL(10,2) DEFAULT 0.00,
        pay_date DATE NOT NULL,
        status ENUM('paid', 'pending') DEFAULT 'pending',
        FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE CASCADE
    )");
    echo "9. Table 'salaries' created.<br>\n";

    // 10. Hostels
    $dbh->exec("CREATE TABLE IF NOT EXISTS hostels (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        type ENUM('boys', 'girls') NOT NULL,
        capacity INT NOT NULL,
        address TEXT
    )");
    echo "10. Table 'hostels' created.<br>\n";

    // 11. Hostel Allotments
    $dbh->exec("CREATE TABLE IF NOT EXISTS hostel_allotments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        hostel_id INT NOT NULL,
        room_no VARCHAR(50) NOT NULL,
        student_id INT NOT NULL,
        alloted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (hostel_id) REFERENCES hostels(id) ON DELETE CASCADE,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
    )");
    echo "11. Table 'hostel_allotments' created.<br>\n";

    // 12. Transports (Routes/Buses)
    $dbh->exec("CREATE TABLE IF NOT EXISTS transports (
        id INT AUTO_INCREMENT PRIMARY KEY,
        route_name VARCHAR(255) NOT NULL,
        vehicle_no VARCHAR(50) NOT NULL,
        driver_name VARCHAR(100) NOT NULL,
        phone VARCHAR(20)
    )");
    echo "12. Table 'transports' created.<br>\n";

    // 13. Transport Allotments
    $dbh->exec("CREATE TABLE IF NOT EXISTS transport_allotments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        transport_id INT NOT NULL,
        student_id INT NOT NULL,
        alloted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (transport_id) REFERENCES transports(id) ON DELETE CASCADE,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
    )");
    echo "13. Table 'transport_allotments' created.<br>\n";

    // 14. Library Books
    $dbh->exec("CREATE TABLE IF NOT EXISTS books (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        author VARCHAR(255) NOT NULL,
        isbn VARCHAR(50) NOT NULL,
        category VARCHAR(100) NOT NULL,
        quantity INT NOT NULL,
        available_quantity INT NOT NULL
    )");
    echo "14. Table 'books' created.<br>\n";

    // 15. Book Issues Ledger
    $dbh->exec("CREATE TABLE IF NOT EXISTS book_issues (
        id INT AUTO_INCREMENT PRIMARY KEY,
        book_id INT NOT NULL,
        user_id INT NOT NULL,
        issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        return_due_date DATE NOT NULL,
        returned_at TIMESTAMP NULL,
        fine_amount DECIMAL(10,2) DEFAULT 0.00,
        FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    echo "15. Table 'book_issues' created.<br>\n";

    // 16. Research Publications
    $dbh->exec("CREATE TABLE IF NOT EXISTS research_publications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        faculty_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        journal_name VARCHAR(255) NOT NULL,
        publication_date DATE NOT NULL,
        description TEXT,
        file_path VARCHAR(255) NULL,
        FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE CASCADE
    )");
    echo "16. Table 'research_publications' created.<br>\n";

    // 17. Mentoring Records
    $dbh->exec("CREATE TABLE IF NOT EXISTS mentoring_records (
        id INT AUTO_INCREMENT PRIMARY KEY,
        faculty_id INT NOT NULL,
        student_id INT NOT NULL,
        meeting_date DATE NOT NULL,
        notes TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE CASCADE,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
    )");
    echo "17. Table 'mentoring_records' created.<br>\n";

    // Seed some mock infrastructure details
    $dbh->exec("INSERT IGNORE INTO hostels (id, name, type, capacity, address) VALUES
        (1, 'Newton Boys Hostel', 'boys', 100, 'Campus Area East'),
        (2, 'Curie Girls Hostel', 'girls', 80, 'Campus Area West')");
        
    $dbh->exec("INSERT IGNORE INTO transports (id, route_name, vehicle_no, driver_name, phone) VALUES
        (1, 'Route 1 - City Center to Campus', 'DL-1CA-1234', 'John Doe', '9876543210'),
        (2, 'Route 2 - Sector 15 to Campus', 'DL-2CB-5678', 'Bob Smith', '9876543211')");

    $dbh->exec("INSERT IGNORE INTO books (id, title, author, isbn, category, quantity, available_quantity) VALUES
        (1, 'Introduction to Algorithms', 'Thomas H. Cormen', '978-0262033848', 'Computer Science', 10, 10),
        (2, 'Database System Concepts', 'Abraham Silberschatz', '978-0073523323', 'Database', 5, 5),
        (3, 'Clean Code', 'Robert C. Martin', '978-0132350884', 'Software Engineering', 8, 8)");

    echo "<h4 style='color: green;'>Database tables created and seeded successfully!</h4>\n";

} catch (PDOException $e) {
    exit("Error updating database: " . $e->getMessage());
}
