<?php

    $page_title = 'Add Credit Card';

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

    if($_SERVER['REQUEST_METHOD'] == 'POST') {

        $errors = array();

        $cc_exists = 0;

        if(empty($_POST['ccNumber'])){
            $errors[] = 'You forgot to enter a credit card number';
        } elseif(!luhn_check(strip_tags(trim($_POST['ccNumber'])))) {
            $errors[] = 'The credit card number is not valid';
        } else {

            $ccNumber = strip_tags(trim($_POST['ccNumber']));

            $query = "SELECT id FROM cc_info WHERE ccNumber = :ccNumber";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':ccNumber', $ccNumber, PDO::PARAM_STR);

            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if($stmt->rowCount() > 0){
                $cc_exists = 1;
            }

            if($cc_exists) {

                $insertQuery = "INSERT INTO purchase_alerts (purchase_alerts_userID, purchase_alerts_ccID, IPAddr, dateTime, alert_type, description) VALUES ( :userid, :ccid, :ipaddr, NOW(), :alertType, :description)";
                $insertStmt = $db->prepare($insertQuery);
                $userid = $_SESSION['user']['id'];
                $ccid = $result['id'];
                $ipaddr= $_SERVER['REMOTE_ADDR'];
				$alertType = "CC_CE";
                $desc = $_SESSION['user']['username'] . ' tried to add an existing credit card in the database';
                $insertStmt->bindParam(':userid', $userid, PDO::PARAM_INT);
                $insertStmt->bindParam(':ccid', $ccid, PDO::PARAM_INT);
                $insertStmt->bindParam(':ipaddr', $ipaddr, PDO::PARAM_STR);
                $insertStmt->bindParam(':description', $desc, PDO::PARAM_STR);
                $insertStmt->bindParam(':alertType', $alertType, PDO::PARAM_STR);
                $insertStmt->execute();

                $errors[] = 'The credit card you entered already exists';
            }
        }

        $user_id = $_SESSION['user']['id'];

        $ccType = strip_tags(trim($_POST['ccType']));

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

                $query = "INSERT INTO cc_info (ccId, ccNumber, first_name, last_name, ccType, exprDate, street, city, state, zipcode)
                            VALUES(:ccId, :ccNumber, :first_name, :last_name, :ccType, :exprDate, :street, :city, :state, :zipcode)";

                $stmt = $db->prepare($query);

                $stmt->bindParam(':ccId', $user_id, PDO::PARAM_INT);
                $stmt->bindParam(':ccNumber', $ccNumber, PDO::PARAM_STR);
                $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
                $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
                $stmt->bindParam(':ccType', $ccType, PDO::PARAM_STR);
                $stmt->bindParam(':exprDate', $exprDate, PDO::PARAM_STR);
                $stmt->bindParam(':street', $street, PDO::PARAM_STR);
                $stmt->bindParam(':city', $city, PDO::PARAM_STR);
                $stmt->bindParam(':state', $state, PDO::PARAM_STR);
                $stmt->bindParam(':zipcode', $zipcode, PDO::PARAM_STR);

                $stmt->execute();

                $success_msg = "The credit card has been added";

            } catch(PDOException $ex){

                echo '<h3>System Error</h3>
                        <p class="text-error">You credit card could not be added to a system error. Please try again later.</p>';

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

<h2>Add Credit Card</h2>
<form class="form-horizontal" action="addcc.php" method="post">
    <div class="control-group">
        <label class="control-label" for="username">Card</label>
        <div class="controls">
            <select name="ccType" class="input-large">
                <option value="VISA" selected="selected">Visa</option>
                <option value="MC">Master Card</option>
                <option value="AMEX">American Express</option>
                <option value="DISC">Discover</option>
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
        <label class="control-label" for="ccNumber">Credit Card</label>
        <div class="controls">
            <input type="text" id="ccNumber" name="ccNumber" placeholder="Credit Card Number" value="">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label">Expiration Date</label>
        <div class="controls">
            <select name="month" class="input-medium">
                <option value='01'>January</option>
                <option value='02'>February</option>
                <option value='03'>March</option>
                <option value='04'>April</option>
                <option value='05'>May</option>
                <option value='06'>June</option>
                <option value='07'>July</option>
                <option value='08'>August</option>
                <option value='09'>September</option>
                <option value='10'>October</option>
                <option value='11'>November</option>
                <option value='12'>December</option>
            </select>
            <select name="day" class="input-mini">
                <option value='01'>01</option>
                <option value='02'>02</option>
                <option value='03'>03</option>
                <option value='04'>04</option>
                <option value='05'>05</option>
                <option value='06'>06</option>
                <option value='07'>07</option>
                <option value='08'>08</option>
                <option value='09'>09</option>
                <option value='10'>10</option>
                <option value='11'>11</option>
                <option value='12'>12</option>
                <option value='13'>13</option>
                <option value='14'>14</option>
                <option value='15'>15</option>
                <option value='16'>16</option>
                <option value='17'>17</option>
                <option value='18'>18</option>
                <option value='19'>19</option>
                <option value='20'>20</option>
                <option value='21'>21</option>
                <option value='22'>22</option>
                <option value='23'>23</option>
                <option value='24'>24</option>
                <option value='25'>25</option>
                <option value='26'>26</option>
                <option value='27'>27</option>
                <option value='28'>28</option>
                <option value='29'>29</option>
                <option value='30'>30</option>
                <option value='31'>31</option>
            </select>
            <input type="text" class="input-small" maxlength="4" name="year" placeholder="2013" value="<?php if (isset($_POST['year'])) echo htmlentities($_POST['year'], ENT_QUOTES, 'UTF-8'); ?>">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="billingAdr">Billing Address</label>
        <div class="controls">
            <input type="text" id="street" name="street" placeholder="Street" maxlength="45">
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

                foreach($state_list as $stateAbv => $stateName){

                    echo '<option value="' . $stateAbv . '">' . $stateName . '</option>';

                }

            echo '</select>';

            ?>
            <input type="text" class="input-mini" maxlength="5" id="zipcode" name="zipcode" placeholder="Zipcode">
        </div>
    </div>
    <div class="control-group">
        <div class="controls">
            <input type="submit" class="btn btn-info" value="Save" />
        </div>
    </div>
</form>

<?php
    include ('./includes/footer.html');
?>
