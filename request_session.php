<?php
session_start();
include 'config/db.php';

// Security: Only Learners can request sessions
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'learner') {
    header("Location: login.php");
    exit();
}

$course_id = $_GET['course_id'];
$learner_id = $_SESSION['user_id'];

// Fetch course and tutor details to show on the page
$course_info = mysqli_query($conn, "SELECT c.*, u.username as tutor_name 
                                    FROM courses c 
                                    JOIN users u ON c.tutor_id = u.user_id 
                                    WHERE c.course_id = $course_id");
$course = mysqli_fetch_assoc($course_info);

// Handle the Form Submission
if (isset($_POST['send_request'])) {
    // Check if a pending request already exists for this specific course by this learner
    $check = mysqli_query($conn, "SELECT * FROM requests WHERE course_id = $course_id AND learner_id = $learner_id AND status = 'pending'");
    
    if (mysqli_num_rows($check) == 0) {
         
        // If you haven't added it to your DB yet, you can skip the variable or add it to your table.
        $sql = "INSERT INTO requests (course_id, learner_id, status, is_read_tutor) 
                VALUES ($course_id, $learner_id, 'pending', 0)";
        
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Request sent to " . $course['tutor_name'] . "!'); window.location='learner_dashboard.php';</script>";
        }
    } else {
        echo "<script>alert('You already have a pending request for this course.'); window.location='learner_dashboard.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=arrow_forward" />
    <link rel="stylesheet" href="assets/style.css">
    <title>Request Session - UniConnect</title>
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            scroll-behavior: smooth;

        }

        body{
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
        }

        .header-containar{
            width: 100%;
            /* height: 100vh; */
        }



        .header-containar nav{
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 50px;
            background-color: rgba(15, 23, 42, 0.8);
            position: fixed;
            width: 100%;
            height: 120px;
            top: 0;
            z-index: 1000;
            transition: background-color 0.3s ease-in-out;
        }

        /* -------------------------Navbar scroll styles------------------------- */
        #mainNavbar.scrolled {
            background-color: rgb(16, 34, 75); 
        }


        .logo img{
            width: 100px;
            height: 100px;
        }


        .header-containar nav .profession{
            display: flex;
            list-style: none;
            gap: 25px;
        }

        .profession h2{
            color: #fff;
            font-size: 28px;
            font-weight: 600;
        }

        .header-containar nav .profession a{
            text-decoration: none;
            color: #fff;
            font-weight: 500;
            font-size: 24px;
            transition: color 0.3s ease;
            position: relative;
        }



        .header-containar nav .profession a:hover{
            color: rgb(249, 115, 22);
        }

        .header-containar nav .btn{
            display: flex;
            align-items: end;
            list-style: none;
            gap: 25px;
            
        }
        .header-containar nav .btn a{
            text-decoration: none;
            padding: 10px 20px;
            background-color: #ff5722;
            color: #fff;
            border-radius: 20px;
            font-weight: 600;
            font-size: 18px;
            transition: font-weight 0.6s ease-in-out;
            position: relative;
        }
        .header-containar nav .btn a:hover{
            background-color: #c0350b;
        }

        .header-containar nav .profile-icon{
            display: flex;
            align-items: end;
            list-style: none;
            
        }

        .header-containar nav .profile-icon img{
            font-size: 56px;
            padding: 20px;
            background-color: #ff5722;
            color: #fff;
            border-radius: 50%;
        }



        /* -------------------------Footer styles------------------------- */
        .footer-containar{
            width: 100%;
            background-color: rgb(15, 23, 42);
            display: flex;
            align-items: space-between;
            /* margin-top: 5%; */

        }
        .footer-containar p, a, h3, h4{
            color: #fff;
            font-size: 18px;
        }
        .footer-containar a{
            text-decoration: none;
        }

        .footer-containar .logo{
            margin-left: 5%;
            padding: 20px 0px;
            display: block;
            flex-direction: column;
            gap: 20px;
        }

        .footer-containar .logo .contact{
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .footer-containar .about{
            width: 40%;
            margin-left: 5%;
            padding: 20px 0px;
            text-align: left;
        }

        .footer-containar .about p{
            text-align: justify;
        }

        .footer-containar .quick-links{
            margin-left: 5%;
            margin-right: 5%;
            padding: 20px 0px;
            /* gap: 20px; */
            display: block;
            flex-direction: column;
            
        }

        .footer-containar .quick-links a{
            text-decoration: none;
            color: #fff;
            font-size: 18px;
            font-weight: 500;
            /* transition: border 0.3s, background-color 0.3s ease-in-out; */
            position: relative;
        }


        .footer-containar .quick-links .left{
            display: flex;
            flex-direction: row;
            gap: 60px;
        }
        .footer-containar .quick-links .right{
            display: flex;
            flex-direction: row;
            gap: 44px;
        }

        .footer-containar .email{
            margin-right: 5%;
            display: block;
            flex-direction: column;
            gap: 20px;
            
        }


        .footer-containar .email p{
            color: black;
            border-radius: 8px;
            background-color: seashell;
            font-size: 16px;
            padding: 10px;
        }

        .footer-containar .email .email_contact{
            display: flex;
            flex-direction: row;
            align-items: center;
            /* gap: 15px; */
            background-color: seashell;
            padding: 10px;
            border-radius: 8px;
            font-weight: 600;
        }

        .copyright{
            background-color: rgb(15, 23, 42);
            width: 100%;
            display: flex;
            justify-content: space-between;
            height: 100px;
        }

        .copyright .social-icons{
            display: flex;
            margin-right: 5%;
            margin-left: 5%;
        }

        .copyright .social-icons svg{
            color: #fff;
            margin-right: 5%;
            margin-top: 18px;
            margin-right: 10px;
        }

        .copyright p{
            color: #fff;
            text-align: right;
            padding: 15px 0px;
            font-size: 16px;
        }

        .copyright .footer-logo{
            width: 40px;
            height: 40px;
            margin-left: 10px;
            margin-bottom: 5px;
            margin-top: 10px;
        }

        .copyright .product-by{
            display: flex;
            align-items: center;
            margin-left: 5%;
            color: #fff;
            font-size: 16px;
        }

        .email_contact img.arrow{
            width: 20px;
            height: 20px;
            margin-left: 10px;
            cursor: pointer;
            color: black;
        }


        .menu-toggle {
            display: none; /* Hidden on desktop */
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
        }

        .menu-toggle span {
            width: 30px;
            height: 3px;
            background-color: white;
            border-radius: 2px;
        }

        @media screen and (max-width: 768px) {
            .header-containar nav {
                padding: 10px 20px;
                height: auto;
                min-height: 80px;
            }

            .logo img {
                width: 60px;
                height: 60px;
            }

            
            .header-containar nav .profession {
                display: none; 
                flex-direction: column;
                position: absolute;
                top: 80px;
                left: 0;
                width: 100%;
                background-color: rgb(16, 34, 75);
                padding: 20px;
                text-align: center;
                gap: 15px;
                font-size: 12px;
            }
            
            .menu-toggle {
                display: flex;
            }
            /* -------------------------Footer responsive styles------------------------- */
            .footer-containar {
                flex-direction: column;
                align-items: flex-start;
                padding-bottom: 30px;
            }

            .footer-containar .about,
            .footer-containar .quick-links,
            .footer-containar .email,
            .footer-containar .logo {
                width: 90%;
                margin: 10px 5%;
                padding: 10px 0;
            }

            .footer-containar .quick-links .left,
            .footer-containar .quick-links .right {
                flex-direction: column;
                gap: 10px;
            }

            /* Copyright Adjustments */
            .copyright {
                flex-direction: column;
                height: auto;
                padding: 20px 0;
                align-items: center;
                text-align: center;
            }

            .copyright .social-icons,
            .copyright .product-by {
                margin: 10px 0;
                justify-content: center;
            }

            .copyright p {
                text-align: center;
            }
        }

        .error-msg { 
            color: #721c24; 
            background-color: #f8d7da; 
            padding: 10px; 
            border-radius: 4px; 
            margin-bottom: 15px; 
            font-size: 0.9rem; 
            text-align: center; 
            border: 1px solid #f5c6cb; 
        }
        /* General Reset */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Roboto, sans-serif;
        }

        /* Background Wrapper */
        body {
            width: 100%;
    
        }

       
        .container {
            background-color: rgba(15, 23, 42, 0.9); 
            color: #fff;
            padding: 40px;
            border-radius: 30px;
            width: 100%;
            max-width: 450px;
            border: 1px solid rgba(45, 212, 191, 0.3); 
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            margin-top: 10%;
            margin-bottom: 10%;
            margin-left: auto;
            margin-right: auto;
        }

        h2 {
            font-size: 32px;
            margin-bottom: 25px;
            text-align: center;
            color: rgb(45, 212, 191); 
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: #cbd5e1;
        }

        input, select {
            width: 100%;
            padding: 12px 15px;
            border-radius: 12px;
            border: 1px solid #334155;
            background-color: rgba(30, 41, 59, 0.5);
            color: #fff;
            font-size: 16px;
            outline: none;
            transition: 0.3s;
        }

        input:focus, select:focus {
            border-color: rgb(45, 212, 191);
        }

        /* Buttons matching your content_btn style */
        button {
            width: 100%;
            text-decoration: none;
            padding: 15px;
            background-color: rgb(45, 212, 191);
            color: #000;
            border-radius: 25px;
            font-weight: 600;
            font-size: 18px;
            margin-top: 10px;
            cursor: pointer;
            border: #07e0e7 1px solid;
            transition: all 0.3s ease-in-out;
        }

        button:hover {
            box-shadow: rgb(50, 153, 139) 0px 5px 15px 0px;
            color: #fff;
            background-color: rgb(35, 180, 160);
        }

        .link {
            text-align: center;
            margin-top: 20px;
            display: block;
            color: #94a3b8;
            text-decoration: none;
            font-size: 14px;
        }

        .link:hover {
            color: rgb(45, 212, 191);
        }

        /* Responsive adjustments */
        @media (max-width: 480px) {
            .container {
                padding: 25px;
            }
        }


        .dashboard-container h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        .dashboard-container .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            background-color: #f9f9f9;
        }

        .dashboard-container .card {
            background: rgba(15, 23, 42, 0.9); 
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .dashboard-container .card h3 {
            margin-bottom: 15px;
            color: #ffffffff;
        }
        .dashboard-container .card h4{
            font-weight: 700;
            color: #2ecc71;
        }
        .dashboard-container .card small{
            font-weight: 700;
            color: #000000ff;
        }

        .dashboard-container .card .form-group textarea {
            margin-bottom: 15px;
            width: 100%;
        }

        @media (max-width: 768px) {
            .dashboard-container .grid {
                grid-template-columns: 1fr;
            }
        }


    </style>
</head>
<body>


    <div class="header-containar">
        <nav id="mainNavbar">
            <div class="menu-toggle" onclick="toggleMenu()">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <div class="logo">
                <a href="home.html"><img id="logo-image" src="assest/Logo.png" alt=""></a>
            </div>

            <div class="profession">
                <a href="home.html">Request a Session</a>
                <!-- <a href="for_std.html">For Students</a> 
                <a href="for_tutor.html">For Teachers</a> -->
                <!-- <h2>UniConnect Tutor</h2>
                <h2>Dashboard</h2> -->
            </div>
            
            <div class="btn">
                <a href="logout.php">Logout</a>
            </div>
        </nav>
        
        <!-- <div class="content">
            <h1>
                Best <br>Platform to <br>Empower Skills

            </h1>
            <div class="content_btn">
                <a href="http://localhost/uniconnect/login.php">Log In</a>

            </div>
        </div> -->
    </div>


    <div class="container">
        <a href="learner_dashboard.php" style="text-decoration: none; font-size: 14px;">← Back</a>
        <h2 style="margin-top: 10px;">Request a Session</h2>
        
        <div style="background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #3498db;">
            <h3 style="margin: 0; color: #2ecc71;"><?php echo $course['title']; ?></h3>
            <p style="margin: 5px 0; color: #555;">Tutor: <strong><?php echo $course['tutor_name']; ?></strong></p>
        </div>

        <form method="POST">
            <p>By clicking the button below, the tutor will be notified that you are interested in this skill. They will then provide a schedule for you.</p>
            
            <button type="submit" name="send_request" style="background: rgb(35, 180, 160)">Send Request</button>
        </form>
    </div>

    <div class="footer-containar">
        <div class="logo">
            <a href="#"><img id="logo-image" src="assest/Logo.png" alt=""></a><br>
            <a href="" class="contact">+94 11 12 34 567</a><br>
            <a href="" class="contact">suport@lift.agency</a>
        </div>
        <div class="about">
            <h3>About</h3><br>
            <p>UniConnect: Powering Peer Success.
UniConnect is the trusted, student-exclusive platform dedicated to mutual academic empowerment. We facilitate direct, verified skill exchange—connecting you to the campus expertise you need, when you need it. Stop struggling solo. Start thriving together.</p>
        </div>
        <div class="quick-links">
            <h3>Quick Links</h3><br>
            <div class="left">
                <a href="#">Skill</a><br>
                <a href="#">Request</a>
            </div>
            <br>
            <div class="right">
                <a href="#">Booking</a>
                <br>
                <a href="#">Feedback</a>
            </div>
        </div>

        <div class="email">
            <br><h4>Email</h4><br>
            <div class="email_contact">
                <p>UniConnect@gmail.com</p>
                <a href="#"><span style="color: black; font-weight: 600;" class="material-symbols-outlined">
                arrow_forward
                </span></a>
            </div>
            
        </div>

        
    </div>
    <hr>
    
    <div class="copyright">
        <div class="social-icons">
            <a href="">
                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="currentColor" class="bi bi-linkedin" viewBox="0 0 16 16">
                    <path d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854zm4.943 12.248V6.169H2.542v7.225zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248S2.4 3.226 2.4 3.934c0 .694.521 1.248 1.327 1.248zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016l.016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225z"/>
                </svg>
            </a>
            <a href="">
                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
                    <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951"/>
                </svg>
            </a>
            <a href="">
                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="currentColor" class="bi bi-twitter" viewBox="0 0 16 16">
                    <path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334q.002-.211-.006-.422A6.7 6.7 0 0 0 16 3.542a6.7 6.7 0 0 1-1.889.518 3.3 3.3 0 0 0 1.447-1.817 6.5 6.5 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.32 9.32 0 0 1-6.767-3.429 3.29 3.29 0 0 0 1.018 4.382A3.3 3.3 0 0 1 .64 6.575v.045a3.29 3.29 0 0 0 2.632 3.218 3.2 3.2 0 0 1-.865.115 3 3 0 0 1-.614-.057 3.28 3.28 0 0 0 3.067 2.277A6.6 6.6 0 0 1 .78 13.58a6 6 0 0 1-.78-.045A9.34 9.34 0 0 0 5.026 15"/>
                </svg>
            </a>
        </div>
        
        <div class="product-by">
            <p>A product of </p>
            <img class="footer-logo" src="assest/Logo.png" alt="UniConnect">
        </div>
        <p>© 2025 UniConnect. All rights reserved.</p>
        
    </div>


    <script src="behavior.js"></script>

</body>
</html>