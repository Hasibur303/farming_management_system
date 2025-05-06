<?php
session_start();
include 'database.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone_number = $_POST['phone_number'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE phone_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $phone_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            $role = $user['role'];
            if ($role === 'Admin') {
                header('Location: admin/admin.php');
            } elseif ($role === 'Farmer') {
                header('Location: farmer.php');
            } elseif ($role === 'Customer') {
                header('Location: customer.php');
            } elseif ($role === 'Investor') {
                header('Location: investor.php');
            } elseif ($role === 'Supplier') {
                header('Location: supplier.php');
            } elseif ($role === 'Labour') {
                header('Location: labour.php');
            } else {
                echo "Invalid role selected.";
            }
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No user found with that phone number.";
    }
}
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartAgri Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
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
            max-width: 420px;
            width: 100%;
            transition: 0.3s ease-in-out;
        }

        .form-container:hover {
            box-shadow: 0 25px 55px rgba(0,0,0,0.15);
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #2e7d32;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
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

        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
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
    <h2>ব্যবহারকারী লগইন</h2>
    <?php if (!empty($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="POST" action="login.php">
        <div class="form-group">
            <label for="phone_number">ফোন নম্বর:</label>
            <input type="text" name="phone_number" required>
        </div>

        <div class="form-group">
            <label for="password">পাসওয়ার্ড:</label>
            <input type="password" name="password" required>
        </div>

        <div class="form-group">
            <input type="submit" value="লগইন">
        </div>
    </form>

    <div class="register-link">
        <p>কোন অ্যাকাউন্ট নেই? <a href="register.php">এখানে নিবন্ধন করুন</a></p>
    </div>

    <div class="back-link">
        <p><a href="dashboard.php">ড্যাশবোর্ডে ফিরে যান</a></p>
    </div>
</div>

</body>
</html>
