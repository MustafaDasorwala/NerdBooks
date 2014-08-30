<?php

$page_title = 'Settings';

include('includes/header.php');

require_once("../config.php");

    if(!isset($_SESSION['user'])) {

        header("Location: index.php");
        exit();

    } elseif(!($_SESSION['user']['is_admin'])){
        header("Location: index.php");
        exit();
    }

    if($_SERVER['REQUEST_METHOD'] == 'GET') {

        try {

            $query = "SELECT 5*AVG(price) AS avgItemPrice FROM inventory";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $avgItemPrice = $stmt->fetchColumn();

            $query = "SELECT purchase_threshold FROM settings";
            $stmt = $db->prepare($query);
            $stmt->execute();

            if($stmt->rowCount() == 0){

                try {

                    $query = "INSERT INTO settings (purchase_threshold) VALUES(:avgPrice)";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':avgPrice', $avgItemPrice, PDO::PARAM_STR);
                    $stmt->execute();

                } catch(PDOException $ex){

                    echo '<h3>System Error</h3>
                    <p class="text-error">There was a system error. Please try again later.</p>';

                    echo "<small class='text-error'>$ex->getMessage()</small>";
                }
            }


            $query = "SELECT purchase_threshold, chargeback_dir, orders_dir FROM settings";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch();
            $purchaseThresholdDB = $row['purchase_threshold'];
            if(empty($row['chargeback_dir'])){
                $chargebackDirDB = '';
            } else {
                $chargebackDirDB = $row['chargeback_dir'];
            }

            if(empty($row['orders_dir'])){
                $ordersDirDB = '';
            } else {
                $ordersDirDB = $row['orders_dir'];
            }


        } catch(PDOException $ex){

            echo '<h3>System Error</h3>
                    <p class="text-error">There was a system error. Please try again later.</p>';

            echo "<small class='text-error'>$ex->getMessage()</small>";
        }
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST') {

        $errors = array();

        if(empty($_POST['purchaseAlert']) or !is_numeric($_POST['purchaseAlert'])){
            $errors[] = 'You forgot to enter a purchase threshold amount';
        } else {
            $purchaseAlert = strip_tags(trim(doubleval($_POST['purchaseAlert'])));

        if(empty($_POST['chargebackDir'])){
            $errors[] = 'You forgot to enter a chargeback directory';

        }

        if(!empty($_POST['chargebackDir']) and !is_dir($_POST['chargebackDir'])){

            $errors[] = 'The directory entered is not valid';

        } else {
            $chargebackDir = strip_tags(trim($_POST['chargebackDir']));
        }

        if(empty($_POST['ordersDir'])){
            $errors[] = 'You forgot to enter an orders directory';

        }

        if(!empty($_POST['ordersDir']) and !is_dir($_POST['ordersDir'])){

            $errors[] = 'The directory entered is not valid';

        } else {
            $ordersDir = strip_tags(trim($_POST['ordersDir']));
        }

        if(empty($errors)) {

            try {

                $query = "UPDATE settings SET purchase_threshold = :purchaseThreshold, chargeback_dir = :chargebackDir, orders_dir = :ordersDir";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':purchaseThreshold',  $purchaseAlert, PDO::PARAM_STR);
                $stmt->bindParam(':chargebackDir',  $chargebackDir, PDO::PARAM_STR);
                $stmt->bindParam(':ordersDir',  $ordersDir, PDO::PARAM_STR);
                $stmt->execute();

                $query = "SELECT purchase_threshold, chargeback_dir, orders_dir FROM settings";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $row = $stmt->fetch();
                $purchaseThresholdDB = $row['purchase_threshold'];
                $chargebackDirDB = $row['chargeback_dir'];
                $ordersDirDB = $row['orders_dir'];

                $success_msg = "The settings have been edited";

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

            $query = "SELECT purchase_threshold, chargeback_dir, orders_dir FROM settings";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch();
            $purchaseThresholdDB = $row['purchase_threshold'];
            $chargebackDirDB = $row['chargeback_dir'];
            $ordersDirDB = $row['orders_dir'];

        }
    }

    if(!empty($success_msg)) {

        echo "<i class='icon-ok'></i> <small class='text-success'>$success_msg</small><br/>";

    }
}

?>
<h3>Settings</h3>
    <form class="form-horizontal" action="settings.php" method="post">
        <div class="control-group">
            <label class="control-label" for="purchaseAlert">Purchase Threshold</label>
            <div class="controls">
                <input type="text" id="purchaseAlert" name="purchaseAlert" placeholder="Threshold Amount" value="<?php if (isset($purchaseThresholdDB)) echo htmlentities(number_format($purchaseThresholdDB,2), ENT_QUOTES, 'UTF-8'); ?>">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="chargebackDir">Chargeback Directory</label>
            <div class="controls">
                <input type="text" id="chargebackDir" name="chargebackDir" placeholder="Chargeback Directory" value="<?php if (isset($chargebackDirDB)) echo htmlentities($chargebackDirDB, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="ordersDir">Orders Directory</label>
            <div class="controls">
                <input type="text" id="orderDir" name="ordersDir" placeholder="Orders Directory" value="<?php if (isset($ordersDirDB)) echo htmlentities($ordersDirDB, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
        </div>
        <div class="control-group">
            <div class="controls">
                <input type="submit" class="btn btn-info" value="Save" />
            </div>
        </div>
    </form>
<?php
    include('includes/footer.html');
?>