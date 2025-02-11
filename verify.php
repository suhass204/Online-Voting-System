<?php
session_start();
require_once("admin/inc/config.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $otp_code = $_POST['otp_code'];

    $stmt = $db->prepare("SELECT otp_code FROM otps WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($stored_otp_code);
    $stmt->fetch();
    $stmt->close();

    if ($otp_code == $stored_otp_code) {
        $stmt = $db->prepare("UPDATE users SET is_verified = TRUE WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        echo "<script>alert('Sign up successful!');</script>";
        header("Refresh: 0.5; url=index.php");
    } else {

        echo "<script>alert('Invalid OTP.Please try again!');</script>";
        header("Refresh: 0.5; url=verify.php?user_id=$user_id");
    }
} else {
    $user_id = $_GET['user_id'];
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP</title>
    <link rel="stylesheet" href="assert/css/bootstrap.min.css">
    <link rel="stylesheet" href="assert/css/style.css">
    <link rel="stylesheet" href="assert/css/login.css">
</head>
<body>
<div class="container">
    <div class="d-flex justify-content-center h-100" style="padding-top: 150px;">
        <div class="card">
            <div class="card-header bg-green">
                <h3>Verify OTP</h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="input-group form-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text bg-green"><i class="fas fa-key"></i></span>
                        </div>
                        <input type="text" name="otp_code" class="form-control" placeholder="Enter OTP" required/>
                        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>"/>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Verify" name="verify_otp_btn" class="btn float-right login_btn">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>

