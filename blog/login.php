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

//initialize the session
if (!isset($_SESSION)) {
  session_start();
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

$currentPage = $_SERVER["PHP_SELF"];

mysqli_select_db(dbconnect(),$database_rayicecms);
$query_setting = "SELECT * FROM settings";
$setting = mysqli_query(dbconnect(),$query_setting) or die(mysqli_connect_error());
$row_setting = mysqli_fetch_assoc($setting);
$totalRows_setting = mysqli_num_rows($setting);

mysqli_select_db(dbconnect(),$database_rayicecms);
$query_blogcates = "SELECT * FROM categories WHERE selecttopic = 'blog'";
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

$maxRows_blog = 10;
$pageNum_blog = 0;
if (isset($_GET['pageNum_blog'])) {
  $pageNum_blog = $_GET['pageNum_blog'];
}
$startRow_blog = $pageNum_blog * $maxRows_blog;

mysqli_select_db(dbconnect(),$database_rayicecms);
$query_blog = "SELECT * FROM blog WHERE status = 'published' ORDER BY blogid DESC";
$query_limit_blog = sprintf("%s LIMIT %d, %d", $query_blog, $startRow_blog, $maxRows_blog);
$blog = mysqli_query(dbconnect(),$query_limit_blog) or die(mysqli_connect_error());
$row_blog = mysqli_fetch_assoc($blog);

if (isset($_GET['totalRows_blog'])) {
  $totalRows_blog = $_GET['totalRows_blog'];
} else {
  $all_blog = mysqli_query(dbconnect(),$query_blog);
  $totalRows_blog = mysqli_num_rows($all_blog);
}
$totalPages_blog = ceil($totalRows_blog/$maxRows_blog)-1;

mysqli_select_db(dbconnect(),$database_rayicecms);
$query_links = "SELECT * FROM friendlinks";
$links = mysqli_query(dbconnect(),$query_links) or die(mysqli_connect_error());
$row_links = mysqli_fetch_assoc($links);
$totalRows_links = mysqli_num_rows($links);

mysqli_select_db(dbconnect(),$database_rayicecms);
$query_featuredblogs = "SELECT * FROM blog WHERE position = 'featured' ORDER BY blogid DESC";
$featuredblogs = mysqli_query(dbconnect(),$query_featuredblogs) or die(mysqli_connect_error());
$row_featuredblogs = mysqli_fetch_assoc($featuredblogs);
$totalRows_featuredblogs = mysqli_num_rows($featuredblogs);

$colname_members = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_members = $_SESSION['MM_Username'];
}
mysqli_select_db(dbconnect(),$database_rayicecms);
$query_members = sprintf("SELECT * FROM members WHERE users = %s", GetSQLValueString($colname_members, "text"));
$members = mysqli_query(dbconnect(),$query_members) or die(mysqli_connect_error());
$row_members = mysqli_fetch_assoc($members);
$totalRows_members = mysqli_num_rows($members);

$queryString_blog = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_blog") == false && 
        stristr($param, "totalRows_blog") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_blog = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_blog = sprintf("&totalRows_blog=%d%s", $totalRows_blog, $queryString_blog);
?>
<?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['users'])) {
  $loginUsername=$_POST['users'];
  $password=$_POST['passs'];
  $MM_fldUserAuthorization = "";
  $MM_redirectLoginSuccess = "account.php";
  $MM_redirectLoginFailed = "login.php";
  $MM_redirecttoReferrer = false;
  mysqli_select_db(dbconnect(),$database_rayicecms);
  
  $LoginRS__query=sprintf("SELECT users, passs FROM members WHERE users=%s AND passs=%s",
    GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
   
  $LoginRS = mysqli_query(dbconnect(),$LoginRS__query) or die(mysqli_connect_error());
  $loginFoundUser = mysqli_num_rows($LoginRS);
  if ($loginFoundUser) {
     $loginStrGroup = "";
    
	if (PHP_VERSION >= 5.1) {session_regenerate_id(true);} else {session_regenerate_id();}
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;	      

    if (isset($_SESSION['PrevUrl']) && false) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}
?>
<?php include("../configuration.php"); ?>
<!-- code start -->
<?php
	  if(($row_setting['installed'] == "yes") && ($row_setting['selecttopic'] == "blog"))
	  {
?>
<!-- index start -->
<?php include($theme_path."".$row_setting['theme']."/topdocs.php"); ?>
<title>Login</title>
<link href="/themes/<?php echo $row_setting['theme']; ?>/multicms.css" rel="stylesheet" type="text/css">
<?php $favicon ?>
<?php include($theme_path."".$row_setting['theme']."/botdocs.php"); ?>
<?php
if($row_setting['onlinestatus'] == "yes")
{
?>
<!-- HEADER INCLUDED -->
<?php include($theme_path."".$row_setting['theme']."/header.php"); ?>
<table class="mainbody">
      <tr>
        <?php include($theme_path."".$row_setting['theme']."/leftmenu.php"); ?> 
        <td valign="top">
        <table class="tables">
          <tr>
                <td valign="top"><div class="title">Login</div>
                  <table>
                    <tr>
                      <td><?php if ($totalRows_members == 0) { // Show if recordset empty ?>
  <form action="<?php echo $loginFormAction; ?>" method="POST" name="loginbox" id="loginbox">
    <table class="texts">
      <tr>
        <td>USERNAME:</td>
        <td><input name="users" type="text" class="form" id="users" required="required"></td>
        </tr>
      <tr>
        <td>PASSWORD:</td>
        <td><input name="passs" type="password" class="form" id="passs" required="required"></td>
        </tr>
      <tr>
        <td></td>
        <td><input name="button2" type="submit" class="button" id="button2" value="Login"></td>
        </tr>
      </table>
  </form>
  <?php } // Show if recordset empty ?>
                        <p>
                          <?php if ($totalRows_members > 0) { // Show if recordset not empty ?>
                            You Are Already Logged, Go to <a href="account.php">Your Account</a>
  <?php } // Show if recordset not empty ?>
                        </p></td>
                    </tr>
                  </table></td>
              </tr>
            </table></td>
        <?php
include($theme_path."".$row_setting['theme']."/rightmenu.php"); 
?>
      </tr>
</table>
	<?php
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

mysqli_free_result($blog);

mysqli_free_result($links);

mysqli_free_result($featuredblogs);

mysqli_free_result($members);
?>