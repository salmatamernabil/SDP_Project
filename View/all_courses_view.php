<?php

require_once '../Controller/all_courses_controller.php';
// Retrieve superadmin status from session
$isSuperAdmin = $_SESSION['isSuperAdmin'] ?? false;
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses</title>
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

        /* Common button styles */
        .btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 1em;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease-in-out;
            cursor: pointer;
        }

        /* Edit Button */
        .edit-btn {
            background-color: #3366ff;
            color: white;
            border: none;
        }

        .edit-btn:hover {
            background-color: #254eda;
        }

        /* Delete Button */
        .delete-btn {
            background-color: #ff4d4f;
            color: white;
            border: none;
        }

        .delete-btn:hover {
            background-color: #d9363e;
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

        /* Action Buttons (Edit and Delete inside the card) */
        .actions {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 10px;
        }

        .actions form {
            display: inline;
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
        <a href="admin_home_view.php" class="logo">Bravo</a>
    </div>

    <!-- Courses Section -->
    <div class="content">
        <h1>All Courses</h1>
        <div class="courses-grid">
            <?php if (!empty($courses)): ?>
                <?php foreach ($courses as $course): ?>
                    <!-- Make the entire card clickable -->
                    <div class="course-card">
                        <a href="course_details_view.php?courseId=<?php echo $course['course_id']; ?>" style="text-decoration: none; color: inherit;">
                            <div class="course-date">Date: <?php echo htmlspecialchars($course['course_date']); ?></div>
                            <div class="course-name"><?php echo htmlspecialchars($course['course_name']); ?></div>
                        </a>

                        <!-- Actions (Edit and Delete buttons inside the card) -->
                        <div class="actions">
                            <form method="POST" action="edit_course_view.php" style="display: inline;">
                                <input type="hidden" name="courseId" value="<?php echo $course['course_id']; ?>">
                                <button type="submit" class="btn edit-btn">Edit</button>
                            </form>


                            <?php if (!$isSuperAdmin): ?>
                                <form method="POST" action="../Controller/all_courses_controller.php" style="display: inline;">
                                    <input type="hidden" name="courseId" value="<?php echo $course['course_id']; ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="btn delete-btn">Delete</button>
                                </form>
                            <?php endif; ?>

                           
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center;">No courses found.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Contact Us: info@ngo.org | © 2024 Bravo</p>
    </div>

</body>
</html>
