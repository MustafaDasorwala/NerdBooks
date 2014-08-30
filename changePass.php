<?php

$page_title = 'Change Password';

include('includes/header.php');

require_once("../config.php");

    if(!isset($_SESSION['user'])){
        header("Location: index.php");
        exit();
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST') {

        $errors = [];
        $pwdHasher = new PasswordHash(8, FALSE);

        if(!empty($_POST['current_pass'])){

            $current_password = strip_tags(trim($_POST['current_pass']));

        } else {
            $errors[] = 'You forgot to enter your current password';
        }

        if(!empty($_POST['new_pass1'])){

            if(strlen($_POST['new_pass1']) < 6) {
                $errors[] = 'Your password must be atleast <b>six</b> characters';
            } elseif($_POST['new_pass1'] != $_POST['new_pass2']){
                $errors[] = 'Your password fields don\'t match';
            } else {

                $new_password = strip_tags(trim($_POST['new_pass1']));
                $new_password_hash = $pwdHasher->HashPassword( $new_password );

            }
        } else {
            $errors[] = 'You forgot to enter your new password';
        }

        try {

            $query = "SELECT password FROM customer_info WHERE id = :id";
            $stmt = $db->prepare($query);

            $stmt->bindParam(':id', $_SESSION['user']['id'], PDO::PARAM_INT);

            $stmt->execute();

            if($stmt->rowCount() > 0){
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $db_pass = $row['password'];

                $checked = $pwdHasher->CheckPassword($current_password, $db_pass);

                if(!$checked) {
                    $errors[] = 'Your current password is invalid';
                }
            }

        } catch(PDOException $ex){

            echo '<h3>System Error</h3>
                <p class="text-error">There was an error changing your password</p>';

            echo "<small class='text-error'>$ex->getMessage()</small>";
        }

        if(empty($errors)) {

            try {

                $query = "UPDATE customer_info SET password = :password WHERE id = :id";
                $stmt = $db->prepare($query);

                $stmt->bindParam(':password', $new_password_hash, PDO::PARAM_STR);
                $stmt->bindParam(':id', $_SESSION['user']['id'], PDO::PARAM_INT);

                $stmt->execute();

                $success_msg = "Your password has been updated";

            } catch(PDOException $ex){

                echo '<h3>System Error</h3>
                <p class="text-error">There was an error changing your password</p>';

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

        echo '<p>Thank You!</p>';

        echo "<i class='icon-ok'></i> <small class='text-success'>$success_msg</small><br/>";

    }

?>


<h3>Change Password</h3>
<hr/>
<form class="form-horizontal" action="changepass.php" method="post">
    <div class="control-group">
        <label class="control-label" for="current_pass">Current Password</label>
        <div class="controls">
            <input type="password" id="current_pass" name="current_pass" placeholder="Current Password">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="new_pass1">New Password</label>
        <div class="controls">
            <input type="password" id="new_pass1" name="new_pass1" placeholder="New Password">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="new_pass2">Confirm Password</label>
        <div class="controls">
            <input type="password" id="new_pass2" name="new_pass2" placeholder="Confirm Password">
        </div>
    </div>
    <input type="hidden" name="id" value="<?php if (isset($id)) echo htmlentities($id, ENT_QUOTES, 'UTF-8'); ?>">
    <div class="control-group">
        <div class="controls">
            <input type="Submit" class="btn btn-info" value="Save" />
        </div>
    </div>
</form>

<?php

    include ('./includes/footer.html');
?>
