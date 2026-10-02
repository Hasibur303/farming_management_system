<?php
require_once __DIR__ . '/security/bootstrap.php';
include 'database.php';
require_once __DIR__ . '/security/auth.php';

ensure_login_attempts_table($pdo);

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone_number = trim((string) ($_POST['phone_number'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (login_is_rate_limited($pdo, $phone_number)) {
        security_log('authentication.rate_limited', ['identifier_hash' => login_identifier_hash($phone_number)]);
        http_response_code(429);
        $error = 'Too many failed login attempts. Please try again in 15 minutes.';
    } else {

        $stmt = $pdo->prepare('SELECT * FROM users WHERE phone_number = ? LIMIT 1');
        $stmt->execute([$phone_number]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            record_login_attempt($pdo, $phone_number, true);
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['_created_at'] = time();

            if (password_needs_rehash($user['password'], preferred_password_algorithm())) {
                $newHash = password_hash($password, preferred_password_algorithm());
                $rehash = $pdo->prepare('UPDATE users SET password = ? WHERE user_id = ?');
                $rehash->execute([$newHash, (int) $user['user_id']]);
            }
            security_log('authentication.login_succeeded', [], 'info');

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
            }
             elseif ($role === 'Agrologist') {
                header('Location: agrologist.php');
             }
                         else {
                echo "Invalid role selected.";
            }
            exit();
        } else {
            record_login_attempt($pdo, $phone_number, false);
            security_log('authentication.login_failed', ['identifier_hash' => login_identifier_hash($phone_number)]);
            usleep(random_int(150000, 350000));
            $error = 'Invalid phone number or password.';
        }
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
    <!-- Logo Section -->
        <div style="text-align: center; margin-bottom: 15px;">
            <img src="1.png" alt="SmartKrishi Logo" style="max-width: 100px;">
        </div>
    <h2>ব্যবহারকারী লগইন</h2>
    <?php if (!empty($error)): ?>
        <div class="error"><?= e($error) ?></div>
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
