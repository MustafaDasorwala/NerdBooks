<?php
$page_title = 'Review Order';

include('includes/header.php');
require_once("../config.php");
include('functions.php');
if(isset($_SESSION['user'])){
    if(isset($_SESSION['cart'])and is_array($_SESSION['cart'])){
        if(isset($_SESSION['checkout']['creditcardid'])){

            //$shipping_address=$_SESSION['checkout']['address'];
            $street=$_SESSION['checkout']['street'];
            $city=$_SESSION['checkout']['city'];
            $state=$_SESSION['checkout']['state'];
            $zipcode=$_SESSION['checkout']['zipcode'];
            $card=$_SESSION['checkout']['creditcardid'];
            $phoneNumber = strip_tags(trim($_POST['phoneNumber']));
            $userid=$_SESSION['user']['id'];
            $amount=0;
            $max=count($_SESSION['cart']);
            $unique_order_number=sha1(rand());
            for($i=0;$i<$max;$i++){
                $product_id=$_SESSION['cart'][$i]['id'];
                $product_name=$_SESSION['cart'][$i]['name'];
                $product_price=$_SESSION['cart'][$i]['price'];
                $product_quantity=$_SESSION['cart'][$i]['quantity'];
                $amount=$amount+($product_price*$product_quantity);
                inserttocheckout($db,$unique_order_number,$_SESSION['user']['id'],$product_id,$product_quantity);
            }

            inserttoorderhistory($db,$userid,$amount,$phoneNumber,$street,$city,$state,$zipcode,$unique_order_number,$card);

            try {

                $query = "SELECT creditcard_id, purchased_date from order_history WHERE userid = :loggedInUser group by creditcard_id, date(purchased_date) having count(distinct phone_number) > 3";

                $stmt = $db->prepare($query);

                $loggedUser = (int) $_SESSION['user']['id'];
                $stmt->bindParam(':loggedInUser', $loggedUser, PDO::PARAM_INT);

                $stmt->execute();

                if($stmt->rowCount() > 0){

                    $userid = $_SESSION['user']['id'];
                    $ipaddr= $_SERVER['REMOTE_ADDR'];
                    $description = $_SESSION['user']['username'] . ' used more than three different phone numbers for a single credit card';
					$alertType = 'CC_MN';

                    $insertQuery = "INSERT INTO purchase_alerts (purchase_alerts_userID, purchase_alerts_ccID, IPAddr, dateTime, alert_type, description) VALUES ( :userid, :ccid, :ipaddr, NOW(), :alertType, :description)";

                    $insertStmt = $db->prepare($insertQuery);

                    $insertStmt->bindParam(':userid', $userid, PDO::PARAM_INT);
                    $insertStmt->bindParam(':ccid', $card, PDO::PARAM_INT);
                    $insertStmt->bindParam(':ipaddr', $ipaddr, PDO::PARAM_STR);
                    $insertStmt->bindParam(':alertType', $alertType, PDO::PARAM_STR);
                    $insertStmt->bindParam(':description', $description, PDO::PARAM_STR);
                    $insertStmt->execute();
                }


                //check if current customer has used more than 4 different credit card today:
                //$query = 'select * from (select userid, purchased_date , creditcard_id from order_history WHERE userid = :loggedInUser and MONTH(purchased_date) = MONTH(NOW()) and DAY(purchased_date) = DAY(NOW()) and YEAR(purchased_date) = YEAR(NOW()) order by purchased_date DESC) as t group by userid, DATE(purchased_date) HAVING count(distinct creditcard_id > 3)';

                $query = 'SELECT userid FROM order_history WHERE userid = :loggedInUser AND DAY(purchased_date) = DAY(NOW()) AND MONTH(purchased_date) = MONTH(NOW()) AND YEAR(purchased_date) = YEAR(NOW()) GROUP BY creditcard_id';
                $stmt = $db->prepare($query);
                $loggedUser = (int) $_SESSION['user']['id'];
                $stmt->bindParam(':loggedInUser', $loggedUser, PDO::PARAM_INT);
                $stmt->execute();
                if($stmt->rowCount() > 3){

                    //need to insert alert here
                    $userId = (int) $_SESSION['user']['id'];
                    $ccId = (int) $_SESSION['checkout']['creditcardid'];
                    $ipAddr = $_SERVER['REMOTE_ADDR'];
                    date_default_timezone_set('America/New_York');
                    $alertTime = date('Y-m-d H:i:s', time());
                    $alertType = 'CT_MC';
                    $alertMsg = 'Customers used more than three different credit cards within one day';

                    $query = 'INSERT INTO purchase_alerts (purchase_alerts_userID, purchase_alerts_ccID, IPAddr, dateTime, alert_type, description) VALUES(:userId, :ccId, :ipAddr, :alertTime, :alertType, :alertMsg)';

                    $stmt = $db->prepare($query);

                    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
                    $stmt->bindParam(':ccId', $ccId, PDO::PARAM_INT);
                    $stmt->bindParam(':ipAddr', $ipAddr, PDO::PARAM_STR);
                    $stmt->bindParam(':alertTime', $alertTime, PDO::PARAM_STR);
                    $stmt->bindParam(':alertType', $alertType, PDO::PARAM_STR);
                    $stmt->bindParam(':alertMsg', $alertMsg, PDO::PARAM_STR);

                    $stmt->execute();

                }


            } catch(PDOException $ex){

                echo '<h3>System Error</h3>
                    <p class="text-error">There was a system error. Please try again later.</p>';

                echo "<small class='text-error'>$ex->getMessage()</small>";
            }

            $max=count($_SESSION['cart']);
            for($i=0;$i<$max;$i++){
                deletefromcart($db,$_SESSION['cart'][$i]['id'],$_SESSION['user']['id']);
                unset($_SESSION['cart'][$i]);
            }
            try {
                $query = "SELECT ccNumber FROM cc_info WHERE id = :cardId";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':cardId', $card, PDO::PARAM_INT);
                $stmt->execute();
                $results = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch(PDOException $ex){

                echo '<h3>System Error</h3>
                        <p class="text-error">There was a system error. Please try again later.</p>';

                echo "<small class='text-error'>$ex->getMessage()</small>";
            }
            if(!empty($success_msg)) {

                echo "<i class='icon-ok'></i> <small class='text-success'>$success_msg</small><br/>";

            }

            echo "Thank you for placing your order. <br>";
            echo "Your order will be shipped in the next twenty-four hours. <br>";

?>
            <FORM METHOD="LINK" ACTION="index.php">
            <INPUT TYPE="submit" class="btn btn-small btn-danger" VALUE="HOME PAGE">
            </FORM>
          <?php
            try {
                $query = "SELECT purchased_date FROM order_history WHERE unique_order_number = :uniquenumber";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':uniquenumber', $unique_order_number, PDO::PARAM_STR);
                $stmt->execute();
                $datetime = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch(PDOException $ex){

                echo '<h3>System Error</h3>
                        <p class="text-error">There was a system error. Please try again later.</p>';

                echo "<small class='text-error'>$ex->getMessage()</small>";
            }
            if(!empty($success_msg)) {

                echo "<i class='icon-ok'></i> <small class='text-success'>$success_msg</small><br/>";

            }


            try {

                $query = "SELECT orders_dir FROM settings";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $dir = $stmt->fetchColumn();

            } catch(PDOException $ex){

                echo '<h3>System Error</h3>
                        <p class="text-error">There was a system error. Please try again later.</p>';

                echo "<small class='text-error'>$ex->getMessage()</small>";
            }

            if(isset($dir) and !empty($dir)){
                $my_file = $dir . '\\' . $unique_order_number.'.txt';
            } else {
                $my_file = $unique_order_number.'.txt';

            }

            $handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file);
            $data = 'Customer Identifying Number:'.$_SESSION['user']['id'].PHP_EOL.'Order Identifying Number:'.$unique_order_number.PHP_EOL.'Credit Card Number:'
                .$results['ccNumber'].PHP_EOL.'Amount:$'.$amount.PHP_EOL.'Order Date:'.$datetime['purchased_date'];

            fwrite($handle, $data);



        }
    }

}



?>
