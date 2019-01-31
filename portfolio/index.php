<?php require_once('../includes/rayicecms.php'); ?>
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
$query_pages = "SELECT * FROM pages WHERE `position` = 'leftmenu'";
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


$maxRows_portfolio = 10;
$pageNum_portfolio = 0;
if (isset($_GET['pageNum_portfolio'])) {
  $pageNum_portfolio = $_GET['pageNum_portfolio'];
}
$startRow_portfolio = $pageNum_portfolio * $maxRows_portfolio;

mysqli_select_db(dbconnect(),$database_rayicecms);
$query_portfolio = "SELECT * FROM portfolio WHERE status = 'published' ORDER BY portfolioid DESC";
$query_limit_portfolio = sprintf("%s LIMIT %d, %d", $query_portfolio, $startRow_portfolio, $maxRows_portfolio);
$portfolio = mysqli_query(dbconnect(),$query_limit_portfolio) or die(mysqli_connect_error());
$row_portfolio = mysqli_fetch_assoc($portfolio);

if (isset($_GET['totalRows_portfolio'])) {
  $totalRows_portfolio = $_GET['totalRows_portfolio'];
} else {
  $all_portfolio = mysqli_query(dbconnect(),$query_portfolio);
  $totalRows_portfolio = mysqli_num_rows($all_portfolio);
}
$totalPages_portfolio = ceil($totalRows_portfolio/$maxRows_portfolio)-1;

mysqli_select_db(dbconnect(),$database_rayicecms);
$query_links = "SELECT * FROM friendlinks";
$links = mysqli_query(dbconnect(),$query_links) or die(mysqli_connect_error());
$row_links = mysqli_fetch_assoc($links);
$totalRows_links = mysqli_num_rows($links);

$colname_members = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_members = $_SESSION['MM_Username'];
}
mysqli_select_db(dbconnect(),$database_rayicecms);
$query_members = sprintf("SELECT * FROM members WHERE users = %s", GetSQLValueString($colname_members, "text"));
$members = mysqli_query(dbconnect(),$query_members) or die(mysqli_connect_error());
$row_members = mysqli_fetch_assoc($members);
$totalRows_members = mysqli_num_rows($members);

$queryString_portfolio = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_portfolio") == false && 
        stristr($param, "totalRows_portfolio") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_portfolio = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_portfolio = sprintf("&totalRows_portfolio=%d%s", $totalRows_portfolio, $queryString_portfolio);
?>
<?php
mysqli_select_db(dbconnect(),$database_rayicecms);
$query_pagesmenu = "SELECT * FROM pages WHERE `position` = 'menu' ORDER BY name ASC";
$pagesmenu = mysqli_query(dbconnect(),$query_pagesmenu) or die(mysqli_connect_error());
$row_pagesmenu = mysqli_fetch_assoc($pagesmenu);
$totalRows_pagesmenu = mysqli_num_rows($pagesmenu);

mysqli_select_db(dbconnect(),$database_rayicecms);
$query_welcome = "SELECT * FROM portfolio WHERE selecttopic = 'homepage'";
$welcome = mysqli_query(dbconnect(),$query_welcome) or die(mysqli_connect_error());
$row_welcome = mysqli_fetch_assoc($welcome);
$totalRows_welcome = mysqli_num_rows($welcome);
?>
<?php include("../configuration.php"); ?>
<!-- code start -->
<?php
	  if(($row_setting['installed'] == "yes") && ($row_setting['selecttopic'] == "portfolio"))
	  {
?>
<!-- index start -->
<?php include($theme_path."".$row_setting['theme']."/topdocs.php"); ?>
<title><?php echo $row_setting['title']; ?></title>
<meta name="keywords" content="<?php echo $row_setting['metakey']; ?>">
<meta name="description" content="<?php echo $row_setting['metadesc']; ?>">
<link href="/themes/<?php echo $row_setting['theme']; ?>/multicms.css" rel="stylesheet" type="text/css">
<?php $favicon ?>
<?php include($theme_path."".$row_setting['theme']."/botdocs.php"); ?>
<?php
if($row_setting['onlinestatus'] == "yes")
{
?>
<!-- HEADER INCLUDED -->
<?php include($theme_path."".$row_setting['theme']."/header.php"); ?><table class="mainbody">
      <tr>
        <?php include($theme_path."".$row_setting['theme']."/leftmenu.php"); ?>
        <td valign="top"><div class="title"><?php echo $row_welcome['title']; ?></div>
          <table>
            <tr>
              <td><?php echo $row_welcome['description']; ?></td>
            </tr>
        </table>
        
                                  <?php
$widgetstatus_query = mysqli_query(dbconnect(),"SELECT * FROM parts WHERE partsid = '12'");
$widgetstatus = mysqli_fetch_assoc($widgetstatus_query);

$widgets_query = mysqli_query(dbconnect(),"SELECT * FROM widgets WHERE position = 'home'");
$widgets = mysqli_fetch_assoc($widgets_query);

if(($widgetstatus['status'] == 'active') && (!empty($widgets)))
{
?>
<div class="title">WIDGETS</div>
<?php do { ?>
<div align="center">
<?php echo $widgets['content']; ?></div>
<?php } while ($widgets = mysqli_fetch_assoc($widgets_query)); ?>
<?php
}
?></td>
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

mysqli_free_result($pages);

mysqli_free_result($theme);

mysqli_free_result($parts);

mysqli_free_result($portfolio);

mysqli_free_result($links);

mysqli_free_result($members);

mysqli_free_result($pagesmenu);

mysqli_free_result($welcome);
?>