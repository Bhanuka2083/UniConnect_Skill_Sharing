<?php
session_start();
include 'config/db.php';

// Security Check: Only allow Admins
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Logic to add a category
if (isset($_POST['add_category'])) {
    $cat_name = mysqli_real_escape_string($conn, $_POST['category_name']);
    if (!empty($cat_name)) {
        $check_category = mysqli_query($conn, "SELECT category_name FROM categories WHERE category_name = '$cat_name'");
        if (mysqli_num_rows($check_category) == 0) {
            mysqli_query($conn, "INSERT INTO categories (category_name) VALUES ('$cat_name')");
        }

        else {
            echo "<script>alert('Category already exists!');</script>";
        }

    }
}

// Logic to delete a category
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM categories WHERE category_id = $id");
    header("Location: admin_dashboard.php");
}

// Fetch stats
$user_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];
$course_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM courses"))['total'];
$categories = mysqli_query($conn, "SELECT * FROM categories");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=arrow_forward" />
    <title>Admin Dashboard - UniConnect</title>
    
    <style>

        .admin-grid { 
            display: grid; 
            grid-template-columns: 250px 1fr; 
            min-height: 100vh;
            margin-top: 120px;
        }
        .sidebar { 
            background: #2c3e50; 
            color: white; 
            padding: 20px; 
        }
        .content { 
            padding: 30px; 
            background: #f4f7f6; 
        }
        .stats-cards { 
            display: flex; 
            gap: 20px; 
            margin-bottom: 30px; 
        }
        .card { 
            background: white; 
            padding: 20px; 
            border-radius: 8px; 
            flex: 1; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); 
        }
        .card h3 { 
            margin-bottom: 10px; 
            color: #000000ff; 
        }
        .cat-list { 
            background: white; 
            padding: 20px; 
            border-radius: 8px; 
        }
        .cat-list input[type="text"] { 
            width: 70%; 
            padding: 10px; 
            margin-right: 10px; 
            border: 1px solid #ccc; 
            border-radius: 4px; 
        }
        .cat-list button { 
            padding: 10px 20px; 
            background-color: rgb(45, 212, 191); 
            color: white; 
            border: none; 
            border-radius: 25px; 
            cursor: pointer; 
            border: #07e0e7 1px solid;
            transition: all 0.3s ease-in-out;
        }
        .cat-list button:hover {
            box-shadow: rgb(50, 153, 139) 0px 5px 5px 0px;
            color: #fff;
            background-color: rgb(35, 180, 160);
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 15px; 
        }
        th, td { 
            text-align: left; 
            padding: 12px; 
            border-bottom: 1px solid #ddd; 
        }
        .btn-delete { 
            color: #e74c3c; 
            text-decoration: none; 
            font-weight: bold; 
        }
        
        @media (max-width: 768px) {
            .admin-grid { grid-template-columns: 1fr; }
            .stats-cards { flex-direction: column; }
        }




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

            <!-- <div class="profession">
                <a href="home.html">Home</a>
                <a href="for_std.html">For Students</a>
                <a href="for_tutor.html">For Teachers</a>
            </div> -->
            
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


    <div class="admin-grid">
        <div class="sidebar">
            <h2>UniConnect</h2>
            <p>Welcome, Admin <span style = "font-weight: bold;"><?php echo $_SESSION['username']; ?></span></p>
            <hr>
            <a href="logout.php" style="color: #ecf0f1; text-decoration: none;">Logout</a>
        </div>
        
        <div class="content">
            <h1>Admin Dashboard</h1>
            
            <div class="stats-cards">
                <div class="card">
                    <h3>Total Users</h3>
                    <p style="font-size: 24px; font-weight: bold;"><?php echo $user_count; ?></p>
                </div>
                <div class="card">
                    <h3>Active Courses</h3>
                    <p style="font-size: 24px; font-weight: bold;"><?php echo $course_count; ?></p>
                </div>
            </div>

            <div class="cat-list">
                <h3>Manage Skill Categories</h3>
                <form method="POST" style="display: flex; gap: 10px; margin-bottom: 20px;">
                    <input type="text" name="category_name" placeholder="New Category (e.g. Photography)" required>
                    <button type="submit" name="add_category" style="width: auto; padding: 0 20px;">Add</button>
                </form>

                <table>
                    <thead>
                        <tr>
                            <th>Category Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($categories)): ?>
                        <tr>
                            <td><?php echo $row['category_name']; ?></td>
                            <td><a href="?delete=<?php echo $row['category_id']; ?>" class="btn-delete" onclick="return confirm('Are you sure?')">Delete</a></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
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