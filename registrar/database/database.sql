-- Create Database
CREATE DATABASE IF NOT EXISTS registrar_db;
USE registrar_db;

-- 1. Personal Info Table
CREATE TABLE IF NOT EXISTS personal_info (
    student_id VARCHAR(20) PRIMARY KEY,
    student_name VARCHAR(100) NOT NULL,
    gender VARCHAR(10),
    dob DATE,
    address TEXT,
    program VARCHAR(50),
    section VARCHAR(10),
    grade_level VARCHAR(20),
    school_year VARCHAR(20),
    status VARCHAR(20) DEFAULT 'Active'
);

-- 1.1 Student Status History Table
CREATE TABLE IF NOT EXISTS student_status_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20),
    status VARCHAR(20),
    school_year VARCHAR(20),
    grade_level VARCHAR(20),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES personal_info(student_id) ON DELETE CASCADE
);

-- 2. Guardian & Emergency Contact Table
CREATE TABLE IF NOT EXISTS student_contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20),
    guardian_name VARCHAR(100),
    relationship VARCHAR(50),
    contact_number VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    FOREIGN KEY (student_id) REFERENCES personal_info(student_id) ON DELETE CASCADE
);

-- 3. Academic History Table
CREATE TABLE IF NOT EXISTS academic_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20),
    prev_school VARCHAR(100),
    year_completed YEAR,
    gpa DECIMAL(4,2),
    remarks TEXT,
    FOREIGN KEY (student_id) REFERENCES personal_info(student_id) ON DELETE CASCADE
);

-- 4. Health Records Table
CREATE TABLE IF NOT EXISTS health_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20),
    blood_type VARCHAR(5),
    allergies TEXT,
    medical_conditions TEXT,
    emergency_med TEXT,
    FOREIGN KEY (student_id) REFERENCES personal_info(student_id) ON DELETE CASCADE
);

-- 5. RFID Logs Table
CREATE TABLE IF NOT EXISTS rfid_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    rfid_uid VARCHAR(50),
    student_id VARCHAR(20),
    name VARCHAR(100),
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    type VARCHAR(20),
    FOREIGN KEY (student_id) REFERENCES personal_info(student_id) ON DELETE CASCADE
);

-- 6. ID Generation Requests Table
CREATE TABLE IF NOT EXISTS id_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20),
    name VARCHAR(100),
    request_date DATE,
    status VARCHAR(20),
    FOREIGN KEY (student_id) REFERENCES personal_info(student_id) ON DELETE CASCADE
);

-- 7. Document Requests Table
CREATE TABLE IF NOT EXISTS document_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20),
    document_type VARCHAR(50),
    purpose TEXT,
    request_date DATE,
    status VARCHAR(20),
    FOREIGN KEY (student_id) REFERENCES personal_info(student_id) ON DELETE CASCADE
);

-- 8. Status Tracker Table
CREATE TABLE IF NOT EXISTS status_tracker (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20),
    academic_status VARCHAR(50),
    enrollment_status VARCHAR(50),
    clearance VARCHAR(50),
    FOREIGN KEY (student_id) REFERENCES personal_info(student_id) ON DELETE CASCADE
);

-- 9. Digital File Storage Table
CREATE TABLE IF NOT EXISTS digital_storage (
    file_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20),
    filename VARCHAR(255),
    file_type VARCHAR(50),
    upload_date DATE,
    size VARCHAR(20),
    FOREIGN KEY (student_id) REFERENCES personal_info(student_id) ON DELETE CASCADE
);

-- INSERT SAMPLE DATA --

-- 1. Personal Info
INSERT INTO personal_info (student_id, student_name, gender, dob, address, program, section, grade_level, school_year, status) VALUES
('2023-0001', 'Doe, John', 'Male', '2005-04-12', '123 Main St', 'Grade 11 STEM', 'A', '11', '2023-2024', 'Active'),
('2023-0002', 'Smith, Jane', 'Female', '2006-08-22', '456 Oak Avenue', 'Grade 11 ABM', 'B', '11', '2023-2024', 'Active'),
('2023-0003', 'Wilson, Mark', 'Male', '2004-11-05', '789 Pine Road', 'Grade 12 HUMSS', 'A', '12', '2022-2023', 'Inactive');

-- 2. Contacts
INSERT INTO student_contacts (student_id, guardian_name, relationship, contact_number, email, address) VALUES
('2023-0001', 'Michael Doe', 'Father', '0917-123-4567', 'michael.doe@email.com', '123 Main St, Springfield'),
('2023-0002', 'Sarah Smith', 'Mother', '0918-987-6543', 'sarah.smith@email.com', '456 Oak Avenue, Springfield'),
('2023-0003', 'Robert Wilson', 'Uncle', '0919-555-8888', 'robert.wilson@email.com', '789 Pine Road, Springfield');

-- 3. Academic History
INSERT INTO academic_history (student_id, prev_school, year_completed, gpa, remarks) VALUES
('2023-0001', 'Lincoln High School', 2022, 88.5, 'Good Moral Character'),
('2023-0002', 'Washington Academy', 2022, 92.0, 'With Honors'),
('2023-0003', 'Roosevelt High', 2021, 84.0, 'Transferee');

-- 4. Health Records
INSERT INTO health_records (student_id, blood_type, allergies, medical_conditions, emergency_med) VALUES
('2023-0001', 'O+', 'Peanuts', 'Asthma', 'Inhaler'),
('2023-0002', 'A-', 'None', 'None', 'None'),
('2023-0003', 'B+', 'Dust', 'Migraines', 'Painkillers');

-- 5. RFID Logs
INSERT INTO rfid_logs (rfid_uid, student_id, name, timestamp, type) VALUES
('5A:4B:3C:2D', '2023-0001', 'John Doe', '2026-03-08 07:30:15', 'Time In'),
('9F:8E:7D:6C', '2023-0002', 'Jane Smith', '2026-03-08 07:35:42', 'Time In'),
('11:22:33:44', '2023-0003', 'Mark Wilson', '2026-03-08 08:05:10', 'Late In');

-- 6. ID Generation Requests
INSERT INTO id_requests (student_id, name, request_date, status) VALUES
('2023-0001', 'John Doe', '2026-03-06', 'Printed'),
('2023-0002', 'Jane Smith', '2026-03-07', 'Pending'),
('2023-0003', 'Mark Wilson', '2026-03-08', 'Processing');

-- 7. Document Requests
INSERT INTO document_requests (student_id, document_type, purpose, request_date, status) VALUES
('2023-0001', 'Form 137', 'Transfer', '2026-03-05', 'Ready for Pickup'),
('2023-0002', 'Good Moral', 'Scholarship', '2026-03-08', 'Pending'),
('2023-0001', 'Transcript of Records', 'Employment', '2026-03-07', 'Processing');

-- 8. Status Tracker
INSERT INTO status_tracker (student_id, academic_status, enrollment_status, clearance) VALUES
('2023-0001', 'Regular', 'Enrolled', 'Cleared'),
('2023-0002', 'Regular', 'Enrolled', 'Cleared'),
('2023-0003', 'Irregular', 'Dropped', 'Pending');

-- 9. Digital File Storage
INSERT INTO digital_storage (student_id, filename, file_type, upload_date, size) VALUES
('2023-0001', 'birth_certificate_john.pdf', 'Birth Certificate', '2023-08-15', '1.2MB'),
('2023-0002', 'form138_jane.jpg', 'Form 138', '2023-08-16', '850KB'),
('2023-0003', 'medical_clearance_mark.pdf', 'Medical Clearance', '2023-08-20', '2.4MB');
