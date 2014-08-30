<?php
require_once("../config.php");
function checkcart($itemid,$userid,$db){
    $query = "SELECT * FROM shopping_cart_info WHERE shopping_cart_info_userID= :userId  and shopping_cart_info_itemID=:itemid";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':userId', $userid, PDO::PARAM_INT);
    $stmt->bindParam(':itemid',$itemid, PDO::PARAM_STR);
    $stmt->execute();
    if($stmt->rowCount() == 0){
        $flag = 0;
    }else $flag=1;
    return $flag;
}
function updatecart($db,$itemid,$userid,$quantity){

    $query = "UPDATE shopping_cart_info SET quantity = quantity+:quantity WHERE shopping_cart_info_userID = :userid and shopping_cart_info_itemID=:itemid";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
    $stmt->bindParam(':userid',$userid, PDO::PARAM_STR);
    $stmt->bindParam(':itemid', $itemid, PDO::PARAM_STR);
    $stmt->execute();

}
function updatecartButton($db,$itemid,$userid,$quantity){

    $query = "UPDATE shopping_cart_info SET quantity =:quantity WHERE shopping_cart_info_userID = :userid and shopping_cart_info_itemID=:itemid";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
    $stmt->bindParam(':userid',$userid, PDO::PARAM_STR);
    $stmt->bindParam(':itemid', $itemid, PDO::PARAM_STR);
    $stmt->execute();

}
function insertcart($db,$itemid,$userid,$quantity){
    $query = "INSERT INTO shopping_cart_info (shopping_cart_info_itemID, quantity, shopping_cart_info_userID)
                        VALUES(:itemid, :quantity, :userid)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
    $stmt->bindParam(':userid',$userid, PDO::PARAM_STR);
    $stmt->bindParam(':itemid', $itemid, PDO::PARAM_STR);
    $stmt->execute();
}
function deletefromcart($db,$itemid,$userid){
    $query = "    DELETE FROM shopping_cart_info WHERE shopping_cart_info_userID=:userid and shopping_cart_info_itemID=:itemid;";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':userid',$userid, PDO::PARAM_STR);
    $stmt->bindParam(':itemid', $itemid, PDO::PARAM_STR);
    $stmt->execute();
}
function inserttocheckout($db,$unique_order_number,$userid,$itemid,$quantity){
    $userid=intval($userid);
    $itemid=intval($itemid);
    $quantity=intval($quantity);
   // $itemid=intval($itemid);

    $query = "INSERT INTO checkout (userid,unique_order_number,purchased_date,itemid,quantity)
                        VALUES(:userid,:unique_order_number,NOW(),:itemid ,:quantity)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':unique_order_number', $unique_order_number, PDO::PARAM_INT);
    $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
    $stmt->bindParam(':itemid', $itemid, PDO::PARAM_INT);
    $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
    $stmt->execute();

}
function inserttoorderhistory($db,$userid,$amount,$phoneNumber,$street, $city, $state, $zipcode,$unique_order_number,$creditcard_id){

    $query = "INSERT INTO order_history (userid,purchased_date,street,city,state,zipcode,phone_number,amount,unique_order_number,creditcard_id)
                        VALUES(:userid,NOW(),:street,:city,:state,:zipcode,:phoneNumber,:amount,:unique_order_number,:creditcard_id)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':unique_order_number', $unique_order_number, PDO::PARAM_STR);
    $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
    $stmt->bindParam(':amount', $amount, PDO::PARAM_INT);
    $stmt->bindParam(':phoneNumber', $phoneNumber, PDO::PARAM_STR);
    $stmt->bindParam(':street', $street, PDO::PARAM_STR);
    $stmt->bindParam(':city', $city, PDO::PARAM_STR);
    $stmt->bindParam(':state', $state, PDO::PARAM_STR);
    $stmt->bindParam(':zipcode', $zipcode, PDO::PARAM_STR);
    $stmt->bindParam(':creditcard_id', $creditcard_id, PDO::PARAM_INT);

    $stmt->execute();

}
function updateaddress($db,$userid, $street, $city, $state, $zipcode){

    $query = "UPDATE customer_info SET street =:street, city = :city, state = :state, zipcode = :zipcode WHERE id = :userid";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':street', $street, PDO::PARAM_STR);
    $stmt->bindParam(':city', $city, PDO::PARAM_STR);
    $stmt->bindParam(':state', $state, PDO::PARAM_STR);
    $stmt->bindParam(':zipcode', $zipcode, PDO::PARAM_STR);

    $stmt->bindParam(':userid',$userid, PDO::PARAM_STR);
    $stmt->execute();
}
?>