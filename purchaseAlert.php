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

    if($_SERVER['REQUEST_METHOD'] == 'GET' and isset($_GET['action']) and ($_GET['action'] = 'delete') and isset($_GET['id']) and !empty($_GET['id']))  {

        $item_does_not_exist = 0;

        $errors = array();

        $id = strip_tags(trim($_GET['id']));

        $query = "SELECT * FROM purchase_alerts WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        if($stmt->rowCount() == 0){
            $item_does_not_exist = 1;
        }

        if($item_does_not_exist) {
            $errors[] = 'The alert does not exist';
        }

        if(empty($errors)) {

            try {

                $query = "DELETE FROM purchase_alerts WHERE purchase_alerts.id = :id";

                $stmt = $db->prepare($query);

                $stmt->bindParam(':id', $id, PDO::PARAM_INT);

                $stmt->execute();

                $success_msg = "The alert has been deleted";

            } catch(PDOException $ex){

                echo '<h3>System Error</h3>
                    <p class="text-error">The alert could not be deleted due to a system error. Please try again later.</p>';

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

            $query = "SELECT purchase_alerts.id as id, CONCAT(customer_info.first_name, ' ', customer_info.last_name) As customer, CONCAT(cc_info.first_name, ' ', cc_info.last_name) AS cardholder, cc_info.ccNumber AS cardNumber,
            IPAddr, dateTime, description FROM purchase_alerts INNER JOIN customer_info ON purchase_alerts.purchase_alerts_userID = customer_info.id INNER JOIN cc_info ON purchase_alerts.purchase_alerts_ccID = cc_info.id ORDER BY dateTime DESC";
            //$query = "select purchase_alerts.id as id, customer_info.first_name as customer_fname, customer_info.last_name as customer_lname, cc_info.ccNumber , cc_info.first_name as cc_fname , cc_info.last_name as cc_lname, purchase_alerts.dateTime, purchase_alerts.description from purchase_alerts, customer_info, cc_info where purchase_alerts.purchase_alerts_userID = customer_info.id and purchase_alerts_ccID = cc_info.ccId;";

            $stmt = $db->prepare($query);

            $stmt->execute();

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(PDOException $ex){

            echo '<h3>System Error</h3>
                    <p class="text-error">There was a system error. Try again at a later time.</p>';

            echo "<small class='text-error'>$ex->getMessage()</small>";
        }

    }

    if(!empty($success_msg)) {

        echo "<i class='icon-ok'></i> <small class='text-success'>$success_msg</small><br/>";

    }


?>

    <h2>Purchase Alerts</h2> <br />

    <?php

        if($stmt->rowCount() > 0){
            echo '
                <table class="table table-striped table-bordered">
                    <tr>
                        <th></th>
                        <th>Customer</th>
                        <th>Card Holder</th>
                        <th>Credit Card Number</th>
                        <th>IP Address</th>
                        <th>Date</th>
                        <th>Description</th>
                    </tr>';

                foreach($results as $row) {

                    echo '<tr>
                            <td align="center"><a class="btn btn-mini btn-danger" href="purchaseAlert.php?action=delete&id=' . $row['id'] . '">Delete</a></td>
                            <td align="left">'. $row['customer']  . '</td>
                            <td align="left">'. $row['cardholder']  . '</td>
                            <td align="left">'. $row['cardNumber']  . '</td>
                            <td align="left">'. $row['IPAddr']  . '</td>
                            <td align="left">'. $row['dateTime']  . '</td>
                            <td align="left">'. $row['description']  . '</td>
                        </tr>
                    ';

                }

                echo '</table>';

        }
    ?>
<?php
    include('includes/footer.html');
?>
