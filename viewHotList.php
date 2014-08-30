<?php

$page_title = 'Hot List';

include('includes/header.php');

require_once("../config.php");

if(!isset($_SESSION['user'])) {

		echo 'Welcome to Movie Zone, ' . $_SESSION['user']['first_name'] . '!';
		header("Location: index.php");
		exit();

} elseif(!($_SESSION['user']['is_admin'])){
		header("Location: index.php");
		exit();
}


?>
	<h2>
		Welcome to Hot List Management Page.
	</h2>

<?php
if($_SERVER['REQUEST_METHOD'] == 'GET' and isset($_GET['action']) and !empty($_GET['action']) and ($_GET['action'] = 'delete') and isset($_GET['id']) and !empty($_GET['id']))  {

		$item_does_not_exist = 0;

		$errors = array();

		$id = strip_tags(trim($_GET['id']));

		$query = "SELECT 1 FROM cc_hotlist WHERE id = :id";
		$stmt = $db->prepare($query);
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);

		$stmt->execute();

		if($stmt->rowCount() == 0){
				$item_does_not_exist = 1;
		}

		if($item_does_not_exist) {
				$errors[] = 'The item does not exist';
		}

		if(empty($errors)) {

				try {

						$query = "DELETE FROM cc_hotlist WHERE id = :id";

						$stmt = $db->prepare($query);

						$stmt->bindParam(':id', $id, PDO::PARAM_INT);

						$stmt->execute();

						$success_msg = "The selected credit card has been deleted from hotlist.";

				} catch(PDOException $ex){

						echo '<h3>System Error</h3>
								<p class="text-error">The delete cannot be done. Please try again later</p>';

						echo "<small class='text-error'>$ex->getMessage()</small>";
				}

		} else {

				echo '<p>The following error(s) occurred:</p>';

				foreach($errors as $msg) {
						echo "<i class='icon-exclamation-sign'></i> <small class='text-error'>$msg</small><br/>";
				}

		}

}

if($_SERVER['REQUEST_METHOD'] == 'GET') {

		try {

				$query = "SELECT * from cc_info, cc_hotlist where cc_hotlist_ccId = cc_info.ccId GROUP BY cc_hotlist_ccId";

				$stmt = $db->prepare($query);

				$stmt->execute();

				$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

		} catch(PDOException $ex){

				echo '<h3>System Error</h3>
						<p class="text-error">The credit card info could not be retrieved due to a system error</p>';

				echo "<small class='text-error'>$ex->getMessage()</small>";
		}

}

if(!empty($success_msg)) {

		echo "<i class='icon-ok'></i> <small class='text-success'>$success_msg</small><br/>";

}


?>

<?php

if($stmt->rowCount() > 0){
		echo '
				<table class="table table-striped">
				<tr>
				<th></th>
				<th>cc_number</th>
				<th>f_name</th>
				<th>l_name</th>
				<th>cc_type</th>
				<th>exp_date</th>
				<th>billing_addr</th>
				</tr>';

		foreach($results as $row) {

				echo '<tr>
						<td align="center"><a class="btn btn-mini btn-danger" href="viewHotList.php?action=delete&id=' . $row['id'] . '">Delete</a></td>
						<td align="left">'. $row['ccNumber']  . '</td>
						<td align="left">'. $row['first_name']  . '</td>
						<td align="left">'. $row['last_name']  . '</td>
						<td align="left">'. $row['ccType']  . '</td>
						<td align="left">'. $row['exprDate']  . '</td>
						<td align="left">'. $row['billingAddr']  . '</td>
						</tr>
						';

		}

		echo '</table>';

}
?>


<?php
include ('./includes/footer.html');
?>
