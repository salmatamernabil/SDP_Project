<?php
require_once '../Controller/instruct_course_controller.php'; // Load courses

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['DoctorID'])) {
    error_log("doctor table with MemberID: " . $_SESSION['DoctorID']);
} else {
    echo "No MemberID found in session.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instruct a Course</title>
    <style>
        /* General Styling */
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Navbar */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background-color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }
        .navbar .logo {
            color: #3366ff;
            font-size: 2em;
            font-weight: 800;
            text-decoration: none;
            transition: transform 0.3s;
        }
        .navbar .logo:hover {
            transform: scale(1.1);
        }

        /* Content */
        .content {
            padding-top: 100px;
            flex-grow: 1;
        }

        h1 {
            color: #3366ff;
            margin-bottom: 20px;
            font-weight: 600;
            text-align: center;
        }

        /* Courses Grid */
        .courses-grid {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
            padding: 50px 20px;
        }

        /* Course Cards */
        .course-card {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 30px 20px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
            width: 250px;
            border-left: 5px solid #3366ff;
            position: relative;
        }

        .course-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .course-date {
            font-size: 1em;
            font-weight: 700;
            color: #3366ff;
            margin-bottom: 10px;
        }

        .course-name {
            font-size: 1.3em;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
        }

        /* Instruct Button */
        .btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 1em;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            background-color: #3366ff;
            color: white;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
        }

        .btn:hover {
            background-color: #254eda;
        }

        .footer {
            background-color: #3366ff;
            color: white;
            padding: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <a href="doctor_home_view.php" class="logo">Bravo</a>
    </div>

    <!-- Courses Section -->
    <div class="content">
        <h1>Instruct a Course</h1>
        <div class="courses-grid">
        <?php if (!empty($courses)): ?>
    <?php foreach ($courses as $course): ?>
        <div class="course-card">
        <a href="course_details_view.php?courseId=<?php echo $course['course_id']; ?>" style="text-decoration: none; color: inherit;">
            <div class="course-date">Date: <?php echo htmlspecialchars($course['course_date']); ?></div>
            <div class="course-name"><?php echo htmlspecialchars($course['course_name']); ?></div>

            <form method="POST" action="../Controller/instruct_course_controller.php">
                <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">
                <button type="submit" class="btn">Instruct Course</button>
            </form>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p style="text-align: center;">No courses available for instruction.</p>
<?php endif; ?>

        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Contact Us: info@ngo.org | © 2024 Bravo</p>
    </div>

</body>
</html>
