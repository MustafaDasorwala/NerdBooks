<?php

    $page_title = 'Statistics';

    include('includes/header.php');

    require_once("../config.php");

    if(!isset($_SESSION['user'])) {

        header("Location: index.php");
        exit();

    } elseif(!($_SESSION['user']['is_admin'])){
        header("Location: index.php");
        exit();
    }

    if($_SERVER['REQUEST_METHOD'] == 'GET'){

        try {

            //Total Orders
            $query = "SELECT COUNT(*) FROM order_history";

            $stmt = $db->prepare($query);

            $stmt->execute();

            if($stmt->rowCount() > 0){
                $totalOrders = (int) $stmt->fetchColumn();
            } else {
                $totalOrders = 0;
            }


            //Total Sales
            $query = "SELECT sum(amount) FROM order_history";

            $stmt = $db->prepare($query);

            $stmt->execute();

            if($stmt->rowCount() > 0){
                $totalSales =  doubleval($stmt->fetchColumn());
            } else {
                $totalSales =  0;
            }


            //Top Items
            $query = "SELECT checkout.itemid, inventory.name, inventory.id as itemId, sum(checkout.quantity) AS total FROM checkout INNER JOIN inventory WHERE checkout.itemid = inventory.id GROUP BY checkout.itemid ORDER BY total DESC limit 3;";

            $stmt = $db->prepare($query);

            $stmt->execute();

            if($stmt->rowCount() > 0) {
                $topitems = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            //Monthly Sales
            $query = "SELECT year(purchased_date) AS year, monthname(purchased_date) AS month, sum(amount) AS totalAmt FROM order_history GROUP BY year(purchased_date), month(purchased_date) ORDER BY year(purchased_date) DESC, month(purchased_date) DESC LIMIT 4";

            $stmt = $db->prepare($query);

            $stmt->execute();

             if($stmt->rowCount() > 0) {
                $monthlysales = $stmt->fetchAll(PDO::FETCH_ASSOC);
             }

            //Monthly Chargebacks
            $query = "SELECT year(chargebackDate) AS year, monthname(chargebackDate) AS month, sum(amount) AS cbTotalAmt FROM chargebacks GROUP BY year(chargebackDate), month(chargebackDate) ORDER BY year(chargebackDate) DESC, month(chargebackDate) DESC LIMIT 4";

            $stmt = $db->prepare($query);

            $stmt->execute();

            if($stmt->rowCount() > 0) {
                $chargebacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

        } catch(PDOException $ex){

            echo '<h3>System Error</h3>
                    <p class="text-error">There was a system error. Please try again later.</p>';

            echo "<small class='text-error'>$ex->getMessage()</small>";
        }

    }

?>
<h3>Summary</h3>
<table class="table table-striped table-bordered">
    <tr>
        <th>Total Sales</th>
        <th>Total Orders</th>
        <th>Average per Order</th>
    </tr>
    <tr>
        <?php if (isset($totalSales))
        {
            //changed here
            ?>
        <td><?php echo '$' . htmlentities(number_format($totalSales,2), ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php echo htmlentities($totalOrders, ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php echo '$' . htmlentities(number_format($totalSales / $totalOrders,2), ENT_QUOTES, 'UTF-8'); ?></td>

        <?php }?>
    </tr>
</table>
<h3>Top Items</h3>
    <?php
    echo '<table align="center"><tr>';

    if(isset($topitems)){

        foreach($topitems as $row){
            echo '<td><a href="itemdetail.php?id='; echo $row['itemId']; echo '"><img width="150" height="100" src="img/covers/'; echo trim($row['name']); echo '.jpg" class="img-polaroid"></img></a>&nbsp;&nbsp;&nbsp;&nbsp;</br>
                            <button class="btn btn-small" disabled><strong>Sold</strong>:'; echo htmlentities($row['total'], ENT_QUOTES, 'UTF-8'); echo '</button></td>';
        }
    }

    echo '</tr></table>'
    ?>
<h3>Monthly Sales</h3>
<table class="table table-bordered table-striped">
    <tr>
        <th>Month</th>
        <th>Year</th>
        <th>Total Sales</th>
    </tr>
    <?php
        if(isset($monthlysales)){
            foreach($monthlysales as $row){

                echo '<tr>';
                echo '<td>'; echo htmlentities($row['month'], ENT_QUOTES, 'UTF-8'); echo '</td>';
                echo '<td>'; echo htmlentities($row['year'], ENT_QUOTES, 'UTF-8'); echo '</td>';
                echo '<td>'; echo '$' . htmlentities(number_format($row['totalAmt'],2)); echo '</td>';
                echo '</tr>';
            }
        }

    ?>
</table>

    <h3>Monthly Chargebacks</h3>
    <table class="table table-bordered table-striped">
        <tr>
            <th>Month</th>
            <th>Year</th>
            <th>Total Chargebacks</th>
        </tr>
        <?php

        if(isset($chargebacks)){
            foreach($chargebacks as $row){

                echo '<tr>';
                echo '<td>'; echo htmlentities($row['month'], ENT_QUOTES, 'UTF-8'); echo '</td>';
                echo '<td>'; echo htmlentities($row['year'], ENT_QUOTES, 'UTF-8'); echo '</td>';
                echo '<td>'; echo '$' . htmlentities(number_format($row['cbTotalAmt'],2), ENT_QUOTES, 'UTF-8'); echo '</td>';
                echo '</tr>';
            }
        }

        ?>
    </table>

<?php
    include ('./includes/footer.html');
?>