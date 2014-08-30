<?php
    ob_start();
    $page_title = 'Inventory';

    include('includes/header.php');

    require_once("../config.php");

    require_once("Zebra_Pagination.php");

    function url_get_param($url, $name) {
        parse_str(parse_url($url, PHP_URL_QUERY), $vars);
        return isset($vars[$name]) ? true : false;
    }

    $records_per_page = 3;

    $pagination = new Zebra_Pagination();

    if($_SERVER['REQUEST_METHOD'] == 'GET'){

        try {

            //Count Format
            $query = "SELECT COUNT(format) as numPDF FROM inventory WHERE format = 'PDF'";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $numPDF = $stmt->fetchColumn();

            $query = "SELECT COUNT(format) as numHardCopy FROM inventory WHERE format = 'HardCopy'";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $numHardCopy = $stmt->fetchColumn();

            //Count Genre
            $query = "SELECT COUNT(category) as numJAVA FROM inventory WHERE category = 'JAVA'";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $numJAVA = $stmt->fetchColumn();

            $query = "SELECT COUNT(category) as numcpp FROM inventory WHERE category = 'c++'";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $numcpp = $stmt->fetchColumn();

            $query = "SELECT COUNT(category) as numMobile FROM inventory WHERE category = 'Mobile'";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $numMobile = $stmt->fetchColumn();

            $query = "SELECT COUNT(category) as numPHP FROM inventory WHERE category = 'PHP'";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $numPHP = $stmt->fetchColumn();

            //Count Prices
            $query = "SELECT COUNT(price) as num0to5 FROM inventory WHERE price < 5";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $num0to5 = $stmt->fetchColumn();

            $query = "SELECT COUNT(price) as num5to10 FROM inventory WHERE price >= 5 and price < 10";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $num5to10 = $stmt->fetchColumn();

            $query = "SELECT COUNT(price) as num10to15 FROM inventory WHERE price >= 10 and price < 15";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $num10to15 = $stmt->fetchColumn();

            $query = "SELECT COUNT(price) as num15to20 FROM inventory WHERE price >= 15 and price < 20";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $num15to20 = $stmt->fetchColumn();

            $query = "SELECT COUNT(price) as numOver20 FROM inventory WHERE price >= 20";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $numOver20 = $stmt->fetchColumn();

        } catch(PDOException $ex){

            echo '<h3>System Error</h3>
                            <p class="text-error">There was a system error. Try again later.</p>';

            echo "<small class='text-error'>$ex->getMessage()</small>";

        }
    }

    if($_SERVER['REQUEST_METHOD'] == 'GET' and isset($_GET['action']) and !empty($_GET['action'])) {

        try {

            if(isset($_GET['sort']) and !empty($_GET['sort']) and $_GET['sort'] == 'lowtohigh'){

                $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory ORDER BY price ASC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

            } else if((isset($_GET['sort']) and !empty($_GET['sort']) and $_GET['sort'] == 'hightolow')){

                $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory ORDER BY price DESC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

            } else {

                $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory ORDER BY dateAdded DESC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

            }

            $stmt = $db->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll();

            //Need for pagination
            $queryNumResults = 'SELECT FOUND_ROWS() as numRows';
            $stmt = $db->prepare($queryNumResults);
            $stmt->execute();
            $row = $stmt->fetchColumn();
            $pagination->records($row);

            $pagination->records_per_page($records_per_page);

        } catch(PDOException $ex){

            echo '<h3>System Error</h3>
                    <p class="text-error">There was a system error. Try again later.</p>';

            echo "<small class='text-error'>$ex->getMessage()</small>";
        }

        if($_GET['action'] == 'view' and isset($_GET['format']) and !empty($_GET['format'])){

            if($_GET['format'] == 'PDF'){

                try {

                    if(isset($_GET['sort']) and !empty($_GET['sort']) and $_GET['sort'] == 'lowtohigh'){

                        $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE format = "PDF" ORDER BY price ASC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

                    } else if((isset($_GET['sort']) and !empty($_GET['sort']) and $_GET['sort'] == 'hightolow')){

                        $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE format = "PDF" ORDER BY price DESC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

                    } else {

                        $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE format = "PDF" ORDER BY dateAdded DESC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

                    }

                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $results = $stmt->fetchAll();

                    //Need for pagination
                    $queryNumResults = 'SELECT FOUND_ROWS() as numRows';
                    $stmt = $db->prepare($queryNumResults);
                    $stmt->execute();
                    $row = $stmt->fetchColumn();
                    $pagination->records($row);

                    $pagination->records_per_page($records_per_page);

                } catch(PDOException $ex){

                    echo '<h3>System Error</h3>
                    <p class="text-error">There was a system error. Try again later.</p>';

                    echo "<small class='text-error'>$ex->getMessage()</small>";
                }

            }

            if($_GET['format'] == 'HardCopy'){

                try {

                    if(isset($_GET['sort']) and !empty($_GET['sort']) and $_GET['sort'] == 'lowtohigh'){

                        $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE format = "HardCopy" ORDER BY price ASC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

                    } else if((isset($_GET['sort']) and !empty($_GET['sort']) and $_GET['sort'] == 'hightolow')){

                        $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE format = "HardCopy" ORDER BY price DESC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

                    } else {

                        $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE format = "HardCopy" ORDER BY dateAdded DESC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

                    }

                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $results = $stmt->fetchAll();

                    //Need for pagination
                    $queryNumResults = 'SELECT FOUND_ROWS() as numRows';
                    $stmt = $db->prepare($queryNumResults);
                    $stmt->execute();
                    $row = $stmt->fetchColumn();
                    $pagination->records($row);

                    $pagination->records_per_page($records_per_page);

                } catch(PDOException $ex){

                    echo '<h3>System Error</h3>
                    <p class="text-error">There was a system error. Try again later.</p>';

                    echo "<small class='text-error'>$ex->getMessage()</small>";
                }
            }

        }

        if($_GET['action'] == 'view' and isset($_GET['category']) and !empty($_GET['category'])){

            if($_GET['category'] == 'JAVA'){

                try {

                    if(isset($_GET['sort']) and !empty($_GET['sort']) and $_GET['sort'] == 'lowtohigh'){

                        $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE category = "JAVA" ORDER BY price ASC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

                    } else if((isset($_GET['sort']) and !empty($_GET['sort']) and $_GET['sort'] == 'hightolow')){

                        $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE category = "JAVA" ORDER BY price DESC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

                    } else {

                        $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE category = "JAVA" ORDER BY dateAdded DESC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

                    }

                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $results = $stmt->fetchAll();

                    //Need for pagination
                    $queryNumResults = 'SELECT FOUND_ROWS() as numRows';
                    $stmt = $db->prepare($queryNumResults);
                    $stmt->execute();
                    $row = $stmt->fetchColumn();
                    $pagination->records($row);

                    $pagination->records_per_page($records_per_page);

                } catch(PDOException $ex){

                    echo '<h3>System Error</h3>
                    <p class="text-error">There was a system error. Try again later.</p>';

                    echo "<small class='text-error'>$ex->getMessage()</small>";
                }

            }

            if($_GET['category'] == 'c++'){

                try {

                    if(isset($_GET['sort']) and !empty($_GET['sort']) and $_GET['sort'] == 'lowtohigh'){

                        $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE category = "c++" ORDER BY price ASC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

                    } else if((isset($_GET['sort']) and !empty($_GET['sort']) and $_GET['sort'] == 'hightolow')){

                        $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE category = "c++" ORDER BY price DESC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

                    } else {

                        $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE category = "c++" ORDER BY dateAdded DESC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

                    }

                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $results = $stmt->fetchAll();

                    //Need for pagination
                    $queryNumResults = 'SELECT FOUND_ROWS() as numRows';
                    $stmt = $db->prepare($queryNumResults);
                    $stmt->execute();
                    $row = $stmt->fetchColumn();
                    $pagination->records($row);

                    $pagination->records_per_page($records_per_page);

                } catch(PDOException $ex){

                    echo '<h3>System Error</h3>
                    <p class="text-error">There was a system error. Try again later.</p>';

                    echo "<small class='text-error'>$ex->getMessage()</small>";
                }

            }

            if($_GET['category'] == 'Mobile'){

                try {

                    if(isset($_GET['sort']) and !empty($_GET['sort']) and $_GET['sort'] == 'lowtohigh'){

                        $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE category = "Mobile" ORDER BY price ASC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

                    } else if((isset($_GET['sort']) and !empty($_GET['sort']) and $_GET['sort'] == 'hightolow')){

                        $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE category = "Mobile" ORDER BY price DESC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

                    } else {

                        $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE category = "Mobile" ORDER BY dateAdded DESC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

                    }

                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $results = $stmt->fetchAll();

                    //Need for pagination
                    $queryNumResults = 'SELECT FOUND_ROWS() as numRows';
                    $stmt = $db->prepare($queryNumResults);
                    $stmt->execute();
                    $row = $stmt->fetchColumn();
                    $pagination->records($row);

                    $pagination->records_per_page($records_per_page);

                } catch(PDOException $ex){

                    echo '<h3>System Error</h3>
                    <p class="text-error">There was a system error. Try again later.</p>';

                    echo "<small class='text-error'>$ex->getMessage()</small>";
                }

            }

            if($_GET['category'] == 'scifi'){

                try {

                    if(isset($_GET['sort']) and !empty($_GET['sort']) and $_GET['sort'] == 'lowtohigh'){

                        $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE category = "PHP" ORDER BY price ASC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

                    } else if((isset($_GET['sort']) and !empty($_GET['sort']) and $_GET['sort'] == 'hightolow')){

                        $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE category = "PHP" ORDER BY price DESC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

                    } else {

                        $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE category = "PHP" ORDER BY dateAdded DESC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

                    }

                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $results = $stmt->fetchAll();

                    //Need for pagination
                    $queryNumResults = 'SELECT FOUND_ROWS() as numRows';
                    $stmt = $db->prepare($queryNumResults);
                    $stmt->execute();
                    $row = $stmt->fetchColumn();
                    $pagination->records($row);

                    $pagination->records_per_page($records_per_page);

                } catch(PDOException $ex){

                    echo '<h3>System Error</h3>
                    <p class="text-error">There was a system error. Try again later.</p>';

                    echo "<small class='text-error'>$ex->getMessage()</small>";
                }

            }

        }

        if($_GET['action'] == 'view' and isset($_GET['price']) and !empty($_GET['price'])){

            if($_GET['price'] == '0to5'){

                try {

                    if(isset($_GET['sort']) and !empty($_GET['sort']) and $_GET['sort'] == 'lowtohigh'){

                        $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE price < 5 ORDER BY price ASC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

                    } else if((isset($_GET['sort']) and !empty($_GET['sort']) and $_GET['sort'] == 'hightolow')){

                        $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE price < 5 ORDER BY price DESC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

                    } else {

                        $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE price < 5 ORDER BY dateAdded DESC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

                    }

                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $results = $stmt->fetchAll();

                    //Need for pagination
                    $queryNumResults = 'SELECT FOUND_ROWS() as numRows';
                    $stmt = $db->prepare($queryNumResults);
                    $stmt->execute();
                    $row = $stmt->fetchColumn();
                    $pagination->records($row);

                    $pagination->records_per_page($records_per_page);

                } catch(PDOException $ex){

                    echo '<h3>System Error</h3>
                    <p class="text-error">There was a system error. Try again later.</p>';

                    echo "<small class='text-error'>$ex->getMessage()</small>";
                }

            }

            if($_GET['price'] == '5to10'){

                try {

                    if(isset($_GET['sort']) and !empty($_GET['sort']) and $_GET['sort'] == 'lowtohigh'){

                        $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE price >= 5 and price < 10 ORDER BY price ASC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

                    } else if((isset($_GET['sort']) and !empty($_GET['sort']) and $_GET['sort'] == 'hightolow')){

                        $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE price >= 5 and price < 10 ORDER BY price DESC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

                    } else {

                        $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE price >= 5 and price < 10 ORDER BY dateAdded DESC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

                    }

                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $results = $stmt->fetchAll();

                    //Need for pagination
                    $queryNumResults = 'SELECT FOUND_ROWS() as numRows';
                    $stmt = $db->prepare($queryNumResults);
                    $stmt->execute();
                    $row = $stmt->fetchColumn();
                    $pagination->records($row);

                    $pagination->records_per_page($records_per_page);

                } catch(PDOException $ex){

                    echo '<h3>System Error</h3>
                    <p class="text-error">There was a system error. Try again later.</p>';

                    echo "<small class='text-error'>$ex->getMessage()</small>";
                }

            }

            if($_GET['price'] == '10to15'){

                try {

                    if(isset($_GET['sort']) and !empty($_GET['sort']) and $_GET['sort'] == 'lowtohigh'){

                        $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE price >= 10 and price < 15 ORDER BY price ASC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

                    } else if((isset($_GET['sort']) and !empty($_GET['sort']) and $_GET['sort'] == 'hightolow')){

                        $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE price >= 10 and price < 15 ORDER BY price DESC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

                    } else {

                        $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE price >= 10 and price < 15 ORDER BY dateAdded DESC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

                    }

                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $results = $stmt->fetchAll();

                    //Need for pagination
                    $queryNumResults = 'SELECT FOUND_ROWS() as numRows';
                    $stmt = $db->prepare($queryNumResults);
                    $stmt->execute();
                    $row = $stmt->fetchColumn();
                    $pagination->records($row);

                    $pagination->records_per_page($records_per_page);

                } catch(PDOException $ex){

                    echo '<h3>System Error</h3>
                    <p class="text-error">There was a system error. Try again later.</p>';

                    echo "<small class='text-error'>$ex->getMessage()</small>";
                }

            }

            if($_GET['price'] == '15to20'){

                try {

                    if(isset($_GET['sort']) and !empty($_GET['sort']) and $_GET['sort'] == 'lowtohigh'){

                        $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE price >= 15 and price < 20 ORDER BY price ASC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

                    } else if((isset($_GET['sort']) and !empty($_GET['sort']) and $_GET['sort'] == 'hightolow')){

                        $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE price >= 15 and price < 20 ORDER BY price DESC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

                    } else {

                        $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE price >= 15 and price < 20 ORDER BY dateAdded DESC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

                    }

                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $results = $stmt->fetchAll();

                    //Need for pagination
                    $queryNumResults = 'SELECT FOUND_ROWS() as numRows';
                    $stmt = $db->prepare($queryNumResults);
                    $stmt->execute();
                    $row = $stmt->fetchColumn();
                    $pagination->records($row);

                    $pagination->records_per_page($records_per_page);

                } catch(PDOException $ex){

                    echo '<h3>System Error</h3>
                    <p class="text-error">There was a system error. Try again later.</p>';

                    echo "<small class='text-error'>$ex->getMessage()</small>";
                }

            }

            if($_GET['price'] == 'over20'){

                try {

                    if(isset($_GET['sort']) and !empty($_GET['sort']) and $_GET['sort'] == 'lowtohigh'){

                        $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE price >= 20 ORDER BY price ASC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

                    } else if((isset($_GET['sort']) and !empty($_GET['sort']) and $_GET['sort'] == 'hightolow')){

                        $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE price >= 20 ORDER BY price DESC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

                    } else {

                        $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE  price >= 20 ORDER BY dateAdded DESC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;

                    }

                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $results = $stmt->fetchAll();

                    //Need for pagination
                    $queryNumResults = 'SELECT FOUND_ROWS() as numRows';
                    $stmt = $db->prepare($queryNumResults);
                    $stmt->execute();
                    $row = $stmt->fetchColumn();
                    $pagination->records($row);

                    $pagination->records_per_page($records_per_page);

                } catch(PDOException $ex){

                    echo '<h3>System Error</h3>
                    <p class="text-error">There was a system error. Try again later.</p>';

                    echo "<small class='text-error'>$ex->getMessage()</small>";
                }

            }

        }

    }

    if($_SERVER['REQUEST_METHOD'] == 'GET' and isset($_GET['search']) and !empty($_GET['search'])){

        try {

            if(isset($_GET['sort']) and !empty($_GET['sort']) and $_GET['sort'] == 'lowtohigh'){

                $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE name LIKE CONCAT("%", :itemName, "%") ORDER BY price ASC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;
                $stmt = $db->prepare($query);
                $stmt->bindParam(':itemName', $_GET['search'], PDO::PARAM_STR);
                $stmt->execute();
                $results = $stmt->fetchAll();


            } else if((isset($_GET['sort']) and !empty($_GET['sort']) and $_GET['sort'] == 'hightolow')){

                $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE name LIKE CONCAT("%", :itemName, "%") ORDER BY price DESC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;
                $stmt = $db->prepare($query);
                $stmt->bindParam(':itemName', $_GET['search'], PDO::PARAM_STR);
                $stmt->execute();
                $results = $stmt->fetchAll();


            } else {

                $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory WHERE name LIKE CONCAT("%", :itemName, "%") ORDER BY dateAdded DESC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;
                $stmt = $db->prepare($query);
                $stmt->bindParam(':itemName', $_GET['search'], PDO::PARAM_STR);
                $stmt->execute();
                $results = $stmt->fetchAll();
            }

            //Need for pagination
            $queryNumResults = 'SELECT FOUND_ROWS() as numRows';
            $stmt = $db->prepare($queryNumResults);
            $stmt->execute();
            $numResults = $stmt->fetchColumn();
            $pagination->records($numResults);

            $pagination->records_per_page($records_per_page);

        }  catch(PDOException $ex){

            echo '<h3>System Error</h3>
                    <p class="text-error">There was a system error. Try again later.</p>';

            echo "<small class='text-error'>$ex->getMessage()</small>";
        }

    }

    if($_SERVER['REQUEST_METHOD'] == 'GET' and (!isset($_GET['action']) or empty($_GET['action'])) and (!isset($_GET['search']) or empty($_GET['search']))) {

        try {

            if(isset($_GET['sort']) and !empty($_GET['sort']) and $_GET['sort'] == 'lowtohigh'){

                $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory ORDER BY price ASC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;
                $stmt = $db->prepare($query);
                $stmt->execute();
                $results = $stmt->fetchAll();


            } else if((isset($_GET['sort']) and !empty($_GET['sort']) and $_GET['sort'] == 'hightolow')){

                $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory ORDER BY price DESC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;
                $stmt = $db->prepare($query);
                $stmt->execute();
                $results = $stmt->fetchAll();

            } else {

                $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM inventory ORDER BY dateAdded DESC LIMIT ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page;
                $stmt = $db->prepare($query);
                $stmt->execute();
                $results = $stmt->fetchAll();

            }

            //Need for pagination
            $queryNumResults = 'SELECT FOUND_ROWS() as numRows';
            $stmt = $db->prepare($queryNumResults);
            $stmt->execute();
            $row = $stmt->fetchColumn();
            $pagination->records($row);

            $pagination->records_per_page($records_per_page);

        } catch(PDOException $ex){

            echo '<h3>System Error</h3>
                    <p class="text-error">There was a system error. Try again later.</p>';

            echo "<small class='text-error'>$ex->getMessage()</small>";
        }
    }
?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span3">
            <div class="sidebar-nav-fixed span2">
            <ul>
                <button class="btn btn-block btn-inverse btn-mini" disabled><b>Movie Search</b></button>
                <div style="line-height:100%;">
                    <br>
                </div>
                    <div align="center">
                <form class="form-search" action="inventory.php" method="get">
                    <div class="input-append">
                        <input class="input-small" name="search" type="text">
                        <button class="btn" type="submit"><i class="icon-search"></i></button>
                    </div>
                </form></div>
                <button class="btn btn-block btn-inverse btn-mini" disabled><b>Format</b></button>
                <ul>
                    <li class="smallFont"><a href="inventory.php?action=view&format=PDF"><small>PDF</small></a> <span class="badge"><?php if(isset($numPDF)) echo $numPDF; ?></span></li>
                    <li class="smallFont"><a href="inventory.php?action=view&format=HardCopy"><small>HardCopy</small></a> <span class="badge"><?php if(isset($numHardCopy)) echo $numHardCopy; ?></span></li>
                </ul>
                <button class="btn btn-block btn-inverse btn-mini" disabled><b>category</b></button>
                <ul>
                    <li class="smallFont"><a href="inventory.php?action=view&category=action"><small>JAVA</small></a> <span class="badge"><?php if(isset($numJAVA)) echo $numJAVA; ?></span></li>
                    <li class="smallFont"><a href="inventory.php?action=view&category=c++"><small>c++</small></a> <span class="badge"><?php if(isset($numcpp)) echo $numcpp; ?></span></li>
                    <li class="smallFont"><a href="inventory.php?action=view&category=Mobile"><small>Mobile</small></a> <span class="badge"><?php if(isset($numMobile)) echo $numMobile; ?></span></li>
                    <li class="smallFont"><a href="inventory.php?action=view&category=scifi"><small>PHP</small></a> <span class="badge"><?php if(isset($numPHP)) echo $numPHP; ?></span></li>
                </ul>
                <button class="btn btn-block btn-inverse btn-mini" disabled><b>Price</b></button>
                <ul>
                    <li class="smallFont"><a href="inventory.php?action=view&price=0to5"><small>$0 to $5</small></a> <span class="badge"><?php if(isset($num0to5)) echo $num0to5; ?></span></li>
                    <li class="smallFont"><a href="inventory.php?action=view&price=5to10"><small>$5 to $10</small></a> <span class="badge"><?php if(isset($num5to10)) echo $num5to10; ?></span></li>
                    <li class="smallFont"><a href="inventory.php?action=view&price=10to15"><small>$10 to $15</small></a> <span class="badge"><?php if(isset($num10to15)) echo $num10to15; ?></span></li>
                    <li class="smallFont"><a href="inventory.php?action=view&price=15to20"><small>$15 to $20</small></a> <span class="badge"><?php if(isset($num15to20)) echo $num15to20; ?></span></li>
                    <li class="smallFont"><a href="inventory.php?action=view&price=over20"><small>Over $20</small></a> <span class="badge"><?php if(isset($numOver20)) echo $numOver20; ?></span></li>
                </ul>
            </ul>
            </div>
        </div>
        <div class="span9">
            <?php if(!url_get_param($_SERVER['REQUEST_URI'], 'page')){
                if(isset($_GET['search']) and !empty($_GET['search'])){
                    echo '<div class="pull-left">';
                        echo '<small>Your search <strong>' . htmlentities($_GET['search'], ENT_QUOTES, 'UTF-8') . '</strong> matched <strong>' . htmlentities($numResults, ENT_QUOTES, 'UTF-8') . '</strong> ' . (($numResults == 1) ? 'movie' :  'movies') . '</small>';
                    echo '</div>';
                }
                echo '<div class="btn-group pull-right">
                <button class="btn">Price</button>
                <button class="btn dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li><a href="inventory.php';

                        if(!empty($_SERVER['QUERY_STRING']) and !url_get_param($_SERVER['REQUEST_URI'], 'sort')) {
                            echo '?' . $_SERVER['QUERY_STRING'] . '&sort=lowtohigh';
                        }

                        if(!empty($_SERVER['QUERY_STRING']) and url_get_param($_SERVER['REQUEST_URI'], 'sort') and count(explode('&', $_SERVER['QUERY_STRING'])) > 1){
                            $newParams = str_replace('hightolow', 'lowtohigh', $_SERVER['QUERY_STRING']);
                            echo '?' . $newParams;
                        }

                        if(count(explode('&', $_SERVER['QUERY_STRING'])) == 1 and !url_get_param($_SERVER['REQUEST_URI'], 'search')) {
                            echo '?sort=lowtohigh';
                        }

                    echo '">Low to High</a></li>

                    <li><a href="inventory.php';

                        if(!empty($_SERVER['QUERY_STRING']) and !url_get_param($_SERVER['REQUEST_URI'], 'sort')) {
                            echo '?' . $_SERVER['QUERY_STRING'] . '&sort=hightolow';
                        }

                        if(!empty($_SERVER['QUERY_STRING']) and url_get_param($_SERVER['REQUEST_URI'], 'sort') and count(explode('&', $_SERVER['QUERY_STRING'])) > 1){
                            $newParams = str_replace('lowtohigh', 'hightolow', $_SERVER['QUERY_STRING']);
                            echo '?' . $newParams;
                        }

                        if(count(explode('&', $_SERVER['QUERY_STRING'])) == 1 and !url_get_param($_SERVER['REQUEST_URI'], 'search')) {
                            echo '?sort=hightolow';
                        }

                echo '">High to Low</a></li>
                </ul>
            </div>
            <div style="line-height:45px;">
                <br>
            </div>';
            } ?>

            <?php
                foreach($results as $row){
                    echo '<table class="table borderless">';
                    echo '<tr>';
                    echo '<td class="centered span3 table-borderless">';
                    echo '<a href="itemdetail.php?id='; echo htmlentities($row['id'], ENT_QUOTES, 'UTF-8'); echo '"><img src="img/covers/'; echo htmlentities($row['name'], ENT_QUOTES, 'UTF-8'); echo '.jpg" width="150" height="100"></img></a>';
                    echo '<div align="center"><a href="itemdetail.php?id='; echo htmlentities($row["id"], ENT_QUOTES, 'UTF-8'); echo '" class="btn btn-mini btn-primary"><i class="icon-info-sign icon-white"></i>&nbsp;<b>Details</b></a></div>';
                    echo '</td>';
                    echo '<td>';
                    echo '<table class="table table-borderless">';
                    echo '<tr>
                        <td width="100"><p><button class="btn btn-mini btn-block disabled"><strong>Title</strong></button></td>
                        <td><small>'; if (isset($row["name"])) echo htmlentities($row["name"], ENT_QUOTES, "UTF-8"); echo '</small></td>
                    </tr>';
                    echo '<tr>
                        <td width="100"><p><button class="btn btn-mini btn-block disabled"><strong>Price</strong></button></td>
                        <td><small>'; if (isset($row["price"])) echo '$'. htmlentities(number_format($row["price"],2), ENT_QUOTES, "UTF-8"); echo '</small></td>
                    </tr>';
                    echo '<tr>
                        <td width="100"><p><button class="btn btn-mini btn-block disabled"><strong>Format</strong></button></td>
                        <td><small>'; if (isset($row["format"])) echo htmlentities($row["format"], ENT_QUOTES, 'UTF-8'); echo '</small></td>
                    </tr>';
                    echo '<tr>
                        <td width="100"><p><button class="btn btn-mini btn-block disabled"><strong>category</strong></button></td>
                        <td><small>'; if (isset($row["category"])) echo htmlentities($row["category"], ENT_QUOTES, 'UTF-8'); echo '</small></td>
                    </tr>';
                    echo '<tr>
                        <td width="100"><p><button class="btn btn-mini btn-block disabled"><strong>Description</strong></button></td>
                        <td><small>'; if (isset($row["description"])) echo htmlentities($row["description"], ENT_QUOTES, 'UTF-8'); echo '</small></td>
                    </tr>';

                    echo '<tr>
                        <td></td>
                        <td>
                           <a class="btn btn-success btn-medium" href="addtocart.php?id=';  echo htmlentities($row["id"], ENT_QUOTES, 'UTF-8'); echo '">
                                    <i class="icon-shopping-cart icon-white"></i>&nbsp;Add to Cart
                                </a>
                            </form>
                        </td>
                    </tr>';

                echo '</table>';
                    echo '</td>';
                    echo '</tr>';
                    echo '</table>';
                }
            ?>
        </div>
    </div>

</div>
<?php $pagination->render(); ?>
</br>
<style rel="stylesheet">

    .centered { vertical-align:middle; text-align:center; }
    .centered img { display:block; margin:0 auto; padding-bottom: 10px; }

    .table-borderless td {
        border: 0;
    }

    .sidebar-nav-fixed {
        position:fixed;
        top:60px;
    }

    .smallFont {
        font-size: medium;
        font-family: Arial;
    }

</style>
<?php
    include('includes/footer.html');
?>
