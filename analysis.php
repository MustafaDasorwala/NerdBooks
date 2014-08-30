<?php

    $page_title = 'Items';

    include('includes/header.php');

    require_once("../config.php");

    if(!isset($_SESSION['user'])) {

        header("Location: index.php");
        exit();

    } elseif(!($_SESSION['user']['is_admin'])){
        header("Location: index.php");
        exit();
    }
?>
	<h2>Analysis Routing</h2> <br />

	<div class="btn-group">
    <button class="btn">Action</button>
    <button class="btn dropdown-toggle" data-toggle="dropdown">
    <span class="caret"></span>
    </button>
    <ul class="dropdown-menu">
		<li><a href="analysis.php?action=1">Show customers who used more than three different credit cards within one day</a></li>
		<li><a href="analysis.php?action=2">Show credit cards that used more than three different phone numbers within one day</a></li>

    </ul>
    </div>
	<br>

<?php

    if($_SERVER['REQUEST_METHOD'] == 'GET' and isset($_GET['action']) and !empty($_GET['action']) and ($_GET['action'] == '1'))  {

		$item_does_not_exist = 0;
        $errors = array();

		$query = "select purchase_alerts.purchase_alerts_userID as userid, purchase_alerts.purchase_alerts_ccID as cc_id, customer_info.username as username, customer_info.first_name as first_name, customer_info.last_name as last_name, customer_info.email as email, purchase_alerts.dateTime as transaction_date from customer_info, purchase_alerts where purchase_alerts.purchase_alerts_userID = customer_info.id and purchase_alerts.alert_type = :alertType";
		
		$alertType = 'CT_MC';
        $stmt = $db->prepare($query);
		$stmt->bindParam(':alertType', $alertType, PDO::PARAM_STR);

        $stmt->execute();

		$results = $stmt->fetchAll();

        if($stmt->rowCount() == 0){
            $item_does_not_exist = 1;
        }

        if($item_does_not_exist) {
            $errors[] = 'no record matches selected criteria.';
        }

		if(empty($errors)) {

			try {
				if($stmt->rowCount() > 0){

					echo '<br/>
						<div class="alert">Customers who used more than three different credit cards within one day</div>
						<table class="table table-striped table-bordered">
						<tr>
						<th>Username</th>
						<th>Customer</th>
						<th>Email</th>
						<th>Violation Date</th>
						</tr>';

					foreach($results as $row) {
					
					echo '<tr>
						<td align="left">'. htmlentities($row['username'], ENT_QUOTES, 'utf-8')  . '</td>
						<td align="left">'. htmlentities($row['first_name'], ENT_QUOTES, 'utf-8') . ' '  . htmlentities($row['last_name'], ENT_QUOTES, 'utf-8') . '</td>
						<td align="left">'. htmlentities($row['email'], ENT_QUOTES, 'utf-8')  . '</td>
						<td align="left">'. htmlentities($row['transaction_date'], ENT_QUOTES, 'utf-8')  . '</td>
						</tr>
						';

					}

					echo '</table>';

				}


			} catch(PDOException $ex){

				echo '<h3>system error</h3>
					<p class="text-error">there was a system error. please try again later.</p>';

				echo "<small class='text-error'>" . $ex->getMessage() . "</small>";
			}

		} else {


            foreach($errors as $msg) {
                echo "<i class='icon-exclamation-sign'></i> <small class='text-error'>$msg</small><br/>";
            }

        }

    }else if($_SERVER['REQUEST_METHOD'] == 'GET' and isset($_GET['action']) and !empty($_GET['action']) and ($_GET['action'] == '2'))  {

		$item_does_not_exist = 0;
        $errors = array();

		$query = "select cc_info.id, cc_info.ccnumber, cc_info.first_name, cc_info.last_name, cc_info.cctype, cc_info.exprdate, purchase_alerts.dateTime as purchased_date from cc_info, purchase_alerts where purchase_alerts.purchase_alerts_ccID= cc_info.id and purchase_alerts.alert_type = :alertType";
		
		$alertType = 'CC_MN';
        $stmt = $db->prepare($query);
		$stmt->bindParam(':alertType', $alertType, PDO::PARAM_STR);

        $stmt->execute();

		$results = $stmt->fetchAll();

        if($stmt->rowCount() == 0){
            $item_does_not_exist = 1;
        }

        if($item_does_not_exist) {
            $errors[] = 'no record matches selected criteria.';
        }

		if(empty($errors)) {

			try {
				if($stmt->rowCount() > 0){

					echo '
<br/>
						<div class="alert">Customers who used more than three different phone numbers within one day</div>
						<table class="table table-striped table-bordered">
						<tr>
						<th>Credit Card Number</th>
						<th>Customer</th>
						<th>Type</th>
						<th>Expiration Date</th>
						<th>Violation Date</th>
						</tr>';

					foreach($results as $row) {
						echo '<tr>
							<td align="left">'. htmlentities($row['ccnumber'], ENT_QUOTES, 'utf-8')  . '</td>
							<td align="left">'. htmlentities($row['first_name'], ENT_QUOTES, 'utf-8') . ' '  . htmlentities($row['last_name'], ENT_QUOTES, 'utf-8') . '</td>
							<td align="left">'. htmlentities($row['cctype'], ENT_QUOTES, 'utf-8')  . '</td>
							<td align="left">'. htmlentities($row['exprdate'], ENT_QUOTES, 'utf-8')  . '</td>
							<td align="left">'. htmlentities($row['purchased_date'], ENT_QUOTES, 'utf-8')  . '</td>
							</tr>
							';


					}

					echo '</table>';

				}


			} catch(pdoexception $ex){

				echo '<h3>system error</h3>
					<p class="text-error">there was a system error. please try again later.</p>';

				echo "<small class='text-error'>$ex->getmessage()</small>";
			}

		}


    } else {

        echo '<br/><div class="alert alert-success">Select an action above to run analysis</div>';
    }




?>



   
<?php
    include('includes/footer.html');
?>
