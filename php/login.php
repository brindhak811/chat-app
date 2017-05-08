<?php

/*
 * Brings up login page for users to sign in to SCU Chat system.
 * Also handles the POST event of form submission.
 */
    
include_once 'header.php';
include_once 'UserProfile.php';  
?>

<script type="text/javascript">
    createCookie("scuchatinfo", "0", 1);
</script>

<div class="login-container">
    <div class="login-title">
        <h3 class="hdr-title">SCU Chat</h3>
    </div>
    <div class="login-box">
        <form method="post" name="login" action="">
            <input id="Username" name="username" type="text" placeholder="Email Address" class="">
            <input id="Passwd" name="passwd" type="password" placeholder="Password" class="">
            <input class="LoginRegisterBtn" name="LoginRegisterBtn" type="button" value="Register" onclick="location.href='register.php'">
            <input class="LoginBtn" name="LoginBtn" type="button" value="Login" onclick="userLogin()">
            <input type="hidden" name="email"/>
            <input type="hidden" name="password"/>
        </form>
    </div>
</div>

<?php include_once 'footer.php'; ?>