<?php
$page_title = 'Checkout Page';
include ('includes/header.php');
include('functions.php');
require_once ("../config.php");
if (!isset($_SESSION['user']))
{
    header("Location: index.php");
}
else{
    if($_SESSION['cart']){
        if(isset($_POST['street']) and !empty($_POST['street']) and $_POST['city'] and !empty($_POST['city']) and $_POST['state'] and !empty($_POST['state']) and $_POST['zipcode'] and !empty($_POST['zipcode'])){
            $_SESSION['checkout']['street']=strip_tags(trim($_POST['street']));
            $_SESSION['checkout']['city']=strip_tags(trim($_POST['city']));
            $_SESSION['checkout']['state']=strip_tags(trim($_POST['state']));
            $_SESSION['checkout']['zipcode']=strip_tags(trim($_POST['zipcode']));
            updateaddress($db,$_SESSION['user']['id'],strip_tags(trim($_POST['street'])), strip_tags(trim($_POST['city'])), strip_tags(trim($_POST['state'])), strip_tags(trim($_POST['zipcode'])));
        }
        $userid=$_SESSION['user']['id'];
        $query = "SELECT street, city, state, zipcode FROM customer_info WHERE customer_info.id=:userid ";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetch(PDO::FETCH_ASSOC);
        if(is_null($results['street']) and is_null($results['city']) and is_null($results['state']) and is_null($results['zipcode'])){
            ?><h2>Shipping Address</h2></br>

            <form class="form-horizontal" action="checkout.php" method="post">
                <div class="control-group">
                    <label class="control-label" for="street">Street</label>
                    <div class="controls">
                        <input type="text" id="street" name="street" placeholder="Street">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="city">City</label>
                    <div class="controls">
                        <input type="text" id="city" name="city" placeholder="City">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">State</label>
                    <div class="controls">
                    <select name="state" >
                        <option value="AL" selected="selected">Alabama</option>
                        <option value="AK">Alaska</option>
                        <option value="AZ">Arizona</option>
                        <option value="AR">Arkansas</option>
                        <option value="CA">California</option>
                        <option value="CO">Colorado</option>
                        <option value="CT">Connecticut</option>
                        <option value="DE">Delaware</option>
                        <option value="DC">District Of Columbia</option>
                        <option value="FL">Florida</option>
                        <option value="GA">Georgia</option>
                        <option value="HI">Hawaii</option>
                        <option value="ID">Idaho</option>
                        <option value="IL">Illinois</option>
                        <option value="IN">Indiana</option>
                        <option value="IA">Iowa</option>
                        <option value="KS">Kansas</option>
                        <option value="KY">Kentucky</option>
                        <option value="LA">Louisiana</option>
                        <option value="ME">Maine</option>
                        <option value="MD">Maryland</option>
                        <option value="MA">Massachusetts</option>
                        <option value="MI">Michigan</option>
                        <option value="MN">Minnesota</option>
                        <option value="MS">Mississippi</option>
                        <option value="MO">Missouri</option>
                        <option value="MT">Montana</option>
                        <option value="NE">Nebraska</option>
                        <option value="NV">Nevada</option>
                        <option value="NH">New Hampshire</option>
                        <option value="NJ">New Jersey</option>
                        <option value="NM">New Mexico</option>
                        <option value="NY">New York</option>
                        <option value="NC">North Carolina</option>
                        <option value="ND">North Dakota</option>
                        <option value="OH">Ohio</option>
                        <option value="OK">Oklahoma</option>
                        <option value="OR">Oregon</option>
                        <option value="PA">Pennsylvania</option>
                        <option value="RI">Rhode Island</option>
                        <option value="SC">South Carolina</option>
                        <option value="SD">South Dakota</option>
                        <option value="TN">Tennessee</option>
                        <option value="TX">Texas</option>
                        <option value="UT">Utah</option>
                        <option value="VT">Vermont</option>
                        <option value="VA">Virginia</option>
                        <option value="WA">Washington</option>
                        <option value="WV">West Virginia</option>
                        <option value="WI">Wisconsin</option>
                        <option value="WY">Wyoming</option>
                    </select>
                        </div>
                </div>
                    <div class="control-group">
                        <label class="control-label" for="zipcode">Zipcode</label>
                        <div class="controls">
                            <input type="text" id="zipcode" name="zipcode" maxlength="5" placeholder="Zipcode">
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="controls">
                            <input type="submit" class="btn btn-info" value="Save" />
                        </div>
                    </div>
                </div>
            </form>
        <?php
        }
        else{
            echo '<h3>Shipping Address';
                echo '<table class="table">';
            echo '<tr>';
            echo '<td>';
            echo '<form class="form-horizontal" action="cccheckout.php" method="POST">';
            echo '<div class="control-group">
            <label class="control-label" for="street">Street</label>
            <div class="controls">
                <input type="text" value="'; echo htmlentities($results['street'], ENT_QUOTES, 'UTF-8'); echo '" disabled>';
            echo '</div>
        </div>';
            echo '<div class="control-group">
            <label class="control-label" for="street">City</label>
            <div class="controls">
                <input type="text" value="'; echo htmlentities($results['city'], ENT_QUOTES, 'UTF-8'); echo '" disabled>';
            echo '</div>
        </div>';
            echo '<div class="control-group">
            <label class="control-label" for="street">State</label>
            <div class="controls">
                <input type="text" value="'; echo htmlentities($results['state'], ENT_QUOTES, 'UTF-8'); echo '" disabled>';
            echo '</div>
        </div>';
            echo '<div class="control-group">
            <label class="control-label" for="street">Zipcode</label>
            <div class="controls">
                <input type="text" value="'; echo htmlentities($results['zipcode'], ENT_QUOTES, 'UTF-8'); echo '" disabled>';
            echo '</div>
        </div>';
            echo '<input type="hidden" name="selectedStreet" value="'; echo htmlentities($results['street'], ENT_QUOTES, 'UTF-8'); echo '">';
            echo '<input type="hidden" name="selectedCity" value="'; echo htmlentities($results['city'], ENT_QUOTES, 'UTF-8'); echo '">';
            echo '<input type="hidden" name="selectedState" value="'; echo htmlentities($results['state'], ENT_QUOTES, 'UTF-8'); echo '">';
            echo '<input type="hidden" name="selectedZipcode" value="'; echo htmlentities($results['zipcode'], ENT_QUOTES, 'UTF-8'); echo '">';
            echo '<div class="control-group">
            <div class="controls">
                <input class="btn btn-success" type="submit" value="Ship to Address">';
            echo '</div>
        </div>';
            echo '</form>';

            echo '</td>';
            echo '</tr>';
                echo '</table>';

        }

        ?>

    <?php
    }else echo "Your cart is empty";
}
?>
<?php
include('./includes/footer.html');
?>
