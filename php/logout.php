<?php

/*
 * Logs out of the session
 */
    
include_once 'header.php';
session_start();
?>

<script type="text/javascript">eraseCookie("scuchatinfo");</script>

<?php
if (isset($_SESSION['user'])) {
    $user_id = $_SESSION['user']['user_id'];
    $query = "DELETE FROM scuchat_active_users WHERE user_id = $user_id";
    $result = mysqli_query($ch, $query) or die("QUERY ERROR:" . mysqli_error($ch));
    unset($_SESSION);
    session_destroy();
}
?>
<div class="logout-container">
<div class="login-title">
<h3 class="hdr-title">SCU Chat</h3>
</div>
<div class="logout-box">
<p class="logout-title">You have been logged out successfully.<br>Click <a href="login.php">here</a> to return to login page.</p>
</div>
</div>
<?php include_once 'footer.php'; ?>