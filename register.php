<?php
include 'database.php'; // Include your database connection file

// Initialize variables to hold success and error messages
$success_message = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];
    $start_date = date('Y-m-d');

    // Check if passwords match
    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if email or phone number already exists
        $check_sql = "SELECT * FROM users WHERE email = ? OR phone_number = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ss", $email, $phone_number);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Email or Phone Number already exists.";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Prepare and execute the SQL statement
            $sql = "INSERT INTO users (name, email, phone_number, password, role, start_date)
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $name, $email, $phone_number, $hashed_password, $role, $start_date);






            if ($stmt->execute()) {
                // Registration successful, set success message
                $success_message = "Registration successful! You can now log in.";
                // Redirect to login page after a short delay
                header("refresh:3;url=login.php");
                exit();
            } else {
                $error = "Error: " . $stmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        * {
                    box-sizing: border-box;
                    margin: 0;
                    padding: 0;
                    font-family: 'Poppins', sans-serif;
                }

                body {
                    background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .form-container {
                    background: white;
                    padding: 40px;
                    border-radius: 20px;
                    box-shadow: 0 20px 50px rgba(0,0,0,0.1);
                    max-width: 620px;
                    width: 100%;
                    transition: 0.3s ease-in-out;
                    text-align: center;
                }

                .form-container:hover {
                    box-shadow: 0 25px 55px rgba(0,0,0,0.15);
                }

                .form-container img {
                    width: 90px;
                    margin-bottom: 20px;
                }

                .form-container h2 {
                    color: #2e7d32;
                    margin-bottom: 30px;
                    font-weight: 600;
                }

                .form-group {
                    margin-bottom: 20px;
                    text-align: left;
                }

                .form-group label {
                    display: block;
                    margin-bottom: 6px;
                    color: #4caf50;
                    font-weight: 500;
                }

                .form-group input {
                    width: 100%;
                    padding: 12px;
                    border-radius: 8px;
                    border: 1px solid #ccc;
                    transition: border-color 0.3s;
                    font-size: 15px;
                    color: #87CEFA;
                }

                .form-group input:focus {
                    border-color: #4caf50;
                    outline: none;
                }

                .form-group input[type="submit"] {
                    background-color: #4caf50;
                    color: white;
                    font-weight: bold;
                    border: none;
                    cursor: pointer;
                    transition: background-color 0.3s;
                }

                .form-group input[type="submit"]:hover {
                    background-color: #388e3c;
                }
                /* Apply light sky blue background to input and select */
                .form-group input[type="text"],
                .form-group input[type="email"],
                .form-group input[type="password"],
                .form-group select {
                    background-color: #DFF3FE; /* Light Sky Blue */
                    color: #000; /* Black text for readability */
                    border: 1px solid #ccc;
                    padding: 12px;
                    border-radius: 8px;
                    font-size: 15px;
                    transition: border-color 0.3s;
                }

                /* Optional: focus effect */
                .form-group input:focus,
                .form-group select:focus {
                    border-color: #00BFFF; /* Deep Sky Blue border on focus */
                    outline: none;
                    box-shadow: 0 0 6px rgba(0, 191, 255, 0.4);
                }


                .error {
                    color: red;
                    text-align: center;
                    margin-bottom: 15px;
                    font-weight: 500;
                }

                .register-link,
                .back-link {
                    text-align: center;
                    margin-top: 15px;
                    font-size: 14px;
                }

                .register-link a,
                .back-link a {
                    color: #388e3c;
                    text-decoration: none;
                    font-weight: 500;
                }

                .register-link a:hover,
                .back-link a:hover {
                    text-decoration: underline;
                }
            </style>



</head>
<body>
    <div class="form-container">
    <!-- Logo Section -->
            <div style="text-align: center; margin-bottom: 15px;">
                <img src="1.png" alt="SmartKrishi Logo" style="max-width: 100px;">
            </div>
        <h2>ব্যবহারকারী নিবন্ধন</h2>

        <!-- Display error or success messages -->
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="success"><?= htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="form-group">
                <label for="name">নাম:</label>
                <input type="text" name="name" required>
            </div>

            <div class="form-group">
                <label for="email">ইমেইল:</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="phone_number">ফোন নম্বর:</label>
                <input type="text" name="phone_number" required>
            </div>

            <div class="form-group">
                <label for="password">পাসওয়ার্ড:</label>
                <input type="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">পাসওয়ার্ড নিশ্চিত করুন:</label>
                <input type="password" name="confirm_password" required>
            </div>

            <div class="form-group">
                <label for="role">ভূমিকা:</label>
                <select name="role" required>
                    <option value="farmer" selected>কৃষক</option>
                    <option value="supplier">সরবরাহকারী</option>
                    <option value="customer">গ্রাহক</option>
                    <option value="labour">শ্রমিক</option>
                    <option value="Agrologist">কৃষি বিশেষজ্ঞ</option>
                </select>
            </div>


            <div class="form-group">
                <input type="submit" value="Register">
            </div>
            <div class="back-to-login-text">
                <a href="login.php" style="color: #388e3c; font-weight: 500;">লগইন পেইজে ফিরে যান</a>
            </div>


        </form>
    </div>
</body>
</html>
