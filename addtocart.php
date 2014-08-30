<?php
ob_start();
$page_title = 'Add to Cart';

include('includes/header.php');
require_once("../config.php");
include('functions.php');

if($_SERVER['REQUEST_METHOD'] == 'GET') {

    if(!isset($_SESSION['user'])) {
        $item_exists = 0;
        $errors = array();
        $id = strip_tags(trim($_GET['id']));
        $query = "SELECT * FROM inventory WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $flag=0;
        if($stmt->rowCount() == 0){
            $item_does_not_exist = 0;
        }else{//add to session
            if(isset($_SESSION['cart']) && is_array($_SESSION['cart'])){
                $id=intval($id);
                $max=count($_SESSION['cart']);
                for($i=0;$i<$max;$i++){
                    if($id==$_SESSION['cart'][$i]['id']){
                        $flag=1;
                        $_SESSION['cart'][$i]['quantity']++;
                        break;
                    }
                }
                if($flag==0){
                    $quantity=1;
                    $max=count($_SESSION['cart']);
                    foreach($results as $row){}{
                        $_SESSION['cart'][$max]['id']=$row['id'];
                        $_SESSION['cart'][$max]['name']=$row['name'];
                        $_SESSION['cart'][$max]['price']=$row['price'];
                        $_SESSION['cart'][$max]['quantity']=$quantity;
                    }
                }
            }
            else{//create session cart
                $_SESSION['cart']=array();
                $max=count($_SESSION['cart']);
                $quantity=1;
                foreach($results as $row){
                    $_SESSION['cart'][$max]['id']=$row['id'];
                    $_SESSION['cart'][$max]['name']=$row['name'];
                    $_SESSION['cart'][$max]['price']=$row['price'];
                    $_SESSION['cart'][$max]['quantity']=$quantity;
                }
            }
        }

    }else{ // User Logged in
        if(isset($_SESSION['cart']) && is_array($_SESSION['cart'])){
            $userid=$_SESSION['user']['id'];
            $itemid = strip_tags(trim($_GET['id']));
            if(checkcart($itemid,$userid,$db)){
                $quantity=intval(1);
                updatecart($db,$itemid,$userid,$quantity);
            }else{
                $quantity=intval(1);
                insertcart($db,$itemid,$userid,$quantity);

            }
            /*$query = "SELECT inventory.id,inventory.name,inventory.price,shopping_cart_info.quantity FROM inventory,shopping_cart_info WHERE inventory.id = :id and shopping_cart_info_itemid=:item";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $itemid, PDO::PARAM_INT);
            $stmt->bindParam(':item', $itemid, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $_SESSION['cart']=$results;*/

        } else {
            $_SESSION['cart'] = array();
            $_SESSION['totalItems'] = 0;
            $userid=$_SESSION['user']['id'];
            $itemid = strip_tags(trim($_GET['id']));
            if(checkcart($itemid,$userid,$db)){
                $quantity=intval(1);
                updatecart($db,$itemid,$userid,$quantity);
            }else{
                $quantity=intval(1);
                insertcart($db,$itemid,$userid,$quantity);

            }
        }
    }
}
?>
<?php
header("Location: shoppingcart.php");
include ('./includes/footer.html');
?>