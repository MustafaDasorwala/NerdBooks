<?php

    $page_title = 'Credit Cards';

    include('includes/header.php');

    require_once("../config.php");

    if(!isset($_SESSION['user'])) {

        header("Location: index.php");
        exit();

    }

    if($_SERVER['REQUEST_METHOD'] == 'GET' and isset($_GET['action']) and !empty($_GET['action']) and ($_GET['action'] = 'delete') and isset($_GET['id']) and !empty($_GET['id']))  {

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
            $errors[] = 'The credit card does not exist';
        }

        if(empty($errors)) {

            try {

                $query = "DELETE FROM cc_info WHERE id = :id";

                $stmt = $db->prepare($query);

                $stmt->bindParam(':id', $id, PDO::PARAM_INT);

                $stmt->execute();

                $success_msg = "The credit card has been deleted";

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

    if($_SERVER['REQUEST_METHOD'] == 'GET') {

        try {

            $query = "SELECT * FROM cc_info WHERE ccId = :userId";

            $stmt = $db->prepare($query);

            $stmt->bindParam(':userId', $_SESSION['user']['id'], PDO::PARAM_INT);

            $stmt->execute();

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(PDOException $ex){

            echo '<h3>System Error</h3>
                        <p class="text-error">There was a system error. Please try again later.</p>';

            echo "<small class='text-error'>$ex->getMessage()</small>";
        }

    }

    if(!empty($success_msg)) {

        echo "<i class='icon-ok'></i> <small class='text-success'>$success_msg</small><br/>";

    }


    ?>

        <h2>Credit Cards</h2> <br />

    <?php

    if($stmt->rowCount() > 0){
        echo '
                    <table class="table table-striped">
                        <tr>
                            <th></th>
                            <th>Card</th>
                            <th>Card Number</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Expiration</th>
                             <th>Billing Address</th>
                        </tr>';

        foreach($results as $row) {

            echo '<tr>
                                <td align="center" width="100"><a class="btn btn-mini btn-primary" href="edititem.php?id=' . htmlentities($row['id'], ENT_QUOTES, 'UTF-8') . '">Edit</a> &nbsp; <a class="btn btn-mini btn-danger" href="viewcards.php?action=delete&id=' . $row['id'] . '">Delete</a></td>
                                <td align="left">'. htmlentities($row['ccType'], ENT_QUOTES, 'UTF-8') . '</td>
                                <td align="left">'. htmlentities($row['ccNumber'], ENT_QUOTES, 'UTF-8') . '</td>
                                <td align="left">'. htmlentities($row['first_name'], ENT_QUOTES, 'UTF-8')  . '</td>
                                <td align="left">'. htmlentities($row['last_name'], ENT_QUOTES, 'UTF-8')  . '</td>
                                <td align="left">'. htmlentities($row['exprDate'], ENT_QUOTES, 'UTF-8')  . '</td>
                                <td align="left">'. htmlentities($row['billingAddr'], ENT_QUOTES, 'UTF-8')  . '</td>
                            </tr>
                        ';

        }

        echo '</table>';

}
?>

<?php
    include ('./includes/footer.html');
?>