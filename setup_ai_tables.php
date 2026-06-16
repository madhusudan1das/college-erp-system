<?php

$db_host = '127.0.0.1';
$db_user = 'root';
$db_pass = '';
$db_name = 'college_erp';

try {
    $dbh = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h3>Setting up AI Academic Management tables...</h3>\n";

    // 1. Answer Sheet Uploads
    $dbh->exec("CREATE TABLE IF NOT EXISTS answer_sheet_uploads (
        id INT AUTO_INCREMENT PRIMARY KEY,
        file_path VARCHAR(500) NOT NULL,
        original_filename VARCHAR(255) NOT NULL,
        mime_type VARCHAR(100) NOT NULL,
        uploaded_by INT NOT NULL,
        detected_subject VARCHAR(255) NULL,
        detected_subject_code VARCHAR(50) NULL,
        detected_department VARCHAR(255) NULL,
        detected_exam_type VARCHAR(100) NULL,
        detected_semester INT NULL,
        detected_student_info TEXT NULL,
        detected_date VARCHAR(100) NULL,
        subject_id INT NULL,
        status ENUM('pending','processing','processed','assigned','failed','review_needed') DEFAULT 'pending',
        ai_confidence_score DECIMAL(5,2) NULL,
        ai_raw_response TEXT NULL,
        ai_source VARCHAR(20) DEFAULT 'gemini',
        error_message TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE SET NULL
    )");
    echo "1. Table 'answer_sheet_uploads' created.<br>\n";

    // 2. Evaluator Assignments
    $dbh->exec("CREATE TABLE IF NOT EXISTS evaluator_assignments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        answer_sheet_id INT NOT NULL,
        evaluator_faculty_id INT NOT NULL,
        assigned_by_ai BOOLEAN DEFAULT TRUE,
        assignment_reason TEXT NULL,
        assignment_score DECIMAL(5,2) NULL,
        status ENUM('assigned','in_progress','completed','rejected') DEFAULT 'assigned',
        assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        completed_at TIMESTAMP NULL,
        FOREIGN KEY (answer_sheet_id) REFERENCES answer_sheet_uploads(id) ON DELETE CASCADE,
        FOREIGN KEY (evaluator_faculty_id) REFERENCES faculty(id) ON DELETE CASCADE,
        UNIQUE KEY unique_sheet_evaluator (answer_sheet_id, evaluator_faculty_id)
    )");
    echo "2. Table 'evaluator_assignments' created.<br>\n";

    // 3. Timetable Uploads
    $dbh->exec("CREATE TABLE IF NOT EXISTS timetable_uploads (
        id INT AUTO_INCREMENT PRIMARY KEY,
        file_path VARCHAR(500) NOT NULL,
        original_filename VARCHAR(255) NOT NULL,
        mime_type VARCHAR(100) NOT NULL,
        uploaded_by INT NOT NULL,
        status ENUM('pending','processing','processed','failed','partial') DEFAULT 'pending',
        slots_created INT DEFAULT 0,
        slots_skipped INT DEFAULT 0,
        conflicts_found INT DEFAULT 0,
        unmatched_entries INT DEFAULT 0,
        ai_raw_response TEXT NULL,
        ai_source VARCHAR(20) DEFAULT 'gemini',
        processing_summary TEXT NULL,
        error_message TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE
    )");
    echo "3. Table 'timetable_uploads' created.<br>\n";

    // 4. AI Audit Logs
    $dbh->exec("CREATE TABLE IF NOT EXISTS ai_audit_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        action_type ENUM('answer_sheet_process','timetable_process','evaluator_assign','timetable_allocate','notification_send') NOT NULL,
        user_id INT NULL,
        target_table VARCHAR(100) NULL,
        target_id INT NULL,
        details TEXT NULL,
        status ENUM('success','failure','partial','skipped') DEFAULT 'success',
        error_message TEXT NULL,
        ip_address VARCHAR(45) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )");
    echo "4. Table 'ai_audit_logs' created.<br>\n";

    // 5. Notifications
    $dbh->exec("CREATE TABLE IF NOT EXISTS notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        type ENUM('info','success','warning','error') DEFAULT 'info',
        icon VARCHAR(50) DEFAULT 'fas fa-bell',
        is_read BOOLEAN DEFAULT FALSE,
        link VARCHAR(500) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    echo "5. Table 'notifications' created.<br>\n";

    // 6. ALTER timetable to add room column (if not exists)
    $columns = $dbh->query("SHOW COLUMNS FROM timetable LIKE 'room'")->fetchAll();
    if (empty($columns)) {
        $dbh->exec("ALTER TABLE timetable ADD COLUMN room VARCHAR(50) NULL AFTER end_time");
        echo "6. Added 'room' column to 'timetable' table.<br>\n";
    } else {
        echo "6. Column 'room' already exists in 'timetable' table. Skipped.<br>\n";
    }

    echo "<h4 style='color: green;'>AI Academic Management tables setup completed successfully!</h4>\n";

} catch (PDOException $e) {
    exit("<h4 style='color: red;'>Error setting up AI tables: " . $e->getMessage() . "</h4>");
}
