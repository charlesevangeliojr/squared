<?php
require_once 'php/config.php'; // Database connection

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
?>

<style>
    .main-number {
        font-weight: 700;
        font-size: 2.5rem;
        color: #2f4f1f;
    }
    .main-label {
        font-weight: 600;
        font-size: 0.9rem;
        margin-top: 0.25rem;
        color: black;
    }
    .divider {
        width: 2px;
        background-color: #000;
        height: 4rem;
        margin: 0 1.5rem;
    }
    .code-block {
        font-weight: 700;
        font-size: 1.25rem;
        color: #000;
        margin-bottom: 0.25rem;
    }
    .btn-code {
        font-weight: 700;
        font-size: 1rem;
        border-radius: 0.3rem;
        padding: 0.375rem 1rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        min-width: 72px;
    }

    .number-slide {
  position: relative;
  overflow: hidden;
  height: 1.5em; /* adjust based on font-size */
}

.number-slide span {
  display: block;
  position: absolute;
  width: 100%;
  left: 0;
  top: 0;
  transition: transform 0.4s ease;
}

    /* Buttons colors */
    .btn-cba { background-color: #0D5B11; color: #fff; }
    .btn-cela { background-color: #187C19; color: #fff; }
    .btn-cje { background-color: #69B41E; color: #fff; }
    .btn-hme { background-color: #8DC71E; color: #fff; }
    .btn-ite { background-color: #B8D53D; color: #fff; }

    /* Responsive styles */
    @media (max-width: 767.98px) {
        .d-flex.align-items-center {
            flex-wrap: wrap;
            justify-content: center;
        }
        .divider {
            width: 100%;
            height: 2px;
            margin: 1rem 0;
        }
        .text-center.mb-3 {
            margin-bottom: 1rem !important;
            margin-left: 0.5rem;
            margin-right: 0.5rem;
        }
        .main-number {
            font-size: 1.8rem;
        }
        .main-label {
            color: black;
        }
        .code-block {
            font-size: 1rem;
        }
        .btn-code {
            font-size: 0.85rem;
            padding: 0.3rem 0.8rem;
            min-width: 60px;
        }
        .number-slide {
  position: relative;
  overflow: hidden;
  height: 1.5em; /* adjust based on font-size */
}

.number-slide span {
  display: block;
  position: absolute;
  width: 100%;
  left: 0;
  top: 0;
  transition: transform 0.4s ease;
}

    }
</style>

<div class="d-flex align-items-center flex-wrap justify-content-center" id="student-counts">
    <div class="text-center mb-3 mx-2">
        <div class="main-number" id="total-students"><?= number_format($student_count); ?></div>
        <div class="main-label text-uppercase">Registered Students</div>
    </div>
    
    <div class="divider"></div>
    
<div class="d-flex align-items-center flex-wrap justify-content-center">
    <div class="text-center mb-3 mx-2">
        <div class="code-block" id="count-cba"><?= $program_counts['CBA'] ?? '0'; ?></div>
        <a href="https://www.facebook.com/profile.php?id=100085423594230" target="_blank" class="btn btn-cba btn-code">CBA</a>
    </div>
    <div class="text-center mb-3 mx-2">
        <div class="code-block" id="count-cela"><?= $program_counts['CELA'] ?? '0'; ?></div>
        <a href="https://www.facebook.com/profile.php?id=61564257299768" target="_blank" class="btn btn-cela btn-code">CELA</a>
    </div>
    <div class="text-center mb-3 mx-2">
        <div class="code-block" id="count-cje"><?= $program_counts['CJE'] ?? '0'; ?></div>
        <a href="https://www.facebook.com/dcccje2009" target="_blank" class="btn btn-cje btn-code">CJE</a>
    </div>
    <div class="text-center mb-3 mx-2">
        <div class="code-block" id="count-hme"><?= $program_counts['HME'] ?? '0'; ?></div>
        <a href="https://www.facebook.com/profile.php?id=100082753567230" target="_blank" class="btn btn-hme btn-code">HME</a>
    </div>
    <div class="text-center mb-3 mx-2">
        <div class="code-block" id="count-ite"><?= $program_counts['ITE'] ?? '0'; ?></div>
        <a href="https://www.facebook.com/Ite.Sincotecs" target="_blank" class="btn btn-ite btn-code">ITE</a>
    </div>
</div>
</div>
