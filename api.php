<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'db_config.php'; // Should define $pdo as a PDO instance

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['studentName'], $data['timeIn'], $data['date'])) {
            echo json_encode(['success' => false, 'message' => 'Missing required data']);
            exit;
        }

        $studentName = $data['studentName'];
        $timeIn = $data['timeIn'];
        $timeOut = $data['timeOut'] ?? null;
        $date = $data['date'];
        $status = $data['status'] ?? 'Present';

        // Check if student exists
        $stmt = $pdo->prepare("SELECT StudentID FROM Students WHERE StudentName = ?");
        $stmt->execute([$studentName]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$student) {
            // Add student if not found
            $stmt = $pdo->prepare("INSERT INTO Students (StudentName) VALUES (?)");
            $stmt->execute([$studentName]);
            $studentId = $pdo->lastInsertId();
        } else {
            $studentId = $student['StudentID'];
        }

        // If timeOut is provided, update existing attendance record
        if ($timeOut) {
            $stmt = $pdo->prepare("UPDATE Attendance SET Time_Out = ? WHERE StudentID = ? AND Date = ? AND Time_In = ?");
            $stmt->execute([$timeOut, $studentId, $date, $timeIn]);
        } else {
            // Otherwise, insert a new attendance record
            $stmt = $pdo->prepare("INSERT INTO Attendance (StudentID, Time_In, Date, Status) VALUES (?, ?, ?, ?)");
            $stmt->execute([$studentId, $timeIn, $date, $status]);
        }

        echo json_encode(['success' => true, 'message' => 'Attendance processed successfully']);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $pdo->query("SELECT a.*, s.StudentName FROM Attendance a JOIN Students s ON a.StudentID = s.StudentID ORDER BY Date DESC, Time_In DESC");
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $records]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>