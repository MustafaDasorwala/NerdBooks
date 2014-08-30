<?php

    $page_title = 'Movie Zone';

    include('includes/header.php');

    require_once("../config.php");

        //Recently Added
        $query = "SELECT id, name, price FROM inventory ORDER BY dateAdded DESC LIMIT 10";
        $stmt = $db->prepare($query);

        $stmt->execute();

        if($stmt->rowCount() > 0){

            $results = $stmt->fetchAll();
        }

        //Highest Rated
        $query = " SELECT DISTINCT inventory.id as itemId, inventory.name as itemName, AVG(item_reviews.rating) as avgReview FROM inventory INNER JOIN item_reviews ON item_reviews.item_reviews_itemID = inventory.id GROUP BY inventory.name ORDER BY avgReview DESC";

        $stmt = $db->prepare($query);

        $stmt->execute();

        if($stmt->rowCount() > 0){

            $topRated = $stmt->fetchAll();
        }

?>

<h3>Recently Added</h3>
    <div class="recentlyAdded" align="center">
    <div id="recentlyAdded">
    <?php

        if(isset($results)){
            foreach($results as $row){

                echo '<a href="itemdetail.php?id='; echo $row['id']; echo '"><img src="img/covers/'; echo trim($row['name']); echo '.jpg" width="175" height="125"></img></a>';
            }
        }

         ?>
        </div>

        <div class="clearfix"></div>
    </div>

    <div align="center"><button id="prev_btn" class="btn btn-mini btn-inverse"><i class="icon-arrow-left icon-white"></i></button>&nbsp;&nbsp;<button id="next_btn" class="btn btn-mini btn-inverse"><i class="icon-arrow-right icon-white"></i></button></div>

    <h3>Top Rated</h3>
    <div class="topRated" align="center">
        <div id="topRated">
            <?php

            if(isset($topRated)){
                foreach($topRated as $rowItem){

                    echo '<a href="itemdetail.php?id='; echo $rowItem['itemId']; echo '"><img src="img/covers/'; echo trim($rowItem['itemName']); echo '.jpg" width="175" height="125"></img></a>';
                }
            }

            ?>
        </div>

        <div class="clearfix"></div>
    </div>

    <div align="center"><button id="prev_tr_btn" class="btn btn-mini btn-inverse"><i class="icon-arrow-left icon-white"></i></button>&nbsp;&nbsp;<button id="next_tr_btn" class="btn btn-mini btn-inverse"><i class="icon-arrow-right icon-white"></i></button></div>

    <script>
        $(window).load(function() {

        // Using default configuration
        //$("#topRated").carouFredSel();

        // Using custom configuration
        $("#recentlyAdded").carouFredSel({
            items				: {
                visible: 5
            },
            circular            : true,
            direction           : "left",
            responsive          : false,
            prev: {
                button:        "#prev_btn",
                items:          1
            },
            next: {
                button:        "#next_btn",
                items:         1
            },
            scroll: {
                easing:         "quadratic"
            },
            auto:               false
        });

        $("#topRated").carouFredSel({
            items				: {
                maximum: 5,
                minimum: 1
            },
            circular            : true,
            direction           : "left",
            responsive          : false,
            prev: {
                button:        "#prev_tr_btn",
                items:          1
            },
            next: {
                button:        "#next_tr_btn",
                items:         1
            },
            scroll: {
                easing:         "quadratic"
            },
            auto:               false
        });
    });
</script>
<style>
    .recentlyAdded {
        padding: 15px 0 15px 40px;
    }

    .recentlyAdded a {
        display: block;
        float: left;
    }

    .recentlyAdded img {
        border: 1px solid #ccc;
        background-color: white;
        padding: 9px;
        margin: 7px;
        display: block;
        float: left;
    }

    .topRated {
        padding: 15px 0 15px 40px;
    }

    .topRated a {
        display: block;
        float: left;
    }

    .topRated img {
        border: 1px solid #ccc;
        background-color: white;
        padding: 9px;
        margin: 7px;
        display: block;
        float: left;
    }

    .clearfix {
        float: none;
        clear: both;
    }
</style>
<?php
    include ('./includes/footer.html');
?>