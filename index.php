<?php 
    require_once("admin/inc/config.php");

    $fetchingElections = mysqli_query($db, "SELECT * FROM elections") OR die(mysqli_error($db));
    while($data = mysqli_fetch_assoc($fetchingElections))
    {
        $stating_date = $data['starting_date'];
        $ending_date = $data['ending_date'];
        $curr_date = date("Y-m-d");
        $election_id = $data['id'];
        $status = $data['status'];


        if($status == "Active")
        {
            $date1=date_create($curr_date);
            $date2=date_create($ending_date);
            $diff=date_diff($date1,$date2);
            
            if((int)$diff->format("%R%a") < 0)
            { 
                mysqli_query($db, "UPDATE elections SET status = 'Expired' WHERE id = '". $election_id ."'") OR die(mysqli_error($db));
            }
        }else if($status == "InActive")
        {
            $date1=date_create($curr_date);
            $date2=date_create($stating_date);
            $diff=date_diff($date1,$date2);
            

            if((int)$diff->format("%R%a") <= 0)
            {
                mysqli_query($db, "UPDATE elections SET status = 'Active' WHERE id = '". $election_id ."'") OR die(mysqli_error($db));
            }
        }
        

    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login-Online Voting System</title>
    <link rel="stylesheet" href="assert/css/bootstrap.min.css">
    <link rel="stylesheet" href="assert/css/login.css">
    <link rel="stylesheet" href="assert/css/style.css">
</head>
<body>
<div class="container">
    <div class="d-flex justify-content-center h-100">
        <div class="card">
            <div class="card-header">
                <h3>Online Voting</h3>
                <div class="d-flex justify-content-end social_icon">
                    <span><i class="fab fa-facebook-square"></i></span>
                    <span><i class="fab fa-google-plus-square"></i></span>
                    <span><i class="fab fa-twitter-square"></i></span>
                </div>
            </div>

            
            <?php
            require 'vendor/autoload.php';
            use PHPMailer\PHPMailer\PHPMailer;
            use PHPMailer\PHPMailer\Exception;

            
            require_once("admin/inc/config.php");

            if (isset($_GET['sign-up'])) {
            ?>

            <div class="card-body">
                <form method="POST">
                    <div class="input-group form-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" name="su_username" class="form-control" placeholder="Username" required/>
                    </div>

                    <div class="input-group form-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                        </div>
                        <input type="text" name="su_aadhar_no" class="form-control" placeholder="Aadhar Number" required/>
                    </div>

                    <div class="input-group form-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        </div>
                        <input type="email" name="su_email" class="form-control" placeholder="Email" required/>
                    </div>

                    <div class="input-group form-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                        </div>
                        <input type="password" name="su_password" class="form-control" placeholder="Password" required/>
                    </div>


                    <div class="form-group">
                        <input type="submit" value="Sign Up" name="sign_up_btn" class="btn float-right login_btn">
                    </div>
                </form>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-center links">
                    Already have an account? <a href="index.php">Sign In</a>
                </div>
            </div>

            <?php
            } else {
            ?>

            <div class="card-body">
                <form method="POST">
                    <div class="input-group form-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" name="aadhar_no" placeholder="Aadhar Number" required/>
                    </div>
                    <div class="input-group form-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                        </div>
                        <input type="password" class="form-control" name="password" placeholder="Password" required/>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="loginbtn" value="Login" class="btn float-right login_btn">
                    </div>
                </form>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-center links">
                    Don't have an account? <a href="?sign-up=1">Sign Up</a>
                </div>
            </div>

            <?php
            }
            ?>

<?php
	if(isset($_GET['registered']))
	{
	?>
		<span class="bg-white text-success text-center my-3">Your account has been created successfully! </span>
	<?php
	}else if(isset($_GET['invalid'])){
	?>
	<span class="bg-white text-danger text-center my-3">Password mismatched, Please try again </span>
	<?php
	}else if(isset($_GET['already_registered'])){
	?>
	<span class="bg-white text-danger text-center my-3">You already have an account</span>
	<?php
	}else if(isset($_GET['not_registered'])){
	?>
	<span class="bg-white text-warning text-center my-3">Sorry, you are not registered!</span>
	<?php
	}else if(isset($_GET['invalid_access'])){
		?>
		<span class="bg-white text-danger text-center my-15">Invalid user name or password!</span>
		<?php
	}
	?>

        </div>
    </div>
</div>

<script src="assert/js/jquery.min.js"></script>
<script src="assert/js/bootstrap.min.js"></script>
</body>
</html>

<?php
require_once("admin/inc/config.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['sign_up_btn'])) {
    $su_username = mysqli_real_escape_string($db, $_POST['su_username']);
    $su_aadhar_no = mysqli_real_escape_string($db, $_POST['su_aadhar_no']);
    $su_password = mysqli_real_escape_string($db, sha1($_POST['su_password']));
    $email = mysqli_real_escape_string($db, $_POST['su_email']);
    $user_role = "Voter";

    $check_query = mysqli_query($db, "SELECT * FROM users WHERE aadhar_no = '$su_aadhar_no'");
    if (mysqli_num_rows($check_query) > 0) {
        echo "<script> location.assign('index.php?sign-up=1&already_registered=1');</script>";
    } else {

        
        $stmt = $db->prepare("INSERT INTO users (username, aadhar_no, password, email, user_role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $su_username, $su_aadhar_no, $su_password, $email, $user_role);
        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;
            $otp_code = rand(100000, 999999);

            $stmt = $db->prepare("INSERT INTO otps (user_id, otp_code) VALUES (?, ?)");
            $stmt->bind_param("is", $user_id, $otp_code);
            $stmt->execute();

            $mail = new PHPMailer(true);

            try {
                $mail->SMTPDebug = 0;
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'shettyvidyashree31@gmail.com';
                $mail->Password = 'sdlp xxxd bzhe dscv';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('shettyvidyashree31@gmail.com', 'Online Voting');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Your OTP Code';
                $mail->Body = 'Your OTP code is ' . $otp_code . '.';

                $mail->send();
                header("Location: verify.php?user_id=$user_id");
                exit();
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}  elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['loginbtn'])) {
    $aadhar_no = mysqli_real_escape_string($db, $_POST['aadhar_no']);
    $password = mysqli_real_escape_string($db, sha1($_POST['password']));

    $fetchingData = mysqli_query($db, "SELECT * FROM users WHERE aadhar_no='$aadhar_no'");

    if (mysqli_num_rows($fetchingData) > 0) {
        $data = mysqli_fetch_assoc($fetchingData);

        if ($aadhar_no==$data['aadhar_no'] AND $password==$data['password']) {
            session_start();
            $_SESSION['user_role'] = $data['user_role'];
            $_SESSION['username'] = $data['username'];
            $_SESSION['user_id'] = $data['id'];

            if ($data['user_role'] == "Admin") {
                $_SESSION['key'] = "AdminKey";
                ?>
						<script>location.assign("admin/index.php?homepage=1");</script>
					<?php
            } else {
                $_SESSION['key'] = "VotersKey";
                ?>
					<script>location.assign("voters/index.php");</script>
					<?php
            }
        } else {
            ?>
				<script> location.assign("index.php?invalid_access=1");</script>
				<?php
        }
    } else {
        ?>
			<script> location.assign("index.php?sign-up=1&not_registered=1");</script>
			<?php
    }
}
?>