<?php
ob_start();
$page_title = 'Item Detail';

include('includes/header.php');

require_once("../config.php");

    if(!isset($_SESSION['user'])) {

        header("Location: index.php");
        exit();

    }

    if($_SERVER['REQUEST_METHOD'] == 'GET') {
        header('Location: inventory.php');
        exit();
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST' and $_POST['action'] == 'add') {


        if(isset($_POST['rating']) and isset($_POST['reviewText']) and isset($_POST['itemId'])
            and isset($_POST['userId']) and isset($_POST['userName'])){

            if($_POST['rating'] == '1' or $_POST['rating'] == '2' or $_POST['rating'] == '3' or $_POST['rating'] == '4' or $_POST['rating'] == '5'){

                if(is_numeric($_POST['itemId']) and is_numeric($_POST['userId'])){
                    $rating = strip_tags(trim($_POST['rating']));
                    $reviewText = strip_tags(trim($_POST['reviewText']));
                    $itemId = strip_tags(trim($_POST['itemId']));
                    $userId = strip_tags(trim($_POST['userId']));
                } else {
                    header("Location: index.php");
                    exit();
                }
            } else {
                header("Location: index.php");
                exit();
            }

        } else {
            header("Location: index.php");
            exit();
        }

        try {

            $query = "INSERT INTO item_reviews (item_reviews_userID, review, rating, item_reviews_itemID, reviewDate)
                        VALUES (:item_reviews_userID, :review, :rating, :item_reviews_itemID,  NOW())";

            $stmt = $db->prepare($query);

            $stmt->bindParam(':item_reviews_userID', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':review', $reviewText, PDO::PARAM_STR);
            $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
            $stmt->bindParam(':item_reviews_itemID', $itemId, PDO::PARAM_INT);

            $stmt->execute();

        } catch(PDOException $ex){

            echo '<h3>System Error</h3>
                    <p class="text-error">There was a system error. Please try again later.</p>';

            echo "<small class='text-error'>$ex->getMessage()</small>";
        }


        header("Location:itemdetail.php?id=". $_POST['itemId']);
        exit();
    }
?>

<?php
    include ('./includes/footer.html');
?>