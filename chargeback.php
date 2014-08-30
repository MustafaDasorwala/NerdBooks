<?php

$page_title = 'Chargebacks';

include('includes/header.php');

require_once("../config.php");

if(!isset($_SESSION['user'])) {

    header("Location: index.php");
    exit();

} elseif(!($_SESSION['user']['is_admin'])){
    header("Location: index.php");
    exit();
}

    try {

        $query = "SELECT chargeback_dir FROM settings";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $dir = $stmt->fetchColumn();

    } catch(PDOException $ex){

        echo '<h3>System Error</h3>
                            <p class="text-error">There was a system error. Please try again later.</p>';

        echo "<small class='text-error'>$ex->getMessage()</small>";
    }

    if((!isset($dir) or empty($dir)) or !is_dir($dir)){
        echo "<p class='text-error'>No chargeback directory has been assigned or it doesn't exist</p>";
    } else {
        $files = preg_grep('/^([^.])/', scandir($dir));

        if(count($files) > 0){

            echo "<h2>Chargebacks</h2></br>";

            $count = 0;
            foreach($files as $file){

                $fullpath = $dir . '/'. $file;
                $path_parts = pathinfo($fullpath);
                $err = 0;

                if(array_key_exists('extension', $path_parts)) {

                    if($path_parts['extension'] == 'txt'){
                        //echo $fullpath;
                        $fh = fopen($fullpath,'r');

                        $linenum = 0;
                        while ($line = fgets($fh)) {
                            $linenum = $linenum+1;
                            //echo $linenum;

                            $line_arr = explode(':',$line);
                            $type = $line_arr[0];
                            $value = $line_arr[1];

                            //echo "$linenum: $type:$value</br>";
                            //echo $type;
                            $customerIDStr = "custid";
                            $orderIDStr = "orderid";
                            $ccNumStr = "ccnum";
                            $amtStr = "amt";
                            $dateStr = "date";

                            if($type == $customerIDStr){
                                $customerID = strip_tags(trim($value));
                            }

                            if($type == $orderIDStr){
                                $orderID = strip_tags(trim($value));
                            }

                            if($type == $ccNumStr){
                                $ccNum = strip_tags(trim($value));
                            }

                            if($type == $amtStr){
                                $amount = strip_tags(trim($value));

                            }

                            if($type == $dateStr){
                                $chargebackDate = strip_tags(trim($value));
                            }


                        }

                        echo '<form action="addchargeback.php" method="post"><table class="table">';
                        echo '<tr>';
                        echo '<td width="100"><button type="submit" class="btn-mini btn-danger"><strong>Chargeback</strong></button></td>';
                        echo '<td>';
                        echo '<input class="input-small" type="text" value="'; echo 'Order: ' . htmlentities($customerID, ENT_QUOTES, 'UTF-8'); echo '" disabled><input type="hidden" name="customerID" value="'; echo htmlentities($customerID, ENT_QUOTES, 'UTF-8'); echo '">';
                        echo ' <input class="input-large" type="text" value="'; echo '' . htmlentities($orderID, ENT_QUOTES, 'UTF-8'); echo '" disabled><input type="hidden" name="orderID" value="'; echo htmlentities($orderID, ENT_QUOTES, 'UTF-8'); echo '">';
                        echo ' <input class="input" type="text" value="'; echo 'CC: ' . htmlentities($ccNum, ENT_QUOTES, 'UTF-8'); echo '" disabled><input type="hidden" name="ccNum" value="'; echo htmlentities($ccNum, ENT_QUOTES, 'UTF-8'); echo '">';
                        echo ' <input class="input-medium" type="text" value="'; echo '$' . htmlentities(number_format($amount,2), ENT_QUOTES, 'UTF-8'); echo '" disabled><input type="hidden" name="amount" value="'; echo htmlentities($amount, ENT_QUOTES, 'UTF-8'); echo '">';
                        echo ' <input class="input-medium" type="text" value="'; echo htmlentities($chargebackDate, ENT_QUOTES, 'UTF-8'); echo '" disabled><input type="hidden" name="chargebackDate" value="'; echo htmlentities($chargebackDate, ENT_QUOTES, 'UTF-8'); echo '">';
                        echo ' <input type="hidden" name="filePath" value="'; echo htmlentities($fullpath, ENT_QUOTES, 'UTF-8'); echo '">';
                        echo '<input type="hidden" name="action" value="add">';
                        echo '</td></tr>';


                        echo '</table></form>';


                    }

                    fclose($fh);

                }



            }

        } else {
            echo '<div class="alert">There are no chargebacks yet</div>';
        }
    }


?>
    <div id="addChargeBack" class="modal hide fade smallModal">
        <form id="addChargeBack" action="viewitems.php" method="post" class="form-horizontal">
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
<script>

    $(document).on("click", ".removeItem", function () {
        var itemId = $(this).data('id');
        $(".modal-footer #item_id").val( itemId );
    });
</script>
<?php
    include ('./includes/footer.html');
?>