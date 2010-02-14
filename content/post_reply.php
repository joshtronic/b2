<?

/******************************************************************************\
 * Copyright (C) 2002 B Squared (b^2) by Josh Sherman <josh@cleancode.org>    *
 *                                                                            *
 * This script displays the contents for the 'Post Reply' page.  Don't forget *
 * the 12 space indent for all content pages.                                 *
 *                                                                            *
 *                                 Last modified : September 24th, 2002 (JJS) *
\******************************************************************************/

/* Stop lame hacker kiddies */
$file_name = "post_reply.php";

/* Get the negative length of $file_name */
$file_name_length = -(strlen($file_name));

/* Check if the values match, if so, redirect */
if (substr($_SERVER['SCRIPT_NAME'], $file_name_length) == $file_name)
  header("Location: ../index.php");

/* Grab the user's IP address from the super global */
$user_ip     = $_SERVER['REMOTE_ADDR'];
$step        = GetVars("step");
$action      = GetVars("action");
$email       = GetVars("email");
$include_sig = GetVars("include_sig");

/* Parse any user input */
CheckVars(&$step, 1);
CheckVars(&$user_ip, 15);

/* Determine which step to use */
if ($action == "Edit Reply")
  $step = 1;
else if ($action == "Post Reply")
  $step = 3;

/* Strip out all escape characters */
if ($step == 1)
  {
    $message = str_replace("<BR>", "", $message);
    $message = stripslashes(htmlspecialchars($message));
  }

/* And again, along with adding line breaks */
if ($step == 2)
  {
    $message = stripslashes(htmlspecialchars($message));
    $message = nl2br($message);
    $message = str_replace("<br />", "<BR>", $message);
  }

/* One more time, but add <BR>'s */
if ($step == 3)
  {
    $message = htmlspecialchars($message);
    $message = str_replace("&lt;BR&gt;", "<BR>", $message);
  }
 
/* Pull the thread list */
$SQL     = "SELECT * FROM " . TABLE_PREFIX . "threads;";
$results = ExeSQL($SQL);

/* Grab the data, and load it in array's */
while ($row = mysql_fetch_array($results))
  {
    $thread_list[] = $row["thread_id"];
    $forum_list[]  = $row["forum_id"];
  }

/* Check to see if the thread the user is requesting is real */
if (!(in_array($thread_id, $thread_list)))
  {
    /* If not, let them know, and redirect them */
    echo "            <CENTER class=\"error_message\">Malformed request detected!</CENTER><BR>\n";
    require ("./content/view_forums.php");
    return;
  }

/* Assign values to use later - yes, I did forget what these do */
$thread_forum  = array_search($thread_id, $thread_list);
$correct_forum = $forum_list[$thread_forum]; 

/* Check to see if the forum the user is requesting is the right one */
if ($correct_forum != $forum_id)
  {
    /* If not, then tell them off */
    echo "            <CENTER class=\"error_message\">Malformed request detected!</CENTER><BR>\n";
    require ("./content/view_forums.php");
    return;
  }

/* Check that the user isn't trying to mess with the $step variable */
if ( $step == "" || ( $step != 1 && $step != 2 && $step != 3 ) )
  $step = 1;

/* Make sure the user isn't feeding information via the query string, thwart all attempts!! */
if ( ( ( $message == "" ) && ( $step == 3 ) ) || strlen($QUERY_STRING) >= 50 || 
     ( ( $step == 2 && $QUERY_STRING != "pid=post_reply&step=2" ) ||
       ( $step == 3 && $QUERY_STRING != "pid=post_reply" ) ) ||
       ( $step != 1 && strlen(trim($message)) == 0 ) ) 
  {
    /* And if they are, tell them off! */
    echo "            <CENTER class=\"error_message\">Malformed request detected!</CENTER><BR>\n";
    $step = 1;
  }

/* Which step do we want to run? */
switch ($step)
  {
    /* Display the post reply form */
    default:
    case 1:
      ShowPostReplyForm( $username, $password, $email, $message, $include_sig, $user_id, $thread_id, $forum_id );
      break;

    /* Display the reply for the user to preview */
    case 2:
      /* Show the top of the form */
      echo "            <FORM action=\"?pid=post_reply\" method=\"POST\" name=\"post_reply\">\n"
         . "              <TABLE cellpadding=\"5\" cellspacing=\"0\" width=\"100%\" border class=\"table_border\">\n"
         . "                <TR class=\"table_header\">\n"
         . "                  <TD colspan=\"2\">New Reply Preview</TD>\n"
         . "                </TR>\n"
         . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
         . "                  <TD width=\"25%\" valign=\"top\"><B>Message:</B></TD>\n"
         . "                  <TD width=\"50%\">\n";

      /* Pull the user's signature */
      $SQL     = "SELECT user_signature FROM " . TABLE_PREFIX . "users WHERE user_id='$user_id';";
      $results = ExeSQL($SQL);

      /* Grab the data, and load it in a variable */
      while ($row = mysql_fetch_array($results))
        $signature = $row["user_signature"];

      /* If the user has a signature and wants to use it, then show it */
      if ($signature != "" && $include_sig == "yes")
        $display_message = $message . "<BR><BR>" . $signature;
      else
        $display_message = $message;
  
      /* Show the bottom of the form */
      echo "                    $display_message\n"
         . "                    <INPUT type=\"hidden\" name=\"message\" value=\"$message\">\n"
         . "                  </TD>\n"
         . "                </TR>\n"
         . "              </TABLE>\n"
         . "              <INPUT type=\"hidden\" name=\"include_sig\" value=\"$include_sig\">\n"
         . "              <INPUT type=\"hidden\" name=\"forum_id\" value=\"$forum_id\">\n"
         . "              <INPUT type=\"hidden\" name=\"thread_id\" value=\"$thread_id\">\n"
         . "              <INPUT type=\"hidden\" name=\"user_id\" value=\"$user_id\">\n"
         . "              <CENTER>\n"
         . "                <BR>\n"
         . "                <INPUT type=\"Submit\" value=\"Edit Reply\" name=\"action\">\n"
         . "                &nbsp;\n"
         . "                <INPUT type=\"Submit\" value=\"Post Reply\" name=\"action\">\n"
         . "              </CENTER>\n"
         . "            </FORM>\n";
      break;

    /* Check the user's input, add the reply to the database, and display the reply */
    case 3:
      /* Make sure they POSTed the form */
      if ( $REQUEST_METHOD == "POST" )
        {
          /* Pull the user's signature */
          $SQL     = "SELECT user_signature FROM " . TABLE_PREFIX . "users WHERE user_id='$user_id';";
          $results = ExeSQL($SQL);

          /* Grab the data and load it in a variable */
          while ($row = mysql_fetch_array($results))
            $signature = $row["user_signature"];

          /* If they have a sig, and want to include it, then include it! */
          if ($signature != "" && $include_sig == "yes")
            $message = $message . "<BR><BR>" . $signature;

          /* Insert the reply into the database */
          $SQL     = "INSERT INTO " . TABLE_PREFIX . "replies (reply_body, user_id, user_ip, thread_id, forum_id) VALUES ('$message', '$user_id', '$user_ip', '$thread_id', '$forum_id');";
          $results = ExeSQL($SQL);

          /* Now be a good forum, and thank the kind user */
          echo "            <CENTER class=\"normal_message\">Thanks for posting!</CENTER><BR>\n";

          /* Show the reply list */
          require ("./content/view_replies.php");
        }
      else
        {
          /* If they didn't POST it, then error out */
          echo "            <CENTER class=\"error_message\"><B>Malformed request detected!</CENTER><BR>\n";
          ShowPostReplyForm( $username, $password, $email, $message, $include_sig, $user_id, $thread_id, $forum_id, $db_name, $connection );
        }
      break;
  }

/*
 * Show the form for the user to fill out 
 */

function
ShowPostReplyForm( $username, $password, $email, $message, $include_sig, $user_id, $thread_id, $forum_id )
{
  /* Show the beginning of the form */
  echo "            <SCRIPT language=\"JavaScript\">\n"
     . "              function\n"
     . "              CheckForm()\n"
     . "              {\n"
     . "                if (document.post_reply.message.value == '')\n"
     . "                  {\n"
     . "                    alert('The \'Message\' field is mandatory!');\n"
     . "                    document.post_reply.message.focus(1);\n"
     . "                    return false;\n"
     . "                  }\n"
     . "              }\n"
     . "            </SCRIPT>\n"
     . "            <FORM action=\"?pid=post_reply&step=2\" method=\"POST\" name=\"post_reply\">\n"
     . "              <TABLE cellpadding=\"5\" cellspacing=\"0\" width=\"100%\" border class=\"table_border\">\n"
     . "                <TR class=\"table_header\">\n"
     . "                  <TD colspan=\"2\">Post New Reply</TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" valign=\"top\" nowrap><B>Message:</B></TD>\n"
     . "                  <TD width=\"50%\" nowrap>\n"
     . "                    <TABLE border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n"
     . "                      <TR>\n"
     . "                        <TD>\n"
     . "                          <TEXTAREA name=\"message\" cols=\"65\" rows=\"10\">$message</TEXTAREA><BR>\n"
     . "                        </TD>\n"
     . "                      </TR>\n";

  /* Pull the user's signature */
  $SQL     = "SELECT user_signature, user_usesig FROM " . TABLE_PREFIX . "users WHERE user_name='$username';";
  $results = ExeSQL($SQL);

  /* Grab the data, and load it into variables */
  while ($row = mysql_fetch_array($results))
    {
      /* Grab the actual signature */
      $signature = $row["user_signature"];

      /* Determine if they use it by default */
      if ($row["user_usesig"] == 1)
        $use_sig = " checked";
      else
        $use_sig = "";
    }

  /* If there is a signature, then display the option to use it */
  if ($signature != "")
    {
      echo "                      <TR class=\"regular_text\">\n"
         . "                        <TD align=\"right\">\n"
         . "                          <INPUT type=\"checkbox\" name=\"include_sig\" value=\"yes\"$use_sig> Include Signature?</A>\n"
         . "                        </TD>\n"
         . "                      </TR>\n";
    }

  /* Now spit out the rest of the HTML so we can get the heck outta this file! */
  echo "                    </TABLE>\n"
     . "                  </TD>\n"
     . "                </TR>\n"
     . "              </TABLE>\n"
     . "              <INPUT type=\"hidden\" name=\"user_id\" value=\"$user_id\">\n"
     . "              <INPUT type=\"hidden\" name=\"thread_id\" value=\"$thread_id\">\n"
     . "              <INPUT type=\"hidden\" name=\"forum_id\" value=\"$forum_id\">\n"
     . "              <CENTER><BR><INPUT type=\"Submit\" value=\"Preview Reply\" onClick=\"return CheckForm();\"></CENTER>\n"
     . "            </FORM>\n";
}

?>
