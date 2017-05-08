<?php

/*
 * Brings up registration page for users to create account
 */
    
include_once 'header.php';
include_once 'UserProfile.php'; 
?>

<div class="register-container">
    <div class="register-title">
        <h3 class="hdr-title">SCU Chat Register</h3>
    </div>
    <div class="register-box">
        <form method="post" name="register" action="">
            <input class="InputText" id="FirstName" name="fname" type="text" placeholder="First Name">
            <input class="InputText" id="LastName" name="lname" type="text" placeholder="Last Name">
            <input class="InputText" id="Email" name="emailaddress" type="text" placeholder="Email Address">
            <input class="InputText" id="Password" name="passwd" type="password" placeholder="Password">
            <input class="RegisterBtn" name="RegisterBtn" type="button" value="Register" onclick="validateRegisterForm()">
            <input type="hidden" name="firstname"/>
            <input type="hidden" name="lastname"/>
            <input type="hidden" name="email"/>
            <input type="hidden" name="password"/>
        </form>
    </div>
<?php
    if (!empty($_POST)) { // check that form was submitted
        extract($_POST);
        $user_profile = new UserProfile();  // Instance of UserProfile class
        $user_profile->setAttributes($firstname, $lastname, $email, $password);
        $user_profile->insert($ch); // inserts user record
        echo "<p id='register_ack_text'>Thank you for registering in SCU Chat. <br>Please click <a href='login.php'>here</a> to login.</p>";
    }
?>
</div>

<?php include_once 'footer.php'; ?>
