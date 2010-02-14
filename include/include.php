<?

/******************************************************************************\
 * Copyright (C) 2002 B Squared (b^2) by Josh Sherman <josh@cleancode.org>    *
 *                                                                            *
 * This script contains commonly used functions and variables for the site.   *
 *                                                                            *
 *                                Last modified : Septemeber 25th, 2002 (JJS) *
\******************************************************************************/

/* B Squared Version Number */
define("VERSION", "0.6.2");

/* B Squared Path */
$b2_path = "./";

/*
 * return a trimmed value based on the given value
 * and length
 *
 * @author   Dean Jones <dean@geekoid.org>
 * @param    string    $var
 *                     the variable we want to trim
 *
 * @param    int       $size
 *                     the length we want to trim the
 *                     varible to
 *
 * @return   string    the variable trimmed to the
 *                     length specified in $size
 */

function
CheckVars($var, $size)
{
  /* Determine the length of $var */
  $length = strlen($var);

  /* If the length is fine, then exit */
  if ($length <= $size)
    return;

  /* else, get your loop on! */
  for ( ; $length >= $size; $length--)
    $var[$length] = "";
}

/*
 *
 */

function
GetVars($varname, $defval=NULL)
{
  if (array_key_exists($varname, $_SERVER))
    $retval = $_SERVER[$varname];
  elseif (array_key_exists($varname, $_COOKIE))
    $retval = $_COOKIE[$varname];
  elseif (array_key_exists($varname, $_POST))
    $retval = $_POST[$varname];
  elseif (array_key_exists($varname, $_GET))
    $retval = $_GET[$varname];
  elseif (array_key_exists($varname, $_ENV))
    $retval = $_ENV[$varname];
  else
    $retval = $defval;

  return $retval;
}

/*
 *
 */

function
ExeSQL($SQL)
{
  $results = @mysql_db_query(DB_NAME, $SQL, CONNECTION);

  if (!$results)
    {
      if (ADMIN_ERRORS != "yes")
        {
          NotifyAdmin("mysql_query");
          exit(ERROR);
        }
      else
        exit("There was an error.<BR><BR><B>MySQL Error:</B> <I>" . mysql_error() . "</I>\n");
    }

  return($results);
}

/*
 *
 */

function
AttemptLogin( $pid, $logged_in, $login, $username, $password, $is_moderator, $is_admin )
{
  /* Attempt to log the user in if they request it */
  if ( $_SERVER['REQUEST_METHOD'] == "POST" && $pid == "login" && $username != "" && $password != "" )
    {
      /* Check to see if the provided username exists in the database */
      $SQL     = "SELECT COUNT(*) AS user_exists FROM " . TABLE_PREFIX . "users WHERE user_name='$username';";
      $results = ExeSQL($SQL);

      /* Grab the data, and analyze it */
      while ($row = mysql_fetch_array($results))
        $user_exists = $row["user_exists"];

      /* User provided correct username */
      if ($user_exists == 1)
        {
          /* Check to see if the provided username exists in the database */
          $SQL     = "SELECT user_pass FROM " . TABLE_PREFIX . "users WHERE user_name='$username';";
          $results = ExeSQL($SQL);

          /* Grab the data, and analyze it */
          while ($row = mysql_fetch_array($results))
            $existing_pass = $row["user_pass"];

          $password = crypt($password, $existing_pass);
          $the_host = GetVars("HTTP_HOST");

          if ($password == $existing_pass)
            {
              /* Set the cookies */
              SetCookie("user_name", $username, time() + 86400, ''); //, $the_host);
              SetCookie("user_pass", $password, time() + 86400, ''); //, $the_host);

              $pid = "view_forums";
              $logged_in = 1;
            }
          else
            {
              /* Clear the cookies */
              SetCookie("user_name", "", time() - 3600, ''); //, $the_host);
              SetCookie("user_pass", "", time() - 3600, ''); //, $the_host);

              $pid = "login";
              $login = "failed";
              $logged_in = 0;
            }

          if ($logged_in == 1)
            {
              /* Pull the user ID for the user */
              $SQL     = "SELECT user_id FROM " . TABLE_PREFIX . "users WHERE user_name='$username';";
              $results = ExeSQL($SQL);

              /* Grab the data */
              while ($row = mysql_fetch_array($results))
                $user_id = $row["user_id"];

              /* Check to see if the user is a moderator */
              $SQL     = "SELECT COUNT(*) AS is_moderator FROM " . TABLE_PREFIX . "moderators WHERE user_id='$user_id';";
              $results = ExeSQL($SQL);

              /* Grab the data */
              while ($row = mysql_fetch_array($results))
                $is_moderator = $row["is_moderator"];

              /* Check to see if the user is an administrator */
              $SQL     = "SELECT COUNT(*) AS is_admin FROM " . TABLE_PREFIX . "administrators WHERE user_id='$user_id';";
              $results = ExeSQL($SQL);

              /* Grab the data */
              while ($row = mysql_fetch_array($results))
                $is_admin = $row["is_admin"];

              /* If user is admin, grant them moderator privileges */
              if ($is_admin != 0)
                $is_moderator = $is_admin;
            }
        }
      /* User provided incorrect username */
      else
        {
          /* Clear the cookies */
          SetCookie("user_name", "", time() - 3600, ''); //, $the_host);
          SetCookie("user_pass", "", time() - 3600, ''); //, $the_host);
  
          $pid = "login";
          $login = "failed";
          $logged_in = 0;
        } 
    }
}

/*
 *
 */

function
VerifyLogin( $logged_in, $user_id, $is_moderator, $is_admin )
{
  $user_name = GetVars("user_name");
  $user_pass = GetVars("user_pass");

  /* Verify the user's integrity */
  if ( $user_name != "" && $user_pass != "" )
    {
      /* Check to see if the provided username exists in the database */
      $SQL     = "SELECT COUNT(*) AS user_verification FROM " . TABLE_PREFIX . "users WHERE user_name='" . $_COOKIE["user_name"] . "';";
      $results = ExeSQL($SQL);

      /* Grab the data, and analyze it */
      while ($row = mysql_fetch_array($results))
        $user_verification = $row["user_verification"];

      if ($user_verification == 1)
        {
          /* Pull the password for the username we just determine existed */
          $SQL     = "SELECT user_name, user_pass FROM " . TABLE_PREFIX . "users WHERE user_name='" . $_COOKIE["user_name"] . "';";
          $results = ExeSQL($SQL);

          /* Grab the data, and analyze it */
          while ($row = mysql_fetch_array($results))
            {
              $existing_user = $row["user_name"];
              $existing_pass = $row["user_pass"];
            }

          $cookie_pass = urldecode($_COOKIE['user_pass']);

          if ($existing_pass == $cookie_pass) 
            {
              /* Set the cookies */
              SetCookie("user_name", $existing_user, time() + 86400, '', $_SERVER['HTTP_HOST']);
              SetCookie("user_pass", $existing_pass, time() + 86400, '', $_SERVER['HTTP_HOST']);

              $pid = "view_forums";
              $logged_in = 1;
            }
          else
            {
              /* Clear the cookies */
              SetCookie("user_name", "", time() - 3600, '', $_SERVER['HTTP_HOST']);
              SetCookie("user_pass", "", time() - 3600, '', $_SERVER['HTTP_HOST']);
              $pid = "login";
              $login = "failed";
              $logged_in = 0;
            }
        }
      else
        {
          SetCookie("user_name", "", time() - 3600, '', $_SERVER['HTTP_HOST']);
          SetCookie("user_pass", "", time() - 3600, '', $_SERVER['HTTP_HOST']);
          $logged_in = 0;
        }

      $is_moderator = $logged_in;
      $is_admin     = $logged_in;

      if ($logged_in == 1)
        {
          /* Pull the user ID for the user */
          $SQL     = "SELECT user_id FROM " . TABLE_PREFIX . "users WHERE user_name='" . $_COOKIE["user_name"] . "';";
          $results = ExeSQL($SQL);
 
          /* Grab the data */
          while ($row = mysql_fetch_array($results))
            $user_id = $row["user_id"];

          /* Check to see if the user is a moderator */
          $SQL     = "SELECT COUNT(*) AS is_moderator FROM " . TABLE_PREFIX . "moderators WHERE user_id='$user_id';";
          $results = ExeSQL($SQL);

          /* Grab the data */
          while ($row = mysql_fetch_array($results))
            $is_moderator = $row["is_moderator"];

          /* Check to see if the user is an administrator */
          $SQL     = "SELECT COUNT(*) AS is_admin FROM " . TABLE_PREFIX . "administrators WHERE user_id='$user_id';";
          $results = ExeSQL($SQL);

          /* Grab the data */
          while ($row = mysql_fetch_array($results))
            $is_admin = $row["is_admin"];

          /* If user is admin, grant them moderator privileges */
          if ($is_admin != 0)
            $is_moderator = $is_admin;
        }
      else
        {
          $is_moderator = 0;
          $is_admin     = 0;
        }
    }
}

/*
 *
 */

function
ModAction ( $is_moderator, $mod_action, $forum_id, $thread_id, $reply_id, $user_id, $hack_attempt, $mod_feedback, $show_thread, $show_forum ) 
{
  if ( $is_moderator == 0 && $mod_action != "" )
    {
      $hack_attempt = "outside";
      return;
    }

  if ($mod_action != "")
    {
      /* Pull the list of forums this user is a moderator for */
      $SQL     = "SELECT * FROM " . TABLE_PREFIX . "moderators WHERE user_id='$user_id';";
      $results = ExeSQL($SQL);

      /* Grab the data and load it in an array */
      while ($row = mysql_fetch_array($results))
        $moderated_forums[] = $row["forum_id"];  

      if (!in_array($forum_id, $moderated_forums))
        {
          $hack_attempt = "inside";
          return;
        }
    }

  switch ($mod_action)
    {
      case "Delete Reply":
        /* Delete the specified reply */
        $SQL     = "DELETE FROM " . TABLE_PREFIX . "replies WHERE reply_id='$reply_id';";
        $results = ExeSQL($SQL);

        $mod_feedback = "The reply has been removed from the board.";
        $show_thread  = $thread_id;
        break;

      case "Delete Entire Thread":
        /* Delete the specified thread */
        $SQL     = "DELETE FROM " . TABLE_PREFIX . "threads WHERE thread_id='$thread_id';";
        $results = ExeSQL($SQL);

        /* Delete the replies to the specified thread */
        $SQL     = "DELETE FROM " . TABLE_PREFIX . "replies WHERE thread_id='$thread_id';";
        $results = ExeSQL($SQL);

        $mod_feedback = "The thread has been removed from the board.";
        $show_forum   = $forum_id;
        break;

      default:
        break;
    }
}

/*
 *
 */

function
AdminAction ( $is_admin, $admin_action, $forum_id, $thread_id, $reply_id, $user_id, $hack_attempt, $admin_feedback, $show_thread, $show_forum )
{
  if ( $is_admin == 0 && $admin_action != "" )
    {
      $hack_attempt = "outside";
      return;
    }

  switch ($admin_action)
    {
      case "Delete Reply":
        /* Delete the specified reply */
        $SQL     = "DELETE FROM " . TABLE_PREFIX . "replies WHERE reply_id='$reply_id';";
        $results = ExeSQL($SQL);

        $mod_feedback = "The reply has been removed from the board.";
        $show_thread  = $thread_id;
        break;

      case "Delete Entire Thread":
        /* Delete the specified thread */
        $SQL     = "DELETE FROM " . TABLE_PREFIX . "threads WHERE thread_id='$thread_id';";
        $results = ExeSQL($SQL);

        /* Delete the replies to the specified thread */
        $SQL     = "DELETE FROM " . TABLE_PREFIX . "replies WHERE thread_id='$thread_id';";
        $results = ExeSQL($SQL);

        $mod_feedback = "The thread has been removed from the board.";
        $show_forum   =  $forum_id;
        break;

      default:
        break;
    }
}

/*
 *
 */

function
NotifyAdmin($what_error)
{
  /* If the admin notification is on, then run this */
  if (NOTIFY_ADMIN == "yes")
    {
      switch ($what_error)
        {
          /* MySQL Query errors */
          case "mysql_connect":
	    $subject = "[b^2] MySQL Error";
	    $body    = "There was an error connecting to MySQL, the error is as follows:\n\n" . mysql_error() . "";
	    break;

	  /* MySQL Query errors */
	  case "mysql_query":
	    $subject = "[b^2] MySQL Error";
	    $body    = "There was an error executing a MySQL Query, the error is as follows:\n\n" . mysql_error() . "";
	    break;

	  /* Default case, this should never be the case */
	  default:
	    $subject = "[b^2] Unknown Error";
	    $body    = "Something fucked up, you should never get this email!!";
	    break;
	}

      /* Send the email to the admin */
      mail(ADMIN_EMAIL, $subject, $body);
    }
}

?>
