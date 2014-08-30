<?php
ob_start();
$page_title = 'Chargeback Added';

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
    header('Location: chargeback.php');
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST' and $_POST['action'] == 'add') {

    try {

        $query = "INSERT INTO chargebacks (customerID, orderID, ccNum, amount, chargebackDate)
                        VALUES (:customerID, :orderID, :ccNum, :amount,  :chargebackDate)";

        $stmt = $db->prepare($query);

        $stmt->bindParam(':customerID', $_POST['customerID'], PDO::PARAM_INT);
        $stmt->bindParam(':orderID', $_POST['orderID'], PDO::PARAM_STR);
        $stmt->bindParam(':ccNum', $_POST['ccNum'], PDO::PARAM_STR);
        $stmt->bindParam(':amount', $_POST['amount'], PDO::PARAM_INT);
        $stmt->bindParam(':chargebackDate', $_POST['chargebackDate'], PDO::PARAM_STR);

        $stmt->execute();

    } catch(PDOException $ex){

        echo '<h3>System Error</h3>
                    <p class="text-error">There was a system error. Please try again later.</p>';

        echo "<small class='text-error'>$ex->getMessage()</small>";
    }

    $fileToDelete = urldecode($_POST['filePath']);
    unlink($fileToDelete);
    ob_end_clean();
    header("Location: chargeback.php");
    exit();
}
?>
<?php
include ('./includes/footer.html');
?>