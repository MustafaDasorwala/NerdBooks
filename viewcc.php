<?php

    $page_title = 'Credit Cards';

    include('includes/header.php');

    require_once("../config.php");

    require_once("Zebra_Pagination.php");

    if(!isset($_SESSION['user'])) {

        header("Location: index.php");
        exit();

    } elseif(($_SESSION['user']['is_admin'])){
        header("Location: index.php");
        exit();
    }

    $records_per_page = 5;

    $pagination = new Zebra_Pagination();

    if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['action']) and !empty($_POST['action']) and ($_POST['action'] = 'delete') and isset($_POST['id']) and !empty($_POST['id']))  {

        $cc_does_not_exist = 0;

        $errors = array();

        $id = strip_tags(trim($_POST['id']));

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

                $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM cc_info WHERE ccId = :userId LIMIT '  . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;
                $stmt = $db->prepare($query);
                $stmt->bindParam(':userId', $_SESSION['user']['id'], PDO::PARAM_INT);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                //Need for pagination
                $queryNumResults = 'SELECT FOUND_ROWS() as numRows';
                $stmt = $db->prepare($queryNumResults);
                $stmt->execute();
                $numResults = $stmt->fetchColumn();
                $pagination->records($numResults);

                $pagination->records_per_page($records_per_page);

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

            $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM cc_info WHERE ccId = :userId LIMIT '  . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;
            $stmt = $db->prepare($query);
            $stmt->bindParam(':userId', $_SESSION['user']['id'], PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            //Need for pagination
            $queryNumResults = 'SELECT FOUND_ROWS() as numRows';
            $stmt = $db->prepare($queryNumResults);
            $stmt->execute();
            $numResults = $stmt->fetchColumn();
            $pagination->records($numResults);

            $pagination->records_per_page($records_per_page);

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
                    <table class="table table-striped table-bordered">
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

            $dbDate = $row['exprDate'];
            $fDate = date("m/d/Y", strtotime($dbDate));

            echo '<tr>
                                <td align="center" width="100"><a class="btn btn-mini btn-primary" href="editcc.php?id=' . htmlentities($row['id'], ENT_QUOTES, 'UTF-8') . '"><strong>Edit</strong></a> &nbsp; <a data-toggle="modal" class="removeCard btn btn-mini btn-danger" href="#removeCCfromList" data-id ="' . $row['id'] . '"><strong>Delete</strong></a></td>
                                <td align="left">'. htmlentities($row['ccType'], ENT_QUOTES, 'UTF-8') . '</td>
                                <td align="left">'. htmlentities($row['ccNumber'], ENT_QUOTES, 'UTF-8') . '</td>
                                <td align="left">'. htmlentities($row['first_name'], ENT_QUOTES, 'UTF-8')  . '</td>
                                <td align="left">'. htmlentities($row['last_name'], ENT_QUOTES, 'UTF-8')  . '</td>
                                <td align="left">'. htmlentities($fDate, ENT_QUOTES, 'UTF-8')  . '</td>
                                <td align="left">'. htmlentities($row['street'] . ', ' . $row['city'] . ', ' . $row['state'] . ' ' . $row['zipcode'], ENT_QUOTES, 'UTF-8')  . '</td>
                            </tr>
                        ';

        }

        echo '</table>';

}
?>
    <div id="removeCCfromList" class="modal hide fade smallModal">
        <form id="removeCCfromList" action="viewcc.php" method="post" class="form-horizontal">
            <div class="modal-header"><a data-dismiss="modal"  class="close"></a>
                <h4>Warning</h4></div>
            <div class="modal-body">
                <fieldset>
                    <p>Are you sure you want to remove this credit card?</p>
                </fieldset>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="id" id="item_id" value=""/>
                <input type="hidden" name="action" value="delete"/>
                <button class="btn btn-danger btn-medium" type="submit">Yes</button>
                <a href="#" data-dismiss="modal" class="btn btn-medium">No</a>
            </div>
        </form>
    </div>
<?php $pagination->render(); ?>
    <script>

        $(document).on("click", ".removeCard", function () {
            var ccId = $(this).data('id');
            $(".modal-footer #item_id").val( ccId );
        });
    </script>
<?php
    include ('./includes/footer.html');
?>