<?php

    require_once("../config.php");
    require_once("resources/PasswordHash.php");


if(!empty($_POST["username"]) && !empty($_POST["password"])){

        try {

            $query = "
            SELECT
                id,
                username,
                first_name,
                last_name,
                email,
                is_admin,
                password
            FROM customer_info
            WHERE
                username = :username
            ";

            //$query_params = array(
            //    ':username' => $_POST['username']
            //);

            $stmt = $db->prepare($query);
            $stmt->bindParam(':username', $_POST['username'], PDO::PARAM_STR);

            $stmt->execute();

        } catch(PDOException $ex){
            die("Failed to run query: " . $ex->getMessage());
        }

        $login_ok = false;
        $row = $stmt->fetch();

        if($row){

            $pwdHasher = new PasswordHash(8, FALSE);
            $password = $_POST['password'];
            $hash = $row['password'];
            $checked = $pwdHasher->CheckPassword($password, $hash);

            if($checked){
                $login_ok = true;
            }
        }

        if($login_ok){

            unset($row['password']);
            $_SESSION['user'] = $row;

        } else {

            header("Location: index.php");
            exit();

        }
    }
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $page_title; ?></title>
    <meta name="description" content="Your source for Books!">

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="jss/zebra_pagination.js"></script>
    <script type="text/javascript" src="js/jquery.carouFredSel-6.2.1-packed.js"></script>
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" href="css/zebra_pagination.css" type="text/css">

    <style type="text/css">
        body { background-color: white; }
        .hero-unit { background-color: #fff; }
        .center { display: block; margin: 0 auto; }
    </style>
</head>

<body>

<div class="navbar navbar-fixed-top navbar-inverse">
    <div class="navbar-inner">
        <div class="container">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <a class="brand" href="index.php">Tech Books</a>
            <div class="nav-collapse collapse">
                <ul class="nav pull-left">

                    <?php
                            if(!isset($_SESSION['user'])){

                                echo '<li><a href="index.php">Home</a></li>
                    <li><a href="inventory.php">Inventory</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">View <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="shoppingcart.php">Shopping Cart</a></li>
                        </ul>
                    </li>';
                            }
                            if(isset($_SESSION['user']) && !$_SESSION['user']['is_admin']) {

                                echo '<li><a href="index.php">Home</a></li>
                                        <li><a href="inventory.php">Inventory</a></li>
                                        <li class="dropdown">
                                        <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">View <b class="caret"></b></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="viewOrderHistory.php">Orders</a></li>
                                            <li><a href="shoppingcart.php">Shopping Cart</a></li>
                                        </ul>
                                    </li>
                                    <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Manage <b class="caret"></b></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="profile.php">Profile</a></li>
                                        <li><a href="changepass.php">Password</a></li>
                                        <li class="dropdown-submenu">
                                        <a tabindex="-1" href="viewcc.php">Credit Cards</a>
                                        <ul class="dropdown-menu">
                                            <li><a href="addcc.php">Add</a></li>
                                            <li><a href="viewcc.php">View</a></li>
                                        </ul>
                                      </li>';
                            }

                            if(isset($_SESSION['user']) && $_SESSION['user']['is_admin']) {

                                echo '<li><a href="index.php">Home</a></li>
                                        <li><a href="inventory.php">Inventory</a></li>
                                        <li class="dropdown">
                                        <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">View <b class="caret"></b></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="viewOrderHistory.php">Orders</a></li>
                                            <li><a href="shoppingcart.php">Shopping Cart</a></li>
                                        </ul>
                                    </li>
                                    <li class="dropdown">
                                      <a href="#" class="dropdown-toggle" data-toggle="dropdown">Manage <b class="caret"></b></a>
                                      <ul class="dropdown-menu">
                                      <li><a href="profile.php">Profile</a></li>
                                      <li><a href="changepass.php">Password</a></li>
                                      <li class="divider"></li>
                                      <li class="nav-header">Admin</li>
                                      <li class="dropdown-submenu">
                                        <a tabindex="-1" href="#">Users</a>
                                        <ul class="dropdown-menu">
                                            <li><a href="adduser.php">Add</a></li>
                                            <li><a href="viewusers.php">View</a></li>
                                        </ul>
                                      </li>
                                      <li class="dropdown-submenu">
                                        <a tabindex="-1" href="#">Inventory</a>
                                        <ul class="dropdown-menu">
                                            <li><a href="additem.php">Add</a></li>
                                            <li><a href="viewitems.php">View</a></li>
                                        </ul>
                                      </li>
                                      <li><a href="creditcards.php">Credit Cards</a></li>
                                      <li><a href="chargeback.php">Chargebacks</a></li>
                                      <li><a href="purchaseAlert.php">Alerts</a></li>
                                      <li><a href="salesinfo.php">Statistics</a></li>
                                      <li><a href="analysis.php">Analysis</a></li>
                                      <li><a href="settings.php">Settings</a></li>';
                            }

                        ?>
                        </ul>
                    </li>

                </ul>

                <ul class="nav pull-right">

                    <?php

                    if(isset($_SESSION['user'])) {
                        if(!$_SESSION['user']['is_admin']){
                            echo '<a href="profile.php" class="btn btn-primary">';
                        } elseif($_SESSION['user']['is_admin']){
                            echo '<a href="profile.php" class="btn btn-success">';
                        }

                        echo '<i class="icon-user icon-white"></i> ' . htmlentities($_SESSION['user']['username'], ENT_QUOTES, 'UTF-8');
                        echo '</a>';
                        echo '&nbsp;&nbsp;';
                        echo '<a href="logout.php" class="btn btn-danger"> <i class="icon-off icon-white"></i> Logout</a>';

                    } else {

                        echo '<li><a href="register.php">Register</a></li>
                            <li class="divider-vertical"></li>
                            <li class="dropdown">
                            <a class="dropdown-toggle" href="#" data-toggle="dropdown">Log In <strong class="caret"></strong></a>
                            <div class="dropdown-menu" style="padding: 15px; padding-bottom: 0px;">
                                <form action="index.php" method="post">
                                    <input type="text" name="username" placeholder="Username" value=""/>
                                    <br />
                                    <input type="password" name="password" placeholder ="Password" value="" />
                                    <br />
                                    <input type="submit" class="btn btn-info" value="Login" />
                                </form>
                            </div>
                         </li>';
                    }

                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="container hero-unit">