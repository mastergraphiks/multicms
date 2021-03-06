<?php require_once('../includes/rayicecms.php'); ?>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "login.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "member";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "login.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) 
  $MM_referrer .= "?" . $QUERY_STRING;
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysqli_real_escape_string") ? mysqli_real_escape_string(dbconnect(), $theValue) : mysqli_escape_string(dbconnect(), $theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysqli_real_escape_string") ? mysqli_real_escape_string(dbconnect(), $theValue) : mysqli_escape_string(dbconnect(), $theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$colname_rsM = "-1";
if (isset($_GET['tutorialsid'])) {
  $colname_rsM = $_GET['tutorialsid'];
}
mysqli_select_db(dbconnect(),$database_rayicecms);
$query_rsM = sprintf("SELECT * FROM tutorials WHERE tutorialsid = %s", GetSQLValueString($colname_rsM, "int"));
$rsM = mysqli_query(dbconnect(),$query_rsM) or die(mysqli_connect_error());
$row_rsM = mysqli_fetch_assoc($rsM);
$totalRows_rsM = mysqli_num_rows($rsM);

mysqli_select_db(dbconnect(),$database_rayicecms);
$query_rsT = "SELECT * FROM tutorials ORDER BY tutorialsid DESC";
$rsT = mysqli_query(dbconnect(),$query_rsT) or die(mysqli_connect_error());
$row_rsT = mysqli_fetch_assoc($rsT);
$totalRows_rsT = mysqli_num_rows($rsT);

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "adpost")) {
	
  // UPLOAD CODE BY SYED RAZA ALI START //
   	$allowed_filetypes = array('.jpg','.gif','.bmp','.png');
  	$no_ID = $row_rsM['tutorialsid'];
	$max_filesize = 5250000;
  	$upload_path = 'images/tutorials/';
	if($_FILES['imageurl']['size'] == 0 || empty($_FILES['imageurl']['name']))
		{
   		   $filename = $row_rsM['imageurl'];
		}
	else
		{
   		   $filename = $no_ID."".str_replace(' ', '_', $_FILES['imageurl']['name']);
		   $ext = substr($filename, strpos($filename,'.'), strlen($filename)-1); // Get the extension from the filename.
 	 	   if(!in_array($ext,$allowed_filetypes))
   		   {die('The file you attempted to upload is not allowed.');} 
   		   if(filesize($_FILES['imageurl']['tmp_name']) > $max_filesize)
           {die('The file you attempted to upload is too large.');}
           if(!is_writable($upload_path))
           {die('You cannot upload to the specified directory, please CHMOD it to 777.');}   
           if(move_uploaded_file($_FILES['imageurl']['tmp_name'],$upload_path . $filename))
           {echo 'Your file upload was successful';} 
           else{echo'There was an error during the file upload.  Please try again.';} 
		}
   // UPLOAD CODE BY SYED RAZA ALI END //
   
  $updateSQL = sprintf("UPDATE tutorials SET title=%s, `description`=%s, imageurl=%s, owner=%s, `date`=%s, catename=%s, tutorialsurl=%s, tutorialpathurl=%s, status=%s, rating=%s, views=%s, `position`=%s, users=%s WHERE tutorialsid=%s",
                       GetSQLValueString($_POST['title'], "text"),
                       GetSQLValueString($_POST['description'], "text"),
                       GetSQLValueString($filename, "text"),
                       GetSQLValueString($_POST['owner'], "text"),
                       GetSQLValueString($_POST['date'], "text"),
                       GetSQLValueString($_POST['catename'], "text"),
                       GetSQLValueString($_POST['tutorialsurl'], "text"),
                       GetSQLValueString($_POST['tutorialpathurl'], "text"),
                       GetSQLValueString($_POST['status'], "text"),
                       GetSQLValueString($_POST['rating'], "text"),
                       GetSQLValueString($_POST['views'], "text"),
                       GetSQLValueString($_POST['position'], "text"),
                       GetSQLValueString($_POST['users'], "text"),
                       GetSQLValueString($_POST['tutorialsid'], "int"));

  mysqli_select_db(dbconnect(),$database_rayicecms);
  $Result1 = mysqli_query(dbconnect(),$updateSQL) or die(mysqli_connect_error());

  $updateGoTo = "mytutorials.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$currentPage = $_SERVER["PHP_SELF"];

mysqli_select_db(dbconnect(),$database_rayicecms);
$query_setting = "SELECT * FROM settings";
$setting = mysqli_query(dbconnect(),$query_setting) or die(mysqli_connect_error());
$row_setting = mysqli_fetch_assoc($setting);
$totalRows_setting = mysqli_num_rows($setting);

mysqli_select_db(dbconnect(),$database_rayicecms);
$query_blogcates = "SELECT * FROM categories WHERE selecttopic = 'tutorials'";
$blogcates = mysqli_query(dbconnect(),$query_blogcates) or die(mysqli_connect_error());
$row_blogcates = mysqli_fetch_assoc($blogcates);
$totalRows_blogcates = mysqli_num_rows($blogcates);

mysqli_select_db(dbconnect(),$database_rayicecms);
$query_pages = "SELECT * FROM pages";
$pages = mysqli_query(dbconnect(),$query_pages) or die(mysqli_connect_error());
$row_pages = mysqli_fetch_assoc($pages);
$totalRows_pages = mysqli_num_rows($pages);


mysqli_select_db(dbconnect(),$database_rayicecms);
$query_parts = "SELECT * FROM parts";
$parts = mysqli_query(dbconnect(),$query_parts) or die(mysqli_connect_error());
$row_parts = mysqli_fetch_assoc($parts);
$totalRows_parts = mysqli_num_rows($parts);

mysqli_select_db(dbconnect(),$database_rayicecms);
$query_theme = "SELECT * FROM themes";
$theme = mysqli_query(dbconnect(),$query_theme) or die(mysqli_connect_error());
$row_theme = mysqli_fetch_assoc($theme);
$totalRows_theme = mysqli_num_rows($theme);


$maxRows_tutorials = 10;
$pageNum_tutorials = 0;
if (isset($_GET['pageNum_tutorials'])) {
  $pageNum_tutorials = $_GET['pageNum_tutorials'];
}
$startRow_tutorials = $pageNum_tutorials * $maxRows_tutorials;

mysqli_select_db(dbconnect(),$database_rayicecms);
$query_tutorials = "SELECT * FROM tutorials WHERE status = 'published' ORDER BY tutorialsid DESC";
$query_limit_tutorials = sprintf("%s LIMIT %d, %d", $query_tutorials, $startRow_tutorials, $maxRows_tutorials);
$tutorials = mysqli_query(dbconnect(),$query_limit_tutorials) or die(mysqli_connect_error());
$row_tutorials = mysqli_fetch_assoc($tutorials);

if (isset($_GET['totalRows_tutorials'])) {
  $totalRows_tutorials = $_GET['totalRows_tutorials'];
} else {
  $all_tutorials = mysqli_query(dbconnect(),$query_tutorials);
  $totalRows_tutorials = mysqli_num_rows($all_tutorials);
}
$totalPages_tutorials = ceil($totalRows_tutorials/$maxRows_tutorials)-1;

mysqli_select_db(dbconnect(),$database_rayicecms);
$query_links = "SELECT * FROM friendlinks";
$links = mysqli_query(dbconnect(),$query_links) or die(mysqli_connect_error());
$row_links = mysqli_fetch_assoc($links);
$totalRows_links = mysqli_num_rows($links);

mysqli_select_db(dbconnect(),$database_rayicecms);
$query_featuredadposts = "SELECT * FROM tutorials WHERE position = 'featured' ORDER BY tutorialsid DESC";
$featuredadposts = mysqli_query(dbconnect(),$query_featuredadposts) or die(mysqli_connect_error());
$row_featuredadposts = mysqli_fetch_assoc($featuredadposts);
$totalRows_featuredadposts = mysqli_num_rows($featuredadposts);

mysqli_select_db(dbconnect(),$database_rayicecms);
$query_cates = "SELECT * FROM categories WHERE selecttopic = 'tutorials'";
$cates = mysqli_query(dbconnect(),$query_cates) or die(mysqli_connect_error());
$row_cates = mysqli_fetch_assoc($cates);
$totalRows_cates = mysqli_num_rows($cates);

$colname_members = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_members = $_SESSION['MM_Username'];
}
mysqli_select_db(dbconnect(),$database_rayicecms);
$query_members = sprintf("SELECT * FROM members WHERE users = %s", GetSQLValueString($colname_members, "text"));
$members = mysqli_query(dbconnect(),$query_members) or die(mysqli_connect_error());
$row_members = mysqli_fetch_assoc($members);
$totalRows_members = mysqli_num_rows($members);

$colname_recordupdate = "-1";
if (isset($_GET['tutorialsid'])) {
  $colname_recordupdate = $_GET['tutorialsid'];
}
mysqli_select_db(dbconnect(),$database_rayicecms);
$query_recordupdate = sprintf("SELECT * FROM tutorials WHERE tutorialsid = %s", GetSQLValueString($colname_recordupdate, "int"));
$recordupdate = mysqli_query(dbconnect(),$query_recordupdate) or die(mysqli_connect_error());
$row_recordupdate = mysqli_fetch_assoc($recordupdate);
$totalRows_recordupdate = mysqli_num_rows($recordupdate);

$queryString_tutorials = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_tutorials") == false && 
        stristr($param, "totalRows_tutorials") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_tutorials = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_tutorials = sprintf("&totalRows_tutorials=%d%s", $totalRows_tutorials, $queryString_tutorials);
?>
<?php include("../configuration.php"); ?>
<!-- code start -->
<?php
	  if(($row_setting['installed'] == "yes") && ($row_setting['selecttopic'] == "tutorials"))
	  {
?>
<!-- index start -->
<?php include($theme_path."".$row_setting['theme']."/topdocs.php"); ?>
<title>Post New Ad</title>
<link href="/themes/<?php echo $row_setting['theme']; ?>/multicms.css" rel="stylesheet" type="text/css">
<?php $favicon ?>
<?php include('../includes/scriptinclude.php'); ?>
<?php include($theme_path."".$row_setting['theme']."/botdocs.php"); ?>
<?php
if($row_setting['onlinestatus'] == "yes")
{
?>
<!-- HEADER INCLUDED -->
<?php include($theme_path."".$row_setting['theme']."/header.php"); ?><table class="mainbody">
      <tr>
        <?php include($theme_path."".$row_setting['theme']."/leftmenu.php"); ?> 
        <td valign="top"><div class="title">My Account</div>
                 <table class="tables">
                    <tr>
                      <td width="50%"><ul>
                        <li><a href="mytutorials.php">My Tutorials</a></li>
                        <li><a href="postnewad.php">POST Tutorials</a></li>
                        <li><a href="setting.php">Setting</a></li>
                        <li><a href="sendbox.php">Sent Messages</a></li>
                        <li><a href="receivebox.php">Receive Messages</a></li>
                      </ul></td>
                      <td width="50%" align="center">Welcome to <?php echo $_SESSION['MM_Username']; ?></td>
                    </tr>
                    <tr>
                      <td colspan="2"><div class="title">Update Product For Market</div>
                        <form action="<?php echo $editFormAction; ?>" method="POST" enctype="multipart/form-data" name="adpost" id="adpost">
                        <table width="100%" height="225" border="0" cellpadding="0" cellspacing="4">
                          <tr>
                            <td width="104" height="22" class="texts"><strong>TITLE:*</strong></td>
                            <td width="728"><input name="title" type="text" class="form" id="title" value="<?php echo $row_recordupdate['title']; ?>" /></td>
                          </tr>
                          <tr>
                            <td height="23" valign="top" class="texts"><strong>DESCRIPTION:*</strong></td>
                            <td><textarea name="description" cols="55" rows="5" class="wysiwyg" id="description"><?php echo $row_recordupdate['description']; ?></textarea></td>
                          </tr>
                          <tr>
                            <td height="24" class="texts"><strong>DATE:*</strong></td>
                            <td><input name="date" type="text" class="formmenusimple" id="date" value="<?php echo $row_recordupdate['date']; ?>" />
                              <span class="textsmall">FORMAT : DD-MM-YYYY</span></td>
                          </tr>
                          <tr>
                            <td height="24" class="texts"><strong>CATEGORIES:*</strong></td>
                            <td><select name="catename" class="formmenu" id="catename">
                              <?php
do {  
?>
                              <option value="<?php echo $row_cates['catename']?>"<?php if (!(strcmp($row_cates['catename'], $row_recordupdate['catename']))) {echo "selected=\"selected\"";} ?>><?php echo $row_cates['catename']?></option>
                              <?php
} while ($row_cates = mysqli_fetch_assoc($cates));
  $rows = mysqli_num_rows($cates);
  if($rows > 0) {
      mysqli_data_seek($cates, 0);
	  $row_cates = mysqli_fetch_assoc($cates);
  }
?>
                            </select></td>
                          </tr>
                          <tr>
                            <td height="24" class="texts"><strong>IMAGE:</strong></td>
                            <td><input name="imageurl" type="file" class="formmenusimple" id="imageurl" />
                              <?php echo $row_recordupdate['imageurl']; ?></td>
                          </tr>
                          <tr>
                            <td height="24" class="texts"><strong>WEBSITE URL:*</strong></td>
                            <td><input name="tutorialsurl" type="text" class="form" id="tutorialsurl" value="<?php echo $row_recordupdate['tutorialsurl']; ?>" /></td>
                          </tr>
                          <tr>
                            <td height="24" class="texts"><strong>TUTORIAL URL:*</strong></td>
                            <td><input name="tutorialpathurl" type="text" class="form" id="tutorialpathurl" value="<?php echo $row_recordupdate['tutorialpathurl']; ?>" /></td>
                          </tr>
                          <tr>
                            <td height="24">&nbsp;</td>
                            <td><input name="button" type="submit" class="button" id="button" value="Update" /></td>
                          </tr>
                        </table>
                        <input name="rating" type="hidden" id="rating" value="<?php echo $row_recordupdate['rating']; ?>">
                        <input name="views" type="hidden" id="views" value="<?php echo $row_recordupdate['views']; ?>">
                        <input name="position" type="hidden" id="position" value="<?php echo $row_recordupdate['position']; ?>">
                        <input name="status" type="hidden" id="status" value="<?php echo $row_recordupdate['status']; ?>">
                        <input name="users" type="hidden" id="users" value="<?php echo $_SESSION['MM_Username']; ?>">
                        <input name="owner" type="hidden" id="owner" value="<?php echo $row_members['users']; ?>">
                        <input name="tutorialsid" type="hidden" id="tutorialsid" value="<?php echo $row_recordupdate['tutorialsid']; ?>">
                        <input type="hidden" name="MM_update" value="adpost">
                        
                        </form>
                        <script language="JavaScript" type="text/javascript">

  var frmvalidator  = new Validator("adpost");
  frmvalidator.addValidation("title","req","Please Enter Post Title");
  frmvalidator.addValidation("title","maxlen=100","Max length for Post is 100");
  
  frmvalidator.addValidation("tutorialsurl","req","Please Provide Tutorial Main Site");

  frmvalidator.addValidation("tutorialpathurl","req","Please Give Full Tutorial Path");

  frmvalidator.addValidation("date","req","Please Enter Date DD-MM-YYYY");
  frmvalidator.addValidation("date","maxlen=10","Please Enter Correct Date");
    	
  frmvalidator.addValidation("description","req","Please Enter Description");

</script></td>
                      </tr>
                  </table></td>
        <?php
include($theme_path."".$row_setting['theme']."/rightmenu.php"); 
?> 
      </tr>
    </table><?php
include($theme_path."".$row_setting['theme']."/footer.php"); 
?>
<?php
}
else
{
?>
<div align="center">Site is currently Offline</div>
<?php
}
?>
</body>
</html>
<!-- index end -->
<?php
	  }
	  else
	  {
	  ?>
<script type="text/javascript"> location.replace("/install.php"); </script>
<?php
	  }
?>
<!-- code end -->
<?php
mysqli_free_result($setting);

mysqli_free_result($blogcates);

mysqli_free_result($pages);

mysqli_free_result($theme);

mysqli_free_result($parts);

mysqli_free_result($tutorials);

mysqli_free_result($links);

mysqli_free_result($featuredadposts);

mysqli_free_result($cates);

mysqli_free_result($members);

mysqli_free_result($recordupdate);

mysqli_free_result($rsT);

mysqli_free_result($rsM);
?>