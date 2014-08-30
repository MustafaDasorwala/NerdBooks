<?php
ob_start();
$page_title = 'Edit Credit Card';

include('includes/header.php');
include('luhn_check.php');

require_once("../config.php");

if(!isset($_SESSION['user'])) {

    header("Location: index.php");
    exit();

} elseif(($_SESSION['user']['is_admin'])){
    header("Location: index.php");
    exit();
}
    if($_SERVER['REQUEST_METHOD'] == 'GET' and (!isset($_GET['id']) or empty($_GET['id'])))  {

        header('Location: viewcc.php');
        exit();

    }

    if($_SERVER['REQUEST_METHOD'] == 'GET' and !empty($_GET['id']))  {

        $cc_does_not_exist = 0;

        $errors = array();

        $id = strip_tags(trim($_GET['id']));

        $query = "SELECT 1 FROM cc_info WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        if($stmt->rowCount() == 0){
            $cc_does_not_exist = 1;
        }

        if($cc_does_not_exist) {
            header("Location: viewcc.php");
            exit();
        }

        if(empty($errors)) {

            try {

                $query = "SELECT * FROM cc_info WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();

                if($stmt->rowCount() > 0){
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $_SESSION['ccType'] = $row['ccType'];
                }

            } catch(PDOException $ex){

                echo '<h3>System Error</h3>
                            <p class="text-error">There was a system error. Please try again later.</p>';

                echo "<small class='text-error'>$ex->getMessage()</small>";
            }

        } else {

            echo '<p>The following error(s) occurred:</p>';

            foreach($errors as $msg) {
                echo "<i class='icon-exclamation-sign'></i> <small class='text-error'>$msg</small><br/>";
            }

        }

    }

    if($_SERVER['REQUEST_METHOD'] == 'POST') {

        $errors = array();

        $_SESSION['cc_id'] = $_POST['id'];

        $user_id = $_SESSION['user']['id'];

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

        if(empty($_POST['year']) or !filter_var($_POST['year'], FILTER_VALIDATE_INT)){
            $errors[] = 'Please enter a valid year';
        } else {
            $year = strip_tags(trim($_POST['year']));
            $day = strip_tags(trim($_POST['day']));
            $month = strip_tags(trim($_POST['month']));

            $input_date_str = $day . "-" . $month . "-" . $year;

            $date = new DateTime($input_date_str);

            $exprDate = date( 'Y-m-d', strtotime($date->format('Y-m-d')));
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

        if(empty($errors)) {

            try {

                $query = "UPDATE cc_info SET ccId = :ccId, first_name = :first_name, last_name = :last_name,
                                  exprDate = :exprDate, street = :street, city = :city, state = :state, zipcode = :zipcode WHERE id = :id";

                $stmt = $db->prepare($query);

                $stmt->bindParam(':ccId', $user_id, PDO::PARAM_INT);
                $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
                $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
                $stmt->bindParam(':exprDate', $exprDate, PDO::PARAM_STR);
                $stmt->bindParam(':street', $street, PDO::PARAM_STR);
                $stmt->bindParam(':city', $city, PDO::PARAM_STR);
                $stmt->bindParam(':state', $state, PDO::PARAM_STR);
                $stmt->bindParam(':zipcode', $zipcode, PDO::PARAM_STR);
                $stmt->bindParam(':id', $_SESSION['cc_id'] , PDO::PARAM_INT);

                $stmt->execute();

                if($stmt->rowCount() > 0 or ($stmt->rowCount() == 0)){
                    $success = true;
                }

            } catch(PDOException $ex){

                echo '<h3>System Error</h3>
                        <p class="text-error">There was a system error. Please try again later.</p>';

                echo "<small class='text-error'>$ex->getMessage()</small>";
            }

        } else {

            echo '<p>The following error(s) occurred:</p>';

            foreach($errors as $msg) {
                echo "<i class='icon-exclamation-sign'></i> <small class='text-error'>$msg</small><br/>";
            }

        }

        if(!empty($success)) {

            header('Location: viewcc.php');
            exit();

        }

    }

?>

<h2>Edit Credit Card</h2> <br />

<form class="form-horizontal" action="editcc.php" method="post">
    <div class="control-group">
        <label class="control-label" for="ccType">Card</label>
        <div class="controls">
            <select name="ccType" class="input-large" disabled>
                <?php

                    $cc_array = [
                        "VISA" => "Visa",
                        "MC" => "Master Card",
                        "AMEX" => "American Express",
                        "DISC" => "Discover"
                    ];

                    if(isset($row) and isset($row['ccType'])){
                        $selCCType = $row['ccType'];
                        echo $selCCType;
                        foreach($cc_array as $ccValue => $ccName){
                            if($ccValue == $selCCType){
                                echo '<option value="' . $ccValue . '" selected="selected">' . $ccName . '</option>';
                            } else {
                                echo '<option value="' . $ccValue . '">' . $ccName . '</option>';
                            }
                        }
                    }

                    if($_SERVER['REQUEST_METHOD'] == 'POST'){
                        $selCCType = $_SESSION['ccType'];
                        foreach($cc_array as $ccValue => $ccName){
                            if($ccValue == $selCCType){
                                echo '<option value="' . $ccValue . '" selected="selected">' . $ccName . '</option>';
                            } else {
                                echo '<option value="' . $ccValue . '">' . $ccName . '</option>';
                            }
                        }
                    }

                ?>

            </select>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="first_name">First Name</label>
        <div class="controls">
            <input type="text" id="first_name" name="first_name" placeholder="First Name" maxlength="25" value="<?php if (isset($_SESSION['user']['first_name'])) echo htmlentities($_SESSION['user']['first_name'], ENT_QUOTES, 'UTF-8'); ?>">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="last_name">Last Name</label>
        <div class="controls">
            <input type="text" id="last_name" name="last_name" placeholder="Last Name" maxlength="25" value="<?php if (isset($_SESSION['user']['last_name'])) echo htmlentities($_SESSION['user']['last_name'], ENT_QUOTES, 'UTF-8'); ?>">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label">Expiration Date</label>
        <div class="controls">
            <?php
                if(isset($row) and isset($row['exprDate'])){

                    $date_array = explode ("-",$row['exprDate']);
                    $year = $date_array[0];
                    $month = $date_array[1];
                    $day = $date_array[2];

                    $month_array = [
                        "01" => "January",
                        "02" => "February",
                        "03" => "March",
                        "04" => "April",
                        "05" => "May",
                        "06" => "June",
                        "07" => "July",
                        "08" => "August",
                        "09" => "September",
                        "10" => "October",
                        "11" => "November",
                        "12" => "December"
                    ];

                echo '<select name="month" class="input-medium">';

                if(isset($row) and isset($row['exprDate'])){
                    foreach($month_array as $monthNum => $monthName){
                        if($monthNum == $month){
                            echo '<option value="' . $monthNum . '" selected="selected">' . $monthName . '</option>';
                        } else {
                            echo '<option value="' . $monthNum . '">' . $monthName . '</option>';
                        }
                    }
                }

            echo '</select>
                <select name="day" class="input-mini">';

                    foreach(range(1,31) as $dayValue){

                        $padDay = str_pad($dayValue, 2, "0", STR_PAD_LEFT);
                        if($day == $dayValue){
                            echo '<option value="' . $padDay . '" selected="selected">' . $padDay . '</option>';
                        } else {
                            echo '<option value="' . $padDay . '">' . $padDay . '</option>';
                        }
                    }
                }

                echo '</select>';
            ?>

            <?php
                    if(isset($_POST['month']) or isset($_POST['day'])){


                        $month_array = [
                            "01" => "January",
                            "02" => "February",
                            "03" => "March",
                            "04" => "April",
                            "05" => "May",
                            "06" => "June",
                            "07" => "July",
                            "08" => "August",
                            "09" => "September",
                            "10" => "October",
                            "11" => "November",
                            "12" => "December"
                        ];

                        echo '<select name="month" class="input-medium">';

                        if(isset($_POST['month'])){
                            foreach($month_array as $monthNum => $monthName){
                                if($monthNum == $_POST['month']){
                                    echo '<option value="' . $monthNum . '" selected="selected">' . $monthName . '</option>';
                                } else {
                                    echo '<option value="' . $monthNum . '">' . $monthName . '</option>';
                                }
                            }
                        }

                        echo '</select>
                        <select name="day" class="input-mini">';

                        if(isset($_POST['day'])){
                            foreach(range(1,31) as $dayValue){

                                $padDay = str_pad($dayValue, 2, "0", STR_PAD_LEFT);
                                if($_POST['day'] == $dayValue){
                                    echo '<option value="' . $padDay . '" selected="selected">' . $padDay . '</option>';
                                } else {
                                    echo '<option value="' . $padDay . '">' . $padDay . '</option>';
                                }
                            }
                        }

                    }

                    echo '</select>';
            ?>

            <input type="text" class="input-small" maxlength="4" name="year" placeholder="2013" value="<?php
                if (isset($row['exprDate'])){
                    echo htmlentities($year, ENT_QUOTES, 'UTF-8');
                } elseif(isset($_POST['year'])){
                    echo htmlentities($_POST['year'], ENT_QUOTES, 'UTF-8');
                }

            ?>">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="billingAdr">Billing Address</label>
        <div class="controls">
            <input type="text" id="street" name="street" placeholder="Street" maxlength="45" value="<?php
                if(isset($row['street'])){
                    echo htmlentities($row['street'], ENT_QUOTES, 'UTF-8');
                } elseif(isset($_POST['street'])){
                    echo htmlentities($_POST['street'], ENT_QUOTES, 'UTF-8');
                }
            ?>">
        </div>
        <div style="line-height:100%;">
            <br>
        </div>
        <div class="controls">
            <input type="text" id="city" name="city" placeholder="City" maxlength="45" value="<?php
                if(isset($row['city'])){
                    echo htmlentities($row['city'], ENT_QUOTES, 'UTF-8');
                } elseif(isset($_POST['city'])){
                    echo htmlentities($_POST['city'], ENT_QUOTES, 'UTF-8');
                }
            ?>">
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

            if(isset($row['state'])){

                foreach($state_list as $stateAbv => $stateName){

                        if($stateAbv == $row['state']){
                            echo '<option value="' . $stateAbv . '" selected="selected">' . $stateName . '</option>';
                        } else {
                            echo '<option value="' . $stateAbv . '">' . $stateName . '</option>';
                        }
                }
            }

            if(isset($_POST['state'])){
                foreach($state_list as $stateAbv => $stateName){
                    if($stateAbv == $_POST['state']){
                        echo '<option value="' . $stateAbv . '" selected="selected">' . $stateName . '</option>';
                    } else {
                        echo '<option value="' . $stateAbv . '">' . $stateName . '</option>';
                    }
                }
            }

            echo '</select>';



            ?>
            <input type="text" class="input-mini" maxlength="5" id="zipcode" name="zipcode" placeholder="Zipcode" value="<?php
            if(isset($row['zipcode'])) {
                echo htmlentities($row['zipcode'], ENT_QUOTES, 'UTF-8');
            } elseif(isset($_POST['zipcode'])){
                echo htmlentities($_POST['zipcode'], ENT_QUOTES, 'UTF-8');
            }

            ?>">
        </div>
    </div>
    <div class="control-group">
        <div class="controls">
            <?php
                if(isset($row['id'])){
                    echo '<input type="hidden" name="id" value="' . $row['id'] . '"/>';
                } else {
                    echo '<input type="hidden" name="id" value="' . $_SESSION['cc_id'] . '"/>';
                }
            ?>
            <input type="submit" class="btn btn-info" value="Save" />
        </div>
    </div>
</form>

<?php
include('includes/footer.html');
?>