<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'php/config.php';

// Fetch total students
$student_query = "SELECT COUNT(*) as total FROM students";
$student_result = mysqli_query($conn, $student_query);
$student_count = mysqli_fetch_assoc($student_result)['total'];

// Fetch gender distribution
$gender_query = "SELECT sex, COUNT(*) as count FROM students GROUP BY sex";
$gender_result = mysqli_query($conn, $gender_query);
$gender_counts = ['Male' => 0, 'Female' => 0];

while ($row = mysqli_fetch_assoc($gender_result)) {
    $gender_counts[$row['sex']] = $row['count'];
}

// Fetch student counts per program
$program_gender_query = "
    SELECT program, 
           COUNT(*) AS total_count,
           SUM(CASE WHEN sex = 'Male' THEN 1 ELSE 0 END) AS male_count,
           SUM(CASE WHEN sex = 'Female' THEN 1 ELSE 0 END) AS female_count
    FROM students 
    WHERE program IN ('ITE', 'CELA', 'CBA', 'HME', 'CJE') 
    GROUP BY program
";

$program_gender_result = mysqli_query($conn, $program_gender_query);

$program_gender_counts = [
    'ITE' => ['Total' => 0, 'Male' => 0, 'Female' => 0],
    'CELA' => ['Total' => 0, 'Male' => 0, 'Female' => 0],
    'CBA' => ['Total' => 0, 'Male' => 0, 'Female' => 0],
    'HME' => ['Total' => 0, 'Male' => 0, 'Female' => 0],
    'CJE' => ['Total' => 0, 'Male' => 0, 'Female' => 0]
];

while ($row = mysqli_fetch_assoc($program_gender_result)) {
    $program_gender_counts[$row['program']]['Total'] = $row['total_count'];
    $program_gender_counts[$row['program']]['Male'] = $row['male_count'];
    $program_gender_counts[$row['program']]['Female'] = $row['female_count'];
}

// Fetch student counts per course
$course_gender_query = "
    SELECT course, 
           COUNT(*) AS total_count,
           SUM(CASE WHEN sex = 'Male' THEN 1 ELSE 0 END) AS male_count,
           SUM(CASE WHEN sex = 'Female' THEN 1 ELSE 0 END) AS female_count
    FROM students 
    GROUP BY course
";

$course_gender_result = mysqli_query($conn, $course_gender_query);

$course_gender_counts = [];

while ($row = mysqli_fetch_assoc($course_gender_result)) {
    $course_gender_counts[$row['course']] = [
        'Total' => $row['total_count'],
        'Male' => $row['male_count'],
        'Female' => $row['female_count']
    ];
}

// Define Programs and Courses
$program_courses = [
    "ITE" => ["Bachelor of Science in Information Technology"],
    "CELA" => [
        "Bachelor of Arts Major in History",
        "Bachelor of Arts Major in Political Science",
        "Bachelor of Elementary Education – Generalist",
        "Bachelor of Special Needs Education",
        "Bachelor of Secondary Education Major in English",
        "Bachelor of Secondary Education Major in Mathematics",
        "Bachelor of Secondary Education Major in Science",
        "Bachelor of Secondary Education Major in Social Studies",
        "Bachelor of Technology and Livelihood Education Major in Home Economics"
    ],
    "CBA" => [
        "Bachelor of Science in Business Administration Major in Financial Management",
        "Bachelor of Science in Business Administration Major in Human Resource Management",
        "Bachelor of Science in Business Administration Major in Marketing Management"
    ],
    "HME" => ["Bachelor of Science in Hospitality Management"],
    "CJE" => ["Bachelor of Science in Criminology"]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Squared Admin Dashboard</title>
    <link rel="icon" type="image/png" href="../images/Squared_Logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="css/style.css">
    <style>
        :root {
            --color-primary: #0C5A11;
            --color-secondary: #69B41E;
            --color-accent: #69B41E;
            --color-male: #0d6efd;
            --color-female: #e83e8c;
        }
        
        /* Modern card styling */
        .stats-card {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: none;
            overflow: hidden;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .stats-card .card-body {
            padding: 1.5rem;
        }
        
        .stats-icon {
            width: clamp(44px, 8vw, 60px);
            height: clamp(44px, 8vw, 60px);
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(1.1rem, 3.5vw, 1.5rem);
            margin-bottom: 1rem;
        }

        .stats-icon i {
            font-size: clamp(1rem, 3vw, 1.3rem);
            line-height: 1;
        }

        .stats-number {
            font-size: clamp(1.6rem, 5vw, 2.5rem);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
            font-weight: 500;
        }
        
        /* Chart container styling */
        .chart-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .chart-wrapper {
            height: 400px;
            position: relative;
        }
        
        /* Accordion styling */
        .accordion {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        
        .accordion-item {
            border: none;
            border-bottom: 1px solid #e9ecef;
        }
        
        .accordion-item:last-child {
            border-bottom: none;
        }
        
        .accordion-button {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border: none;
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            color: var(--color-primary);
        }
        
        .accordion-button:not(.collapsed) {
            background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
            color: white;
            box-shadow: none;
        }
        
        .accordion-button::after {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%230C5A11'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
        }
        
        .accordion-button:not(.collapsed)::after {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='white'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
        }
        
        .program-badge {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--color-secondary);
        }
        
        .program-summary {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        /* Course card styling */
        .course-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .course-card:hover {
            border-color: var(--color-secondary);
            box-shadow: 0 2px 8px rgba(105, 180, 30, 0.1);
        }
        
        .course-card .card-body {
            padding: 1.25rem;
        }
        
        .course-card .card-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--color-primary);
            margin-bottom: 1rem;
            line-height: 1.3;
            white-space: normal; /* allow wrapping instead of truncation */
        }
        
        .gender-distribution .progress {
            height: 8px;
            border-radius: 4px;
        }
        
        .bg-male {
            background-color: var(--color-male) !important;
        }
        
        .bg-female {
            background-color: var(--color-female) !important;
            color: #fff !important; /* Ensure text is readable on pink background */
            border-color: var(--color-female) !important;
        }

        .bg-female {
    background: linear-gradient(135deg, #eb3da2ff, #e251c3ff) !important;
}

        /* Ensure progress bars and badges using bg-female render correctly (override bootstrap gradients) */
        .progress-bar.bg-female,
        .progress .bg-female {
            background-color: var(--color-female) !important;
            background-image: none !important;
        }

        .badge.bg-female {
            color: #fff !important;
        }

        .stat-number {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        
        .text-pink {
            color: var(--color-female) !important;
            font-weight: 700;
        }
        
        /* FIXED: Correct z-index hierarchy */
        #sidebar {
            z-index: 1035 !important; /* Higher than modal backdrop */
        }

        .modal-backdrop {
            z-index: 1030 !important; /* Lower than sidebar */
        }

        .modal {
            z-index: 1040 !important; /* Higher than sidebar */
        }

        .navbar {
            z-index: 1030 !important;
        }

        /* Mobile optimizations */
        @media (max-width: 768px) {
            .stats-card .card-body {
                padding: 1rem;
            }
            
            .stats-icon {
                width: 50px;
                height: 50px;
                font-size: 1.25rem;
                margin-bottom: 0.75rem;
                display: flex; /* ensure centering on mobile */
                align-items: center;
                justify-content: center;
            }
            
            .stats-number {
                font-size: 2rem;
            }
            
            .chart-wrapper {
                height: 300px;
            }
            
            .chart-container {
                padding: 1rem;
            }
            
            .accordion-button {
                padding: 1rem;
            }
            
            .program-summary {
                flex-direction: column;
                gap: 0.5rem;
                align-items: flex-start;
            }
            
            .course-card .card-body {
                padding: 1rem;
            }
            
            .stats-label {
                font-size: 0.8rem;
            }
            
            /* Ensure sidebar overlay works properly on mobile */
            .sidebar-overlay {
                z-index: 1025 !important;
            }
        }
        
        @media (max-width: 576px) {
            .stats-card {
                margin-bottom: 1rem;
            }
            
            .chart-wrapper {
                height: 250px;
            }
            
            .accordion-button .d-flex {
                flex-direction: column;
                align-items: flex-start !important;
            }

            /* Disable truncation for course titles on small screens so full text is visible */
            .course-card .card-title.text-truncate {
                white-space: normal !important;
                overflow: visible !important;
                text-overflow: clip !important;
            }
            
            .program-summary {
                margin-top: 0.5rem;
            }
            
            .row.g-3 .col-12 {
                margin-bottom: 1rem;
            }
            /* Keep the three stats cards in a single horizontal row on small screens */
            .row.g-3.mt-2.mb-4 {
                display: flex;
                flex-wrap: nowrap;
                -webkit-overflow-scrolling: touch;
                padding-bottom: 0.25rem;
            }

            .row.g-3.mt-2.mb-4 > .col-12.col-sm-6.col-md-4 {
                flex: 0 0 33.3333%;
                max-width: 33.3333%;
            }

            /* Slightly reduce paddings and font sizes for mobile fit */
            .stats-card .card-body {
                padding: 0.75rem;
            }

            .stats-number {
                font-size: 1.8rem;
            }
        }
        
        /* Custom scrollbar for accordion content */
        .accordion-body::-webkit-scrollbar {
            width: 6px;
        }
        
        .accordion-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .accordion-body::-webkit-scrollbar-thumb {
            background: var(--color-secondary);
            border-radius: 10px;
        }
        
        .accordion-body::-webkit-scrollbar-thumb:hover {
            background: var(--color-primary);
        }
        
        /* Animation for stats cards */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .stats-card {
            animation: fadeInUp 0.6s ease-out;
        }
        
        .stats-card:nth-child(2) {
            animation-delay: 0.1s;
        }
        
        .stats-card:nth-child(3) {
            animation-delay: 0.2s;
        }
    </style>
</head>
<body>
    <!-- Include Navigation -->
    <?php include 'navbar.php'; ?>
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div id="content">
        <div class="container-fluid">
            <h2 class="tt">Squared Dashboard</h2>

            <!-- Stats Cards -->
            <div class="row g-3 mt-2 mb-4">
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="card stats-card bg1 text-white h-100">
                        <div class="card-body text-center">
                            <div class="stats-icon">
                                <i class="bi bi-people"></i>
                            </div>
                            <div class="stats-number"><?= $student_count; ?></div>
                            <div class="stats-label">Total Students</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="card stats-card bg2 text-white h-100">
                        <div class="card-body text-center">
                            <div class="stats-icon">
                                <i class="bi bi-gender-male"></i>
                            </div>
                            <div class="stats-number"><?= $gender_counts['Male']; ?></div>
                            <div class="stats-label">Male Students</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="card stats-card bg3 text-white h-100">
                        <div class="card-body text-center">
                            <div class="stats-icon">
                                <i class="bi bi-gender-female"></i>
                            </div>
                            <div class="stats-number"><?= $gender_counts['Female']; ?></div>
                            <div class="stats-label">Female Students</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Program Chart -->
            <div class="chart-container">
                <h3 class="tt mb-3">Students per Program</h3>
                <div class="chart-wrapper">
                    <canvas id="programGenderChart"></canvas>
                </div>
            </div>
            
            <!-- Course Statistics -->
            <div class="course-stats-container">
                <h3 class="tt mb-3">Students per Course</h3>
                
                <!-- Program Accordions -->
                <div class="accordion" id="courseAccordion">
                    <?php foreach ($program_courses as $program => $courses): ?>
                        <?php 
                        $programTotal = 0;
                        $programMale = 0;
                        $programFemale = 0;
                        foreach ($courses as $course) {
                            if (isset($course_gender_counts[$course])) {
                                $programTotal += $course_gender_counts[$course]['Total'];
                                $programMale += $course_gender_counts[$course]['Male'];
                                $programFemale += $course_gender_counts[$course]['Female'];
                            }
                        }
                        ?>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $program ?>">
                                    <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                        <div class="d-flex align-items-center">
                                            <span class="program-badge me-3"></span>
                                            <strong><?= $program ?></strong>
                                        </div>
                                        <div class="program-summary">
                                            <small class="badge bg-primary">
                                                <i class="bi bi-people-fill me-1"></i><?= $programTotal ?> Students
                                            </small>
                                            <small class="badge bg-info">
                                                <i class="bi bi-gender-male me-1"></i><?= $programMale ?> Male
                                            </small>
                                            <small class="badge bg-female">
                                                <i class="bi bi-gender-female me-1"></i><?= $programFemale ?> Female
                                            </small>
                                        </div>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse<?= $program ?>" class="accordion-collapse collapse" data-bs-parent="#courseAccordion">
                                <div class="accordion-body">
                                    <div class="row g-3">
                                        <?php foreach ($courses as $course): ?>
                                            <?php 
                                            $courseData = isset($course_gender_counts[$course]) ? $course_gender_counts[$course] : ['Total' => 0, 'Male' => 0, 'Female' => 0];
                                            $malePercent = $courseData['Total'] > 0 ? ($courseData['Male'] / $courseData['Total']) * 100 : 0;
                                            $femalePercent = $courseData['Total'] > 0 ? ($courseData['Female'] / $courseData['Total']) * 100 : 0;
                                            ?>
                                            <div class="col-12 col-md-6 col-lg-4">
                                                <div class="course-card card h-100">
                                                    <div class="card-body">
                                                        <h6 class="card-title text-truncate" title="<?= $course ?>"><?= $course ?></h6>
                                                        
                                                        <!-- Progress Bars -->
                                                        <div class="gender-distribution mb-3">
                                                            <div class="d-flex justify-content-between mb-1">
                                                                <small class="text-muted">Male: <?= $courseData['Male'] ?></small>
                                                                <small class="text-muted">Female: <?= $courseData['Female'] ?></small>
                                                            </div>
                                                            <div class="progress" style="height: 8px;">
                                                                <div class="progress-bar bg-male" style="width: <?= $malePercent ?>%"></div>
                                                                <div class="progress-bar bg-female" style="width: <?= $femalePercent ?>%"></div>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Stats -->
                                                        <div class="course-stats">
                                                            <div class="row text-center">
                                                                <div class="col-4">
                                                                    <div class="stat-number text-primary"><?= $courseData['Total'] ?></div>
                                                                    <small class="text-muted">Total</small>
                                                                </div>
                                                                <div class="col-4">
                                                                    <div class="stat-number text-info"><?= $courseData['Male'] ?></div>
                                                                    <small class="text-muted">Male</small>
                                                                </div>
                                                                <div class="col-4">
                                                                    <div class="stat-number text-pink"><?= $courseData['Female'] ?></div>
                                                                    <small class="text-muted">Female</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
    <script>
        // Chart.js for program gender distribution
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById("programGenderChart").getContext("2d");
            var programGenderChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: ["ITE", "CELA", "CBA", "HME", "CJE"],
                    datasets: [
                        {
                            label: "Total",
                            data: [
                                <?= $program_gender_counts['ITE']['Total']; ?>, 
                                <?= $program_gender_counts['CELA']['Total']; ?>, 
                                <?= $program_gender_counts['CBA']['Total']; ?>, 
                                <?= $program_gender_counts['HME']['Total']; ?>, 
                                <?= $program_gender_counts['CJE']['Total']; ?>
                            ],
                            backgroundColor: "#0C5A11",
                            borderColor: "#0C5A11",
                            borderWidth: 1
                        },
                        {
                            label: "Male",
                            data: [
                                <?= $program_gender_counts['ITE']['Male']; ?>, 
                                <?= $program_gender_counts['CELA']['Male']; ?>, 
                                <?= $program_gender_counts['CBA']['Male']; ?>, 
                                <?= $program_gender_counts['HME']['Male']; ?>, 
                                <?= $program_gender_counts['CJE']['Male']; ?>
                            ],
                            backgroundColor: "#0d6efd",
                            borderColor: "#0d6efd",
                            borderWidth: 1
                        },
                        {
                            label: "Female",
                            data: [
                                <?= $program_gender_counts['ITE']['Female']; ?>, 
                                <?= $program_gender_counts['CELA']['Female']; ?>, 
                                <?= $program_gender_counts['CBA']['Female']; ?>, 
                                <?= $program_gender_counts['HME']['Female']; ?>, 
                                <?= $program_gender_counts['CJE']['Female']; ?>
                            ],
                            backgroundColor: "#e83e8c",
                            borderColor: "#e83e8c",
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 10
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: {
                                size: 14
                            },
                            bodyFont: {
                                size: 13
                            }
                        }
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeOutQuart'
                    }
                }
            });
            
            // Add animation to accordion items when they open
            document.querySelectorAll('.accordion-button').forEach(button => {
                button.addEventListener('click', function() {
                    const target = document.querySelector(this.getAttribute('data-bs-target'));
                    if (target) {
                        const cards = target.querySelectorAll('.course-card');
                        cards.forEach((card, index) => {
                            card.style.animationDelay = `${index * 0.1}s`;
                            card.classList.add('animate__animated', 'animate__fadeInUp');
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>