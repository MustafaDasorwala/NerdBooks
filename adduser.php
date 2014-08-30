<?php

$page_title = 'Add User';

include('includes/header.php');

require_once("../config.php");


    if(!isset($_SESSION['user'])) {

        header("Location: index.php");
        exit();

    } elseif(!($_SESSION['user']['is_admin'])){
        header("Location: index.php");
        exit();
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST') {

        $user_exists = 0;
        $email_exists = 0;

        $errors = array();

        if(($_POST['role'] == '0') or ($_POST['role'] == '1')) {
            $role = strip_tags(trim($_POST['role']));;
        } else {
            $errors[] = "A role must be either an user or admin";
        }


        if(empty($_POST['username'])){
            $errors[] = 'You forgot to enter a username';
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
            $errors[] = 'You forgot to enter a first name';
        } else {
            $first_name = strip_tags(trim($_POST['first_name']));
        }

        if(empty($_POST['last_name'])){
            $errors[] = 'You forgot to enter a last name';
        } else {
            $last_name = strip_tags(trim($_POST['last_name']));
        }

        if(empty($_POST['shipping_address'])){
            $errors[] = 'You forgot to enter a shipping address';
        } else {
            $last_name = strip_tags(trim($_POST['shipping_address']));
        }

        if(empty($_POST['email'])){
            $errors[] = 'You forgot to enter an email address';
        } elseif(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
            $errors[] = 'The email address is not valid';
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
                $errors[] = 'The email address you entered already exists';
            }
        }

        if(!empty($_POST['pass1'])){

            if(strlen($_POST['pass1']) < 6) {
                $errors[] = 'The password must be atleast <b>six</b> characters';
            } elseif($_POST['pass1'] != $_POST['pass2']){
                $errors[] = 'The password fields don\'t match';
            } else {
                $pwdHasher = new PasswordHash(8, FALSE);
                $password = strip_tags(trim($_POST['pass1']));
                $password_hash = $pwdHasher->HashPassword( $password );

            }
        } else {
            $errors[] = 'You forgot to enter the password';
        }

        if(empty($_POST['ageConfirm'])){
            $errors[] = 'The user must be 18 or older';
        }

        if(empty($errors)) {

            try {

                $query = "INSERT INTO customer_info(username, first_name, last_name, email, is_admin, password, shipping_address, registration_date)
                        VALUES(:username, :first_name, :last_name, :email, :is_admin, :password, :shipping_address, NOW())";

                $stmt = $db->prepare($query);

                $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
                $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->bindParam(':is_admin', $role, PDO::PARAM_INT);
                $stmt->bindParam(':password', $password_hash, PDO::PARAM_STR);
                $stmt->bindParam(':shipping_address', $shipping_address, PDO::PARAM_STR);

                $stmt->execute();

                $success_msg = "The user record has been added";

            } catch(PDOException $ex){

                echo '<h3>System Error</h3>
                    <p class="text-error">The user could not be registered due to a system error. Please try again later.</p>';

                echo "<small class='text-error'>$ex->getMessage()</small>";
            }

        } else {

            echo '<p>The following error(s) occurred:</p>';

            foreach($errors as $msg) {
                echo "<i class='icon-exclamation-sign'></i> <small class='text-error'>$msg</small><br/>";
            }

        }
    }

    if(!empty($success_msg)) {

        echo "<i class='icon-ok'></i> <small class='text-success'>$success_msg</small><br/>";

    }

?>

<h2>Add User</h2> <br />

<form class="form-horizontal" action="adduser.php" method="post">
    <div class="control-group">
        <label class="control-label" for="username">Role</label>
        <div class="controls">
            <select name="role">
                <option value="0" selected="selected">User</option>
                <option value="1">Admin</option>
            </select>
        </div>
    </div>
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
        <label class="control-label" for="shippingAddr">Shipping Address</label>
        <div class="controls">
            <input type="text" id="street" name="street" placeholder="Street" maxlength="45" value="<?php if (isset($shippingAddr)) echo htmlentities($shippingAddr, ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div style="line-height:100%;">
            <br>
        </div>
        <div class="controls">
            <input type="text" id="city" name="city" placeholder="City" maxlength="45">
        </div>
        <div style="line-height:100%;">
            <br>
        </div>
        <div class="controls">
            <select name="state" class="input-medium" class="span2">
                <option value="AL" selected="selected">Alabama</option>
                <option value="AK">Alaska</option>
                <option value="AZ">Arizona</option>
                <option value="AR">Arkansas</option>
                <option value="CA">California</option>
                <option value="CO">Colorado</option>
                <option value="CT">Connecticut</option>
                <option value="DE">Delaware</option>
                <option value="DC">District Of Columbia</option>
                <option value="FL">Florida</option>
                <option value="GA">Georgia</option>
                <option value="HI">Hawaii</option>
                <option value="ID">Idaho</option>
                <option value="IL">Illinois</option>
                <option value="IN">Indiana</option>
                <option value="IA">Iowa</option>
                <option value="KS">Kansas</option>
                <option value="KY">Kentucky</option>
                <option value="LA">Louisiana</option>
                <option value="ME">Maine</option>
                <option value="MD">Maryland</option>
                <option value="MA">Massachusetts</option>
                <option value="MI">Michigan</option>
                <option value="MN">Minnesota</option>
                <option value="MS">Mississippi</option>
                <option value="MO">Missouri</option>
                <option value="MT">Montana</option>
                <option value="NE">Nebraska</option>
                <option value="NV">Nevada</option>
                <option value="NH">New Hampshire</option>
                <option value="NJ">New Jersey</option>
                <option value="NM">New Mexico</option>
                <option value="NY">New York</option>
                <option value="NC">North Carolina</option>
                <option value="ND">North Dakota</option>
                <option value="OH">Ohio</option>
                <option value="OK">Oklahoma</option>
                <option value="OR">Oregon</option>
                <option value="PA">Pennsylvania</option>
                <option value="RI">Rhode Island</option>
                <option value="SC">South Carolina</option>
                <option value="SD">South Dakota</option>
                <option value="TN">Tennessee</option>
                <option value="TX">Texas</option>
                <option value="UT">Utah</option>
                <option value="VT">Vermont</option>
                <option value="VA">Virginia</option>
                <option value="WA">Washington</option>
                <option value="WV">West Virginia</option>
                <option value="WI">Wisconsin</option>
                <option value="WY">Wyoming</option>
            </select>
            <input type="text" class="input-mini" maxlength="5" id="zipcode" name="zipcode" placeholder="Zipcode">
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
