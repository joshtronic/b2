<?

/******************************************************************************\
 * Copyright (C) 2002 B Squared (b^2) by Josh Sherman <josh@cleancode.org>    *
 *                                                                            *
 * Just like on Mtv's Cribs, this is where the magic happen.  This is the     *
 * only file that will output anything to the user.  Huh?  Yeah, all the      *
 * content pages are called from this file and loaded that way, they had been *
 * set up to NOT let you call them directly.                                  *
 *                                                                            *
 *                                 Last modified : September 24th, 2002 (JJS) *
\******************************************************************************/

/* Grab the time the page started loading */
$start_time = microtime();

/* Define the generic error message */
define("ERROR", "<B>There was a error.</B><BR><BR>The administrator has been notified, and the problem will be resolved as soon as he/she feels like it!\n");

/* Load the include file, and quit if it messes up */
if (!@include("./include/include.php"))
  exit(ERROR);

/* Check the current state, and proceed to the installer is appropriate */

/* Check to see if config.php is present */
if ( !@include("config.php") )
  {
    /* No config? then call the installer! */
    require("install.php");
    exit;
  }
/* If config.php is there, then check the installation status */
else if (INSTALLED != "yes")
  {
    /* Not installed? then call the installer! */
    require("install.php");
    exit;
  }

/* Enable output buffering, so we can tweak the headers anytime */
ob_start();

/* Check the super globals and pull the values */
$destination    = GetVars("destination");
$message        = GetVars("message");
$password       = GetVars("password");
$title          = GetVars("title");
$username       = GetVars("username");
$mod_action     = GetVars("mod_action");
$admin_action   = GetVars("admin_action");
$logout         = GetVars("logout");
$pid            = GetVars("pid");
$HTTP_HOST      = GetVars("HTTP_HOST");
$REQUEST_METHOD = GetVars("REQUEST_METHOD");
$QUERY_STRING   = GetVars("QUERY_STRING");
$forum_id       = GetVars("forum_id");
$thread_id      = GetVars("thread_id");
$reply_id       = GetVars("reply_id");
$preview_scheme = GetVars("preview_scheme");
$user_name      = GetVars("user_name");

/* Assign null values to these variables */
$logged_in       = 0;
$login           = "";
$user_id         = "";
$is_moderator    = 0;
$is_admin        = 0;
$hack_attempt    = "";
$mod_feedback    = "";
$admin_feedback  = "";
$show_thread     = "";
$show_forum      = "";
$scheme_error    = "";
$scheme_feedback = "";

/* Parse the variables and trim them to a specified length */
CheckVars(&$pid, 16);

/* Connect to the MySQL database */
define("CONNECTION", @mysql_connect(DB_HOST, DB_USER, DB_PASS));
if (!CONNECTION)
  {
    if (ADMIN_ERRORS != "yes")
      {
        NotifyAdmin("mysql_connect");
        exit(ERROR);
      }
    else
      exit("There was an error.<BR><BR><B>MySQL Error:</B> <I>" . mysql_error() . "</I>\n");
  }

/* Pull the general properties from the database */
$SQL     = "SELECT * FROM " . TABLE_PREFIX . "properties;";
$results = ExeSQL($SQL);

/* Grab the data and assign the values to constants */
while ($row = mysql_fetch_array($results))
  {
    define("BOARD_NAME",  $row["board_name"]);
    define("TITLE_IMAGE", $row["title_image"]);
  }

/* Attempt to log the user in, if requested */
AttemptLogin(&$pid, &$logged_in, &$login, $username, &$password, &$is_moderator, &$is_admin );

/* Verify their identity, if they are logged in */
VerifyLogin( &$logged_in, &$user_id, &$is_moderator, &$is_admin );

/* Attempt to perform a moderator action, if requested */
ModAction( &$is_moderator, &$mod_action, $forum_id, $thread_id, $reply_id, $user_id, &$hack_attempt, &$mod_feedback, &$show_thread, &$show_forum );

/* Attempt to perform an admin action, if requested */
AdminAction( &$is_admin, &$admin_action, $forum_id, $thread_id, $reply_id, $user_id, &$hack_attempt, &$mod_feedback, &$show_thread, &$show_forum );

/* Determine if we pull the default scheme, or preview another */
if ($is_admin != 1)
  $SQL = "SELECT * FROM " . TABLE_PREFIX . "schemes WHERE active_scheme='1';";
else
  {
    if ($preview_scheme == "")
      $SQL = "SELECT * FROM " . TABLE_PREFIX . "schemes WHERE active_scheme='1';";
    else
      {
        /* Pull the scheme that was requested */
        $SQL     = "SELECT COUNT(*) AS scheme_exists FROM " . TABLE_PREFIX . "schemes WHERE scheme_id='$preview_scheme';";
        $results = ExeSQL($SQL);

        /* Grab data and load it in a variable */
        while ($row = mysql_fetch_array($results))
          $scheme_exists = $row["scheme_exists"];

        /* If the scheme doesn't exist then ... */
        if ($scheme_exists == 0)
          {
            /* Pull the active scheme anyway! */
            $SQL          = "SELECT * FROM " . TABLE_PREFIX . "schemes WHERE active_scheme='1';";
            $scheme_error = "The scheme you requested to preview is unknown.";
          }
        else
          {
            /* Pull the name of the requested scheme */
            $SQL     = "SELECT scheme_name FROM " . TABLE_PREFIX . "schemes WHERE scheme_id='$preview_scheme';";
            $results = ExeSQL($SQL);

            /* Grab the name of the scheme and load it in a variable */
            while ($row = mysql_fetch_array($results))
              $scheme_name = $row["scheme_name"];

            /* Pull the request scheme's properties */
            $SQL             = "SELECT * FROM " . TABLE_PREFIX . "schemes WHERE scheme_id='$preview_scheme';";
            $scheme_feedback = "You are currently previewing the '$scheme_name' scheme.";
          }
      }
  }

/* Executed the winning scheme query */
$results = ExeSQL($SQL);

/* Grab the data and load it into constants */
while ($row = mysql_fetch_array($results))
  {
    define("BACKGROUND_COLOR",        $row["background_color"]);
    define("TABLE_BORDER_COLOR",      $row["table_border_color"]);
    define("TABLE_BORDER_SIZE",       $row["table_border_size"]);
    define("HEADER_BACKGROUND",       $row["header_background"]);
    define("MENU_BACKGROUND",         $row["menu_background"]);
    define("TEXT_COLOR",              $row["text_color"]);
    define("TEXT_FONT",               $row["text_font"]);
    define("TEXT_SMALL",              $row["text_small"]);
    define("TEXT_REGULAR",            $row["text_regular"]);
    define("LINK_COLOR",              $row["link_color"]);
    define("TABLE_HEADER_BACKGROUND", $row["table_header_background"]);
    define("TABLE_HEADER_TEXT_COLOR", $row["table_header_text_color"]);
    define("TABLE_COLOR_1",           $row["table_color_1"]);
    define("TABLE_COLOR_2",           $row["table_color_2"]);
    define("ERROR_MESSAGE",           $row["error_message"]);
  }

/* Attempt to redefine the colors with the defaults (success = there is nothing in the schemes table) */
define("BACKGROUND_COLOR",        "#FFFFFF");
define("TABLE_BORDER_COLOR",      "#000000");
define("TABLE_BORDER_SIZE",       "1");
define("HEADER_BACKGROUND",       "#FFFFFF");
define("MENU_BACKGROUND",         "#EEEEEE");
define("TEXT_COLOR",              "#000000");
define("TEXT_FONT",               "Verdana");
define("TEXT_SMALL",              "10");
define("TEXT_REGULAR",            "12");
define("LINK_COLOR",              "#000000");
define("TABLE_HEADER_BACKGROUND", "#000000");
define("TABLE_HEADER_TEXT_COLOR", "#FFFFFF");
define("TABLE_COLOR_1",           "#EEEEEE");
define("TABLE_COLOR_2",           "#CCCCCC");
define("ERROR_MESSAGE",           "#FF0000");

/* Log the user out if requested */
if ($logout == "now")
  {
    /* Blow out the cookie */
    SetCookie("user_name", "", time() - 3600, ''); //, $HTTP_HOST);
    SetCookie("user_pass", "", time() - 3600, ''); //, $HTTP_HOST);

    /* Blow out the variables */
    $logged_in    = 0;
    $is_admin     = 0;
    $is_moderator = 0;
  }

/* If the destination is specified, then assign it to the $pid */
if ($destination != "")
  $pid = $destination;

/* If there's no specified $pid, then default to 'view_forums' */
if ($pid == "")
  $pid = "view_forums";

/* If $show_thread isn't 0, then set the $pid and $thread_id */
if ($show_thread != 0)
  {
    $pid       = "view_replies";
    $thread_id = $show_thread;
  }

/* Same deal as before, except it happens if $show_forum isn't 0 */
if ($show_forum != 0)
  {
    $pid       = "view_threads";
    $thread_id = $show_forum;
  }

/* Determine which page to load based on the querystring */
switch ($pid)
  {
    /* The default page is the 'view forums' page */
    default:
    case "view_forums":
      $page_title = "View Forums";
      $pid = "view_forums";
      break;

    /* Nothing special */
    case "view_threads":
      $page_title = "View Threads";
      break;

    /* Nadda */
    case "view_replies":
      $page_title = "View Replies";
      break;

    /* Zippo */
    case "register":
      $page_title = "Register";
      break;

    /* Zilch */
    case "login":
      $page_title = "Login";
      break;

    /* If the user is trying to post a thread, check if they are logged in */
    case "post_thread":
      $page_title = "Post Thread";

      /* If not, then direct them to the login page */
      if ($logged_in == 0)
        {
          $destination = $pid;
          $pid = "login";
        }
      break;
    
    /* If the user is trying to post a reply, check if they are logged in */
    case "post_reply":
      $page_title = "Post Reply";

      /* If not, then direct them to the login page */
      if ($logged_in == 0)
        {
          $destination = $pid;
          $pid = "login";
        }
      break;

    /* If the user is trying to edit a profile, check if they are logged in */
    case "edit_profile":
      $page_title = "Edit Profile";
 
      /* If not, then direct them to the login page */
      if ($logged_in == 0)
        {
          $destination = $pid;
          $pid = "login";
        }
      break;

    /* Do the normal thang */
    case "view_profile":
      $page_title = "View Profile";
      break;

    /* These are the admin sections */
    case "forum_admin":
    case "user_admin":
    case "scheme_admin":
    case "general_admin":

      /* If th user isn't logged in, send them there */
      if ($logged_in == 0)
        {
          $destination = $pid;
          $pid = "login";
        }

      /* If the user isn't an admin, assume it's a hack attempt */
      if ($is_admin == 0)
        {
          $hack_attempt = "outside";
          $pid = "view_forums";
        }
      break;
 
    /* Show the FAQ for the board */
    case "faq":
      $page_title = "Frequently Asked Questions";
      $message    = $pid;
      $pid        = "view_message";
      break;
  }

/* Conver the $pid to lower case, and pull that filename */
$page_file = "./content/" . strtolower($pid) . ".php";

/* Display the page header, including CSS stuff */
echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n"
   . "<HTML>\n"
   . "  <HEAD>\n"
   . "    <TITLE>" . BOARD_NAME . " [ powered by b^2 " . VERSION . " ]</TITLE>\n"
   . "    <SCRIPT language=\"JavaScript\" src=\"./include/javascript.js\"></SCRIPT>\n"
   . "    <STYLE>\n"
   . "      A\n"
   . "        {\n"
   . "          color: " . LINK_COLOR . ";\n"
   . "        }\n"
   . "      A:hover\n"
   . "        {\n"
   . "          text-decoration: none;\n"
   . "        }\n"
   . "      BODY, FONT\n"
   . "        {\n"
   . "          font-family: " . TEXT_FONT . ";\n"
   . "          font-size:   " . TEXT_REGULAR . ";\n"
   . "          color:       " . TEXT_COLOR . ";\n"
   . "        }\n"
   . "      INPUT, TEXTAREA\n"
   . "        {\n"
   . "          font-family: " . TEXT_FONT . ";\n"
   . "          font-size:   " . TEXT_REGULAR . ";\n"
   . "        }\n"
   . "      .small_text\n"
   . "        {\n"
   . "          font-size: " . TEXT_SMALL . ";\n"
   . "        }\n"
   . "      .regular_text\n"
   . "        {\n"
   . "          font-size: " . TEXT_REGULAR . ";\n"
   . "        }\n"
   . "      .error_message\n"
   . "        {\n"
   . "          font-family: " . TEXT_FONT . ";\n"
   . "          font-size:   " . TEXT_REGULAR . ";\n"
   . "          color:       " . ERROR_MESSAGE . ";\n"
   . "          font-weight: BOLD;\n"
   . "        }\n"
   . "      .normal_message\n"
   . "        {\n"
   . "          font-family: " . TEXT_FONT . ";\n"
   . "          font-size:   " . TEXT_REGULAR . ";\n"
   . "          color:       " . TEXT_COLOR . ";\n"
   . "          font-weight: BOLD;\n"
   . "        }\n"
   . "      .table_header\n"
   . "        {\n"
   . "          font-size:        " . TEXT_SMALL . ";\n"
   . "          color:            " . TABLE_HEADER_TEXT_COLOR . ";\n"
   . "          background-color: " . TABLE_HEADER_BACKGROUND . ";\n"
   . "          font-weight:      BOLD;\n"
   . "        }\n"
   . "      .table_border, td, tr\n"
   . "        {\n"
   . "          border-width: " . TABLE_BORDER_SIZE . ";\n"
   . "          border-color: " . TABLE_BORDER_COLOR . ";\n"
   . "        }\n"
   . "    </STYLE>\n"
   . "  </HEAD>\n"
   . "  <BODY bgcolor=\"" . BACKGROUND_COLOR . "\">\n"
   . "    <TABLE align=\"center\" valign=\"top\" cellpadding=\"8\" cellspacing=\"0\" width=\"100%\">\n"
   . "      <TR>\n"
   . "        <TD>\n"
   . "          <TABLE width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border class=\"table_border\">\n"
   . "            <TR>\n"
   . "              <TD>\n"
   . "                <TABLE width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\n"
   . "                  <TR bgcolor=\"" . HEADER_BACKGROUND . "\">\n"
   . "                    <TD><A href=\"?pid=view_forums\"><IMG src=\"" . TITLE_IMAGE . "\" border=\"0\"></A></TD>\n"
   . "                    <TD align=\"right\" valign=\"bottom\" nowrap>\n"
   . "                      <TABLE cellpadding=\"5\" cellspacing=\"0\" border=\"0\">\n"
   . "                        <TR>\n"
   . "                          <TD>\n";

/* Check if the user is logged in */
if ($logged_in == 0)
  {
    /* If not, then display the 'Log In' option */
    $login_status = "Not logged in (<A href=\"?pid=login\">Log In</A>)";
  }
else
  {
    /* If they are logged in, pull the username form the cookie */
    if ($user_name == "")
      $username = $username;
    else
      $username = $user_name;

    /* Tell them they are logged in, and give them the option to log out */
    $login_status = "Logged in as <B>$username</B> (<A href=\"?pid=login&logout=now\">Log Out</A>)";
  }

/* Display the login status, and start on the menu */
echo "                            <FONT class=\"small_text\">$login_status</FONT>\n"
   . "                          </TD>\n"
   . "                        </TR>\n"
   . "                      </TABLE>\n"
   . "                    </TD>\n"
   . "                  </TR>\n"
   . "                </TABLE>\n"
   . "              </TD>\n"
   . "            </TR>\n"
   . "            <TR>\n"
   . "              <TD bgcolor=\"" . MENU_BACKGROUND . "\" valign=\"middle\">\n"
   . "                <TABLE cellpadding=\"3\" cellspacing=\"0\" border=\"0\" width=\"100%\">\n"
   . "                  <TR>\n"
   . "                    <TD valign=\"middle\">\n";

/* If not logged in, give the register link */
if ($logged_in == 0)
  {
    $show_profile  = "";
    $show_register = "<A href=\"?pid=register\">Register</A> | ";
  }
/* If logged in, then give a link to their profile */
else
  {
    $show_profile  = "<A href=\"?pid=edit_profile\">My Profile</A> | ";
    $show_register = "";
  }

/* If the user is an admin, and logged in, display the admin links */
if ( $is_admin == 1 && $logged_in == 1 )
  $show_admin = "<A href=\"?pid=general_admin\">General Admin</A> | <A href=\"?pid=scheme_admin\">Scheme Admin</A> | <A href=\"?pid=forum_admin\">Forum Admin</A> | <A href=\"?pid=user_admin\">User Admin</A> | ";
else
  $show_admin = "";

/* Display the rest of the menu, and continue to the body of the page */
echo "                      <FONT class=\"small_text\">&nbsp;$show_profile$show_register$show_admin <A href=\"?pid=faq\">FAQ</A> <!-- | Search --></FONT><BR>\n"
   . "                    </TD>\n"
   . "                  </TR>\n"
   . "                </TABLE>\n"
   . "              </TD\n"
   . "            </TR>\n"
   . "          </TABLE>\n"
   . "        </TD>\n"
   . "      </TR>\n"
   . "      <TR>\n"
   . "        <TD class=\"body_part\">\n"
   . "          <!-- Begin Dynamic Content -->\n";

/* If there is a scheme error, then show it! */
if ($scheme_error != "")
  echo "            <CENTER class=\"error_message\">$scheme_error</CENTER><BR>\n";

/* If there's scheme feedback, then show it */
if ($scheme_feedback != "")
  echo "            <CENTER class=\"normal_message\">$scheme_feedback</CENTER><BR>\n";

/* If there's a malformed request to the moderator tools, then error out */
if ($hack_attempt == "outside")
  echo "            <CENTER class=\"error_message\">Malformed request detected!</CENTER><BR>\n";
else if ($hack_attempt == "inside")
  echo "            <CENTER class=\"error_message\">Sorry, but your moderator privileges don't extend to this particular forum.</CENTER></BR>\n";

/* If a moderator tool have been executed, give feedback on it, positive or negative */
if ($mod_feedback != "")
  echo "            <CENTER class=\"normal_message\">$mod_feedback</CENTER><BR>\n";

/* Load the content for the page that was requested */
require($page_file);

/* It's all downhill from here ... */
echo "          <!-- End Dynamic Content -->\n"
   . "        </TD>\n"
   . "      </TR>\n"
   . "      <TR>\n"
   . "        <TD align=\"center\" class=\"small_text\">\n"
   . "          Powered by <B><A href=\"http://www.cleancode.org/b2/\" target=\"_blank\">b^2</A></B> " . VERSION . "<BR>\n";

/* Grab the current time, and figure the difference */
$load_time = round((microtime() - $start_time), 5);

/* If it's negative, then strip off the '-' */
if (substr($load_time, 0, 1) == "-")
  $load_time = substr($load_time, 1);

echo "          [ Page rendered in $load_time seconds ]\n"
   . "        </TD>\n"
   . "      </TR>\n"
   . "    </TABLE>\n"
   . "  </BODY>\n"
   . "</HTML>\n";

/* Close the MySQL connection like a good code monkey! */
mysql_close(CONNECTION);

/* Display the buffer, and stop buffering */
ob_end_flush();

?>
