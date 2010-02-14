<?

/******************************************************************************\
 * Copyright (C) 2002 B Squared (b^2) by Josh Sherman <josh@cleancode.org>    *
 *                                                                            *
 * Blah blah blah blah                                                        *
 *                                                                            *
 *                                 Last modified : September 17th, 2002 (JJS) *
\******************************************************************************/

/* Installation page title */
$title = "b^2 " . VERSION . " Installer";

/* Section headings */
$general = "General Properties";
$mysql   = "MySQL Properties";
$admin   = "Admin Account";
$forum   = "Initial Forum";

/* Section field names */
$board_name     = "Board Name";
$title_image    = "Title Image";
$username       = "Username";
$password       = "Password";
$database       = "Database";
$hostname       = "Hostname";
$table_prefix   = "Table Prefix";
$confirm_pass   = "Confirm Password";
$email          = "Email";
$name           = "Name";
$description    = "Description";
$forum_name     = "General Discussion";
$forum_desc     = "This forum is for general discussion";
$install_button = "Install b^2 " . VERSION;

/*************** FYI, you shouldn't need to edit below here ... ***************/

/* Define all the stuff as constants, so I can work with it */
define("INSTALL_TITLE", $title);
define("GENERAL", $general);
define("MYSQL", $mysql);
define("ADMIN", $admin);
define("FORUM", $forum);
define("BOARD_NAME", $board_name);
define("TITLE_IMAGE", $title_image);
define("USERNAME", $username);
define("PASSWORD", $password);
define("DATABASE", $database);
define("HOSTNAME", $hostname);
define("TABLE_PREFIX", $table_prefix);
define("CONFIRM_PASSWORD", $confirm_pass);
define("EMAIL", $email);
define("NAME", $name);
define("DESC", $description);
define("FORUM_NAME", $forum_name);
define("FORUM_DESC", $forum_desc);
define("INSTALL_BUTTON", $install_button);

?>
