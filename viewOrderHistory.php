<?php
ob_start();
$page_title = 'Orders';

include('includes/header.php');

require_once("../config.php");

if(!isset($_SESSION['user'])) {

    header("Location: index.php");
    exit();

}

if($_SERVER['REQUEST_METHOD'] == 'GET') {

    try {

        //$query = "select inventory.name, order_history.amount , order_history.shipping_address , order_history.purchased_date from inventory, order_history, checkout where order_history.unique_order_number = checkout.unique_order_number and inventory.id = checkout.itemid and order_history.userid = :userId";

        $query = "SELECT order_history.unique_order_number AS orderNumber, SUM(checkout.quantity) AS totalItems, order_history.amount AS orderAmount, order_history.purchased_date AS orderPurchaseDate, order_history.street AS orderStreet, order_history.city AS orderCity, order_history.state AS orderState, order_history.zipcode AS orderZipcode FROM order_history INNER JOIN checkout ON order_history.unique_order_number = checkout.unique_order_number WHERE order_history.userid = :userId GROUP BY checkout.unique_order_number ORDER BY orderPurchaseDate DESC";

        $stmt = $db->prepare($query);

        $stmt->bindParam(':userId', $_SESSION['user']['id'], PDO::PARAM_INT);

        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);


        echo ' <h2>Order History</h2> <br /> ';


        if($stmt->rowCount() > 0){

            foreach($results as $row) {

                $orderNum = htmlentities($row['orderNumber'], ENT_QUOTES, 'UTF-8');
                //echo 'Order number ' . $row['unique_order_number'];

                $innerQuery = "SELECT inventory.name AS itemName, checkout.quantity as itemQuantity, inventory.price AS itemPrice FROM order_history, inventory, checkout where order_history.unique_order_number = checkout.unique_order_number and checkout.itemid = inventory.id and order_history.unique_order_number = :uid";

                $innerStmt = $db->prepare($innerQuery);

                $innerStmt->bindParam(':uid', $orderNum, PDO::PARAM_STR);

                $innerStmt->execute();

                $innerResults = $innerStmt->fetchAll(PDO::FETCH_ASSOC);


                $dbDate = $row['orderPurchaseDate'];
                $fDate = date("m/d/Y", strtotime($dbDate));

                echo '<table class="table table-bordered table-striped span9">';
                echo '<tr><td class="span8"><button button="btn btn-mini" disabled>Order: ';
                echo htmlentities($orderNum, ENT_QUOTES, 'UTF-8');
                echo '</button>&nbsp;<button class="btn btn-inverse btn-mini" disabled>'; echo $fDate; echo '</button>';
                echo '</br></br><button class="btn btn-mini btn-inverse" disabled><b>Shipped To: </b>'; echo htmlentities($row['orderStreet'], ENT_QUOTES, 'UTF-8') . ', ' . htmlentities($row['orderCity'], ENT_QUOTES, 'UTF-8') . ', ' . htmlentities($row['orderState'], ENT_QUOTES, 'UTF-8') . ' ' . htmlentities($row['orderZipcode'], ENT_QUOTES, 'UTF-8'); echo '</button></td>';

                echo '<td>';
                echo '<button class="btn btn-mini btn-block btn-inverse" disabled><b>Items</b></button>';

                foreach($innerResults as $innerRow) {

                    echo '<button class="btn btn-mini btn-block" disabled><b>'; echo htmlentities($innerRow['itemName'], ENT_QUOTES, 'UTF-8'); echo '</b> ('; echo htmlentities($innerRow['itemQuantity'], ENT_QUOTES, 'UTF-8'); echo ') - $'; echo htmlentities(number_format($innerRow['itemPrice'],2), ENT_QUOTES, 'UTF-8'); echo '</button></br>';
                    //echo $innerRow['purchaseDate'];
                    /*echo '
                    <table class="table table-striped">
                        <tr>
                            <th>Item Name</th>
                            <th>Amount</th>
                            <th>Shipping Address</th>
                            <th>Date</th>
                        </tr>';*/


                    //echo 'hello';




                   /* echo '<tr>
						<td align="left">'. htmlentities($innerRow['name'], ENT_QUOTES, 'UTF-8') . '</td>
						<td align="left">'. htmlentities($innerRow['amount'], ENT_QUOTES, 'UTF-8') . '</td>
						<td align="left">'. htmlentities($innerRow['shipping_address'], ENT_QUOTES, 'UTF-8')  . '</td>
						<td align="left">'. htmlentities($fDate, ENT_QUOTES, 'UTF-8')  . '</td>
						</tr>
						';*/

                }

                echo '</td></tr><tr><td colspan="2"><button class="btn btn-primary btn-small" disabled><b>Total Items</b>: '; echo htmlentities($row['totalItems'], ENT_QUOTES, 'UTF-8'); echo '</button>&nbsp;<button class="btn btn-primary btn-small" disabled>'; echo '<b>Total</b>: $'. htmlentities(number_format($row['orderAmount'],2), ENT_QUOTES, 'UTF-8'); echo '</button></td></tr></table>';


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


}
?>
<?php
include ('./includes/footer.html');
?>
