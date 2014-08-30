<?php

$page_title = 'Review Order';

include('includes/header.php');

require_once("../config.php");

if(isset($_SESSION['user'])){
	if(isset($_SESSION['cart'])and is_array($_SESSION['cart'])){
		if($_SERVER['REQUEST_METHOD'] == 'GET' and isset($_GET['action']) and !empty($_GET['action']) and ($_GET['action'] == 'card') and isset($_GET['id']) and !empty($_GET['id']))  {
			$_SESSION['checkout']['creditcardid']=intval(strip_tags(trim($_GET['id'])));
			try {

				$query = "SELECT * FROM cc_info WHERE ccId = :userId and cc_info.id=:cardId LIMIT 1";
				$stmt = $db->prepare($query);
				$stmt->bindParam(':userId', $_SESSION['user']['id'], PDO::PARAM_INT);
				$stmt->bindParam(':cardId', $_SESSION['checkout']['creditcardid'], PDO::PARAM_INT);
				$stmt->execute();
				$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
			} catch(PDOException $ex){

				echo '<h3>System Error</h3>
					<p class="text-error">There was a system error. Please try again later.</p>';

				echo "<small class='text-error'>$ex->getMessage()</small>";
			}
			if($stmt->rowCount() > 0){
				try {
					//echo 'cardID='. $_SESSION['checkout']['creditcardid'];
					$query = "SELECT * FROM cc_hotlist WHERE ccHotList_Id = :cardId";
					$stmt = $db->prepare($query);
					$stmt->bindParam(':cardId', $_SESSION['checkout']['creditcardid'], PDO::PARAM_INT);
					$stmt->execute();
					if($stmt->rowCount()==0)
					{
?>
						<h3>Review Order</h3></br>
						<table align="center" class="table table-bordered table-striped">
						<tr class="active">
							<th>Item</th>
							<th>Price</th>
							<th>Quantity</th>
							<th>Subtotal</th>
						</tr>

<?php
						$max=count($_SESSION['cart']);
                        $sum = 0;
						for($i=0;$i<$max;$i++){
							$product_id=$_SESSION['cart'][$i]['id'];
							$product_name=$_SESSION['cart'][$i]['name'];
							$product_price=$_SESSION['cart'][$i]['price'];
							$product_quantity=$_SESSION['cart'][$i]['quantity'];
							$Total_price=$product_quantity*$product_price;
                            $sum = $sum + $Total_price;

							//Add high purchase event to alerts
							try {

								$query = "SELECT 5*AVG(price) AS avgItemPrice FROM inventory";
								$stmt = $db->prepare($query);
								$stmt->execute();
								$avgItemPrice = $stmt->fetchColumn();

								$query = "SELECT purchase_threshold FROM settings";
								$stmt = $db->prepare($query);
								$stmt->execute();

								if($stmt->rowCount() == 0){

									$priceToCheck = $avgItemPrice;

								} else {

									$query = "SELECT purchase_threshold FROM settings";
									$stmt = $db->prepare($query);
									$stmt->execute();

									$priceToCheck = $stmt->fetchColumn();
								}

								//echo $Total_price;
								//echo $priceToCheck;

								if($Total_price > $priceToCheck){

									$userId = (int) $_SESSION['user']['id'];
									$ccId = (int) $_SESSION['checkout']['creditcardid'];
									$ipAddr = $_SERVER['REMOTE_ADDR'];
									$alertType = 'CT_LP';
									$alertMsg = 'Large purchase of $' . htmlentities(number_format($Total_price,2), ENT_QUOTES, 'UTF-8') . ' has been detected';

									$query = 'INSERT INTO purchase_alerts (purchase_alerts_userID, purchase_alerts_ccID, IPAddr, dateTime, alert_type, description)	VALUES(:userId, :ccId, :ipAddr, NOW(), :alertType, :alertMsg)';

									$stmt = $db->prepare($query);

									$stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
									$stmt->bindParam(':ccId', $ccId, PDO::PARAM_INT);
									$stmt->bindParam(':ipAddr', $ipAddr, PDO::PARAM_STR);
									$stmt->bindParam(':alertType', $alertType, PDO::PARAM_STR);
									$stmt->bindParam(':alertMsg', $alertMsg, PDO::PARAM_STR);

									$stmt->execute();

								}
//
//								//check if current customer has used more than 4 different credit card today:
//								//$query = 'select * from (select userid, purchased_date , creditcard_id from order_history WHERE userid = :loggedInUser and MONTH(purchased_date) = MONTH(NOW()) and DAY(purchased_date) = DAY(NOW()) and YEAR(purchased_date) = YEAR(NOW()) order by purchased_date DESC) as t group by userid, DATE(purchased_date) HAVING count(distinct creditcard_id > 3)';
//
//                                $query = 'SELECT userid FROM order_history WHERE userid = :loggedInUser AND DAY(purchased_date) = DAY(NOW()) AND MONTH(purchased_date) = MONTH(NOW()) AND YEAR(purchased_date) = YEAR(NOW()) GROUP BY creditcard_id';
//                                $stmt = $db->prepare($query);
//                                $loggedUser = (int) $_SESSION['user']['id'];
//                                $stmt->bindParam(':loggedInUser', $loggedUser, PDO::PARAM_INT);
//								$stmt->execute();
//								if($stmt->rowCount() > 3){
//
//									//need to insert alert here
//									$userId = (int) $_SESSION['user']['id'];
//									$ccId = (int) $_SESSION['checkout']['creditcardid'];
//									$ipAddr = $_SERVER['REMOTE_ADDR'];
//									date_default_timezone_set('America/New_York');
//									$alertTime = date('Y-m-d H:i:s', time());
//									$alertType = 'CT_MC';
//									$alertMsg = 'Customers used more than three different credit cards within one day';
//
//									$query = 'INSERT INTO purchase_alerts (purchase_alerts_userID, purchase_alerts_ccID, IPAddr, dateTime, alert_type, description) VALUES(:userId, :ccId, :ipAddr, :alertTime, :alertType, :alertMsg)';
//
//									$stmt = $db->prepare($query);
//
//									$stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
//									$stmt->bindParam(':ccId', $ccId, PDO::PARAM_INT);
//									$stmt->bindParam(':ipAddr', $ipAddr, PDO::PARAM_STR);
//									$stmt->bindParam(':alertTime', $alertTime, PDO::PARAM_STR);
//									$stmt->bindParam(':alertType', $alertType, PDO::PARAM_STR);
//									$stmt->bindParam(':alertMsg', $alertMsg, PDO::PARAM_STR);
//
//									$stmt->execute();
//
//								}



							} catch(PDOException $ex){

								echo '<h3>System Error</h3>
									<p class="text-error">There was a system error. Please try again later.</p>';

								echo "<small class='text-error'>$ex->getMessage()</small>";

							}
?>
							<tr>
								<td align="left"> <?php echo htmlentities($product_name, ENT_QUOTES, 'utf-8');  ?></td>
								<td align="left"> <?php echo '$',htmlentities($product_price, ENT_QUOTES, 'utf-8'); ?></td>
								<td align="left"> <?php echo htmlentities($product_quantity, ENT_QUOTES, 'utf-8'); ?></td>
								<td align="left"> <?php echo '$',htmlentities($Total_price, ENT_QUOTES, 'utf-8'); ?></td>
							</tr>
<?php
						}?>
                        <tr><td></td><td></td><td></td><td><b>Total</b>: <?php echo '$' . htmlentities(number_format($sum,2), ENT_QUOTES, 'utf-8'); ?></td></tr>
						</table>
                        <table class="table table-bordered table-striped">
                            <th>Shipping Address</th>
                            <th>Billing Address</th>
                            <tr><td>
                                    <?php
                                    echo '<address>'; echo htmlentities($_SESSION['checkout']['street'], ENT_QUOTES, 'utf-8'); echo '<br/>'; echo htmlentities($_SESSION['checkout']['city'], ENT_QUOTES, 'utf-8') . ', ' . htmlentities($_SESSION['checkout']['state'], ENT_QUOTES, 'utf-8') . ' ' . htmlentities($_SESSION['checkout']['zipcode'], ENT_QUOTES, 'utf-8');

                                    ?>
                            </td>
                                <td>
                                    <?php
                                       foreach($results as $row){
                                        echo '<address>'; echo htmlentities($row['street'], ENT_QUOTES, 'utf-8'); echo '<br/>'; echo htmlentities($row['city'], ENT_QUOTES, 'utf-8') . ', ' . htmlentities($row['state'], ENT_QUOTES, 'utf-8') . ' ' . htmlentities($row['zipcode'], ENT_QUOTES, 'utf-8');
                                    }
                                    ?>
                                </td></tr>
                        </table>
						<form class="form-horizontal" action="placeorder.php" method="post">
							<div class="control-group">
								<label class="control-label" for="email"><b>Phone Number</b></label>
								<div class="controls">
									<input type="text" id="phoneNumber" name="phoneNumber" placeholder="Phone Number"/>
								</div>
							</div>
							<div class="control-group">
								<div class="controls">
									<input type="submit" id="placeOrderBtn" class="btn btn-info" value="Place Order" />
								</div>
							</div>
						</form>
<?php
					} else {


						//Add hotlist event to alerts
						try {

							$userId = (int) $_SESSION['user']['id'];
							$ccId = (int) $_SESSION['checkout']['creditcardid'];
							$ipAddr = $_SERVER['REMOTE_ADDR'];
							$alertType = "CC_HL";
							$alertMsg = "Credit card on hotlist detected and stopped";

							$query = "INSERT INTO purchase_alerts (purchase_alerts_userID, purchase_alerts_ccID, IPAddr, dateTime, alert_type, description)
								VALUES(:userId, :ccId, :ipAddr, NOW(), :alertType, :alertMsg)";

							$stmt = $db->prepare($query);

							$stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
							$stmt->bindParam(':ccId', $ccId, PDO::PARAM_INT);
							$stmt->bindParam(':ipAddr', $ipAddr, PDO::PARAM_STR);
							$stmt->bindParam(':alertMsg', $alertMsg, PDO::PARAM_STR);
							$stmt->bindParam(':alertType', $alertType, PDO::PARAM_STR);

							$stmt->execute();

							echo '<p class="text-error">Your credit card has been rejected. Please use another card.</p>';

						} catch(PDOException $ex){

							echo '<h3>System Error</h3>
								<p class="text-error">There was a system error. Please try again later.</p>';

							echo "<small class='text-error'>$ex->getMessage()</small>";
						}

					}
				} catch(PDOException $ex){

					echo '<h3>System Error</h3>
						<p class="text-error">There was a system error. Please try again later.</p>';

					echo "<small class='text-error'>$ex->getMessage()</small>";
				}
				if(!empty($success_msg)) {

					echo "<i class='icon-ok'></i> <small class='text-success'>$success_msg</small><br/>";

				}

			}else echo"no card";
		}else echo"no post";
?>

<script>
    $('#placeOrderBtn').prop("disabled",true);

    var phoneNumReg = /^\d{3}-\d{3}-\d{4}$/;
    $('input[name="phoneNumber"]').keyup(function(){
        if($(this).val().length > 0 && phoneNumReg.test($(this).val())){
            $('#placeOrderBtn').prop('disabled',false);
        }else{
            $('#placeOrderBtn').prop('disabled',true);
        }
    });
</script>

<?php


	}
}


?>
