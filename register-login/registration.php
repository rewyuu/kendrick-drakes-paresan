<?php
session_start();

$fullName = "";
$email = "";
$password = "";
$passwordRepeat = "";

if (isset($_POST["submit"])) {
    require_once "database.php";

    $fullName = $_POST["fullname"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $passwordRepeat = $_POST["repeat_password"];

    $errors = array();

    if (empty($fullName) || empty($email) || empty($password) || empty($passwordRepeat)) {
        array_push($errors, "All fields are required");
    }
    if (strlen($password) < 8) {
        array_push($errors, "Password must be at least 8 characters long");
    }
    if ($password !== $passwordRepeat) {
        array_push($errors, "Passwords do not match");
    }

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        die("SQL statement failed");
    } else {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rowCount = mysqli_num_rows($result);
        if ($rowCount > 0) {
            array_push($errors, "Email already exists!");
        }
    }

    if (count($errors) > 0) {
        $_SESSION["errors"] = $errors;
        header("Location: registration.php");
        exit();
    } else {
        $sql = "INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            die("SQL statement failed");
        } else {
            mysqli_stmt_bind_param($stmt, "sss", $fullName, $email, $password);
            mysqli_stmt_execute($stmt);
            $_SESSION["success"] = "You are registered successfully.";
            header("Location: registration.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <style>
        * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
    font-weight: 700;
}

body {
    font-family: 'Poppins', sans-serif;
    font-size: 1.6rem;
    line-height: 1.6;
    margin: 50px;
    padding: 0;
    background-image: url("./bg/hero-bg.png");
    background-size: cover;
    background-repeat: no-repeat;
    justify-content: center;
    align-items: center;
    height: 100vh;
    overflow: hidden;
}

.container {
    background-color: rgba(255, 255, 255, 0.8);
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 100%;
    margin-top: 100px;
}

.container-title {
    text-align: center;
    margin-bottom: 20px; 
}

.error-container {
    text-align: center;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px; 
}

.form-btn {
    text-align: center;
    margin-top: 20px; 
}

.alert-danger {
    font-size: 16px;
}

.registered {
    margin-top: 40px;
}

.form-control {
    font-weight: 700;
}

#showPassword {
    font-weight: 700;
}

#registerButton {
    font-weight: 700;
}

    </style>
</head>
<body>

    <div class="container">
        <?php
        if (isset($_SESSION["errors"]) && !empty($_SESSION["errors"])) {
            echo "<div class='error-container'>";
            foreach ($_SESSION["errors"] as $error) {
                echo "<div class='alert alert-danger'>$error</div>";
            }
            echo "</div>";
            unset($_SESSION["errors"]);
        }
        if (isset($_SESSION["success"])) {
            echo "<div class='alert alert-success'>{$_SESSION["success"]}</div>";
            unset($_SESSION["success"]);
        }
        ?>
        <form action="registration.php" method="post">
            <div class="container-title">
                <p>SIGN UP</p>
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="fullname" placeholder="Username" value="<?php echo htmlspecialchars($fullName); ?>">
            </div>
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                <button type="button" id="showPassword" class="btn btn-sm btn-outline-secondary">Show Password</button>
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="repeat_password" placeholder="Repeat Password">
            </div>
            <div class="form-btn">
                <input id="registerButton" type="submit" class="btn btn-primary" value="Register" name="submit">
            </div>
        </form>
        <div class="registered">
            <p>Already Registered ?  <a href="login.php"> Click me to Log in</a></p>
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const showPasswordButton = document.getElementById('showPassword');

        showPasswordButton.addEventListener('click', function() {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                showPasswordButton.textContent = 'Hide Password';
            } else {
                passwordInput.type = 'password';
                showPasswordButton.textContent = 'Show Password';
            }
        });
    </script>
</body>
</html>
