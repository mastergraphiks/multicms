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

$colname_posts = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_posts = $_SESSION['MM_Username'];
}
mysqli_select_db(dbconnect(),$database_rayicecms);
$query_posts = sprintf("SELECT * FROM tutorials WHERE users = %s", GetSQLValueString($colname_posts, "text"));
$posts = mysqli_query(dbconnect(),$query_posts) or die(mysqli_connect_error());
$row_posts = mysqli_fetch_assoc($posts);
$totalRows_posts = mysqli_num_rows($posts);

$colname_members = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_members = $_SESSION['MM_Username'];
}
mysqli_select_db(dbconnect(),$database_rayicecms);
$query_members = sprintf("SELECT * FROM members WHERE users = %s", GetSQLValueString($colname_members, "text"));
$members = mysqli_query(dbconnect(),$query_members) or die(mysqli_connect_error());
$row_members = mysqli_fetch_assoc($members);
$totalRows_members = mysqli_num_rows($members);

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
<title>My Ads</title>
<link href="/themes/<?php echo $row_setting['theme']; ?>/multicms.css" rel="stylesheet" type="text/css">
<?php $favicon ?><?php include($theme_path."".$row_setting['theme']."/botdocs.php"); ?>
<?php
if($row_setting['onlinestatus'] == "yes")
{
?>
<!-- HEADER INCLUDED -->
<?php include($theme_path."".$row_setting['theme']."/header.php"); ?>
<table class="mainbody">
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
          <td colspan="2"><?php if ($totalRows_posts > 0) { // Show if recordset not empty ?>
            <div class="title">My Tutorials</div>
            <table width="100%">
              <tr>
                <td width="48%"><strong>Tutorials</strong></td>
                <td width="10%"><strong>Status</strong></td>
                <td width="10%"></td>
                <td width="10%"></td>
              </tr>
            </table>
            <?php do { ?>
            <table width="100%" border="0" cellpadding="2" cellspacing="2">
              <tr>
                <td width="48%"><?php echo $row_posts['title']; ?></td>
                <td width="10%" align="center"><?php echo $row_posts['status']; ?></td>
                <td width="10%" align="center"><a href="editpost.php?tutorialsid=<?php echo $row_posts['tutorialsid']; ?>">Edit</a></td>
                <td width="10%" align="center"><a href="deletepost.php?tutorialsid=<?php echo $row_posts['tutorialsid']; ?>">Delete</a></td>
              </tr>
            </table>
            <?php } while ($row_posts = mysqli_fetch_assoc($posts)); ?>
            <?php } // Show if recordset not empty ?></td>
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

mysqli_free_result($tutorials);

mysqli_free_result($links);

mysqli_free_result($featuredadposts);

mysqli_free_result($posts);

mysqli_free_result($members);
?>