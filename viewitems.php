<?php

    $page_title = 'Items';

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

    if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['action']) and !empty($_POST['action']) and ($_POST['action'] = 'delete') and isset($_POST['id']) and !empty($_POST['id']))  {

        $item_does_not_exist = 0;

        $errors = array();

        $id = strip_tags(trim($_POST['id']));

        $query = "SELECT 1 FROM inventory WHERE id = :id";
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

                $query = "DELETE FROM inventory WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();

                $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory ORDER BY dateAdded DESC LIMIT '  . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;
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

        } else {

            echo '<p>The following error(s) occurred:</p>';

            foreach($errors as $msg) {
                echo "<i class='icon-exclamation-sign'></i> <small class='text-error'>$msg</small><br/>";
            }

        }

    }

    if($_SERVER['REQUEST_METHOD'] == 'GET') {

        try {

            $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory ORDER BY dateAdded DESC LIMIT '  . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;
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
                    <p class="text-error">The items could not be retrieved due to a system error</p>';

            echo "<small class='text-error'>$ex->getMessage()</small>";
        }

    }

?>

    <h3>Items (<?php echo $numResults; echo ($numResults == 1) ? ' movie' :  ' movies'; ?>)</h3> <br />

    <?php

        if($stmt->rowCount() > 0){
            echo '
                <table class="table table-striped">
                    <tr>
                        <th></th>
                        <th>Item</th>
                        <th>Price</th>
                        <th>category</th>
                        <th>Format</th>
                        <th>Date Added</th>
                        <th>Description</th>
                    </tr>';

                foreach($results as $row) {

                    echo '<tr>
                            <td align="center" width="100"><a class="btn btn-mini btn-primary" href="edititem.php?id=' . $row['id'] . '"><strong>Edit</strong></a> &nbsp; <a data-toggle="modal" class="removeItem btn btn-mini btn-danger" href="#removeItemFromList" data-id ="' . $row['id'] . '"><strong>Delete</strong></a></td>
                            <td align="left">'. htmlentities($row['name'], ENT_QUOTES, 'UTF-8')  . '</td>
                            <td align="left">'. '$' . htmlentities(number_format($row['price'],2), ENT_QUOTES, 'UTF-8')  . '</td>
                            <td align="left">'. htmlentities($row['category'], ENT_QUOTES, 'UTF-8')  . '</td>
                            <td align="left">'. htmlentities($row['format'], ENT_QUOTES, 'UTF-8')  . '</td>';
                            $dbDate = $row['dateAdded'];
                            $fDate = date("m/d/Y", strtotime($dbDate));
                            echo '<td align="left">'. htmlentities($fDate, ENT_QUOTES, 'UTF-8')  . '</td>
                            <td align="left" style="word-wrap: break-word;" width="400">'.  htmlentities($row['description'], ENT_QUOTES, 'UTF-8')  . '</td>
                        </tr>
                    ';

                }

                echo '</table>';

        }
    ?>

    <div id="removeItemFromList" class="modal hide fade smallModal">
        <form id="removeItemFromList" action="viewitems.php" method="post" class="form-horizontal">
            <div class="modal-header"><a data-dismiss="modal"  class="close"></a>
                <h4>Warning</h4></div>
            <div class="modal-body">
                <fieldset>
                    <p>Are you sure you want to remove this item?</p>
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

        $(document).on("click", ".removeItem", function () {
            var itemId = $(this).data('id');
            $(".modal-footer #item_id").val( itemId );
        });
    </script>
<?php
    include('includes/footer.html');
?>