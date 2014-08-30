
<?php
ob_start();
$page_title = 'Inventory';

include('includes/header.php');

require_once("../config.php");
?>
<form class="form-horizontal"  action="inventory.php" method="get">

    <?php
	/*
	try {
			//echo "enter";

            $query = "SELECT * FROM inventory";

            $stmt = $db->prepare($query);

            $stmt->execute();
            
			$conut = $stmt->rowCount();

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

			echo $conut;

			 foreach($results as $row)
			{
				echo"1"; 
			}

        }catch(PDOException $ex){

            echo '<h3>System Error</h3>
                    <p class="text-error">The items could not be retrieved due to a system error</p>';

            echo "<small class='text-error'>$ex->getMessage()</small>";
        }
	*/
    if(isset($_GET["sort"]))
    {
        if(($_GET["sort"])=="byprice")
        {
            //echo "sort sort";
            echo "Sort:
	<select name=\"sort\">
	<option value=\"byprice\">Price Low to high</option>
	<option value=\"default\">default</option>
	</select>";
        }
        else
        {
            if(($_GET["sort"])=="default")
            {
                echo "Sort:
		<select name=\"sort\">
		<option value=\"default\">default</option>
		<option value=\"byprice\">Price Low to high</option>
		</select>";
            }
            else
            {
                //need to add warning information
            }
        }
    }
    else
    {
        echo "Sort:
<select name=\"sort\">
<option value=\"default\">default</option>
<option value=\"byprice\">Price Low to high</option>
</select>";
    }

    if (isset($_GET["filter"]))
    {
        if(($_GET["filter"])=="HardCopy")
        {
            echo " Filter:
	<select name=\"filter\">
	<option value=\"HardCopy\">HardCopy</option>
	<option value=\"nofilter\" >nofilter</option>
	<option value=\"PDF\">PDF</option>
	</select>
	";
        }
        else
            if(($_GET["filter"])=="PDF")
            {
                echo " Filter:
		<select name=\"filter\">
		<option value=\"PDF\">PDF</option>
		<option value=\"nofilter\" >nofilter</option>
		<option value=\"HardCopy\">HardCopy</option>
		</select>
		";
            }
            else
                if(($_GET["filter"])=="nofilter")
                {
                    echo " Filter:
			<select name=\"filter\">
			<option value=\"nofilter\" >nofilter</option>
			<option value=\"HardCopy\">HardCopy</option>
			<option value=\"PDF\">PDF</option>
			</select>
			";
                }
                else
                {
                    //need to add warning information
                }
    }
    else
    {
        echo " Filter:
	<select name=\"filter\">
	<option value=\"nofilter\" >nofilter</option>
	<option value=\"HardCopy\">HardCopy</option>
	<option value=\"PDF\">PDF</option>
	</select>
	";
    }
    ?>

    <input class="btn btn-info" type="submit" value="Submit"/>
</form >
<form  class="form-horizontal" action="inventory.php" method="get">

    Search: <input  type="text" name="searchname" />
    <input class="btn btn-info" type="submit" value="search" />
</form>
<?php
/*
$con = mysql_connect("localhost","root","gb891204");
if (!$con)
{
    die('Could not connect: ' . mysql_error());
}
else
{
    //echo "Connected";
}
mysql_select_db("moviezone", $con);
*/
if(isset($_GET["searchname"]))
{
    //search part
    $query = "SELECT * FROM inventory where name like :searchTerm";
	$stmt = $db->prepare($query);
    $stmt->bindParam(':searchTerm', $_GET['searchname'], PDO::PARAM_STR);
	$stmt->execute();            
	$conut = $stmt->rowCount();
	$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //$result = mysql_query("SELECT *  FROM inventory where name like '%".($_GET["searchname"])."%' ");
    //echo "SELECT ".($_GET["searchname"])."  FROM inventory ";
    //echo $result;
    //$result_num = mysql_num_rows($result);

    //echo $result_num;
}
else
{
	//echo "enter";
    if(!isset($_GET["filter"])) $v_filter="nofilter";
    else                        $v_filter=$_GET["filter"];
    if(!isset($_GET["sort"]))   $v_sort="default";
    else                        $v_sort=$_GET["sort"];

   // echo $v_filter;
    //echo $v_sort;
    //if (isset($_GET["filter"]))
    //filter part
    //{
    //echo "enter";
    //}
    //else
    //if (isset($_GET["orderbyprice"]))
    //price sort part
    //{
    //echo "enter";

    //}
    //else
    {
        //$result = mysql_query("SELECT * FROM inventory ");
        //echo "$result_num";
        // The result_num is the amount of selected result.
        if(isset($_GET["p"]))
        {
            $result_start = ($_GET["p"]-1)*4;
			
            if($v_sort=="default")
            {
                if($v_filter=="nofilter")
                {
					$query = "SELECT * FROM inventory";
					$stmt = $db->prepare($query);
					$stmt->execute();            
					$conut = $stmt->rowCount();
					$query = "SELECT * FROM inventory LIMIT $result_start,4";
					$stmt = $db->prepare($query);
					$stmt->execute();
					$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
					
					/*
                    $result = mysql_query("SELECT * FROM inventory ");
                    $result_num = mysql_num_rows($result);
                    $result = mysql_query("SELECT * FROM inventory LIMIT $result_start,4");
					*/
                }
                else if($v_filter=="HardCopy")
                {
					//echo"enter";
					$query = "SELECT * FROM inventory where format='HardCopy'";
					$stmt = $db->prepare($query);
					$stmt->execute();            
					$conut = $stmt->rowCount();
					$query = "SELECT * FROM inventory where format='HardCopy' LIMIT $result_start,4";
					$stmt = $db->prepare($query);
					$stmt->execute();
					$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
					/*
                    $result = mysql_query("SELECT * FROM inventory where format='DVD'");
                    $result_num = mysql_num_rows($result);
                    $result = mysql_query("SELECT * FROM inventory where format='DVD' LIMIT $result_start,4");
					*/
                }
                else
                {
					$query = "SELECT * FROM inventory where format='PDF'";
					$stmt = $db->prepare($query);
					$stmt->execute();            
					$conut = $stmt->rowCount();
					$query = "SELECT * FROM inventory where format='PDF' LIMIT $result_start,4";
					$stmt = $db->prepare($query);
					$stmt->execute();
					$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
					/*
                    $result = mysql_query("SELECT * FROM inventory where format='BluRay'");
                    $result_num = mysql_num_rows($result);
                    $result = mysql_query("SELECT * FROM inventory where format='BluRay' LIMIT $result_start,4");
					*/
                }
            }
            else
            {
                if($v_filter=="nofilter")
                {
					$query = "SELECT * FROM inventory";
					$stmt = $db->prepare($query);
					$stmt->execute();            
					$conut = $stmt->rowCount();
					$query = "SELECT * FROM inventory ORDER BY price LIMIT $result_start,4";
					$stmt = $db->prepare($query);
					$stmt->execute();
					$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
					//echo $_GET["p"];
					/*
                    $result = mysql_query("SELECT * FROM inventory ");
                    $result_num = mysql_num_rows($result);
                    $result = mysql_query("SELECT * FROM inventory ORDER BY price LIMIT $result_start,4");
					*/
                }
                else if($v_filter=="HardCopy")
                {
					$query = "SELECT * FROM inventory where format='HardCopy'";
					$stmt = $db->prepare($query);
					$stmt->execute();            
					$conut = $stmt->rowCount();
					$query = "SELECT * FROM inventory where format='HardCopy' ORDER BY price LIMIT $result_start,4";
					$stmt = $db->prepare($query);
					$stmt->execute();
					$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

					/*
                    $result = mysql_query("SELECT * FROM inventory where format='DVD'");
                    $result_num = mysql_num_rows($result);
                    $result = mysql_query("SELECT * FROM inventory where format='DVD' ORDER BY price LIMIT $result_start,4");
					*/
                }
                else
                {
					$query = "SELECT * FROM inventory where format='PDF'";
					$stmt = $db->prepare($query);
					$stmt->execute();            
					$conut = $stmt->rowCount();
					$query = "SELECT * FROM inventory where format='PDF' ORDER BY price LIMIT $result_start,4";
					$stmt = $db->prepare($query);
					$stmt->execute();
					$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
					/*
                    $result = mysql_query("SELECT * FROM inventory where format='BluRay'");
                    $result_num = mysql_num_rows($result);
                    $result = mysql_query("SELECT * FROM inventory where format='BluRay' ORDER BY price LIMIT $result_start,4");
					*/
                }
            }
            /*
            if (isset($_GET["orderbyprice"])&&(($_GET["orderbyprice"])==1))
                {
                //echo $result_start;
                $result = mysql_query("SELECT * FROM inventory ORDER BY price LIMIT $result_start , 4");
                }
            else
                {
            */

            //$result = mysql_query("SELECT * FROM inventory LIMIT $result_start , 4");
            //}
        }
        else
        {
		
            //echo"enter here";
            //echo $v_sort;
            //echo $v_filter;
            if($v_sort=="default")
            {
                if($v_filter=="nofilter")
                {
                    //echo "got the result_num";

					$query = "SELECT * FROM inventory";
					$stmt = $db->prepare($query);
					$stmt->execute();            
					$conut = $stmt->rowCount();
					$query = "SELECT * FROM inventory LIMIT 0,4";
					$stmt = $db->prepare($query);
					$stmt->execute();
					$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    //$conut = $stmt->rowCount();
					//echo $conut;

					/*
                    $result = mysql_query("SELECT * FROM inventory ");
                    $result_num = mysql_num_rows($result);
                    $result = mysql_query("SELECT * FROM inventory LIMIT 0,4");
					*/
                }
                else if($v_filter=="HardCopy")
                {
					$query = "SELECT * FROM inventory where format='HardCopy'";
					$stmt = $db->prepare($query);
					$stmt->execute();            
					$conut = $stmt->rowCount();
					$query = "SELECT * FROM inventory where format='HardCopy' LIMIT 0,4";
					$stmt = $db->prepare($query);
					$stmt->execute();
					$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
					/*
                    $result = mysql_query("SELECT * FROM inventory where format='DVD'");
                    $result_num = mysql_num_rows($result);
                    $result = mysql_query("SELECT * FROM inventory where format='DVD' LIMIT 0,4");
					*/
                }
                else
                {
					if($v_filter=="PDF")
					{
					$query = "SELECT * FROM inventory where format='PDF'";
					$stmt = $db->prepare($query);
					$stmt->execute();            
					$conut = $stmt->rowCount();
					$query = "SELECT * FROM inventory where format='PDF' LIMIT 0,4";
					$stmt = $db->prepare($query);
					$stmt->execute();
					$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
					}
					else
					{
					//echo"enter";
					/*$url="errorpage.php";
					echo "<script language='javascript' type='text/javascript'>";
					echo "window.location.href='$url'";
					echo "</script>";*/
                    header('Location:inventory.php');

					exit;
                    //header("Location: $url");
					}
					/*
                    $result = mysql_query("SELECT * FROM inventory where format='BluRay'");
                    $result_num = mysql_num_rows($result);
                    $result = mysql_query("SELECT * FROM inventory where format='BluRay' LIMIT 0,4");
					*/
                }
            }
            else
            {
			 if ($v_sort=="byprice")
			 {
				//echo"enter here";
                if($v_filter=="nofilter")
                {
					$query = "SELECT * FROM inventory";
					$stmt = $db->prepare($query);
					$stmt->execute();            
					$conut = $stmt->rowCount();
					$query = "SELECT * FROM inventory ORDER BY price LIMIT 0,4";
					$stmt = $db->prepare($query);
					$stmt->execute();
					$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
					/*
                    $result = mysql_query("SELECT * FROM inventory ");
                    $result_num = mysql_num_rows($result);
                    $result = mysql_query("SELECT * FROM inventory ORDER BY price LIMIT 0,4");
					*/
                }
                else if($v_filter=="HardCopy")
                {
					$query = "SELECT * FROM inventory where format='HardCopy'";
					$stmt = $db->prepare($query);
					$stmt->execute();            
					$conut = $stmt->rowCount();
					$query = "SELECT * FROM inventory where format='HardCopy' ORDER BY price LIMIT 0,4";
					$stmt = $db->prepare($query);
					$stmt->execute();
					$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
					/*
                    $result = mysql_query("SELECT * FROM inventory where format='DVD'");
                    $result_num = mysql_num_rows($result);
                    $result = mysql_query("SELECT * FROM inventory where format='DVD' ORDER BY price LIMIT 0,4");
					*/
                }
                else
                {
					if($v_filter=="PDF")
					{
					$query = "SELECT * FROM inventory where format='PDF'";
					$stmt = $db->prepare($query);
					$stmt->execute();            
					$conut = $stmt->rowCount();
					$query = "SELECT * FROM inventory where format='PDF' ORDER BY price LIMIT 0,4";
					$stmt = $db->prepare($query);
					$stmt->execute();
					$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

					/*
                    $result = mysql_query("SELECT * FROM inventory where format='BluRay'");
                    $result_num = mysql_num_rows($result);
                    $result = mysql_query("SELECT * FROM inventory where format='BluRay' ORDER BY price LIMIT 0,4");
					*/
					}
					else
					{
					//echo"enter";
					/*$url="errorpage.php";
					echo "<script language='javascript' type='text/javascript'>";
					echo "window.location.href='$url'";
					echo "</script>";*/
                        header('Location:inventory.php');

					exit;
					}
                }
			 }
			 else
				{
                    header('Location:inventory.php');
                    exit();
			 }
            }
		 
			 /*
			
		 $url="errorpage.php";
					echo "<script language='javascript' type='text/javascript'>";
					echo "window.location.href='$url'";
					echo "</script>";

					exit;*/
		 
        }
    }
}

if($results!=NULL)
{

    echo "<table class=\"table table-striped\">
	<tr>
	<th></th>
	<th></th>
	<th>Name</th>
	<th>Price</th>
	<th>Format</th>
	</tr>";


    foreach($results as $row)
    {

        echo "<tr>";
        echo '<td align=\"left\" width="75"> <a class="btn btn-success btn-small" href="addtocart.php?id='; echo $row['id']; echo '"><i class="icon-shopping-cart icon-white"></i>&nbsp;<strong>Add</strong></a></td>';
        echo '<td align=\"left\" width="100"> <a class="btn btn-primary btn-small" href="itemdetail.php?id='; echo $row['id']; echo '"><i class="icon-info-sign icon-white"></i>&nbsp;<strong>Details</strong></a></td>';
        echo "<td class=\"span3\">" . htmlentities($row['name'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td class=\"span2\">$" . htmlentities(number_format($row["price"],2), ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td >" . htmlentities($row["format"], ENT_QUOTES, 'UTF-8') . "</td>";
        //echo '<td align=\"left\"> <form action="itemdetail.php" method="get"><input type="hidden" name="id" value=' . $row["id"] . '><input class="btn btn-info btn-mini" type="submit" value="Detail"></form> </td>';
        echo "</tr>";

    }
    echo "</table>";
}
else
{
    echo "No result!";
}


?>

<div class="control-group">
    <?php
    if($results!=NULL)
    {

//echo $result_num;
        if(isset($_GET["p"]) )
            //judge if it is the first page
        {
            if($_GET["p"]!=1)
            {//if this is not the first page
                echo "<a  class=\"btn btn-mini btn-primary\" href=\"inventory.php?p=".($_GET["p"]-1)."&sort=$v_sort&filter=$v_filter\">Previous</a>";
                if(($result_start+4)<$conut)
                {
                    echo "<a  class=\"btn btn-mini btn-primary\" href=\"inventory.php?p=".($_GET["p"]+1)."&sort=$v_sort&filter=$v_filter\">Next</a>";
                }
            }
            else
                echo "<a  class=\"btn btn-mini btn-primary\" href=\"inventory.php?p=2&sort=$v_sort&filter=$v_filter\">Next</a>";
        }
        else
        {//if this is the first page
            if($conut >4)
            {
                //if(isset($_GET["orderbyprice"])&&(($_GET["orderbyprice"])==1))
                //{
                echo "<a  class=\"btn btn-mini btn-primary\" href=\"inventory.php?p=2&sort=$v_sort&filter=$v_filter\">Next</a>";
                //}
            }
        }
    }
    //mysql_close($con);
    ?>
</div>

<?php
include('includes/footer.html');
?>


