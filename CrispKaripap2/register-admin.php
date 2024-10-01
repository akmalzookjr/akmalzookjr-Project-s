<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style4.css">
    <title>Register</title>
</head>
<body>
      <div class="container">
      <h2 style="margin-bottom: 20px; font-size: 50px; color: #e4e9f7; text-shadow: 0 0 20px white;">CrispKaripap</h2>
        <div class="box form-box">

        <?php 

        include("php/config.php");

        // Correct code
        $correct_code = "1234";

        if(isset($_POST['submit'])){
            $username = $_POST['username'];
            $email = $_POST['email'];
            $age = $_POST['age'];
            $password = $_POST['password'];
            $code = $_POST['code']; // Get the entered code

            // Check if the entered code matches the correct one
            if($code != $correct_code){
                echo "<div class='message'>
                        <p>Wrong verify code. <br>Please enter the correct code.</p>
                    </div> <br>";
                echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button>";
            } else {
                // Proceed with registration
                
                $verify_query = mysqli_query($con,"SELECT Email FROM admin WHERE Email='$email' UNION SELECT Email FROM users WHERE Email='$email'");

                if(mysqli_num_rows($verify_query) !=0 ){
                    echo "<div class='message'>
                            <p>This email is used, Try another One Please!</p>
                        </div> <br>";
                    echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button>";
                } else {
                    mysqli_query($con,"INSERT INTO admin(Username,Email,Age,Password,Role) VALUES('$username','$email','$age','$password','admin')") or die("Error Occurred");

                    echo "<div class='message'>
                            <p>Registration successful!</p>
                        </div> <br>";
                    echo "<a href='login-admin.php'><button class='btn'>Login Now</button>";
                }
            }
        } else {
        ?>

            <header>Admin Sign Up</header>
            <form action="" method="post">
                <div class="field input">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="age">Age</label>
                    <input type="number" name="age" id="age" autocomplete="off" required>
                </div>
                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" autocomplete="off" required>
                </div>
                <div class="field input">
                    <label for="code">Verify Code</label>
                    <input type="text" name="code" id="code" autocomplete="off" required>
                </div>
                <div class="field">
                    
                    <input type="submit" class="btn" name="submit" value="Register" required>
                </div>
                <div class="links">
                    Already an admin member? <a href="login-admin.php">Sign In</a>
                </div>
            </form>
        </div>
        <?php } ?>
      </div>
</body>
</html>