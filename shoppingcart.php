<?php
$page_title = 'Shopping Cart';
include('includes/header.php');
require_once("../config.php");
include('functions.php');

if(isset($_SESSION['user'])){
    if(isset($_SESSION['cart'])and is_array($_SESSION['cart'])){
        $max=count($_SESSION['cart']);
        for($i=0;$i<$max;$i++){
            if(checkcart($_SESSION['cart'][$i]['id'],$_SESSION['user']['id'],$db)){
            }else{
                insertcart($db,$_SESSION['cart'][$i]['id'],$_SESSION['user']['id'],$_SESSION['cart'][$i]['quantity']);
            }
        }
        if($_SERVER['REQUEST_METHOD'] == 'GET' and isset($_GET['action']) and !empty($_GET['action']) and ($_GET['action'] == 'delete') and isset($_GET['id']) and !empty($_GET['id']))  {
            $errors = array();
            $deleteid=intval($_GET['id']);
            $max=count($_SESSION['cart']);
            for($i=0;$i<$max;$i++){
                if($deleteid==$_SESSION['cart'][$i]['id']){
                    deletefromcart($db,$deleteid,$_SESSION['user']['id']);
                    unset($_SESSION['cart'][$i]);
                    break;
                }
            }
            $_SESSION['cart']=array_values($_SESSION['cart']);
        }elseif($_SERVER['REQUEST_METHOD'] == 'POST'){
            //the check for update is added & delete in zero
            if(!is_numeric($_POST['quantity'])) {
                $errors[] = "The quantity is invalid";
            }else{
                $cart_items_new=$_POST['item_to_adjust'];
                $newquantity=intval(strip_tags(trim($_POST['quantity'])));
// update items
                if($newquantity>0 && $newquantity<=99){
                    $max=count($_SESSION['cart']);
                    if(checkcart($cart_items_new,$_SESSION['user']['id'],$db)){
                        updatecartButton($db,$cart_items_new,$_SESSION['user']['id'],$newquantity);
                    }
                }elseif($newquantity==0){
                    $max=count($_SESSION['cart']);
                    for($i=0;$i<$max;$i++){
                        if($cart_items_new==$_SESSION['cart'][$i]['id']){
                            deletefromcart($db,$cart_items_new,$_SESSION['user']['id']);
                            unset($_SESSION['cart'][$i]);
                            break;
                        }

                    }
                }

            }
        }
        $userid=$_SESSION['user']['id'];
        $query = "SELECT inventory.id,inventory.name,inventory.price,shopping_cart_info.quantity FROM inventory,shopping_cart_info WHERE inventory.id =shopping_cart_info_itemID and shopping_cart_info_userID=:userid ";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $_SESSION['cart']=$results;
    }else{
        $userid=$_SESSION['user']['id'];
        $query = "SELECT inventory.id,inventory.name,inventory.price,shopping_cart_info.quantity FROM inventory,shopping_cart_info WHERE inventory.id =shopping_cart_info_itemID and shopping_cart_info_userID=:userid ";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $_SESSION['cart']=array();
        $_SESSION['cart']=$results;
    }
}
if(isset($_SESSION['cart'])and is_array($_SESSION['cart'])){
    $max=0;
    $max=count($_SESSION['cart']);
    //if statement for delete
    if($_SERVER['REQUEST_METHOD'] == 'GET' and isset($_GET['action']) and !empty($_GET['action']) and ($_GET['action'] == 'delete') and isset($_GET['id']) and !empty($_GET['id']))  {
        $errors = array();
        // $id = strip_tags(trim($_GET['id']));
        $deleteid=intval($_GET['id']);
        $max=count($_SESSION['cart']);
        for($i=0;$i<$max;$i++){
            if($deleteid==$_SESSION['cart'][$i]['id']){
                unset($_SESSION['cart'][$i]);
                break;
            }
        }
        $_SESSION['cart']=array_values($_SESSION['cart']);
    }//if statement for update
    elseif($_SERVER['REQUEST_METHOD'] == 'POST'){
        if(!isset($_SESSION['user'])){
            $errors = array();
            //the check for update is added & delete in zero
            if(!is_numeric($_POST['quantity'])) {
                $errors[] = "The quantity is invalid";
            } else {
// update items
                $cart_items_new=intval($_POST['item_to_adjust']);
                $newquantity=intval(strip_tags(trim($_POST['quantity'])));
                if($newquantity>0 && $newquantity<=99){
                    $count=count($cart_items_new);
                    $max=count($_SESSION['cart']);
                    for($i=0;$i<$max;$i++){
                        if($cart_items_new==$_SESSION['cart'][$i]['id']){
                            $_SESSION['cart'][$i]['quantity']=$newquantity;
                        }
                    }
                }elseif($newquantity==0)
                {
                    $max=count($_SESSION['cart']);
                    for($i=0;$i<$max;$i++){
                        if($cart_items_new==$_SESSION['cart'][$i]['id']){
                            unset($_SESSION['cart'][$i]);
                            break;
                        }
                    }
                }
            }
        }

    }
    ?>
    <h2>Shopping Cart</h2></br>

    <?php
    $max=0;
    $max=count($_SESSION['cart']);
    if($max > 0){

        echo '<table class="table table-striped table-bordered">
    <tr>
        <th class="th-center"></th>
        <th ></th>
        <th >Item</th>
        <th>Price</th>
        <th>Quantity</th>
    </tr>';
    }
    for($i=0;$i<$max;$i++){
        $product_id=$_SESSION['cart'][$i]['id'];
        $product_name=$_SESSION['cart'][$i]['name'];
        $product_price=$_SESSION['cart'][$i]['price'];
        ?>
        <tr>
            <td width="50"> <a class="btn btn-small btn-block btn-danger" href="shoppingcart.php?action=delete&id=<?php echo $product_id?> "><b>Delete</b></a></td>
            <td width="100" class="centered"><a href="itemdetail.php?id=<?php echo $product_id ?>"><img width="100" height="50" src="img/covers/<?php echo $product_name ?>.jpg"></img></a></td>
            <td width="250"><?php echo $product_name  ?></a></td>
            <td width="150"> <?php echo '$',$product_price ?></td>
            <td width="250"> <form class="form-horizontal" action="shoppingcart.php" method="post">
                    <input name="quantity" type="text"  class="input-small" value="<?php echo $_SESSION['cart'][$i]['quantity']?>" />
                    <input name="item_to_adjust" type="hidden" value="<?php echo $_SESSION['cart'][$i]['id']?>" />
                    <input name="adjustBtn" class="btn btn-small btn-danger" type="submit" value="Update" />
                </form>
            </td>

        </tr>
    <?php
    }
    echo '</table>'; ?>
    <?php
    if($max==0)
    {?>

        <?php echo "<p class='text-error'>Your shopping cart is empty</p>";?>

       <div align="left"><a class="btn btn-info" href="inventory.php">Continue Shopping</a></div>
    <?php
    }else{
        ?>
        <div align="left">
        <a class="btn btn-info" href="inventory.php"><b>Continue Shopping</b></a>&nbsp;&nbsp;
        <a class="btn btn-success" href="checkout.php"><b>Checkout</b></a></div>
    <?php
    }
}else{
    echo '<h2>Shopping Cart</h2></br>';
    echo"<p class='text-error'>Your shopping cart is empty</p>";
}
?>


<style rel="stylesheet">
    .form-horizontal  {
        margin-bottom: 5px;
    }

    .centered { vertical-align:middle; text-align:center; }
    .centered img { display:block; margin:0 auto; padding-bottom: 10px; }

    table .th-center {
        text-align: center;
    }
</style>


<?php
include('includes/footer.html');
?>