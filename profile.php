<?php

    $page_title = 'Profile';

    include('includes/header.php');

    require_once("../config.php");

    if($_SERVER['REQUEST_METHOD'] == 'GET') {

        try {

            if(isset($_SESSION['user'])) {

                $username = $_SESSION['user']['username'];

                $query = "SELECT first_name, last_name, email, street, city, state, zipcode FROM customer_info WHERE username = :username LIMIT 1";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':username', $username, PDO::PARAM_STR);

                $stmt->execute();

                if($stmt->rowCount() > 0){
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $first_name = $row['first_name'];
                    $last_name = $row['last_name'];
                    $email = $row['email'];
                    $street = $row['street'];
                    $city = $row['city'];
                    $state = $row['state'];
                    $zipcode = $row['zipcode'];

                    $_SESSION['user']['email'] = $email;
                }

            } else {
                header("Location: index.php");
                exit();
            }

        } catch(PDOException $ex){

            echo '<h3>System Error</h3>
                <p class="text-error">There was an error fetching the user.</p>';

            echo "<small class='text-error'>$ex->getMessage()</p>";
        }

    }

    if(($_SERVER['REQUEST_METHOD'] == 'POST')) {

        $email_exists = 0;

        $errors = array();

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

        if(empty($_POST['street'])){
            $errors[] = 'You forgot to enter a street';
        } else {
            $street = strip_tags(trim($_POST['street']));
        }

        if(empty($_POST['city'])){
            $errors[] = 'You forgot to enter a city';
        } else {
            $city = strip_tags(trim($_POST['city']));
        }

        if(empty($_POST['state']) or (strlen($_POST['state']) != 2)){
            $errors[] = 'You forgot to enter a state';
        } else {
            $state = strip_tags(trim($_POST['state']));
        }

        if(empty($_POST['zipcode']) or (strlen($_POST['zipcode']) != 5) or !is_numeric($_POST['zipcode'])){
            $errors[] = 'You forgot to enter a zipcode';
        } else {
            $zipcode = strip_tags(trim($_POST['zipcode']));
        }

        if(empty($_POST['email'])){
            $errors[] = 'You forgot to enter your email address';
        } elseif(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
            $errors[] = 'Your <b>Email</b> is not valid';
        }
        else {

            $email = strip_tags(trim($_POST['email']));

            if(isset($_SESSION['user']) && ($_SESSION['user']['email'] != $email)){

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

        }

        if(empty($errors)) {

            try {

                $query = "UPDATE customer_info SET first_name = :first_name, last_name = :last_name, email = :email, street = :street, city = :city, state = :state, zipcode = :zipcode WHERE id = :id";
                $stmt = $db->prepare($query);

                $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
                $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->bindParam(':street', $street, PDO::PARAM_STR);
                $stmt->bindParam(':city', $city, PDO::PARAM_STR);
                $stmt->bindParam(':state', $state, PDO::PARAM_STR);
                $stmt->bindParam(':zipcode', $zipcode, PDO::PARAM_STR);
                $stmt->bindParam(':id', $_SESSION['user']['id'], PDO::PARAM_INT);

                $stmt->execute();

                $success_msg = "Your user record has been updated";

            } catch(PDOException $ex){

                echo '<h3>System Error</h3>
                <p class="text-error">There was an error updating the user</p>';

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

<h3>User Profile</h3>
<hr/>
    <form class="form-horizontal" action="profile.php" method="post">
          <div class="control-group">
            <label class="control-label" for="first_name">First Name</label>
            <div class="controls">
                <input type="text" id="first_name" name="first_name" placeholder="First Name" maxlength="25" value="<?php if (isset($first_name)) echo htmlentities($first_name, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="last_name">Last Name</label>
            <div class="controls">
                <input type="text" id="last_name" name="last_name" placeholder="Last Name" maxlength="25" value="<?php if (isset($last_name)) echo htmlentities($last_name, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="email">Email</label>
            <div class="controls">
                <input type="text" id="email" name="email" placeholder="Email Address" maxlength="45" value="<?php if (isset($email)) echo htmlentities($email, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="shippingAddr">Shipping Address</label>
            <div class="controls">
                <input type="text" id="street" name="street" placeholder="Street" maxlength="45" value="<?php if (isset($street)) echo htmlentities($street, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div style="line-height:100%;">
                <br>
            </div>
            <div class="controls">
                <input type="text" id="city" name="city" placeholder="City" maxlength="45" value="<?php if (isset($city)) echo htmlentities($city, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div style="line-height:100%;">
                <br>
            </div>
            <div class="controls">
                <?php
                    $state_list = array('AL'=>"Alabama",
                        'AK'=>"Alaska",
                        'AZ'=>"Arizona",
                        'AR'=>"Arkansas",
                        'CA'=>"California",
                        'CO'=>"Colorado",
                        'CT'=>"Connecticut",
                        'DE'=>"Delaware",
                        'DC'=>"District Of Columbia",
                        'FL'=>"Florida",
                        'GA'=>"Georgia",
                        'HI'=>"Hawaii",
                        'ID'=>"Idaho",
                        'IL'=>"Illinois",
                        'IN'=>"Indiana",
                        'IA'=>"Iowa",
                        'KS'=>"Kansas",
                        'KY'=>"Kentucky",
                        'LA'=>"Louisiana",
                        'ME'=>"Maine",
                        'MD'=>"Maryland",
                        'MA'=>"Massachusetts",
                        'MI'=>"Michigan",
                        'MN'=>"Minnesota",
                        'MS'=>"Mississippi",
                        'MO'=>"Missouri",
                        'MT'=>"Montana",
                        'NE'=>"Nebraska",
                        'NV'=>"Nevada",
                        'NH'=>"New Hampshire",
                        'NJ'=>"New Jersey",
                        'NM'=>"New Mexico",
                        'NY'=>"New York",
                        'NC'=>"North Carolina",
                        'ND'=>"North Dakota",
                        'OH'=>"Ohio",
                        'OK'=>"Oklahoma",
                        'OR'=>"Oregon",
                        'PA'=>"Pennsylvania",
                        'RI'=>"Rhode Island",
                        'SC'=>"South Carolina",
                        'SD'=>"South Dakota",
                        'TN'=>"Tennessee",
                        'TX'=>"Texas",
                        'UT'=>"Utah",
                        'VT'=>"Vermont",
                        'VA'=>"Virginia",
                        'WA'=>"Washington",
                        'WV'=>"West Virginia",
                        'WI'=>"Wisconsin",
                        'WY'=>"Wyoming");

                    echo '<select name="state" class="input-medium" class="span2">';

                        if(isset($state)){
                            foreach($state_list as $stateAbv => $stateName){
                                if($stateAbv == $state){
                                    echo '<option value="' . $stateAbv . '" selected="selected">' . $stateName . '</option>';
                                } else {
                                    echo '<option value="' . $stateAbv . '">' . $stateName . '</option>';
                                }
                            }
                        }

                        echo '</select>';

                    ?>
                <input type="text" class="input-mini" maxlength="5" id="zipcode" name="zipcode" placeholder="Zipcode" value="<?php if (isset($zipcode)) echo htmlentities($zipcode, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
        </div>
        <div class="control-group">
            <div class="controls">
                <input type="Submit" class="btn btn-info" value="Save" />
            </div>
        </div>
    </form>

<br/>

<?php

    include ('./includes/footer.html');
?>
