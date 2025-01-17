<?php
require_once '../Controller/all_courses_controller.php';

if (isset($_POST['courseId'])) {
    $courseId = $_POST['courseId'];
    $controller = new AllCoursesController();
    $courses = $controller->getAllCourses();
    $course = array_filter($courses, fn($c) => $c['course_id'] == $courseId);
    $course = reset($course); // Get the first (and only) course
} else {
    die("Course ID is missing.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course</title>
    <style>
        /* General Styling */
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            color: #333;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        /* Navbar */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background-color:white;
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
        }

        .navbar .logo:hover {
            transform: scale(1.05);
        }

        /* Form Styling */
        .content {
            padding-top: 120px; 
            padding-bottom: 120px;/* Adjust for navbar */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 160px); /* Adjust for footer */
        }

        .form-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 500px;
            text-align: left;
        }

        .form-container h2 {
            color: #3366ff;
            margin-bottom: 20px;
            font-size: 1.5em;
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
        }

        input[type="text"],
        input[type="date"],
        input[type="number"],
        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            background-color: #f4f4f4;
            transition: background-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        input[type="date"]:focus {
            background-color: #fff;
            border-color: #3366ff;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #3366ff;
            color: white;
            border: none;
            border-radius: 30px;
            font-size: 1.2em;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #254eda;
        }

        /* Footer */
        .footer {
            background-color: #3366ff;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 1em;
            margin-top: auto;
        }

        /* Alert */
        .alert {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 20px;
            text-align: center;
            border-radius: 5px;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <a href="admin_home_view.php" class="logo">Bravo</a>
    </div>

    <!-- Content -->
    <div class="content">
        <div class="form-container">
            <h2>Edit Course: <?php echo htmlspecialchars($course['course_name']); ?></h2>
            <form action="../Controller/edit_course_controller.php" method="POST">
                <input type="hidden" name="course_id" value="<?= $course['course_id']; ?>">

                <!-- Course Name -->
                <label for="courseName">Course Name</label>
                <input type="text" name="courseName" id="courseName" value="<?= htmlspecialchars($course['course_name']); ?>" required>

                <!-- Course Date -->
                <label for="dateOfCourse">Course Date</label>
                <input type="date" name="dateOfCourse" id="dateOfCourse" value="<?= $course['course_date']; ?>" required>

                <!-- Course Place -->
                <label for="placeOfCourse">Course Place</label>
                <input type="text" name="placeOfCourse" id="placeOfCourse" value="<?= htmlspecialchars($course['course_place']); ?>" required>

                <!-- Hosting Hospital -->
                <label for="hospitalOfHosting">Hosting Hospital</label>
                <input type="text" name="hospitalOfHosting" id="hospitalOfHosting" value="<?= htmlspecialchars($course['hosting_hospital']); ?>" required>

                <!-- Total Cost -->
                <label for="totalCost">Total Cost</label>
                <input type="number" name="totalCost" id="totalCost" value="<?= $course['total_cost']; ?>" required>

                <!-- Update Button -->
                <button type="submit">Update Course</button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Contact Us: info@ngo.org | © 2024 Bravo</p>
    </div>

</body>
</html>
