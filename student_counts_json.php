<?php
require_once 'php/config.php';

// Fetch total students
$student_query = "SELECT COUNT(*) as total FROM students";
$student_result = mysqli_query($conn, $student_query);
$student_count = mysqli_fetch_assoc($student_result)['total'];

// Fetch student counts per program
$program_gender_query = "
    SELECT program, 
           COUNT(*) AS total_count
    FROM students 
    WHERE program IN ('ITE', 'CELA', 'CBA', 'HME', 'CJE') 
    GROUP BY program
";

$program_gender_result = mysqli_query($conn, $program_gender_query);

$program_counts = [
    'ITE' => 0,
    'CELA' => 0,
    'CBA' => 0,
    'HME' => 0,
    'CJE' => 0
];

while ($row = mysqli_fetch_assoc($program_gender_result)) {
    $program_counts[$row['program']] = $row['total_count'];
}

header('Content-Type: application/json');
echo json_encode([
    'total' => (int)$student_count,
    'ITE' => (int)$program_counts['ITE'],
    'CELA' => (int)$program_counts['CELA'],
    'CBA' => (int)$program_counts['CBA'],
    'HME' => (int)$program_counts['HME'],
    'CJE' => (int)$program_counts['CJE'],
]);
