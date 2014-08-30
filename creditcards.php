<?php

$page_title = 'Credit Cards';

include('includes/header.php');

require_once("../config.php");

require_once("Zebra_Pagination.php");

if(!isset($_SESSION['user'])) {

    header("Location: index.php");
    exit();

} elseif(!($_SESSION['user']['is_admin'])){
    header("Location: index.php");
    exit();
}

$records_per_page = 5;

$pagination = new Zebra_Pagination();

    if($_SERVER['REQUEST_METHOD'] == 'POST' and ($_POST['action'] == 'add')) {

        $ccId = $_POST['id'];

        try {

            $query = "INSERT INTO cc_hotlist (ccHotList_Id) VALUES (:ccId)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':ccId', $ccId, PDO::PARAM_INT);

            if($stmt->execute()){
                $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM cc_info LIMIT '  . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;
                $stmt = $db->prepare($query);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                //Need for pagination
                $queryNumResults = 'SELECT FOUND_ROWS() as numRows';
                $stmt = $db->prepare($queryNumResults);
                $stmt->execute();
                $numResults = $stmt->fetchColumn();
                $pagination->records($numResults);

                $pagination->records_per_page($records_per_page);
            } else {
                header('Location: creditcards.php');
                exit();
            }

        } catch(PDOException $ex){

            echo '<h3>System Error</h3>
                            <p class="text-error">There was a system error. Please try again later.</p>';

            echo "<small class='text-error'>$ex->getMessage()</small>";
        }

    }

    if($_SERVER['REQUEST_METHOD'] == 'POST' and ($_POST['action'] == 'remove')) {

        $ccId = $_POST['id'];

        try {

            $query = "DELETE FROM cc_hotlist WHERE ccHotList_Id = :ccId";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':ccId', $ccId, PDO::PARAM_INT);

            if($stmt->execute()){
                $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM cc_info LIMIT '  . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;
                $stmt = $db->prepare($query);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                //Need for pagination
                $queryNumResults = 'SELECT FOUND_ROWS() as numRows';
                $stmt = $db->prepare($queryNumResults);
                $stmt->execute();
                $numResults = $stmt->fetchColumn();
                $pagination->records($numResults);

                $pagination->records_per_page($records_per_page);
            } else {
                header('Location: creditcards.php');
                exit();
            }

        } catch(PDOException $ex){

            echo '<h3>System Error</h3>
                                <p class="text-error">There was a system error. Please try again later.</p>';

            echo "<small class='text-error'>$ex->getMessage()</small>";
        }

    }

    if($_SERVER['REQUEST_METHOD'] == 'GET') {

        try {

            $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM cc_info LIMIT '  . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;
            $stmt = $db->prepare($query);
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

?>

    <h2>Credit Cards</h2> <br />

<?php

    if($stmt->rowCount() > 0){
        echo '
                        <table class="table table-striped table-bordered">
                            <tr>
                                <th class="th-center">Hot List</th>
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

            echo '<tr>';

            try {

                $query = "SELECT COUNT(cc_info.id) AS numCConHL FROM cc_info INNER JOIN cc_hotlist on cc_info.id = ccHotList_Id WHERE cc_info.id = :ccId";
                $stmt = $db->prepare($query);

                $stmt->bindParam(':ccId', $row['id'], PDO::PARAM_INT);

                $stmt->execute();

                $isCConHL = $stmt->fetchColumn();

                if($isCConHL){
                    echo '<td width="100"><a data-toggle="modal" class="removeHL btn btn-small btn-inverse btn-block" href="#removeFromHL" data-id ="';
                    if(isset($row['id'])){
                        echo htmlentities($row['id'], ENT_QUOTES, 'UTF-8');
                    }

                    echo '"><strong>Remove</strong></a></td>';

                } else {
                    echo '<td width="100"><a data-toggle="modal" class="addHL btn btn-small btn-danger btn-block" href="#addToHL" data-id ="';
                    if(isset($row['id'])){
                        echo htmlentities($row['id'], ENT_QUOTES, 'UTF-8');
                    }

                    echo '"><strong>Add</strong></a></td>';
                }


            } catch(PDOException $ex){

                echo '<h3>System Error</h3>
                            <p class="text-error">There was a system error. Please try again later.</p>';

                echo "<small class='text-error'>$ex->getMessage()</small>";
            }

            echo '
                <td align="left">'. htmlentities($row['ccType'], ENT_QUOTES, 'UTF-8') . '</td>
                <td align="left">'. htmlentities($row['ccNumber'], ENT_QUOTES, 'UTF-8') . '</td>
                <td align="left">'. htmlentities($row['first_name'], ENT_QUOTES, 'UTF-8')  . '</td>
                <td align="left">'. htmlentities($row['last_name'], ENT_QUOTES, 'UTF-8')  . '</td>
                <td align="left">'. htmlentities($fDate, ENT_QUOTES, 'UTF-8')  . '</td>
                <td align="left">'. htmlentities($row['street'] . ', ' . $row['city'] . ', ' . $row['state'] . ' ' . $row['zipcode'], ENT_QUOTES, 'UTF-8')  . '</td>
            </tr>';

        }

        echo '</table>';

}
?>
<?php $pagination->render(); ?>

    <div id="addToHL" class="modal hide fade smallModal">
        <form id="addToHotList" action="creditcards.php" method="post" class="form-horizontal">
            <div class="modal-header"><a data-dismiss="modal"  class="close"></a>
                <h4>Warning</h4></div>
            <div class="modal-body">
                <fieldset>
                    <p>Are you sure you want to add this credit card to the hot list?</p>
                </fieldset>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="id" id="cc_id" value=""/>
                <input type="hidden" name="action" value="add"/>
                <button class="btn btn-danger btn-medium" type="submit">Yes</button>
                <a href="#" data-dismiss="modal" class="btn btn-medium">No</a>
            </div>
        </form>
    </div>

    <div id="removeFromHL" class="modal hide fade smallModal">
        <form id="removeFromHotList" action="creditcards.php" method="post" class="form-horizontal">
            <div class="modal-header"><a data-dismiss="modal"  class="close"></a>
                <h4>Warning</h4></div>
            <div class="modal-body">
                <fieldset>
                    <p>Are you sure you want to remove this credit card from the hot list?</p>
                </fieldset>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="id" id="cc_id" value=""/>
                <input type="hidden" name="action" value="remove"/>
                <button class="btn btn-danger btn-medium" type="submit">Yes</button>
                <a href="#" data-dismiss="modal" class="btn btn-medium">No</a>
            </div>
        </form>
    </div>
<script>
    $(document).on("click", ".addHL", function () {
        var ccId = $(this).data('id');
        $(".modal-footer #cc_id").val( ccId );
    });

    $(document).on("click", ".removeHL", function () {
        var ccId = $(this).data('id');
        $(".modal-footer #cc_id").val( ccId );
    });
</script>

<style rel="stylesheet">

    table .th-center {
        text-align: center;
    }

</style>

<?php
    include ('./includes/footer.html');
?>