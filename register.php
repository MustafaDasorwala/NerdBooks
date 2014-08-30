<?php

    $page_title = 'Register';

    include('includes/header.php');

    require_once("../config.php");

    if($_SERVER['REQUEST_METHOD'] == 'POST') {

        $user_exists = 0;
        $email_exists = 0;

        $errors = array();

        if(empty($_POST['username'])){
            $errors[] = 'You forgot to enter a username</b>';
        } else {

            $username = strip_tags(trim($_POST['username']));

            $query = "SELECT 1 FROM customer_info WHERE username = :username";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);

            $stmt->execute();

            if($stmt->rowCount() > 0){
                $user_exists = 1;
            }

            if($user_exists) {
                $errors[] = 'The username you entered has already been taken';
            }
        }

        if(empty($_POST['first_name'])){
            $errors[] = 'You forgot to enter your first name';
        } else {
            $first_name = strip_tags(trim($_POST['first_name']));
        }

        if(empty($_POST['last_name'])){
            $errors[] = 'You forgot to enter your last name';
        } else {
            $last_name = strip_tags(trim($_POST['last_name']));
        }

        if(empty($_POST['email'])){
            $errors[] = 'You forgot to enter an email address';
        } elseif(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
            $errors[] = 'Your email address is not valid';
        }
        else {
            $email = strip_tags(trim($_POST['email']));

            $query = "SELECT 1 FROM customer_info WHERE email = :email";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);

            $stmt->execute();

            if($stmt->rowCount() > 0){
                $email_exists = 1;
            }

            if($email_exists) {
                $errors[] = 'The email address you entered has already been taken';
            }
        }

        if(!empty($_POST['pass1'])){

            if(strlen($_POST['pass1']) < 6) {
                $errors[] = 'Your password must be atleast <b>six</b> characters';
            } elseif($_POST['pass1'] != $_POST['pass2']){
                $errors[] = 'Your password fields don\'t match';
            } else {
                $pwdHasher = new PasswordHash(8, FALSE);
                $password = strip_tags(trim($_POST['pass1']));
                $password_hash = $pwdHasher->HashPassword( $password );

            }
        } else {
            $errors[] = 'You forgot to enter your password';
        }

        if(empty($_POST['ageConfirm'])){
            $errors[] = 'You forgot to confirm that you are 18 or older';
        }

        if(empty($errors)) {

            try {

                $query = "INSERT INTO customer_info(username, first_name, last_name, email, password, registration_date)
                    VALUES(:username, :first_name, :last_name, :email, :password, NOW())";

                $stmt = $db->prepare($query);

                $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
                $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->bindParam(':password', $password_hash, PDO::PARAM_STR);

                $stmt->execute();

                echo '<h3>Thank you!</h3>
                <p>You are now registered and can proceed to login.</p>
                <p><br/></p>';

            } catch(PDOException $ex){

                echo '<h3>System Error</h3>
                <p class="text-error">You could not be registered due to a system error. Please try again later.</p>';

                echo "<small class='text-error'>$ex->getMessage()</small>";
            }

        } else {

            echo '<p>The following error(s) occurred:</p>';

            foreach($errors as $msg) {
                echo "<i class='icon-exclamation-sign'></i> <small class='text-error'>$msg</small><br/>";
            }

        }
    }

    if(isset($_SESSION['user'])) {

        header("Location: index.php");
        exit();

    }

?>

<h2>Registration</h2> <br />

    <form class="form-horizontal" action="register.php" method="post">
        <div class="control-group">
            <label class="control-label" for="username">Username</label>
            <div class="controls">
                <input type="text" id="username" name="username" placeholder="Username" maxlength="15" value="<?php if (isset($_POST["username"])) echo htmlentities($_POST["username"], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="first_name">First Name</label>
            <div class="controls">
                <input type="text" id="first_name" name="first_name" placeholder="First Name" maxlength="25" value="<?php if (isset($_POST["first_name"])) echo htmlentities($_POST["first_name"], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="last_name">Last Name</label>
            <div class="controls">
                <input type="text" id="last_name" name="last_name" placeholder="Last Name" maxlength="25" value="<?php if (isset($_POST["last_name"]))echo htmlentities($_POST["last_name"], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="email">Email</label>
            <div class="controls">
                <input type="text" id="email" name="email" placeholder="Email Address" maxlength="45" value="<?php if (isset($_POST["email"])) echo htmlentities($_POST["email"], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="pass1">Password</label>
            <div class="controls">
                <input type="password" id="pass1" name="pass1" placeholder="Password">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="pass2">Confirm Password</label>
            <div class="controls">
                <input type="password" id="pass2" name="pass2" placeholder="Confirm Password">
            </div>
        </div>
        <div class="control-group">
            <div class="controls">
                <label class="checkbox">
                    <input type="checkbox" name="ageConfirm"> 18 Years or Older?
                </label><br/>
                <input type="submit" class="btn btn-info" value="Register" />
            </div>
        </div>
    </form>

<?php
    include ('./includes/footer.html');
?>
