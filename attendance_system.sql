-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS attendance_system;
USE attendance_system;

-- Create Students table
CREATE TABLE IF NOT EXISTS Students (
    StudentID INT PRIMARY KEY AUTO_INCREMENT,
    StudentName VARCHAR(255) NOT NULL
);

-- Create Attendance table (normalized and constraints enforced)
CREATE TABLE IF NOT EXISTS Attendance (
    AttendanceID INT PRIMARY KEY AUTO_INCREMENT,
    StudentID INT NOT NULL,
    Time_In TIME NOT NULL,
    Time_Out TIME,
    Date DATE NOT NULL,
    Status ENUM('Present', 'Late', 'Absent') NOT NULL,
    FOREIGN KEY (StudentID) REFERENCES Students(StudentID)
);

-- Insert sample student data
INSERT INTO Students (StudentName) VALUES
    ('Agustin, Vrenelli'),
    ('De Guzman, Marie Joy'),
    ('Rotugal, Jasmine');