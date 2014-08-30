<?php

$page_title = 'Item Detail';

include('includes/header.php');

require_once("../config.php");

?>

<?php

if($_SERVER['REQUEST_METHOD'] == 'GET' and isset($_GET['id']) and !empty($_GET['id'])) {

    $id = $_GET["id"];

    try {

        $query = "SELECT * FROM inventory WHERE id =  :id ";

        $stmt = $db->prepare($query);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        if($stmt->rowCount() == 0){
            header('Location: inventory.php');
            exit();
        } else {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        $query2 = "SELECT customer_info.username, item_reviews.review, item_reviews.rating, item_reviews.reviewDate FROM customer_info INNER JOIN item_reviews
                WHERE customer_info.id = item_reviews.item_reviews_userID AND item_reviews.item_reviews_itemID = :itemId";

        $stmt2 = $db->prepare($query2);

        $stmt2->bindParam(':itemId', $id, PDO::PARAM_INT);

        $stmt2->execute();

        if($stmt2->rowCount() > 0){
            $results = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        }

    } catch(PDOException $ex){

        echo '<h3>System Error</h3>
                    <p class="text-error">There was a system error. Please try again later.</p>';

        echo "<small class='text-error'>$ex->getMessage()</small>";
    }


} else {
    header('Location: inventory.php');
    exit();
}


?>

    <h3><?php if (isset($row['name'])) echo $row['name']; ?></h3>
    <table class="table">
        <tr>
            <td class="centered" width="200">
                <img width="225" height="200" src="img/covers/<?php echo trim($row['name']); ?>.jpg" class="img-polaroid">
            </td>
            <td width="400">
                <table class="table table-borderless">
                    <tr>
                        <td width="100"><p><button class="btn btn-mini btn-block disabled"><strong>Price</strong></button></td>
                        <td><small><?php if (isset($row["price"])) echo '$'. htmlentities(number_format($row["price"],2), ENT_QUOTES, 'UTF-8');?></small></td>
                    </tr>
                    <tr>
                        <td width="100"><p><button class="btn btn-mini btn-block disabled"><strong>Format</strong></button></td>
                        <td><small><?php if (isset($row["format"])) echo htmlentities($row["format"], ENT_QUOTES, 'UTF-8');?></small></td>
                    </tr>
                    <tr>
                        <td width="100"><p><button class="btn btn-mini btn-block disabled"><strong>category</strong></button></td>
                        <td><small><?php if (isset($row["category"])) echo htmlentities($row["category"], ENT_QUOTES, 'UTF-8');?></small></td>
                    </tr>
                    <tr>
                        <td width="100"><p><button class="btn btn-mini btn-block disabled"><strong>Description</strong></button></td>
                        <td><small><?php if (isset($row["description"])) echo htmlentities($row["description"], ENT_QUOTES, 'UTF-8');?></small></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                                <a href="addtocart.php?id=<?php echo htmlentities($row["id"], ENT_QUOTES, 'UTF-8');?>"class="btn btn-success btn-medium">
                                    <i class="icon-shopping-cart icon-white"></i>&nbsp;Add to Cart
                                </a>

                        </td>
                    </tr>
                </table>
            </td>

        </tr>

        <tr>
            <?php
            if(!isset($_SESSION['user'])) {

                $textLoggedIn = 'href="#mustLogin" data-toggle="modal" class="btn btn-large btn-primary"';

            } else {

                $textLoggedIn = 'href="#addReview" data-toggle="modal" class="reviewModal btn btn-large btn-primary" data-id ="' . htmlentities($row['id'], ENT_QUOTES, 'UTF-8') . '"';
            }
            ?>
            <td colspan="2"><a <?php echo $textLoggedIn; ?>><strong>Add Review</strong></a></td>
        </tr>
        <tr>
            <?php

            if(isset($results)){

                $ratings = array(
                    "1" => "Poor",
                    "2" => "Bad",
                    "3" => "Average",
                    "4" => "Good",
                    "5" => "Excellent"

                );

                $ratings_name = array(
                    "1" => "label label-important",
                    "2" => "label label-warning",
                    "3" => "label",
                    "4" => "label label-info",
                    "5" => "label label-success"
                );

                foreach($results as $row){

                    $dbDate = $row['reviewDate'];
                    $reviewDate = date("m/d/Y", strtotime($dbDate));

                    echo '<table class="table table-striped table-bordered">
                                <tr>
                                    <td colspan="1"><button class="btn btn-mini disabled"><strong>User</strong></button>&nbsp;<button class="btn btn-inverse btn-mini disabled"><strong>'; echo htmlentities($row['username'], ENT_QUOTES, 'UTF-8');
                    echo '</strong></button>&nbsp;&nbsp;|&nbsp;&nbsp;<span class="';
                    echo htmlentities($ratings_name[$row['rating']], ENT_QUOTES, 'UTF-8');
                    echo '">';
                    echo  htmlentities($ratings[$row['rating']], ENT_QUOTES, 'UTF-8');
                    echo '</span>&nbsp;<small><strong>on</strong></small>&nbsp;';
                    echo '<button class="btn btn-inverse btn-mini disabled"><strong>';
                    echo $reviewDate;
                    echo '</strong></button></td></tr>';

                    echo '<tr>
                                        <td colspan="2"><small>'; echo htmlentities($row['review'], ENT_QUOTES, 'UTF-8');
                    echo '</small></td>
                                    </tr>
                            </table>';
                }
            }
            ?>
        </tr>
    </table>

    <div id="addReview" class="modal hide fade smallModal">
        <form id="reviewForm" action="reviewadded.php" method="post" class="form-horizontal">
            <div class="modal-header"><a data-dismiss="modal" class="close"></a>
                <h3>Review</h3></div>
            <div class="modal-body">
                <div class="control-group">
                    <label class="control-label"><strong>Rating</strong></label>
                    <div class="controls">
                        <select name="rating">
                            <option value="5">Excellent</option>
                            <option value="4">Good</option>
                            <option value="3">Average</option>
                            <option value="2">Bad</option>
                            <option value="1">Poor</option>
                        </select>
                    </div>
                </div>
                <textarea name="reviewText" maxlength="2000"></textarea>
            </div>
            <div class="modal-footer"><a href="#" data-dismiss="modal" class="btn">Close</a>
                <input type="hidden" name="itemId" id="itemId" value=""/>
                <input type="hidden" name="userId" id="userId" value="<?php echo htmlentities($_SESSION['user']['id'], ENT_QUOTES, 'UTF-8'); ?>"/>
                <input type="hidden" name="userName" id="userName" value="<?php echo htmlentities($_SESSION['user']['username'], ENT_QUOTES, 'UTF-8'); ?>"/>
                <input type="hidden" name="action" value="add"/>
                <button class="btn btn-primary create-button" type="submit">Add</button>
            </div>
        </form>
    </div>

    <div id="mustLogin" class="modal hide fade smallModal">
        <div class="modal-header"><a data-dismiss="modal" class="close"></a>
            <h3>Warning</h3></div>
        <div class="modal-body">
            <p>You must login to post a review</p>
        </div>
        <div class="modal-footer"><a href="#" data-dismiss="modal" class="btn btn-primary create-button">Ok</a>
        </div>
    </div>

    <script>
        $(document).on("click", ".reviewModal", function () {
            var itemId = $(this).data('id');
            $(".modal-footer #itemId").val( itemId );
        });

    </script>

    <style rel="stylesheet">

        .table-borderless td,
        .table-borderless th {
            border: 0;
        }

        .centered { vertical-align:middle; text-align:center; }
        .centered img { display:block; margin:0 auto; }

        textarea {
            resize: none;
            width: 515px;
            min-width:515px;
            max-height:515px;

            height:150px;
            min-height:150px;
            max-height:150px;
        }
    </style>
<?php
include ('./includes/footer.html');
?>