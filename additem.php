<?php

$page_title = 'Add Item';

include('includes/header.php');

require_once("../config.php");

if(!isset($_SESSION['user'])) {

    header("Location: index.php");
    exit();

} elseif(!($_SESSION['user']['is_admin'])){
    header("Location: index.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    $item_exists = 0;
    $errors = array();

    if(empty($_POST['name'])){
        $errors[] = 'You forgot the item name';
    } else {

        $name = strip_tags(trim($_POST['name']));

        $query = "SELECT 1 FROM inventory WHERE name = :name";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);

        $stmt->execute();

        if($stmt->rowCount() > 0){
            $item_exists = 1;
        }

        if($item_exists) {
            $errors[] = 'The item you entered already exists';
        }
    }

    if(($_POST['format'] == 'HardCopy') or ($_POST['format'] == 'PDF')) {
        $format = strip_tags(trim($_POST['format']));
    } else {
        $errors[] = "The format must either be HardCopy or PDF";
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
    //echo "asda";
    //echo (is_uploaded_file($_FILES["file"]["name"]));
    if(!empty($_FILES["file"]["type"]))
    {
        if ( ($_FILES["file"]["type"] == "image/jpeg")&& ($_FILES["file"]["size"] < 2000000))
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
            $errors[]= "Invalid image. The image should be JPEG and smaller than 2MB.";
        }
    }
    else
    {
        $errors[]= "You need select a cover for the movie";
    }
    //echo $row["name"];
    $temp_pic= $_POST["name"].".jpg";
    if(empty($errors)) {

        try {

            $query = "INSERT INTO inventory (name, description, price, format, category, dateAdded)
                        VALUES(:name, :description, :price, :format, :category, NOW())";

            $stmt = $db->prepare($query);

            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':format', $format, PDO::PARAM_STR);
            $stmt->bindParam(':category', $category, PDO::PARAM_STR);
            move_uploaded_file($_FILES["file"]["tmp_name"], "img/covers/" .$temp_pic);
            $stmt->execute();

            $success_msg = "The item has been added";

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


<h2>Add Item</h2> <br />

<form class="form-horizontal" action="additem.php" method="post" enctype="multipart/form-data">
    <div class="control-group">
        <label class="control-label" for="name">Name</label>
        <div class="controls">
            <input type="text" id="name" name="name" placeholder="Item Name" value="<?php if (isset($_POST["name"])) echo htmlentities($_POST["name"], ENT_QUOTES, 'UTF-8'); ?>">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="format">Format</label>
        <div class="controls">
            <select name="format">
                <option value="HardCopy" selected="selected">HardCopy</option>
                <option value="PDF">PDF</option>
            </select>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="category">category</label>
        <div class="controls">
            <select name="category">
                <option value="JAVA" selected="selected">JAVA</option>
                <option value="c++">c++</option>
                <option value="PHP">PHP</option>
                <option value="Mobile">Mobile</option>
            </select>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="price">Price</label>
        <div class="controls">
            <input type="text" id="price" name="price" placeholder="Price">
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
            <textarea name="description" maxlength="2000" rows="5"></textarea>
        </div>
    </div>
    <div class="control-group">
        <div class="controls">
            <input type="submit" class="btn btn-info" value="Save" />
        </div>
    </div>
</form>

<style rel="stylesheet">
    textarea {
        resize: none;
    }
</style>

<script type="text/javascript">
    $('input[id=file]').change(function() {
        $('#movieCover').val($(this).val());
    });
</script>

<?php
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
*/
?>

<?php
include('includes/footer.html');
?>
