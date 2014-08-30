<?php

$page_title = 'Users';

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

    $records_per_page = 10;

    $pagination = new Zebra_Pagination();

    if($_SERVER['REQUEST_METHOD'] == 'GET') {

        try {

            $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM customer_info ORDER BY registration_date DESC LIMIT '  . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;
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

    if($_SERVER['REQUEST_METHOD'] == 'POST' and ($_POST['action'] == 'remove')) {

        $userId = $_POST['id'];

        $query = "DELETE FROM customer_info WHERE id = :userId";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);

        if($stmt->execute()){
            $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM customer_info LIMIT '  . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;
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
            header('Location: viewusers.php');
            exit();
        }
    }

?>

    <h3>Users (<?php if(isset($numResults)) echo $numResults; ?>)</h3> <br />

    <table class="table table-striped table-bordered">
        <tr>
            <th></th>
            <th>Username</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Registration Date</th>
        </tr>

    <?php
            if(isset($results)){
                foreach($results as $row) {

                    echo '<tr>
                        <td align="center" width="100"><a data-toggle="modal" class="removeUser btn btn-small btn-block btn-danger" href="#removeUserfromList" data-id ="' . $row['id'] . '"><strong>Delete</strong></a></td>
                        <td align="left">'. htmlentities($row['username'], ENT_QUOTES, 'UTF-8')  . '</td>
                        <td align="left">'. htmlentities($row['first_name'], ENT_QUOTES, 'UTF-8')  . ' ' . htmlentities($row['last_name'], ENT_QUOTES, 'UTF-8') . '</td>
                        <td align="left">'. htmlentities($row['email'], ENT_QUOTES, 'UTF-8')  . '</td>
                        <td align="left">';
                    if($row['is_admin']){
                        echo '<button class="btn btn-success btn-mini" disabled><b>Admin</b></button>';
                    } else {
                        echo '<button class="btn btn-primary btn-mini" disabled><b>User</b></button>';
                    }
                    echo '</td>
                        <td align="left">'. htmlentities($row['registration_date'], ENT_QUOTES, 'UTF-8')  . '</td>
                        </tr>';
                }
            }
    ?>
    </table>
    <div id="removeUserfromList" class="modal hide fade smallModal">
        <form id="removeUserfromList" action="viewusers.php" method="post" class="form-horizontal">
            <div class="modal-header"><a data-dismiss="modal"  class="close"></a>
                <h4>Warning</h4></div>
            <div class="modal-body">
                <fieldset>
                    <p>Are you sure you want to remove this user?</p>
                </fieldset>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="id" id="user_id" value=""/>
                <input type="hidden" name="action" value="remove"/>
                <button class="btn btn-danger btn-medium" type="submit">Yes</button>
                <a href="#" data-dismiss="modal" class="btn btn-medium">No</a>
            </div>
        </form>
    </div>
<?php $pagination->render(); ?>
    <script>

        $(document).on("click", ".removeUser", function () {
            var userId = $(this).data('id');
            $(".modal-footer #user_id").val( userId );
        });
    </script>
<?php
    include('includes/footer.html');
?>