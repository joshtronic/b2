<?

/******************************************************************************\
 * Copyright (C) 2002 B Squared (b^2) by Josh Sherman <josh@cleancode.org>    *
 *                                                                            *
 * This script displays the contents for the 'User Administration' page.      *
 * Don't forget the 12 space indent for all content pages.                    *
 *                                                                            *
 *                                 Last modified : September 13th, 2002 (JJS) *
\******************************************************************************/

/* Redirect the would-be haX0rz */
$file_name = "user_admin.php";

/* Get the negative length of $file_name */
$file_name_length = -(strlen($file_name));

/* Check if the values match, if so, redirect */
if (substr($_SERVER['SCRIPT_NAME'], $file_name_length) == $file_name)
  header("Location: ../index.php");

/* Grab the veriables held by superglobals */
$user_id          = GetVars("user_id");
$old_name         = GetVars("old_name");
$email            = GetVars("email");
$old_email        = GetVars("old_email");
$acct_name        = GetVars("acct_name");
$acct_pass        = GetVars("acct_pass");
$confirm_password = GetVars("confirm_password");
$location         = GetVars("location");
$occupation       = GetVars("occupation");
$homepage         = GetVars("homepage");
$picture          = GetVars("picture");
$interests        = GetVars("interests");
$aim              = GetVars("aim");
$icq              = GetVars("icq");
$yahoo            = GetVars("yahoo");
$signature        = GetVars("signature");
$include_sig      = GetVars("include_sig");
$query            = GetVars("query");
$moderated        = GetVars("moderated");
$moderated_forums = GetVars("moderated_forums");
$admin_acct       = GetVars("admin_acct");
$action           = GetVars("action");
$step             = GetVars("step");
$forum_index      = GetVars("forum_index");

/* Start off the array */
//$mod_array[] = "";

/* Loop through the forums and grab the variables */
for ($i = 0; $i < $forum_index; $i++)
  {
     $this = "mod_" . $i;
     $mod_array[] = GetVars($this);
  }


/* Parse any user input */
CheckVars(&$step, 1);
CheckVars(&$user_id, 10);
CheckVars(&$acct_name, 64);
CheckVars(&$old_name, 64);
CheckVars(&$acct_pass, 64);
CheckVars(&$acct_confirm_pass, 64);
CheckVars(&$email, 128);
CheckVars(&$old_email, 128);
CheckVars(&$location, 128);
CheckVars(&$occupation, 64);
CheckVars(&$homepage, 128);
CheckVars(&$picture, 128);
CheckVars(&$interests, 255);
CheckVars(&$aim, 16);
CheckVars(&$icq, 16);
CheckVars(&$yahoo, 32);
CheckVars(&$signature, 255);
CheckVars(&$include_sig, 1);
CheckVars(&$admin_acct, 1);

/* Check that the user isn't trying to mess with the $step variable */
if ( $step == "" || ( $step != 1 && $step != 2 && $step != 3 && $step != 4 && $step != 5 && $step != 6 && $step != 7 ) )
  $step = 1;

/* Make sure the user isn't feeding information via the query string, thwart all attempts!! */
if ( ( ( $acct_name == "" || $email == "" || $user_id == "" ) && ( $step == 4 || $step == 5 ) ) ||
     ( ( $step == 1 && $QUERY_STRING != "pid=user_admin" ) ||
       ( $step == 2 && $QUERY_STRING != "pid=user_admin" ) ||
       ( $step == 3 && $QUERY_STRING != "pid=user_admin" ) ||
       ( $step == 4 && $QUERY_STRING != "pid=user_admin" ) ||
       ( $step == 5 && $QUERY_STRING != "pid=user_admin&step=5" ) ||
       ( $step == 6 && $QUERY_STRING != "pid=user_admin" ) ||
       ( $step == 7 && $QUERY_STRING != "pid=user_admin" ) ) ||
       ( ( $step != 1 && $step != 2 ) &&
         ( strlen(trim($acct_name)) == 0 || strlen(trim($email)) == 0 ) ) )
  {
    /* Bitch them out if they are f-ing around */
    echo "            <CENTER class=\"error_message\">Malformed request detected!</CENTER><BR>\n";
    $step = 1;
  }

/* Determine the active step */
if ($action == "Search")
  $step = 2;
else if ($action == "Edit")
  $step = 3;
else if ($action == "Edit Account")
  $step = 4;
else if ($action == "Preview Information")
  $step = 5;
else if ($action == "Submit Account")
  $step = 6;
else if ($action == "Delete")
  $step = 7;

/* Parse some of the variables to ensure accurate values */
if ( $step == 4 && $homepage == "" )
  $homepage = "http://";

if ( $step == 4 && $picture == "" )
  $picture = "http://";

/* Strip out all escape characters */
if ($step == 4)
  {
    $acct_name  = stripslashes(strip_tags($acct_name));
    $acct_pass  = stripslashes(strip_tags($acct_pass));
    $email      = stripslashes(strip_tags($email));
    $location   = stripslashes(strip_tags($location));
    $occupation = stripslashes(strip_tags($occupation));
    $homepage   = stripslashes(strip_tags($homepage));
    $picture    = stripslashes(strip_tags($picture));
    $interests  = stripslashes(strip_tags($interests));
    $aim        = stripslashes(strip_tags($aim));
    $icq        = stripslashes(strip_tags($icq));
    $yahoo      = stripslashes(strip_tags($yahoo));
    $signature  = stripslashes(strip_tags($signature));
  }

/* Do it again, and clean up the signature */
if ($step == 5)
  {
    $acct_name  = stripslashes(strip_tags($acct_name));
    $acct_pass  = stripslashes(strip_tags($acct_pass));
    $email      = stripslashes(strip_tags($email));
    $location   = stripslashes(strip_tags($location));
    $occupation = stripslashes(strip_tags($occupation));
    $homepage   = stripslashes(strip_tags($homepage));
    $picture    = stripslashes(strip_tags($picture));
    $interests  = stripslashes(strip_tags($interests));
    $aim        = stripslashes(strip_tags($aim));
    $icq        = stripslashes(strip_tags($icq));
    $yahoo      = stripslashes(strip_tags($yahoo));

    $signature  = stripslashes(htmlspecialchars($signature));
    $signature  = nl2br($signature);
    $signature  = str_replace("<br />", "<BR>", $signature);
  }

/* This time, just clean up the signature */
if ($step == 6)
  {
    $signature = htmlspecialchars($signature);
    $signature = str_replace("&lt;BR&gt;", "<BR>", $signature);
  }

/* Mirror, mirror, on the wall... which step do we want? */
switch ($step)
  {
    /* Show the search page */
    default:
    case 1:
      ShowUserSearch();
      break;

    /* Display the search results */
    case 2:
      ShowSearchResults( $query );
      echo "            <BR>\n";
      ShowUserSearch();
      break;

    /* Show the user's existing profile */
    case 3:
      /* Pull the number of accounts with the same userid */
      $SQL     = "SELECT * FROM " . TABLE_PREFIX . "users WHERE user_id='$user_id';";
      $results = ExeSQL($SQL);

      /* Grab the data, and load it into variables */
      while ($row = mysql_fetch_array($results))
        {
          $user_id     = $row["user_id"];
          $acct_name   = $row["user_name"];
          $acct_pass   = "";
          $email       = $row["user_email"];
          $location    = $row["user_location"];
          $occupation  = $row["user_occupation"];
          $homepage    = $row["user_homepage"];
          $picture     = $row["user_picture"];
          $interests   = $row["user_interests"];
          $aim         = $row["user_aim"];
          $icq         = $row["user_icq"];
          $yahoo       = $row["user_yahoo"];
          $signature   = $row["user_signature"];
          $include_sig = $row["user_usesig"];
        }

      /* Start showing the form */
      echo "            <FORM action=\"?pid=user_admin\" method=\"POST\" name=\"user_admin\">\n"
         . "              <TABLE cellpadding=\"5\" cellspacing=\"0\" width=\"100%\" border class=\"table_border\">\n"
         . "                <TR class=\"table_header\">\n"
         . "                  <TD colspan=\"2\">Account Information</TD>\n"
         . "                </TR>\n";

      /* Set the active color */
      $the_color = TABLE_COLOR_2;

      /* Display the mandatory info */
      PreviewSection ( $acct_name, "Username", &$the_color );
      PreviewSection ( $acct_pass, "Password", &$the_color );
      PreviewSection ( $email, "Email", &$the_color );

      /* Then display the optional info, assuming it has a value */
      if ( $location != "" )
        PreviewSection( $location, "Location", &$the_color );

      if ( $occupation != "" )
        PreviewSection( $occupation, "Occupation", &$the_color );

      if ( $homepage != "" && $homepage != "http://" )
        PreviewSection( $homepage, "Homepage", &$the_color );

      if ( $picture != "" && $picture != "http://" )
        PreviewSection ( $picture, "Picture", &$the_color );

      if ( $interests != "" )
        PreviewSection ( $interests, "Interests", &$the_color );

      if ( $aim != "" )
        PreviewSection ( $aim, "AOL Instant Messenger", &$the_color );

      if ( $icq != "" )
        PreviewSection ( $icq, "ICQ", &$the_color );

      if ( $yahoo != "" )
        PreviewSection ( $yahoo, "Yahoo Pager", &$the_color );

      if ( $signature != "" )
        {
          /* Swap out the colors */
          if ($the_color == TABLE_COLOR_1)
            $the_color = TABLE_COLOR_2;
          else
            $the_color = TABLE_COLOR_1;

          /* Start showing this part of the preview page */
          echo "                <TR bgcolor=\"$the_color\" class=\"regular_text\">\n"
             . "                  <TD width=\"25%\" valign=\"top\"><B>Signature:</B></TD>\n"
             . "                  <TD width=\"50%\">\n"
             . "                    $signature<BR><BR>\n"
             . "                    <I>\n";

          /* Display if the signature will / will not be included */
          if ($include_sig == 1)
            echo "                    Signatures will be included on new posts.\n";
          else
            echo "                    Signatures will not be included on new posts.\n";

          /* Finish it off */
          echo "                    </I>\n"
             . "                    <INPUT type=\"hidden\" name=\"signature\" value=\"$signature\">\n"
             . "                    <INPUT type=\"hidden\" name=\"include_sig\" value=\"$include_sig\">\n"
             . "                  </TD>\n"
             . "                </TR>\n";
        }

      /* Swap out the colors */
      if ($the_color == TABLE_COLOR_1)
        $the_color = TABLE_COLOR_2;
      else
        $the_color = TABLE_COLOR_1;

      /* Start displaying the moderator section */
      echo "                <TR bgcolor=\"$the_color\" class=\"regular_text\">\n"
         . "                  <TD width=\"25%\" valign=\"top\"><B>Moderator:</B></TD>\n"
         . "                  <TD width=\"50%\">\n"
         . "                    ";

      /* Pull the forum names that the user is a moderator for */
      $SQL     = "SELECT " . TABLE_PREFIX . "forums.* FROM " . TABLE_PREFIX . "forums LEFT JOIN " . TABLE_PREFIX . "moderators ON " . TABLE_PREFIX . "forums.forum_id=" . TABLE_PREFIX . "moderators.forum_id WHERE user_id='$user_id' ORDER BY forum_name;";
      $results = ExeSQL($SQL);

      /* Grab the data, load the values in an array */
      while ($row = mysql_fetch_array($results))
        $moderated_forums[] = $row["forum_name"];

      /* Set this variable to NULL */
      $moderated = "";

      /* If the array is empty, then display "none" */
      if (sizeof($moderated_forums) == 0)
         $moderated = "<I>none</I>";
      else
        {
          /* Look through the array */
          for ( $i = 0; $i < sizeof($moderated_forums); $i++ )
            {
              /* Add the forum names to the variable */
              $moderated = $moderated . $moderated_forums[$i];

              /* Add a comma if it's not the last value */
              if ( $i != (sizeof($moderated_forums) - 1 ) )
                $moderated = $moderated . ", ";
            }
        }

      /* Display the forums the user is a moderator for */
      echo "                    $moderated\n"
         . "                  </TD>\n"
         . "                </TR>\n";

      /* Swap the colors */
      if ($the_color == TABLE_COLOR_1)
        $the_color = TABLE_COLOR_2;
      else
        $the_color = TABLE_COLOR_1;

      /* Show the Admin section */
      echo "                <TR bgcolor=\"$the_color\" class=\"regular_text\">\n"
         . "                  <TD width=\"25%\" valign=\"top\"><B>Administrator:</B></TD>\n"
         . "                  <TD width=\"50%\">\n";

      /* Check to see if the user is an admin or not */
      $SQL     = "SELECT COUNT(*) AS is_admin FROM " . TABLE_PREFIX . "administrators WHERE user_id='$user_id';";
      $results = ExeSQL($SQL);

      /* Grab the data, and load it in a variable */
      while ($row = mysql_fetch_array($results))
        $admin_acct = $row["is_admin"];

      /* If the user is an admin say so, if not, ditto */
      if ($admin_acct != 1)
        {
          $admin      = "User is not an administrator.";
          $admin_acct = "";
        }
      else
        {
          $admin      = "User is an administrator.";
          $admin_acct = "1";
        }

      /* Display if the user is an admin, and finish off the form */
      echo "                    <I>$admin</I>\n"
         . "                  </TD>\n"
         . "                </TR>\n"
         . "              </TABLE>\n"
         . "              <INPUT type=\"hidden\" name=\"admin_acct\" value=\"$admin_acct\">\n"
         . "              <INPUT type=\"hidden\" name=\"moderated_forums\" value=\"$moderated\">\n"
         . "              <INPUT type=\"hidden\" name=\"user_id\" value=\"$user_id\">\n"
         . "              <INPUT type=\"hidden\" name=\"old_name\" value=\"$acct_name\">\n"
         . "              <INPUT type=\"hidden\" name=\"old_email\" value=\"$email\">\n"
         . "              <CENTER><BR><INPUT type=\"submit\" value=\"Edit Account\" name=\"action\"></CENTER>\n"
         . "            </FORM>\n";
      break;

    /* Show the profile edit screen */
    case 4:
      ShowProfileForm( $user_id, $acct_name, $acct_pass, $confirm_password, $email, $location, $occupation, $homepage, $picture, $interests, $aim, $icq, $yahoo, $signature, $include_sig, $moderated_forums, $admin_acct );
      break;

    /* Preview the updated information for the profile */
    case 5:
      /* Start the form */
      echo "            <FORM action=\"?pid=user_admin\" method=\"POST\" name=\"user_admin\">\n"
         . "              <TABLE cellpadding=\"5\" cellspacing=\"0\" width=\"100%\" border class=\"table_border\">\n"
         . "                <TR class=\"table_header\">\n"
         . "                  <TD colspan=\"2\">Account Preview</TD>\n"
         . "                </TR>\n";

      /* Set the active color */
      $the_color = TABLE_COLOR_2;

      /* Preview the mandatory sections */
      PreviewSection ( $acct_name, "Username", &$the_color );
      PreviewSection ( $acct_pass, "Password", &$the_color );
      PreviewSection ( $email, "Email", &$the_color );

      /* Preview the optional fields if they aren't blank */
      if ( $location != "" )
        PreviewSection( $location, "Location", &$the_color );

      if ( $occupation != "" )
        PreviewSection( $occupation, "Occupation", &$the_color );

      if ( $homepage != "" && $homepage != "http://" )
        PreviewSection( $homepage, "Homepage", &$the_color );

      if ( $picture != "" && $picture != "http://" )
        PreviewSection ( $picture, "Picture", &$the_color );

      if ( $interests != "" )
        PreviewSection ( $interests, "Interests", &$the_color );

      if ( $aim != "" )
        PreviewSection ( $aim, "AOL Instant Messenger", &$the_color );

      if ( $icq != "" )
        PreviewSection ( $icq, "ICQ", &$the_color );

      if ( $yahoo != "" )
        PreviewSection ( $yahoo, "Yahoo Pager", &$the_color );

      /* Let's play the signature game, kids! */
      if ( $signature != "" )
        {
          /* Swap out the colors */
          if ($the_color == TABLE_COLOR_1)
            $the_color = TABLE_COLOR_2;
          else
            $the_color = TABLE_COLOR_1;

          /* Display the section header and signature */
          echo "                <TR bgcolor=\"$the_color\" class=\"regular_text\">\n"
             . "                  <TD width=\"25%\" valign=\"top\"><B>Signature:</B></TD>\n"
             . "                  <TD width=\"50%\">\n"
             . "                    $signature<BR><BR>\n"
             . "                    <I>\n";

          /* State if the signature is added by default or not */
          if ($include_sig == 1)
            echo "                    Signatures will be included on new posts.\n";
          else
            echo "                    Signatures will not be included on new posts.\n";

          /* Finish off the section */
          echo "                    </I>\n"
             . "                    <INPUT type=\"hidden\" name=\"signature\" value=\"$signature\">\n"
             . "                    <INPUT type=\"hidden\" name=\"include_sig\" value=\"$include_sig\">\n"
             . "                  </TD>\n"
             . "                </TR>\n";
        }

      /* Swap out the colors */
      if ($the_color == TABLE_COLOR_1)
        $the_color = TABLE_COLOR_2;
      else
        $the_color = TABLE_COLOR_1;

      /* Start the moderator section */
      echo "                <TR bgcolor=\"$the_color\" class=\"regular_text\">\n"
         . "                  <TD width=\"25%\">\n"
         . "                    <B>Moderator:</B>\n"
         . "                  </TD>\n"
         . "                  <TD width=\"50%\">\n";

      /* Set variable to NULL */
      $moderated = "";

      /* Now it's time to get our look on */
      for ( $i = 0; $i < sizeof($mod_array); $i++ )
        {
          /* If the array value isn't NULL */
	  if ($mod_array[$i] != "")
	    {
	      /* Pull the form names */
              $SQL     = "SELECT * FROM " . TABLE_PREFIX . "forums WHERE forum_id='" . $mod_array[$i] . "' ORDER BY forum_name;";
              $results = ExeSQL($SQL);

              /* Grab the data, and throw it in an array */
              while ($row = mysql_fetch_array($results))
                $forum_name = $row["forum_name"];

	      /* Add the values from the array */
	        $moderated = $moderated . $forum_name;

              /* And comma separate them if they aren't the last value */
              if ( $i != (sizeof($mod_array) - 1 ) )
                $moderated = $moderated . ", ";
            }
        }

      if ($moderated == "")
        $moderated = "<I>none</I>";

      /* Finish off the section */
      echo "                    $moderated\n"
         . "                  </TD>\n"
         . "                </TR>\n";

      /* Swap the colors */
      if ($the_color == TABLE_COLOR_1)
        $the_color = TABLE_COLOR_2;
      else
        $the_color = TABLE_COLOR_1;

      /* Start off the Admin section */
      echo "                <TR bgcolor=\"$the_color\" class=\"regular_text\">\n"
         . "                  <TD width=\"25%\">\n"
         . "                    <B>Administrator:</B>\n"
         . "                  </TD>\n"
         . "                  <TD width=\"50%\">\n";

      /* Let us know if the user is an admin or not */
      if ($admin_acct != 1)
        {
          $admin      = "User is not an administrator.";
          $admin_acct = "";
        }
      else
        {
          $admin      = "User is an administrator.";
          $admin_acct = "1";
        }

      /* Finish off this form */
      echo "                    <I>$admin</I>\n"
         . "                  </TD>\n"
         . "                </TR>\n"
         . "              </TABLE>\n"
         . "              <INPUT type=\"hidden\" name=\"admin_acct\" value=\"$admin_acct\">\n"
         . "              <INPUT type=\"hidden\" name=\"moderated_forums\" value=\"$moderated\">\n"
         . "              <INPUT type=\"hidden\" name=\"user_id\" value=\"$user_id\">\n"
         . "              <INPUT type=\"hidden\" name=\"old_name\" value=\"$old_name\">\n"
         . "              <INPUT type=\"hidden\" name=\"old_email\" value=\"$old_email\">\n"
         . "              <CENTER><BR><INPUT type=\"Submit\" value=\"Edit Account\" name=\"action\"> <INPUT type=\"Submit\" value=\"Submit Account\" name=\"action\"></CENTER>\n"
         . "            </FORM>\n";
      break;

    /* Update an existing account */
    case 6:
      /* Make sure the page was POSTed */
      if ( $REQUEST_METHOD == "POST" )
        {
          /* No errors */
          $no_err = 0;

          /* If the old and new accounts don't have the same name then ... */
          if ($acct_name != $old_name)
            {
              /* Pull the number of accounts with the same name */
              $SQL     = "SELECT COUNT(*) AS name_exists FROM " . TABLE_PREFIX . "users WHERE user_name='$acct_name';";
              $results = ExeSQL($SQL);

              /* Grab the data, and parse the results */
              while ($row = mysql_fetch_array($results))
                {
                  /* Username exists? Error out */
                  if ($row["name_exists"] != 0)
                    {
                      echo "            <CENTER class=\"error_message\">That username is already taken by another user!</CENTER><BR>\n";
                      $no_err++;
                    }
                }
            }

          /* Let's do the same stuff, but for the email addy */
          if ($email != $old_email)
            {
              /* Pull the number of forums with the same email */
              $SQL     = "SELECT COUNT(*) AS email_exists FROM " . TABLE_PREFIX . "users WHERE user_email='$email';";
              $results = ExeSQL($SQL);

              /* Grab the data, parse the results */
              while ($row = mysql_fetch_array($results))
                {
                  /* Email exists? Error out */
                  if ($row["email_exists"] != 0)
                    {
                      echo "            <CENTER class=\"error_message\">An account has already been registered using that email address!</CENTER><BR>\n";
                      $no_err++;
                    }
                }
            }

          /* If there are no errors ... */
          if ($no_err == 0)
            {
              /* Clear out the URL variables if they still contain 'http://' */
              if ($homepage == "http://") { $homepage = ""; }
              if ($picture == "http://") { $picture = ""; }

              /* Crypt the password to a random salt */
              if ($acct_pass != "")
                $acct_pass = crypt($acct_pass);

              /* Set the include_sig variable */
              if ($include_sig != 1)
                $include_sig = 0;

              /* Update the user in the database */
              if ($acct_pass != "")
                $SQL = "UPDATE " . TABLE_PREFIX . "users SET user_name='$acct_name', user_email='$email', user_pass='$acct_pass', user_location='$location', user_occupation='$occupation', user_homepage='$homepage', user_picture='$picture', user_interests='$interests', user_aim='$aim', user_icq='$icq', user_yahoo='$yahoo', user_signature='$signature', user_usesig='$include_sig' WHERE user_id='$user_id';";
              else
                $SQL = "UPDATE " . TABLE_PREFIX . "users SET user_name='$acct_name', user_email='$email', user_location='$location', user_occupation='$occupation', user_homepage='$homepage', user_picture='$picture', user_interests='$interests', user_aim='$aim', user_icq='$icq', user_yahoo='$yahoo', user_signature='$signature', user_usesig='$include_sig' WHERE user_id='$user_id';";

              $results = ExeSQL($SQL);

              /* Update the moderater table, kill all associated entries, first.. */
              $SQL     = "DELETE FROM " . TABLE_PREFIX . "moderators WHERE user_id='$user_id';";
              $results = ExeSQL($SQL);

              /* Then readd them */
              if ($moderated_forums != "<I>none</I>")
                {
                  /* Blow the variable up into an array */
                  $forums = explode(", ", $moderated_forums);

                  /* Loop the array */
                  for ( $i = 0; $i < sizeof($forums); $i++ )
                    {
                      /* Select the forum id */
                      $SQL     = "SELECT * FROM " . TABLE_PREFIX . "forums WHERE forum_name='" . $forums[$i] . "';";
                      $results = ExeSQL($SQL);

                      /* Grab it and throw it in a variable */
                      while ($row = mysql_fetch_array($results))
                        $forum_id = $row["forum_id"];

                      /* Insert the data into the moderators table */
                      $SQL     = "INSERT INTO " . TABLE_PREFIX . "moderators (forum_id, user_id) VALUES ('$forum_id', '$user_id');";
                      $results = ExeSQL($SQL);
                    }
                }

              /* Update the administrator table, kill all associated entries, first.. */
              $SQL     = "DELETE FROM " . TABLE_PREFIX . "administrators WHERE user_id='$user_id';";
              $results = ExeSQL($SQL);

              /* Then readd them */
              if ($admin_acct == 1)
                {
                  $SQL     = "INSERT INTO " . TABLE_PREFIX . "administrators (user_id) VALUES ('$user_id');";
                  $results = ExeSQL($SQL);
                }

              /* Let the user know the update is complete */
              echo "            <CENTER class=\"normal_message\">The account has been updated!</CENTER><BR>\n";
              ShowUserSearch();
              return;
            }
          else
            ShowProfileForm( $user_id, $acct_name, $acct_pass, $confirm_password, $email, $location, $occupation, $homepage, $picture, $interests, $aim, $icq, $yahoo, $signature, $include_sig, $moderated_forums, $admin_acct );
        }
      else
        {
          /* If it wasn't POSTed, then error out */
          echo "            <CENTER class=\"error_message\">Malformed request detected!<CENTER><BR>\n";
          ShowProfileForm( $user_id, $acct_name, $acct_pass, $confirm_password, $email, $location, $occupation, $homepage, $picture, $interests, $aim, $icq, $yahoo, $signature, $include_sig, $moderated_forums, $admin_acct );
        }
      break;

      /* Delete the user, and all his/her's associated threads and replies */
      case 7:
        /* The user from the database */
        $SQL     = "DELETE FROM " . TABLE_PREFIX . "users WHERE user_id='$user_id';";
        $results = ExeSQL($SQL);

        /* Delete the threads associated with the user */
        $SQL     = "DELETE FROM " . TABLE_PREFIX . "threads WHERE user_id='$user_id';";
        $results = ExeSQL($SQL);

        /* Delete the replies associated with the user */
        $SQL     = "DELETE FROM " . TABLE_PREFIX . "replies WHERE user_id='$user_id';";
        $results = ExeSQL($SQL);

        /* Delete the user from the moderators list */
        $SQL     = "DELETE FROM " . TABLE_PREFIX . "moderators WHERE user_id='$user_id';";
        $results = ExeSQL($SQL);

        /* Delete the use from the administrator list */
        $SQL     = "DELETE FROM " . TABLE_PREFIX . "administrators WHERE user_id='$user_id';";
        $results = ExeSQL($SQL);

        /* Tell the user all is good */
        echo "            <CENTER class=\"normal_message\">The user has successfully been removed!</CENTER><BR>\n";
        ShowUserSearch();
        return;
        break;
  }

/*
 * Show the user search box
 */

function
ShowUserSearch()
{
  /* Well show it already!! */
  echo "            <FORM action=\"?pid=user_admin\" method=\"POST\" name=\"user_search\">\n"
     . "              <TABLE cellpadding=\"5\" cellspacing=\"0\" width=\"100%\" border class=\"table_border\">\n"
     . "                <TR class=\"table_header\">\n"
     . "                  <TD colspan=\"2\">User Search</TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\">\n"
     . "                  <TD>\n"
     . "                    <TABLE align=\"center\">\n"
     . "                      <TR>\n"
     . "                        <TD align=\"right\">\n"
     . "                          <INPUT type=\"text\" name=\"query\">\n"
     . "                        </TD>\n"
     . "                        <TD>\n"
     . "                          <INPUT type=\"submit\" name=\"action\" value=\"Search\">\n"
     . "                        </TD>\n"
     . "                      </TR>\n"
     . "                      <TR class=\"small_text\">\n"
     . "                        <TD colspan=\"2\">\n"
     . "                          User * as a wildcard for partial matches\n"
     . "                        </TD>\n"
     . "                      </TR>\n"
     . "                    </TABLE>\n"
     . "                  </TD>\n"
     . "                </TR>\n"
     . "              </TABLE>\n"
     . "            </FORM>\n";
}

/*
 * Parse the query and display the results
 */

function
ShowSearchResults( $query )
{
  /* If the query is NULL, then set it to pull all the users */
  if (trim($query) == "")
    $query = "*";

  /* Start the table for the results */
  echo "            <TABLE cellpadding=\"5\" cellspacing=\"0\" width=\"100%\" border class=\"table_border\">\n"
     . "              <TR class=\"table_header\">\n"
     . "                <TD colspan=\"2\">Search Results for '$query'</TD>\n"
     . "              </TR>\n";

  /* If there's a * in the query, then change it to % */
  if (strstr ($query, "*") != "" )
    {
      $query     = str_replace("*", "%", $query);
      $sql_where = "user_name LIKE '$query'";
    }
  else
    $sql_where = "user_name='$query'";

  /* Set the active color */
  $the_color = TABLE_COLOR_2;

  /* And the number of results */
  $how_many = 0;

  /* Pull the data based on the query */
  $SQL     = "SELECT * FROM " . TABLE_PREFIX . "users WHERE $sql_where ORDER BY user_name;";
  $results = ExeSQL($SQL);

  /* Grab the data, display the results */
  while ($row = mysql_fetch_array($results))
    {
      /* Swap colors */
      if ($the_color == TABLE_COLOR_1)
        $the_color = TABLE_COLOR_2;
      else
        $the_color = TABLE_COLOR_1;

      /* Let the output begin! */
      echo "              <TR bgcolor=\"$the_color\">\n"
         . "                <TD>\n"
         . "                  <TABLE cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\">\n"
         . "                    <TR class=\"regular_text\">\n"
         . "                      <TD valign=\"top\">\n"
         . "                        " . $row["user_name"] . "\n"
         . "                      </TD>\n"
         . "                      <TD align=\"right\">\n"
         . "                        <FORM action=\"?pid=user_admin\" method=\"POST\">\n"
         . "                          <INPUT type=\"hidden\" name=\"user_id\" value=\"" . $row["user_id"] . "\">\n"
         . "                          <INPUT type=\"submit\" name=\"action\" value=\"Edit\">\n"
         . "                          <INPUT type=\"submit\" name=\"action\" value=\"Delete\" onClick=\"return Confirm('Are you sure you want to delete this user and all of his/her posts?');\">\n"
         . "                        </FORM>\n"
         . "                      </TD>\n"
         . "                    </TR>\n"
         . "                  </TABLE>\n"
         . "                </TD>\n"
         . "              </TR>\n";

      /* Increment the total number of results */
      $how_many++;
    }

  /* If no results were foind, let the user know... same with if 1 or more results were found */
  if ($how_many == 0)
    {
      echo "              <TR class=\"table_header\">\n"
         . "                <TD align=\"center\" class=\"table_header\">Your search did not return any matches!</TD>\n"
         . "              </TR>\n";
    }
  else if ($how_many == 1)
    {
      echo "              <TR class=\"table_header\">\n"
         . "                <TD align=\"center\" class=\"table_header\">Your search returned 1 match!</TD>\n"
         . "              </TR>\n";
    }
  else
    {
      echo "              <TR>\n"
         . "                <TD align=\"center\" class=\"table_header\">Your search returned $how_many matches!</TD>\n"
         . "              </TR>\n";
    }

  echo "            </TABLE>\n";
}

/*
 * Show the edit form
 */

function
ShowProfileForm( $user_id, $acct_name, $acct_pass, $confirm_password, $email, $location, $occupation, $homepage, $picture, $interests, $aim, $icq, $yahoo, $signature, $include_sig, $moderated_forums, $admin_acct )
{
  echo "            <SCRIPT language=\"JavaScript\">\n"
     . "              function\n"
     . "              CheckForm()\n"
     . "              {\n"
     . "                if (document.user_admin.acct_name.value == '')\n"
     . "                  {\n"
     . "                    alert('The \'Username\' field is mandatory!');\n"
     . "                    document.user_admin.acct_name.focus();\n"
     . "                    return false;\n"
     . "                  }\n"
     . "                if (document.user_admin.acct_pass.value != document.user_admin.confirm_acct_pass.value)\n"
     . "                  {\n"
     . "                    alert('The \'Password\' and \'Confirm Password\' fields must be the same!');\n"
     . "                    document.user_admin.acct_pass.focus();\n"
     . "                    document.user_admin.acct_pass.select();\n"
     . "                    return false;\n"
     . "                  }\n"
     . "                if ( document.user_admin.acct_pass.value.length < 6 && document.user_admin.acct_pass.value != '' )\n"
     . "                  {\n"
     . "                    alert('The \'Password\' field must be at least 6 characters!');\n"
     . "                    document.user_admin.acct_pass.focus();\n"
     . "                    document.user_admin.acct_pass.select();\n"
     . "                    return false;\n"
     . "                  }\n"
     . "                if (document.user_admin.email.value == '')\n"
     . "                  {\n"
     . "                    alert('The \'Email\' field is mandatory!');\n"
     . "                    document.user_admin.email.focus();\n"
     . "                    return false;\n"
     . "                  }\n"
     . "                if (!ValidateEmail(document.user_admin.email.value))\n"
     . "                  {\n"
     . "                    alert('You must supply a valid email address.');\n"
     . "                    document.user_admin.email.focus();\n"
     . "                    document.user_admin.email.select();\n"
     . "                    return false;\n"
     . "                  }\n"
     . "                if (document.user_admin.signature.value.length > 255)\n"
     . "                  {\n"
     . "                    alert('The \'Signature\' field cannot exceed 255 characters!');\n"
     . "                    document.user_admin.signature.focus();\n"
     . "                    document.user_admin.signature.select();\n"
     . "                    return false;\n"
     . "                  }\n"
     . "                return true;\n"
     . "              }\n"
     . "              function\n"
     . "              ValidateEmail(address)\n"
     . "              {\n"
     . "                if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(address))\n"
     . "                  {\n"
     . "                    return true;\n"
     . "                  }\n"
     . "                return false;\n"
     . "              }\n"
     . "            </SCRIPT>\n"
     . "            <FORM action=\"?pid=user_admin&step=5\" method=\"POST\" name=\"user_admin\">\n"
     . "              <TABLE cellpadding=\"5\" cellspacing=\"0\" width=\"100%\" border class=\"table_border\">\n"
     . "                <TR class=\"table_header\">\n"
     . "                  <TD colspan=\"2\">Required Information&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<I>Leave the password fields blank if you wish to keep the current password.</I></TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" nowrap><B>Username:</B></TD>\n"
     . "                  <TD width=\"50%\" nowrap><INPUT type=\"text\" name=\"acct_name\" value=\"$acct_name\" maxlength=\"64\" size=\"50\"> <FONT class=\"small_text\"> Max 64 characters</FONT></TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_2 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" nowrap><B>Password:</B></TD>\n"
     . "                  <TD width=\"50%\" nowrap><INPUT type=\"password\" name=\"acct_pass\" value=\"$acct_pass\" maxlength=\"64\" size=\"50\"> <FONT class=\"small_text\">Min 6 characters - Max: 64 characters</FONT></TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" nowrap><B>Confirm Password:</B></TD>\n"
     . "                  <TD width=\"50%\" nowrap><INPUT type=\"password\" name=\"confirm_acct_pass\" value=\"$acct_pass\" maxlength=\"64\" size=\"50\"> <FONT class=\"small_text\">Min: 6 characters - Max: 64 characters</FONT></TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_2 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" nowrap><B>Email:</B></TD>\n"
     . "                  <TD width=\"50%\" nowrap><INPUT type=\"text\" name=\"email\" value=\"$email\" maxlength=\"128\" size=\"50\"> <FONT class=\"small_text\">Max: 128 characters</FONT></TD>\n"
     . "                </TR>\n"
     . "                <TR class=\"table_header\">\n"
     . "                  <TD colspan=\"2\">Optional Information</TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" nowrap><B>Location:</B></TD>\n"
     . "                  <TD width=\"50%\" nowrap><INPUT type=\"text\" name=\"location\" value=\"$location\" maxlength=\"128\" size=\"50\"> <FONT class=\"small_text\">Max: 128 characters</FONT></TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_2 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" nowrap><B>Occupation:</B></TD>\n"
     . "                  <TD width=\"50%\" nowrap><INPUT type=\"text\" name=\"occupation\" value=\"$occupation\" maxlength=\"64\" size=\"50\"> <FONT class=\"small_text\">Max: 64 characters</FONT></TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" nowrap><B>Homepage:</B></TD>\n"
     . "                  <TD width=\"50%\" nowrap><INPUT type=\"text\" name=\"homepage\" value=\"$homepage\" maxlength=\"128\" size=\"50\"> <FONT class=\"small_text\">Max: 128 characters</FONT></TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_2 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" nowrap><B>Picture:</B></TD>\n"
     . "                  <TD width=\"50%\" nowrap><INPUT type=\"text\" name=\"picture\" value=\"$picture\" maxlength=\"128\" size=\"50\"> <FONT class=\"small_text\">Max: 128 characters</FONT></TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" nowrap><B>Interests:</B></TD>\n"
     . "                  <TD width=\"50%\" nowrap><INPUT type=\"text\" name=\"interests\" value=\"$interests\" maxlength=\"255\" size=\"50\"> <FONT class=\"small_text\">Max: 255 characters</FONT></TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_2 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" nowrap><B>AOL Instant Messenger:</B></TD>\n"
     . "                  <TD width=\"50%\" nowrap><INPUT type=\"text\" name=\"aim\" value=\"$aim\" maxlength=\"16\" size=\"50\"> <FONT class=\"small_text\">Max: 16 characters</FONT></TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" nowrap><B>ICQ:</B></TD>\n"
     . "                  <TD width=\"50%\" nowrap><INPUT type=\"text\" name=\"icq\" value=\"$icq\" maxlength=\"16\" size=\"50\"> <FONT class=\"small_text\">Max: 16 characters</FONT></TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_2 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" nowrap><B>Yahoo Pager:</B></TD>\n"
     . "                  <TD width=\"50%\" nowrap><INPUT type=\"text\" name=\"yahoo\" value=\"$yahoo\" maxlength=\"32\" size=\"50\"> <FONT class=\"small_text\">Max: 32 characters</FONT></TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" valign=\"top\" nowrap><B>Signature:</B></TD>\n"
     . "                  <TD width=\"50%\" valign=\"top\" nowrap>\n"
     . "                    <TABLE cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n"
     . "                      <TR>\n"
     . "                        <TD><TEXTAREA name=\"signature\" rows=\"5\" cols=\"40\" maxlength=\"255\">$signature</TEXTAREA></TD><TD valign=\"top\" nowrap>&nbsp;<FONT class=\"small_text\">Max: 255 characters</FONT></TD>\n"
     . "                      </TR>\n";

  /* Check the include signature box if they want to include the signature */
  if ($include_sig == 1)
    $checked = " checked";
  else
    $checked = "";

  /* Spit out some more of the form */
  echo "                      <TR class=\"regular_text\">\n"
     . "                        <TD colspan=\"2\"><INPUT type=\"checkbox\" name=\"include_sig\" value=\"1\"$checked> Include signature on new posts?</TD>\n"
     . "                      </TR>\n"
     . "                    </TABLE>\n"
     . "                  </TD>\n"
     . "                </TR>\n"
     . "                <TR class=\"table_header\">\n"
     . "                  <TD colspan=\"2\">Account Privileges</TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_2 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" valign=\"top\" nowrap><B>Moderator:</B></TD>\n"
     . "                  <TD width=\"50%\" valign=\"top\" nowrap>\n";

  /* NULL out these .. */
  $forums[] = " ";
  $forum_index = 0;

  /* Blow up the variable into an array */
  $moderated_split = explode(", ", $moderated_forums);

  /* Pull the moderators */
  $SQL     = "SELECT * FROM " . TABLE_PREFIX . "moderators;";
  $results = ExeSQL($SQL);

  /* Grab the data, add the values to an array */
  while ($row = mysql_fetch_array($results))
    $forums_ids[] = $row["forum_id"];

  /* Pull the forum names */
  $SQL     = "SELECT * FROM " . TABLE_PREFIX . "forums ORDER BY forum_name;";
  $results = ExeSQL($SQL);

  /* Grab the data, parse the results */
  while ($row = mysql_fetch_array($results))
    {
      /* If the user is a moderator, then check the box */
      if (in_array($row["forum_name"], $moderated_split))
        $checked = " checked";
      else
        $checked = "";

      /* Display the check box */
      //echo "                    <INPUT type=\"checkbox\" name=\"" . $forum_index . "\" value=\"" . $row["forum_id"]. "\"$checked> " . $row["forum_name"] . "<BR>\n";
      echo "                    <INPUT type=\"checkbox\" name=\"mod_" . $forum_index . "\" value=\"" . $row["forum_id"]. "\"$checked> " . $row["forum_name"] . "<BR>\n";

      /* Increment the file */
      $forum_index++;
    }

  /* Finish the moderator section, and move to the admin section */
  echo "                    <INPUT type=\"hidden\" name=\"forum_index\" value=\"$forum_index\">\n"
     . "                  </TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" valign=\"top\" nowrap><B>Administrator:</B></TD>\n"
     . "                  <TD width=\"50%\" valign=\"top\" nowrap>\n";

  /* If the user is an admin, then check the box */
  if ($admin_acct == 1)
    $checked = " checked";
  else
    $checked = "";

  /* Finish off this God forsaken form */
  echo "                    <INPUT type=\"checkbox\" name=\"admin_acct\" value=\"1\"$checked> <FONT face=\"Verdana\" size=\"2\">User is an administrator?\n"
     . "                  </TD>\n"
     . "                </TR>\n"
     . "              </TABLE>\n"
     . "              <INPUT type=\"hidden\" name=\"user_id\" value=\"$user_id\">\n"
     . "              <INPUT type=\"hidden\" name=\"old_name\" value=\"$acct_name\">\n"
     . "              <INPUT type=\"hidden\" name=\"old_email\" value=\"$email\">\n"
     . "              <CENTER><BR><INPUT type=\"Submit\" value=\"Preview Information\" onClick=\"return CheckForm();\"></CENTER>\n"
     . "            </FORM>\n";
}

/*
 * This section cuts down repetative code, and lets us preview sections
 */

function
PreviewSection ( $section_value, $section_title, $the_color )
{
  /* Swap out the colors */
  if ($the_color == TABLE_COLOR_1)
    $the_color = TABLE_COLOR_2;
  else
    $the_color = TABLE_COLOR_1;

  /* Show the top part */
  echo "                <TR bgcolor=\"$the_color\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" valign=\"top\"><B>$section_title:</B></TD>\n"
     . "                  <TD width=\"50%\">\n";

  /* There are different types of sections, depending which one we're on, is what we'll display */
  if ($section_title == "Password")
    echo "                    <I>Password is hidden for security purposes.</I>\n";
  else
    echo "                    $section_value\n";

  if ($section_title == "Picture")
    {
      /* Grab the image size */
      $profile_img = getimagesize($section_value);

      /* Set the caption */
      $image_caption = "Image size";

      /* If the image is larger than 320x240, then resize */
      if ($profile_img[0] > 320)
        {
          $profile_img[0] = 320;
          $image_caption = "Scaled image size";
        }
      if ($profile_img[1] > 240)
        {
          $profile_img[1] = 240;
          $image_caption = "Scaled image size";
        }

      /* Show the [re]sized image */
      if ($profile_img[0] > $profile_img[1])
        $scale_img = "height=\"$profile_img[1]\"";
      else
        $scale_img = "width=\"$profile_img[0]\"";

      /* Display the image */
      echo "                    <BR><BR>\n"
         . "                    $image_caption: {$profile_img[0]}x{$profile_img[1]}\n"
         . "                    <TABLE cellspacing=\"0\" cellpadding=\"0\" border class=\"table_border\"><TR><TD><A href=\"$section_value\" target=\"_blank\"><IMG src=\"$section_value\" $scale_img border=\"0\"></A></TD></TR></TABLE>\n";
    }

  /* Show wht needs to be shown */
  if ($section_title == "AOL Instant Messenger")
    $section_title = "aim";
  else if ($section_title == "Username")
    $section_title = "acct_name";
  else if ($section_title == "Password")
    $section_title = "acct_pass";
  else
    $section_title = strtolower($section_title);

  /* Finish up the section */
  echo "                    <INPUT type=\"hidden\" name=\"$section_title\" value=\"$section_value\">\n"
     . "                  </TD>\n"
     . "                </TR>\n";
}

?>
