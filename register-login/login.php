<?php
session_start();
if (isset($_SESSION["user"])) {
   header("Location: index.php");
   exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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

        .container-title {
            text-align: center;
            margin-bottom: 20px; 
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

        #loginButton {
            font-weight: 700;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="container-title">
            <p>LOG IN</p>
        </div>
        <?php
        if (isset($_POST["login"])) {
           $username = $_POST["username"];
           $password = $_POST["password"];
            require_once "database.php";
            $sql = "SELECT * FROM users WHERE full_name = ?";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                die("SQL statement failed");
            } else {
                mysqli_stmt_bind_param($stmt, "s", $username);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
                if ($user) {
                    if ($password === $user["password"]) {
                        session_start();
                        $_SESSION["user"] = $user["full_name"];
                        $_SESSION["user_role"] = $user["full_name"] === 'admin' ? 'admin' : 'user';
                        if ($_SESSION["user_role"] === 'admin') {
                            header("Location: admin.php");
                        } else {
                            header("Location: index.php");
                        }
                        exit();
                    } else {
                        echo "<div class='alert alert-danger'>Password does not match</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger'>Username does not exist</div>";
                }
            }
        }
        ?>
      <form action="login.php" method="post">
        <div class="form-group">
            <input type="text" placeholder="Enter Username" name="username" class="form-control" required>
        </div>
        <div class="form-group">
            <input type="password" placeholder="Enter Password" name="password" id="password" class="form-control" required>
            <button type="button" id="showPassword" class="btn btn-sm btn-outline-secondary">Show Password</button>
        </div>
        <div class="form-btn">
            <input type="submit" id="loginButton" value="Login" name="login" class="btn btn-primary">
        </div>
      </form>
     <div class="registered"><p>Not registered yet ? <a href="registration.php">Register Here</a></p></div>
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