<?

/******************************************************************************\
 * Copyright (C) 2002 B Squared (b^2) by Josh Sherman <josh@cleancode.org>    *
 *                                                                            *
 * This script displays the contents for the 'Post Thread' page.  Don't       *
 * forget the 12 space indent for all content pages.                          *
 *                                                                            *
 *                                 Last modified : September 24th, 2002 (JJS) *
\******************************************************************************/

/* Disallow direct access to this file */
$file_name = "post_thread.php";

/* Get the negative length of $file_name */
$file_name_length = -(strlen($file_name));

/* Check if the values match, if so, redirect */
if (substr($_SERVER['SCRIPT_NAME'], $file_name_length) == $file_name)
  header("Location: ../index.php");

/* Get the variables we need */
$user_ip     = GetVars("REMOTE_ADDR");
$action      = GetVars("action");
$email       = GetVars("email");
$include_sig = GetVars("include_sig");
$step        = GetVars("step");

/* Parse any user input */
CheckVars(&$step, 1);
CheckVars(&$user_ip, 15);

/* Determine which step to use */
if ($action == "Edit Thread")
  $step = 1;
else if ($action == "Post Thread")
  $step = 3;

/* Strip out all escape characters */
if ($step == 1)
  {
    $title   = stripslashes(htmlspecialchars($title));
    $message = str_replace("<BR>", "", $message);
    $message = stripslashes(htmlspecialchars($message));
  }

/* Along with replacing the </ br>'s */
if ($step == 2)
  {
    $title   = stripslashes(htmlspecialchars($title));
    $message = stripslashes(htmlspecialchars($message));
    $message = nl2br($message);
    $message = str_replace("<br />", "<BR>", $message);
  }

/* And also adding <BR>'s */
if ($step == 3)
  {
    $title   = htmlspecialchars($title); 
    $message = htmlspecialchars($message);
    $message = str_replace("&lt;BR&gt;", "<BR>", $message);
  }

/* Pull the forum list */
$SQL     = "SELECT * FROM " . TABLE_PREFIX . "forums;";
$results = ExeSQL($SQL);

/* Grab the data, and load it in an array */
while ($row = mysql_fetch_array($results))
  $forum_list[] = $row["forum_id"];

/* Check to see if the forum the user is requesting is real */
if (!(in_array($forum_id, $forum_list)))
  {
    /* If not, let them know */
    echo "            <CENTER class=\"error_message\">Malformed request detected!</CENTER><BR>\n";
    require ("./content/view_forums.php");
    return;
  }

/* Check that the user isn't trying to mess with the $step variable */
if ( $step == "" || ( $step != 1 && $step != 2 && $step != 3 ) )
  $step = 1;

/* Make sure the user isn't feeding information via the query string, thwart all attempts!! */
if ( ( ( $title == "" || $message == "" ) && ( $step == 3 ) ) || strlen($QUERY_STRING) >= 50 ||
     ( ( $step == 2 && $QUERY_STRING != "pid=post_thread&step=2" ) ||
       ( $step == 3 && $QUERY_STRING != "pid=post_thread" ) ) ||
       ( $step != 1 && ( strlen(trim($title)) == 0 || strlen(trim($message)) == 0 ) ) )
  {
    /* If so, bitch at them */
    echo "            <CENTER class=\"error_message\">Malformed request detected!</CENTER><BR>\n";
    $step = 1;
  }

/* Display the desired step */
switch ($step)
  {
    /* Display the post thread form */
    default:
    case 1:
      ShowPostThreadForm( $username, $password, $email, $title, $message, $include_sig, $user_id, $forum_id );
      break;

    /* Display the thread for the user to preview */
    case 2:
      /* Display the top part */
      echo "            <FORM action=\"?pid=post_thread\" method=\"POST\" name=\"post_thread\">\n"
         . "              <TABLE cellpadding=\"5\" cellspacing=\"0\" width=\"100%\" border class=\"table_border\">\n"
         . "                <TR class=\"table_header\">\n"
         . "                  <TD colspan=\"2\">New Thread Preview</TD>\n"
         . "                </TR>\n"
         . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
         . "                  <TD width=\"25%\"><B>Title:</B></TD>\n"
         . "                  <TD width=\"50%\">\n"
         . "                    $title\n"
         . "                    <INPUT type=\"hidden\" name=\"title\" value=\"$title\">\n"
         . "                  </TD>\n"
         . "                </TR>\n"
         . "                <TR bgcolor=\"" . TABLE_COLOR_2 . "\" class=\"regular_text\">\n"
         . "                  <TD width=\"25%\" valign=\"top\"><B>Message:</B></TD>\n"
         . "                  <TD width=\"50%\">\n";

      /* Pull the user's signature */
      $SQL     = "SELECT user_signature FROM " . TABLE_PREFIX . "users WHERE user_id='$user_id';";
      $results = ExeSQL($SQL);

      /* Grab the data, and load it in a variable */
      while ($row = mysql_fetch_array($results))
        $signature = $row["user_signature"];

      /* If we have a signature, then include it */
      if ($signature != "" && $include_sig == "yes")
        $display_message = $message . "<BR><BR>" . $signature;
      else
        $display_message = $message;

      /* Display the rest of the page */
      echo "                    $display_message\n"
         . "                    <INPUT type=\"hidden\" name=\"message\" value=\"$message\">\n"
         . "                  </TD>\n"
         . "                </TR>\n"
         . "              </TABLE>\n"
         . "              <INPUT type=\"hidden\" name=\"include_sig\" value=\"$include_sig\">\n"
         . "              <INPUT type=\"hidden\" name=\"forum_id\" value=\"$forum_id\">\n"
         . "              <INPUT type=\"hidden\" name=\"user_id\" value=\"$user_id\">\n"
         . "              <CENTER>\n"
         . "                <BR>\n"
         . "                <INPUT type=\"Submit\" value=\"Edit Thread\" name=\"action\">\n"
         . "                &nbsp;\n"
         . "                <INPUT type=\"Submit\" value=\"Post Thread\" name=\"action\">\n"
         . "              </CENTER>\n"
         . "            </FORM>\n";
      break;

    /* Check the user's input, add the thread to the database, and display the thread */
    case 3:
      /* Make sure the form was POSTed */
      if ( $REQUEST_METHOD == "POST" )
        {
          /* Pull the user's signature */
          $SQL     = "SELECT user_signature FROM " . TABLE_PREFIX . "users WHERE user_id='$user_id';";
          $results = ExeSQL($SQL);

          /* Grab the data, and load it in a variable */
          while ($row = mysql_fetch_array($results))
            $signature = $row["user_signature"];

          /* Include the signature if they want it to be */
          if ($signature != "" && $include_sig == "yes")
            $message = $message . "<BR><BR>" . $signature;

          /* Insert the thread into the database */
          $SQL     = "INSERT INTO " . TABLE_PREFIX . "threads (thread_title, thread_body, user_id, user_ip, forum_id) VALUES ('$title', '$message', '$user_id', '$user_ip', '$forum_id');";
          $results = ExeSQL($SQL);
      
          /* Give 'em props */
          echo "            <CENTER class=\"normal_message\">Thanks for posting!</CENTER><BR>\n";

          /* Show the thread list */          
          require ("./content/view_threads.php");
        }
      else
        {
          /* If not POSTed, then error out */
          echo "            <CENTER class=\"error_message\">Malformed request detected!</CENTER><BR>\n";
          ShowPostThreadForm( $username, $password, $email, $title, $message, $include_sig, $user_id, $forum_id );
        }
      break;
  }

/*
 * Show the form the user needs to fill out to post
 */

function
ShowPostThreadForm( $username, $password, $email, $title, $message, $include_sig, $user_id, $forum_id )
{
  /* Start with the JavaScript header, and then some */
  echo "            <SCRIPT language=\"JavaScript\">\n"
     . "              function\n"
     . "              CheckForm()\n"
     . "              {\n"
     . "                if (document.post_thread.title.value == '')\n"
     . "                  {\n"
     . "                    alert('The \'Title\' field is mandatory!');\n"
     . "                    document.post_thread.title.focus(1);\n"
     . "                    return false;\n"
     . "                  }\n"
     . "                if (document.post_thread.message.value == '')\n"
     . "                  {\n"
     . "                    alert('The \'Message\' field is mandatory!');\n"
     . "                    document.post_thread.message.focus(1);\n"
     . "                    return false;\n"
     . "                  }\n"
     . "                return true;\n"
     . "              }\n"
     . "            </SCRIPT>\n"
     . "            <FORM action=\"?pid=post_thread&step=2\" method=\"POST\" name=\"post_thread\">\n"
     . "              <TABLE cellpadding=\"5\" cellspacing=\"0\" width=\"100%\" border class=\"table_border\">\n"
     . "                <TR class=\"table_header\">\n"
     . "                  <TD colspan=\"2\">Post New Thread</TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" nowrap><B>Title:</B></TD>\n"
     . "                  <TD width=\"50%\" nowrap><INPUT type=\"text\" name=\"title\" value=\"$title\" maxlength=\"64\" size=\"50\"> <FONT class=\"small_text\">Max: 128 characters</FONT></TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_2 . "\" class=\"regular_text\">\n"
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

  /* Grab the data, and figure out if we want to include the signature or not */
  while ($row = mysql_fetch_array($results))
    {
      $signature = $row["user_signature"];
      if ($row["user_usesig"] == 1)
        $use_sig = " checked";
      else
        $use_sig = "";
    }

  /* If the user has a signature, then give them the option to use it */
  if ($signature != "")
    {
      echo "                      <TR class=\"regular_text\">\n"
         . "                        <TD align=\"right\">\n"
         . "                          <INPUT type=\"checkbox\" name=\"include_sig\" value=\"yes\"$use_sig> Include Signature?</A>\n"
         . "                        </TD>\n"
         . "                      </TR>\n";
    }

  /* Finish it off */
  echo "                    </TABLE>\n"
     . "                  </TD>\n"
     . "                </TR>\n"
     . "              </TABLE>\n"
     . "              <INPUT type=\"hidden\" name=\"user_id\" value=\"$user_id\">\n"
     . "              <INPUT type=\"hidden\" name=\"forum_id\" value=\"$forum_id\">\n"
     . "              <CENTER><BR><INPUT type=\"Submit\" value=\"Preview Thread\" onClick=\"return CheckForm();\"></CENTER>\n"
     . "            </FORM>\n";
}

?>
