-- MySQL dump 10.13  Distrib 5.1.52, for redhat-linux-gnu (x86_64)
--
-- Host: localhost    Database: info3005_forum
-- ------------------------------------------------------
-- Server version	5.1.52

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `info3005_blocks`
--

DROP TABLE IF EXISTS `info3005_blocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `info3005_blocks` (
  `block_id` int(11) NOT NULL AUTO_INCREMENT,
  `block_title` tinytext NOT NULL,
  `block_content` text NOT NULL,
  `block_php` tinyint(1) NOT NULL,
  `block_order` int(11) NOT NULL,
  PRIMARY KEY (`block_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `info3005_blocks`
--

/*!40000 ALTER TABLE `info3005_blocks` DISABLE KEYS */;
INSERT INTO `info3005_blocks` VALUES (1,'Your Account','global $user, $document;\r\n\r\necho \"Welcome, <strong>$user</strong><br />\";\r\n\r\n/* Not logged in */\r\nif($user->info[\'user_id\'] == -1) {\r\necho  $document->get_template(\"quick_login\");\r\n}\r\n\r\n/* Logged in */\r\nelse {\r\necho \'<p></p>\';\r\necho date(\"l F j\") . \", \" . date(\"H:i\");\r\n}',1,1),(2,'Recent Topics','global $core;\r\n\r\nrequire(\"modules/forums.php\");\r\n$threads = new Threads($core);\r\n\r\n$site_url = $core->config[\'Paths\'][\'web\'];\r\n\r\nprint \'<p>The freshest forum topics:</p>\';\r\n\r\n$recent_threads = $threads->get_recent_threads();\r\nwhile ($thread = $recent_threads->fetch_assoc()) {\r\n   print \'<small><a href=\"\' . $site_url . \'/showthread.php?t=\' . $thread[\'thread_id\'] . \'\">\' . $thread[\'thread_name\'] . \'</a></small><br/>\';\r\n}',1,5),(3,'Navigation','global $core;\r\n\r\nrequire(\"modules/forums.php\");\r\n\r\n$site_url = $core->config[\'Paths\'][\'web\'];\r\n\r\nprint \'<p>Select a forum to jump to:</p>\';\r\n\r\n$boards = new Boards($core);\r\n$all_boards = $boards->get_all_boards();\r\n   print \'<div style=\"text-align: center\"><form method=\"get\" action=\"\' . ${\'site_url\'} . \'/forumdisplay.php\">\';\r\n   print \'<select name=\"f\">\';\r\n\r\nwhile ($board = $all_boards->fetch_assoc()) {\r\n   print \'<option value=\"\' . $board[\'board_id\'] . \'\">\' . $board[\'board_name\'] . \'</option>\';\r\n}\r\n\r\n   print \'</select>\';\r\n   print \'&nbsp;<input type=\"submit\" value=\"Go\"></div>\';\r\n   print \'</form>\';\r\n',1,7),(5,'Register','global $core, $user, $document;\r\n\r\n$site_url = $core->config[\'Paths\'][\'web\'];\r\n\r\n/* Only when not logged in */\r\nif($user->info[\'user_id\'] == -1) {\r\n?>\r\n<p>If you don\'t already have a FailForum account, then you should <a href=\"$siteurl/register.php\">Register now</a>.</p>\r\n\r\n<p>Start enjoying all the benefits of this forum today!</p>\r\n\r\n<p class=\"center\"><a href=\"$siteurl/register.php\">Register</a></p>\r\n<?php\r\n}',1,5),(4,'Admin Box','global $core, $user;\r\n\r\n/* Only show for admins */\r\nif ($user->info[\'user_type\'] == 2) {\r\n\r\n//Set site URL\r\n$site_url = $core->config[\'Paths\'][\'web\'];\r\n\r\necho \'\r\n<ul>\r\n<li><a href=\"\' . $site_url . \'/admin.php\" title=\"Admin Home\">Admin Centre</a></li>\r\n<li><a href=\"\' . $site_url . \'/admin.php/config\" title=\"Settings\">Settings</a></li>\r\n<li><a href=\"\' . $site_url . \'/admin.php/categories\" title=\"Categories\">Categories</a></li>\r\n<li><a href=\"\' . $site_url . \'/admin.php/boards\" title=\"Boards\">Boards</a></li>\r\n<li><a href=\"\' . $site_url . \'/admin.php/users\" title=\"Users\">Users</a></li>\r\n<li><a href=\"\' . $site_url . \'/admin.php/blocks\" title=\"Blocks\">Blocks</a></li>\r\n<li><a href=\"\' . $site_url . \'/admin.php/templates\" title=\"Templates\">Templates</a></li>\r\n</ul>\'; }',1,10),(6,'Quick Search','global $core;\r\n$site_url = $core->config[\'Paths\'][\'web\'];\r\n\r\necho \'\r\n<div align=\"center\" style=\"text-align: center\">\r\n<form method=\"post\" action=\"\' . $site_url . \'/search.php?do=search\">\r\n<input type=\"text\" name=\"text\"></input>\r\n<input type=\"submit\" value=\"Search\"></input>\r\n</form>\r\n</div>\';',1,8);
/*!40000 ALTER TABLE `info3005_blocks` ENABLE KEYS */;

--
-- Table structure for table `info3005_boards`
--

DROP TABLE IF EXISTS `info3005_boards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `info3005_boards` (
  `board_id` int(11) NOT NULL AUTO_INCREMENT,
  `board_name` varchar(255) NOT NULL,
  `board_description` text NOT NULL,
  `category_id` int(11) NOT NULL,
  `board_order` tinyint(4) NOT NULL,
  `board_posts` int(11) NOT NULL,
  `board_threads` int(11) NOT NULL,
  `board_lastpost` int(11) NOT NULL,
  PRIMARY KEY (`board_id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `info3005_boards`
--

/*!40000 ALTER TABLE `info3005_boards` DISABLE KEYS */;
INSERT INTO `info3005_boards` VALUES (1,'Announcements','All the latest news about FailForum',1,0,1,1,1),(2,'General Discussion','General discussion about anything',2,0,0,0,0),(3,'Testing','A board to test things out',2,0,0,0,0);
/*!40000 ALTER TABLE `info3005_boards` ENABLE KEYS */;

--
-- Table structure for table `info3005_categories`
--

DROP TABLE IF EXISTS `info3005_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `info3005_categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) NOT NULL,
  `category_description` text NOT NULL,
  `category_order` tinyint(4) NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `info3005_categories`
--

/*!40000 ALTER TABLE `info3005_categories` DISABLE KEYS */;
INSERT INTO `info3005_categories` VALUES (1,'News','Exciting news about FailForum',0),(2,'General','General discussion',0);
/*!40000 ALTER TABLE `info3005_categories` ENABLE KEYS */;

--
-- Table structure for table `info3005_config`
--

DROP TABLE IF EXISTS `info3005_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `info3005_config` (
  `config_id` int(11) NOT NULL AUTO_INCREMENT,
  `config_key` text NOT NULL,
  `config_value` text NOT NULL,
  `config_type` tinytext NOT NULL,
  `config_title` tinytext NOT NULL,
  `config_description` text NOT NULL,
  PRIMARY KEY (`config_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `info3005_config`
--

/*!40000 ALTER TABLE `info3005_config` DISABLE KEYS */;
INSERT INTO `info3005_config` VALUES (1,'title','Fail Forum','text','Forum Title','The title of the forum'),(3,'default_style','blue','text','Default Style','The default style on the website'),(2,'description','Welcome to the FailForum forums!','text','Forum Description','A little bit about your forum'),(5,'valid_styles','blue','text','Valid Styles','What styles are currently available?'),(6,'cookie_name','forumlogin','text','Cookie Name','Name of the cookie used by the website'),(7,'upload_dir','./uploads','text','Upload Directory','Where to upload files'),(8,'upload_webdir','http://kanga-info3005.ecs.soton.ac.uk/~ao2g10/forum/uploads','text','Upload Web Directory','The online directory where files are uploaded to'),(9,'contact_email','root@localhost','text','Contact Email','Email address to send contact messages to');
/*!40000 ALTER TABLE `info3005_config` ENABLE KEYS */;

--
-- Table structure for table `info3005_hooks`
--

DROP TABLE IF EXISTS `info3005_hooks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `info3005_hooks` (
  `hook_id` int(11) NOT NULL AUTO_INCREMENT,
  `hook_name` tinytext NOT NULL,
  `hook_code` text NOT NULL,
  PRIMARY KEY (`hook_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `info3005_hooks`
--

/*!40000 ALTER TABLE `info3005_hooks` DISABLE KEYS */;
INSERT INTO `info3005_hooks` VALUES (1,'sidebar','global $core;\r\n\r\ninclude_once(\"./modules/hooks/sidebar.php\");\r\ndo_sidebar();');
/*!40000 ALTER TABLE `info3005_hooks` ENABLE KEYS */;

--
-- Table structure for table `info3005_posts`
--

DROP TABLE IF EXISTS `info3005_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `info3005_posts` (
  `post_id` int(11) NOT NULL AUTO_INCREMENT,
  `thread_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_name` varchar(255) NOT NULL,
  `post_message` text NOT NULL,
  `post_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`post_id`),
  KEY `thread_id` (`thread_id`,`post_timestamp`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `info3005_posts`
--

/*!40000 ALTER TABLE `info3005_posts` DISABLE KEYS */;
INSERT INTO `info3005_posts` VALUES (1,1,1,'Welcome to Fail Forum!','Welcome to your Fail Forum!','2011-11-11 16:52:44');
/*!40000 ALTER TABLE `info3005_posts` ENABLE KEYS */;

--
-- Table structure for table `info3005_template`
--

DROP TABLE IF EXISTS `info3005_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `info3005_template` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT,
  `template_name` varchar(50) NOT NULL,
  `template_text` text NOT NULL,
  `template_php` tinyint(1) NOT NULL,
  PRIMARY KEY (`template_id`),
  UNIQUE KEY `template_name` (`template_name`)
) ENGINE=MyISAM AUTO_INCREMENT=85 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `info3005_template`
--

/*!40000 ALTER TABLE `info3005_template` DISABLE KEYS */;
INSERT INTO `info3005_template` VALUES (1,'header','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\r\n<html xmlns=\"http://www.w3.org/1999/xhtml\" dir=\"ltr\" lang=\"en\">\r\n<head>\r\n<meta name=\"generator\" content=\"FailCMS 0.9alpha with FailForum module - http://failcms.cslib.org.uk\" />\r\n<meta name=\"sad\" content=\"Keyboard Cat is gone\" />\r\n<meta http-equiv=\"Content-type\" content=\"text/html; charset=utf-8\" />\r\n<title>$title - $page</title>\r\n<link rel=\"stylesheet\" href=\"$siteurl/resources/styles/stylesheet.php?style=$style.css\" type=\"text/css\" />\r\n<script type=\"text/javascript\" src=\"$siteurl/resources/scripts/jquery-1.7.js\"></script>\r\n</head>\r\n<body>\r\n<div class=\"header\"><div id=\"logo\"> </div>\r\n</div>',0),(2,'footer','<div class=\"footer\">\r\nCopyright 2011, Fail Forum plc. in association with Keyboard Cat\r\n</div>\r\n</body>\r\n</html>',0),(3,'form','<form id=\"$id\" name=\"$name\" action=\"$action\" method=\"$method\" $additional>\r\n$elements\r\n</form>',0),(4,'form_element_text','<input id=\"$id\" name=\"$id\" type=\"text\" value=\"$default\" class=\"textbox\" $additional />',0),(5,'form_element_submit','<input id=\"$id\" name=\"$id\" type=\"submit\" value=\"$default\" />',0),(6,'form_element','<p><strong>$title</strong>:<br/>\r\n<small>$description</small><br />\r\n$element</p>',0),(7,'form_element_password','<input id=\"$id\" name=\"$id\" type=\"password\" value=\"$default\" $additional />',0),(8,'form_element_textarea','<textarea id=\"$id\" name=\"$id\" rows=\"40\" cols=\"10\">$default</textarea>',0),(9,'form_element_radio','<input type=\"radio\" id=\"$id\" name=\"$id\" value=\"$default\" $additional>$title</input>',0),(10,'form_element_checkbox','<input type=\"checkbox\" id=\"$id\" name=\"$id\" value=\"$default\" $additional>$title</input>',0),(11,'form_element_fieldset','<fieldset id=\"$id\" class=\"fieldset\"><legend>$title</legend>',0),(12,'form_element_fieldset_end','</fieldset>',0),(13,'form_element_only','<p><strong>$title</strong>:</p>\r\n<p>$description</small></p>',0),(14,'page_template','<h2>$title</h2>\r\n$text\r\n\r\n<p><small>Last modified $timestamp</small></p>',0),(15,'menu_0','<div class=\"menu\">\r\n<ul>\r\n<li><a href=\"$siteurl/index.php\" title=\"Home\">Home</a></li>\r\n<li><a href=\"$siteurl/forums.php\" title=\"Forums\">Forums</a></li>\r\n<li><a href=\"$siteurl/help.php\" title=\"Help\">Help</a></li>\r\n<li><a href=\"$siteurl/login.php\" title=\"Login\">Login</a></li>\r\n<li><a href=\"$siteurl/register.php\" title=\"Register\">Register</a></li>\r\n</ul>\r\n</div>',0),(16,'content_header','<div class=\"content\">',0),(17,'content_footer','</div>',0),(18,'sidebar','<div class=\"sidebar\">\r\n$blocks\r\n</div>',0),(19,'block','<div class=\"block\">\r\n<h2>$title</h2>\r\n$content\r\n</div>',0),(20,'quick_login','<div class=\"box\">\r\n<form name=\"login\" action=\"$siteurl/login.php\" method=\"post\">\r\n<strong>Username:</strong><br/>\r\n<input type=\"text\" name=\"user_name\" /><br/>\r\n<strong>Password:</strong><br/>\r\n<input type=\"password\" name=\"user_password\" /><br/>\r\n<input type=\"submit\" value=\"Login\"/>\r\n</form>\r\n</div>',0),(21,'fatal_error','<div class=\"window\">\r\n<div class=\"title\">Fatal Error</div>\r\n<div class=\"windowbg\">\r\n$error\r\n<br /><br />\r\n$details\r\n<br />\r\n<a href=\"javascript:history.go(-1);\">Back</a> | <a href=\"$siteurl/index.php\">Home</a>\r\n</div>\r\n</div>',0),(22,'simple_template','<h2>$title</h2>\r\n$text',0),(23,'window','<div class=\"window\">\r\n<div class=\"title\">$title</div>\r\n<div class=\"windowbg\">\r\n$content\r\n</div>\r\n</div>',0),(24,'title','<h2>$title</h2>',0),(25,'user_error','<div class=\"window\">\r\n<div class=\"title\">Error</div>\r\n<div class=\"windowbg\">\r\n<strong>$error</strong>\r\n<br /><br />\r\n$details\r\n<br /><br />\r\n<a href=\"$siteurl/index.php\">Return</a>\r\n</div>\r\n</div>',0),(26,'menu_1','<div class=\"menu\">\r\n<ul>\r\n<li><a href=\"$siteurl/index.php\" title=\"Home\">Home</a></li>\r\n<li><a href=\"$siteurl/forums.php\" title=\"Forums\">Forums</a></li>\r\n<li><a href=\"$siteurl/search.php\" title=\"Search\">Search</a></li>\r\n<li><a href=\"$siteurl/members.php\" title=\"Members\">Members</a></li>\r\n<li><a href=\"$siteurl/user.php\" title=\"Profile\">Profile</a></li>\r\n<li><a href=\"$siteurl/help.php\" title=\"Help\">Help</a></li>\r\n<li><a href=\"$siteurl/contact.php\" title=\"Contact\">Contact</a></li>\r\n<li><a href=\"$siteurl/login.php?action=logout\" title=\"Logout\">Logout</a></li>\r\n</ul>\r\n</div>',0),(27,'menu_2','<div class=\"menu\">\r\n<ul>\r\n<li><a href=\"$siteurl/index.php\" title=\"Home\">Home</a></li>\r\n<li><a href=\"$siteurl/forums.php\" title=\"Forums\">Forums</a></li>\r\n<li><a href=\"$siteurl/admin.php\" title=\"Admin\">Admin</a></li>\r\n<li><a href=\"$siteurl/search.php\" title=\"Search\">Search</a></li>\r\n<li><a href=\"$siteurl/members.php\" title=\"Members\">Members</a></li>\r\n<li><a href=\"$siteurl/user.php\" title=\"Profile\">Profile</a></li>\r\n<li><a href=\"$siteurl/help.php\" title=\"Help\">Help</a></li>\r\n<li><a href=\"$siteurl/contact.php\" title=\"Contact\">Contact</a></li>\r\n<li><a href=\"$siteurl/login.php?action=logout\" title=\"Logout\">Logout</a></li>\r\n</ul>\r\n</div>',0),(76,'forum_form','<h2>$heading</h2>\r\n<p>$description</p>\r\n\r\n$breadcrumb\r\n\r\n$content',0),(70,'forum_posts','$posts',0),(31,'form_element_hidden','<input id=\"$id\" name=\"$id\" type=\"hidden\" value=\"$default\" $additional />',0),(71,'forum_lastpost_nobody','<div style=\"text-align: center\">\r\n<p>No posts</p>\r\n</div>',0),(64,'forum_post','<!-- Start post -->\r\n<div style=\"padding:0px 0px 4px 0px\">\r\n<table width=\"100%\" cellspacing=\"1\" cellpadding=\"4\" border=\"0\" align=\"center\" class=\"window\">\r\n<tbody><tr>\r\n        <td align=\"left\" class=\"title\">\r\n                <div style=\"float:right\" class=\"normal\">#<strong>$post_counter</strong> &nbsp;</div>\r\n                <div class=\"normal\">$post_timestamp</div>\r\n        </td>\r\n</tr><tr>\r\n        <td align=\"left\" style=\"padding:0px\" class=\"windowbg2\">\r\n                <!-- Start user info -->\r\n                <table width=\"100%\" cellspacing=\"4\" cellpadding=\"0\" border=\"0\">\r\n                <tbody><tr>\r\n\r\n                        <td nowrap=\"nowrap\" align=\"left\">\r\n                                        <a href=\"user.php?id=$user_id\" class=\"bigusername\">$user_name</a>\r\n                                        <div class=\"smallfont\">$user_type</div>\r\n                        </td>\r\n                        <td width=\"100%\">&nbsp;</td>\r\n                        <td valign=\"top\" nowrap=\"nowrap\" align=\"left\">\r\n                                <div class=\"smallfont\">\r\n                                        <div>Join Date: $user_joined</div>\r\n                                <div></div>\r\n                                </div>\r\n                        </td>\r\n                </tr>\r\n                </tbody></table>\r\n                <!-- End user info -->\r\n        </td>\r\n</tr>\r\n<tr>   \r\n        <td align=\"left\" class=\"windowbg\">\r\n                <!-- Message -->\r\n                <div style=\"margin: 0; padding: 0;\">\r\n                <p style=\"font-weight: bold; margin: 0; padding: 0;\">$post_name</p>\r\n                <p>$post_message</p>\r\n                </div>\r\n                <!-- End Message -->\r\n<div align=\"right\" style=\"margin-top: 10px;\">\r\n$post_buttons\r\n</div>\r\n        </td>\r\n</tr>\r\n</tbody></table>\r\n<!-- End post -->',0),(73,'forum_board_empty','<!-- Start Board -->\r\n<tr align=\"center\">\r\n        <td width=\"29\" class=\"windowbg2\" colspan=\"5\"><p>There are no posts in this forum</p></td></tr>\r\n<!-- End Board -->\r\n',0),(35,'form_element_upload','<input id=\"$id\" name=\"$id\" type=\"file\" class=\"textbox\" $additional />',0),(36,'user_profile','<p>$user_name has been a member of $title since $user_timestamp</p>\r\n$breadcrumb \r\n\r\n<div class=\"title\">User Profile</div>\r\n<div class=\"windowbg1\">\r\n<div class=\"user_profile\">\r\n<div class=\"imageframe\" style=\"float: left\"><img src=\"$user_picture\" alt=\"Profile Picture\" title=\"Profile Picture\"/></div>\r\n<h3>$user_name</h3>\r\n<div class=\"user_field\">\r\n<span class=\"field_name\">User Name</span>: $user_name</div>\r\n<div class=\"user_field\">\r\n<span class=\"field_name\">Homepage</span>: $user_homepage</div>\r\n<div class=\"user_field\">\r\n<span class=\"field_name\">Join Date</span>: $user_timestamp</div>\r\n</div>\r\n<p><br/></p>\r\n<h3>User Bio:</h3>\r\n<p>$user_bio</p>\r\n</div>',0),(37,'form_element_hidden_text','<input id=\"$id\" name=\"$id\" type=\"hidden\" value=\"$default\" $additional />$default',0),(72,'forum_lastpost','                        <div style=\"clear:both\"><img border=\"0\" src=\"images/misc/poll_posticon.gif\" class=\"inlineimg\">\r\n                                <a href=\"showthread.php?t=$thread_id\"><strong>$post_name</strong></a>\r\n                        </div>\r\n                        <div>by <a rel=\"nofollow\" href=\"user.php?id=$user_id\">$user_name</a></div>\r\n                        <div align=\"right\">$post_timestamp</div>',0),(42,'admin_user_item','<li><strong><a href=\"$siteurl/user.php?uid=$user_id\">$user_name</a> </strong> [<a href=\"$siteurl/user.php?uid=$user_id&action=edit\">Edit</a>]</li>',0),(43,'admin_user_list','<ul>\r\n$users\r\n</ul>',0),(44,'admin','<ul>\r\n<li><a href=\"$siteurl/admin.php/config\" title=\"Site Configuration\">Site Configuration</a></li>\r\n<li><a href=\"$siteurl/admin.php/categories\" title=\"Category Management\">Category Management</a></li>\r\n<li><a href=\"$siteurl/admin.php/boards\" title=\"Board Management\">Board Management</a></li>\r\n<li><a href=\"$siteurl/admin.php/users\" title=\"User Management\">User Management</a></li>\r\n<li><a href=\"$siteurl/admin.php/blocks\" title=\"Block Management\">Block Management</a></li>\r\n<li><a href=\"$siteurl/admin.php/templates\" title=\"Template Management\">Template Management</a></li>\r\n</ul>',0),(45,'admin_template_item','<li><strong><a href=\"$siteurl/admin.php/templates/edit/$template_id\">$template_name</a>  </strong> [<a href=\"$siteurl/admin.php/templates/edit/$template_id\">Edit</a>] [<a href=\"$siteurl/admin.php/templates/delete/$template_id\">Delete</a>]</li>',0),(46,'admin_template_list','<ul>\r\n$templates\r\n</ul>',0),(50,'admin_config_item_text','<p><strong>$config_title</strong></p>\r\n<small><p>$config_description</p></small>\r\n<input type=\"text\" id=\"$config_key\" name=\"$config_key\" value=\"$config_value\" class=\"textbox\"/>',0),(51,'admin_config_list','<form name=\"config\" id=\"config\" action=\"$siteurl/admin.php/config/save\" method=\"post\">\r\n$config\r\n<div class=\"center\"><input type=\"submit\" name=\"submit\" value=\"Save\"/></div>\r\n</form>',0),(52,'admin_block_item','<li><strong><a href=\"$siteurl/admin.php/blocks/edit/$block_id\">$block_title</a>  </strong> [<a href=\"$siteurl/admin.php/blocks/edit/$block_id\">Edit</a>] [<a href=\"$siteurl/admin.php/blocks/delete/$block_id\">Delete</a>]</li>',0),(53,'admin_block_list','<ul>\r\n$blocks\r\n</ul>',0),(63,'forum_board','<!-- Start Board -->\r\n<tr align=\"center\">\r\n        <td width=\"29\" class=\"windowbg2\"><img border=\"0\" src=\"$siteurl/resources/images/$style/forum.png\"></td>\r\n        <td align=\"left\" id=\"f2\" class=\"windowbg\">\r\n                <div style=\"font-weight: bold;\"><a href=\"forumdisplay.php?f=$board_id\">$board_name</a></div>\r\n                <div class=\"smallfont\">$board_description</div>\r\n        </td>\r\n        <td nowrap=\"nowrap\" class=\"windowbg2\">\r\n                <div align=\"left\" class=\"smallfont\">\r\n                $lastpost\r\n                </div>\r\n        </td>\r\n        <td class=\"windowbg\">$board_threads</td>\r\n        <td class=\"windowbg2\">$board_posts</td>   \r\n</tr>\r\n<!-- End Board -->\r\n',0),(62,'forum_category','<!-- Begin Category -->\r\n<table width=\"100%\" cellspacing=\"1\" cellpadding=\"4\" border=\"0\" align=\"center\" class=\"window\">\r\n<thead>\r\n        <tr><td align=\"left\" colspan=\"5\" class=\"title\">$category_name</td></tr>\r\n</thead>\r\n<tbody>\r\n<!-- Category Header -->\r\n        <tr align=\"center\">\r\n          <td class=\"category\">&nbsp;</td>\r\n          <td width=\"50%\" align=\"left\" class=\"category\">Forum</td>\r\n          <td width=\"34%\" class=\"category\">Last Post</td>\r\n          <td width=\"8%\" class=\"category\">Threads</td>\r\n          <td width=\"8%\" class=\"category\">Posts</td>  \r\n        </tr>\r\n<!-- End Category Header -->\r\n$boards\r\n</tbody>\r\n</table>\r\n<!-- End Category -->',0),(59,'form_element_list','<select id=\"$id\" name=\"$id\">$default</select>',0),(60,'form_element_number','<input id=\"$id\" name=\"$id\" type=\"text\" value=\"$default\" class=\"textbox\" style=\"width: 200px;\" $additional />',0),(65,'forum_posts_view','<h2>$thread_name</h2>\r\n<p>Thread started by $user_name at $thread_timestamp</p>\r\n<div style=\"float: right; margin-top: 10px;\">$buttons</div>\r\n$breadcrumb\r\n\r\n$posts\r\n\r\n<div align=\"right\" style=\"margin-top: 10px;\">$buttons</div>',0),(66,'forum_front_view','<h2>$forum_title</h2>\r\n<p>$forum_description</p>\r\n\r\n$breadcrumb\r\n\r\n$categories',0),(67,'forum_thread','<!-- Start Post -->\r\n<tr align=\"center\">\r\n        <td width=\"29\" class=\"windowbg2\"><img border=\"0\" src=\"$siteurl/resources/images/$style/thread.png\"></td>\r\n        <td align=\"left\" id=\"f2\" class=\"windowbg\">\r\n                <div style=\"font-weight: bold;\"><a href=\"showthread.php?t=$thread_id\">$thread_name</a></div>\r\n                <div class=\"smallfont\"><a href=\"user.php?id=$user_id\">$user_name</a></div>\r\n        </td>\r\n        <td nowrap=\"nowrap\" class=\"windowbg2\">\r\n                <div align=\"left\" class=\"smallfont\">\r\n                        <div align=\"right\"><p>$last_post_time<br/> by <a rel=\"nofollow\" href=\"user.php?id=$last_user_id\">$last_user_name</a></div></p></div>\r\n                </div>\r\n        </td>\r\n        <td class=\"windowbg\">$thread_replies</td>\r\n        <td class=\"windowbg2\">$thread_views</td>   \r\n</tr>\r\n<!-- End Post -->',0),(68,'forum_threads','<!-- Begin Board -->\r\n<table width=\"100%\" cellspacing=\"1\" cellpadding=\"4\" border=\"0\" align=\"center\" class=\"window\">\r\n<thead>\r\n        <tr><td align=\"left\" colspan=\"5\" class=\"title\">$board_name</td></tr>\r\n</thead>\r\n<tbody>\r\n<!-- Board Header -->\r\n        <tr align=\"center\">\r\n          <td class=\"category\">&nbsp;</td>\r\n          <td width=\"100%\" align=\"left\" class=\"category\">Thread</td>\r\n          <td width=\"250\" nowrap=\"nowrap\" class=\"category\">Last Post</td>\r\n          <td nowrap=\"nowrap\" class=\"category\">Replies</td>\r\n          <td nowrap=\"nowrap\" class=\"category\">Views</td>  \r\n        </tr>\r\n<!-- End Board Header -->\r\n$posts\r\n</tbody>\r\n</table>\r\n<!-- End Board -->\r\n',0),(69,'forum_board_view','<h2>$board_name</h2>\r\n<p>$board_description</p>\r\n<div style=\"margin-top: 10px; float: right\">$buttons</div>\r\n$breadcrumb\r\n\r\n$threads\r\n\r\n<div align=\"right\" style=\"margin-top: 10px;\">$buttons</div>',0),(74,'forum_buttons','<div align=\"right\" style=\"margin-top: 10px;\">\r\n$buttons\r\n</div>',0),(75,'forum_button','<a href=\"$siteurl/$action\"><img border=\"0\" title=\"$name\" src=\"$siteurl/resources/images/$style/$image\" alt=\"$name\"></a> ',0),(77,'forum_members','<h2>Members List</h2>\r\n<p>A list of all the members on the site</p>\r\n\r\n$breadcrumb\r\n\r\n<div class=\"window\">\r\n<div class=\"title\">User List</div>\r\n<div class=\"windowbg\">\r\n<ul>\r\n$userlist\r\n</ul>\r\n</div>\r\n</div>',0),(78,'forum_member','<li><strong><a href=\"$siteurl/user.php?id=$user_id\">$user_name</a></li>',0),(79,'forum_search_result','<!-- Start post -->\r\n<div style=\"padding:0px 0px 4px 0px\">\r\n<table width=\"100%\" cellspacing=\"1\" cellpadding=\"4\" border=\"0\" align=\"center\" class=\"window\">\r\n<tbody><tr>\r\n        <td align=\"left\" class=\"title\">\r\n                <div style=\"float:right\" class=\"normal\"><a href=\"$siteurl/showthread.php?t=$thread_id\" style=\"color: white;\">#<strong>$post_counter</strong></a> &nbsp;</div>\r\n                <div class=\"normal\">$post_timestamp</div>\r\n        </td>\r\n</tr><tr>\r\n        <td align=\"left\" style=\"padding:0px\" class=\"windowbg2\">\r\n                <!-- Start user info -->\r\n                <table width=\"100%\" cellspacing=\"4\" cellpadding=\"0\" border=\"0\">\r\n                <tbody><tr>\r\n\r\n                        <td nowrap=\"nowrap\" align=\"left\">\r\n                                        <a href=\"user.php?id=$user_id\" class=\"bigusername\">$user_name</a>\r\n                                        <div class=\"smallfont\">$user_type</div>\r\n                        </td>\r\n                        <td width=\"100%\">&nbsp;</td>\r\n                        <td valign=\"top\" nowrap=\"nowrap\" align=\"left\">\r\n                                <div class=\"smallfont\">\r\n                                        <div>Join Date: $user_joined</div>\r\n                                <div></div>\r\n                                </div>\r\n                        </td>\r\n                </tr>\r\n                </tbody></table>\r\n                <!-- End user info -->\r\n        </td>\r\n</tr>\r\n<tr>   \r\n        <td align=\"left\" class=\"windowbg\">\r\n                <!-- Message -->\r\n                <div style=\"margin: 0; padding: 0;\">\r\n                <p style=\"font-weight: bold; margin: 0; padding: 0;\"><a href=\"$siteurl/showthread.php?t=$thread_id\">$post_name</a></p>\r\n                <p>$post_message</p>\r\n                </div>\r\n                <!-- End Message -->\r\n        </td>\r\n</tr>\r\n</tbody></table>\r\n<!-- End post -->',0),(80,'admin_category_item','<li><strong><a href=\"$siteurl/admin.php/categories/edit/$category_id\">$category_name</a>  </strong> [<a href=\"$siteurl/admin.php/categories/edit/$category_id\">Edit</a>] [<a href=\"$siteurl/admin.php/categories/delete/$category_id\">Delete</a>]</li>',0),(81,'admin_board_item','<li><strong><a href=\"$siteurl/admin.php/boards/edit/$board_id\">$board_name</a>  </strong> [<a href=\"$siteurl/admin.php/boards/edit/$board_id\">Edit</a>] [<a href=\"$siteurl/admin.php/boards/delete/$board_id\">Delete</a>]</li>',0),(82,'admin_category_list','<ul>\r\n$categories\r\n</ul>',0),(83,'admin_board_list','<ul>\r\n$boards\r\n</ul>',0),(84,'front_page','?>\r\n<h2>Welcome to FailForum</h2>\r\n<div style=\"float: right\"><img src=\"$siteurl/resources/images/$style/forum_image.png\"></img></div>\r\n\r\n<p>Thank you for installing Fail Forum, the forum of a thousand fails. This is Forum <b>2.0</b>! No other forum can encompass such levels of 360 degree thinking and provide all this at an amazing cost while at the end of the day being better than every other piece of software.</p>\r\n<p>With such amazing features as:</p>\r\n<ul>\r\n<li>Threads and posts allow everyone to communicate, together.</li>\r\n<li>Create your own categories and boards. The blue sky is the limit!</li>\r\n<li>Users can register and have their own profiles to facilitate open collaboration and discussion</li>\r\n<li>Administrators can delete and edit all threads and posts</li>\r\n<li>Powerful search feature which lets you find what you want to find, when you want to find it</li>\r\n<li>Members list, social features and contact functionality enables you to touch base with your users</li>\r\n<li>Powerful administration center to maintain control</li>\r\n<li>The blue sky is the limit!</li>\r\n</ul>\r\n\r\n<p>No other forum can compete with the power, ease, flexibility and fail of FailForum!</p>\r\n\r\n<h3>Getting Started</h3>\r\n<p>To get started with your new forum and being your journey up the strategic staircase, you can immediately start customising the categories and boards and site from the <a href=\"$siteurl/admin.php\">administration panel</a>.</p>\r\n<p>Going forward, visit your <a href=\"$siteurl/forums.php\">forums</a> and take your community to the next level. The default username and password is admin:admin</p>\r\n<p>To edit the content of your front page, edit the <a href=\"$siteurl/admin.php/templates/edit/84\">front page template</a>\r\n\r\n<h3>Recent Topics</h3>\r\n<?php\r\n\r\nglobal $core;\r\n\r\nrequire(\"modules/forums.php\");\r\n$threads = new Threads($core);\r\n\r\n$site_url = $core->config[\'Paths\'][\'web\'];\r\n\r\nprint \'<p>The freshest forum topics:</p>\';\r\n\r\n$recent_threads = $threads->get_recent_threads();\r\nwhile ($thread = $recent_threads->fetch_assoc()) {\r\n   print \'<small><a href=\"\' . $site_url . \'/showthread.php?t=\' . $thread[\'thread_id\'] . \'\">\' . $thread[\'thread_name\'] . \'</a></small><br/>\';\r\n}\r\n',1);
/*!40000 ALTER TABLE `info3005_template` ENABLE KEYS */;

--
-- Table structure for table `info3005_threads`
--

DROP TABLE IF EXISTS `info3005_threads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `info3005_threads` (
  `thread_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `board_id` int(11) NOT NULL,
  `thread_name` varchar(255) NOT NULL,
  `thread_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `thread_views` int(11) NOT NULL,
  `thread_replies` int(11) NOT NULL,
  PRIMARY KEY (`thread_id`),
  KEY `user_id` (`user_id`,`board_id`),
  KEY `thread_timestamp` (`thread_timestamp`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `info3005_threads`
--

/*!40000 ALTER TABLE `info3005_threads` DISABLE KEYS */;
INSERT INTO `info3005_threads` VALUES (1,1,1,'Welcome to Fail Forum!','2011-11-11 17:04:47',7,1);
/*!40000 ALTER TABLE `info3005_threads` ENABLE KEYS */;

--
-- Table structure for table `info3005_user`
--

DROP TABLE IF EXISTS `info3005_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `info3005_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` tinytext NOT NULL,
  `user_email` tinytext NOT NULL,
  `user_picture` tinytext NOT NULL,
  `user_bio` text NOT NULL,
  `user_homepage` tinytext NOT NULL,
  `user_type` tinyint(4) NOT NULL,
  `user_style` tinytext NOT NULL,
  `user_password` text NOT NULL,
  `user_ip` tinytext NOT NULL,
  `user_cookie` text NOT NULL,
  `user_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `info3005_user`
--

/*!40000 ALTER TABLE `info3005_user` DISABLE KEYS */;
INSERT INTO `info3005_user` VALUES (1,'admin','test@example.com','','I love FailForum!','http://ecs.soton.ac.uk',2,'','admin','152.78.71.142','','2011-11-08 00:00:00');
/*!40000 ALTER TABLE `info3005_user` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2011-11-11 17:54:28
