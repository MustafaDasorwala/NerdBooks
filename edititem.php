<?php

$page_title = 'Edit Item';

include('includes/header.php');

require_once("../config.php");

/*
if(isset($_FILES["file"]))
{
if ((($_FILES["file"]["type"] == "image/gif")|| ($_FILES["file"]["type"] == "image/jpeg")|| ($_FILES["file"]["type"] == "image/pjpeg"))&& ($_FILES["file"]["size"] < 2000000))
  {
  if ($_FILES["file"]["error"] > 0)
    {
    echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
    }
  else
    {
    echo "Upload: " . $_FILES["file"]["name"] . "<br />";
    echo "Type: " . $_FILES["file"]["type"] . "<br />";
    echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
    echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br />";

    if (file_exists("uploadpic/" . $_FILES["file"]["name"]))
      {
      echo $_FILES["file"]["name"] . " already exists. ";
      }
    else
      {
      move_uploaded_file($_FILES["file"]["tmp_name"], "uploadpic/" . $_POST['name']);
      //echo "Stored in: " . "uploadpic/" . $_FILES["file"]["name"];
      }
    }
  }
else
  {
  echo "Invalid file!";
  }
}
else
{
echo "bad";
}
*/

if(!isset($_SESSION['user'])) {

    header("Location: index.php");
    exit();

} elseif(!($_SESSION['user']['is_admin'])){
    header("Location: index.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'GET' and isset($_GET['id']) and !empty($_GET['id']))  {

    $item_does_not_exist = 0;

    $errors = array();

    $id = strip_tags(trim($_GET['id']));

    $query = "SELECT 1 FROM inventory WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    $stmt->execute();

    if($stmt->rowCount() == 0){
        $item_does_not_exist = 1;
    }

    if($item_does_not_exist) {
        $errors[] = 'The item does not exist';
    }

    if(empty($errors)) {

        try {

            $query = "SELECT * FROM inventory WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if($stmt->rowCount() > 0){
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
            }

        } catch(PDOException $ex){

            echo '<h3>System Error</h3>
                        <p class="text-error">There was a system error. Please try again later.</p>';

            echo "<small class='text-error'>$ex->getMessage()</small>";
        }

    } else {

        echo '<p>The following error(s) occurred:</p>';

        foreach($errors as $msg) {
            echo "<i class='icon-exclamation-sign'></i> <small class='text-error'>$msg</small><br/>";
        }

    }

}

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    $errors = array();

    if(($_POST['format'] == 'HardCopy') or ($_POST['format'] == 'PDF')) {
        $format = strip_tags(trim($_POST['format']));
    } else {
        $errors[] = "The format must either be PDF or HardCopy";
    }

    if(($_POST['category'] == 'JAVA') or ($_POST['category'] == 'c++')  or ($_POST['category'] == 'PHP')
        or ($_POST['category'] == 'Mobile')) {

        $category = strip_tags(trim($_POST['category']));

    } else {
        $errors[] = "The category you entered is invalid";
    }

    if(empty($_POST['price'])){
        $errors[] = 'You forgot to enter a price';
    } elseif(!preg_match('/^[0-9]+(?:\.[0-9]{0,2})?$/', $_POST['price'])) {
        $errors[] = "The price is invalid";
    } else {

        //setlocale(LC_MONETARY, 'en_US');
        //$price = money_format('%i', strip_tags(trim($_POST['price'])));

        $price = strip_tags(trim($_POST['price']));
    }

    if(empty($_POST['description'])){
        $errors[] = 'You forgot to enter the item description';
    } else {

        $description = strip_tags(trim($_POST['description']));
    }
    /*
    if(is_uploaded_file($_FILES["file"]["name"])){echo "enter1";}
    echo "Upload: " . $_FILES["file"]["name"] . "<br />";
    if(isset($_POST['file'])){
        echo "file is not sent";
        echo "Upload: " . $_POST["file"] . "<br />";
    }*/
    /*
    if(isset($_POST["file"]))
            {
            echo $_POST["file"]["type"];
            echo "enter";
                if ((($_POST["file"]["type"] == "image/gif")|| ($_POST["file"]["type"] == "image/jpeg")|| ($_POST["file"]["type"] == "image/pjpeg"))&& ($_POST["file"]["size"] < 2000000))
                {
                    if ($$_POST["file"]["error"] > 0)
                         {
                            $errors[]= "Return Code: " . $_FILES["file"]["error"] . "<br />";
                         }
                }
                else
                {
                $errors[] = "Invalid file! The picture should be gif or jpg and smaller than 2MB!";
                }
            }
        else
            {
            //if(!is_uploaded_file($_FILES["file"]["name"]))
                $errors[] = "No file!";
            }
        */
    //echo "Upload: " . $_POST['files'] . "<br />";
    if(!empty($_FILES["file"]["type"]))
    {
        //echo "enter it";
        if ((($_FILES["file"]["type"] == "image/gif")|| ($_FILES["file"]["type"] == "image/jpeg")|| ($_FILES["file"]["type"] == "image/pjpeg"))&& ($_FILES["file"]["size"] < 2000000))
        {
            if ($_FILES["file"]["error"] > 0)
            {
                $errors[]= "Return Code: " . $_FILES["file"]["error"] . "<br />";
            }
            else
            {
                //echo "Upload: " . $_FILES["file"]["name"] . "<br />";
                //echo "Type: " . $_FILES["file"]["type"] . "<br />";
                //echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
                //echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br />";

                //if (file_exists("uploadpic/" . $_FILES["file"]["name"]))
                //{
                //echo $_FILES["file"]["name"] . " already exists. ";
                //}
                //else
                //{
                //move_uploaded_file($_FILES["file"]["tmp_name"], "uploadpic/" . $_POST['name']);
                //echo "Stored in: " . "uploadpic/" . $_FILES["file"]["name"];
            }
        }
        else
        {
            echo "Invalid picture! The picture should be JPG and small than 2MB";
        }
    }
    else
    {
        //$errors[]= "You need select a picture!";
    }
    //echo $row["name"];
    $temp_pic= $_POST['movieName'].".jpg";
    //echo $temp_pic;
    //if (isset($row["name"])) echo htmlentities($row["name"], ENT_QUOTES, 'UTF-8');
    if(empty($errors)) {

        try {

            $query = "UPDATE inventory SET description = :description, price = :price,
                                  format = :format, category = :category WHERE id = :id";

            $stmt = $db->prepare($query);

            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':format', $format, PDO::PARAM_STR);
            $stmt->bindParam(':category', $category, PDO::PARAM_STR);
            $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_STR);
            move_uploaded_file($_FILES["file"]["tmp_name"], "img/covers/" .$temp_pic);
            $stmt->execute();

            $success_msg = "The item has been updated";

        } catch(PDOException $ex){

            echo '<h3>System Error</h3>
                        <p class="text-error">The item could not be added due to a system error</p>';

            echo "<small class='text-error'>$ex->getMessage()</small>";
        }


    } else {

        echo '<p>The following error(s) occurred:</p>';

        foreach($errors as $msg) {
            echo "<i class='icon-exclamation-sign'></i> <small class='text-error'>$msg</small><br/>";
        }
    }

    if(!empty($success_msg)) {

        echo "<i class='icon-ok'></i> <small class='text-success'>$success_msg</small><br/>";

    }

}

?>

<h2>Edit Item</h2>

<form class="form-horizontal" action="edititem.php" method="post" enctype="multipart/form-data">
    <div class="control-group">
        <label class="control-label" for="name">Name</label>
        <div class="controls">
            <input type="text" value="<?php
            if (isset($row["name"])) {
                echo htmlentities($row["name"], ENT_QUOTES, 'UTF-8');
            } elseif($_POST['movieName']){
                echo htmlentities($_POST["movieName"], ENT_QUOTES, 'UTF-8');
            }
             ?>" disabled><input type="hidden" name="movieName" value="<?php if (isset($row["name"])) echo htmlentities($row["name"], ENT_QUOTES, 'UTF-8'); ?>">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="format">Format</label>
        <div class="controls">
            <select name="format">
                <?php
                if(isset($row['format'])){
                    if($row['format'] == 'HardCopy'){
                        echo '
                            <option value="HardCopy" selected="selected">HardCopy</option>
                            <option value="PDF">PDF</option>';
                    } elseif($row['format'] == 'PDF') {
                        echo '
                            <option value="HardCopy">HardCopy</option>
                            <option value="PDF" selected="selected">PDF</option>';
                    }
                }


                if(isset($_POST['format'])){
                    if($_POST['format'] == 'HardCopy'){
                        echo '
                            <option value="HardCopy" selected="selected">HardCopy</option>
                            <option value="PDF">PDF</option>';
                    } elseif($_POST['format'] == 'PDF') {
                        echo '
                            <option value="HardCopy">HardCopy</option>
                            <option value="PDF" selected="selected">PDF</option>';
                    }
                }
                ?>
            </select>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="category">category</label>
        <div class="controls">
            <select name="category">
                <?php
                if(isset($row['category'])){
                    if($row['category'] == 'JAVA'){
                        echo '
                                <option value="JAVA" selected="selected">JAVA</option>
                                <option value="c++">c++</option>
                                <option value="PHP">PHP</option>
                                <option value="Mobile">Mobile</option>
                            </select>';
                    } elseif($row['category'] == 'c++') {
                        echo '
                            <option value="JAVA">JAVA</option>
                            <option value="c++" selected="selected">c++</option>
                            <option value="PHP">PHP</option>
                            <option value="Mobile">Mobile</option>';
                    } elseif($row['category'] == 'PHP') {
                        echo '
                            <option value="JAVA">JAVA</option>
                            <option value="c++">c++</option>
                            <option value="PHP" selected="selected">PHP</option>
                            <option value="Mobile">Mobile</option>';

                    } elseif($row['category'] == 'Mobile') {
                        echo '
                            <option value="JAVA">JAVA</option>
                            <option value="c++">c++</option>
                            <option value="PHP">PHP</option>
                            <option value="Mobile" selected="selected">Mobile</option>';
                    }
                }


                if(isset($_POST['category'])){

                    if($_POST['category'] == 'JAVA'){
                        echo '
                                <option value="JAVA" selected="selected">JAVA</option>
                                <option value="c++">c++</option>
                                <option value="PHP">PHP</option>
                                <option value="Mobile">Mobile</option>
                            </select>';
                    } elseif($_POST['category'] == 'c++') {
                        echo '
                            <option value="JAVA">JAVA</option>
                            <option value="c++" selected="selected">c++</option>
                            <option value="PHP">PHP</option>
                            <option value="Mobile">Mobile</option>';
                    } elseif($_POST['category'] == 'PHP') {
                        echo '
                            <option value="JAVA">JAVA</option>
                            <option value="c++">c++</option>
                            <option value="PHP" selected="selected">PHP</option>
                            <option value="Mobile">Mobile</option>';

                    } elseif($_POST['category'] == 'Mobile') {
                        echo '
                            <option value="JAVA">JAVA</option>
                            <option value="c++">c++</option>
                            <option value="PHP">PHP</option>
                            <option value="Mobile" selected="selected">Mobile</option>';
                    }
                }
                ?>
            </select>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="price">Price</label>
        <div class="controls">
            <input type="text" id="price" name="price" placeholder="Price" value="<?php
            if (isset($row['price'])) {
                echo htmlentities(number_format($row['price'],2), ENT_QUOTES, 'UTF-8');
            } elseif($_POST['price']){
                echo htmlentities(number_format($_POST['price'],2), ENT_QUOTES, 'UTF-8');
            }
            ?>">
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="file">Cover</label>
        <div class="controls">
            <input id="file" type="file" name="file" style="display:none">
            <div class="input-append">
                <input id="movieCover" class="input-large" type="text">
                <a class="btn" onclick="$('input[id=file]').click();">Browse</a>
            </div>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="pass2">Description</label>
        <div class="controls">
            <textarea name="description" rows="5" value="<?php

            if (isset($row['description'])){
                echo htmlentities($row['description'], ENT_QUOTES, 'UTF-8');
            } elseif(isset($_POST['description'])){
                echo htmlentities($_POST['description'], ENT_QUOTES, 'UTF-8');
            }?>"><?php if (isset($row['description'])){
                    echo htmlentities($row['description'], ENT_QUOTES, 'UTF-8');
             } elseif(isset($_POST['description'])){
                    echo htmlentities($_POST['description'], ENT_QUOTES, 'UTF-8');
                } ?></textarea>
        </div>
    </div>
    <div class="control-group">
        <div class="controls">
            <input type="hidden" name="id" value="<?php if (isset($row['id'])) echo htmlentities($row['id'], ENT_QUOTES, 'UTF-8'); ?>">
            <input type="submit" class="btn btn-info" value="Save" />
        </div>
    </div>
</form>

<script type="text/javascript">
    $('input[id=file]').change(function() {
        $('#movieCover').val($(this).val());
    });
</script>

<?php
include('includes/footer.html');
?>
