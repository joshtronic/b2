<?

/******************************************************************************\
 * Copyright (C) 2002 B Squared (b^2) by Josh Sherman <josh@cleancode.org>    * 
 *                                                                            *
 * This script displays the contents for the 'Forum Administration' page.     *
 * Don't forget the 12 space indent for all content pages.                    *
 *                                                                            *
 *                                 Last modified : September 13th, 2002 (JJS) *
\******************************************************************************/

/* Don't let people call this file directly */
$file_name = "forum_admin.php";

/* Get the negative length of $file_name */
$file_name_length = -(strlen($file_name));

/* Check if the values match, if so, redirect */
if (substr($_SERVER['SCRIPT_NAME'], $file_name_length) == $file_name)
  header("Location: ../index.php");

/* Grab the veriables held by superglobals */
$forum_name  = GetVars("forum_name");
$forum_desc  = GetVars("forum_desc");
$forum_order = GetVars("forum_order");
$old_name    = GetVars("old_name");
$type        = GetVars("type");
$action      = GetVars("action");
$step        = GetVars("step");

/* Parse any user input */
CheckVars(&$step, 1);
CheckVars(&$forum_name, 64);
CheckVars(&$forum_desc, 255);
CheckVars(&$forum_order, 10);
CheckVars(&$old_name, 64);

/* Check that the user isn't trying to mess with the $step variable */
if ( $step == "" || ( $step != 1 && $step != 2 && $step != 3 && $step != 4 && $step != 5 && $step != 6 ) )
  $step = 1;

/* Make sure the user isn't feeding information via the query string, thwart all attempts!! */
if ( ( ( $forum_name == "" || $forum_desc == "" ) && ( $step == 3 || $step == 4 ) ) ||
     ( ( $step == 1 && $QUERY_STRING != "pid=forum_admin" ) ||
       ( $step == 2 && $QUERY_STRING != "pid=forum_admin&step=2" ) ||
       ( $step == 3 && $QUERY_STRING != "pid=forum_admin&step=3" ) ||
       ( $step == 4 && $QUERY_STRING != "pid=forum_admin" ) ||
       ( $step == 5 && $QUERY_STRING != "pid=forum_admin" ) ) || 
       ( ( $step != 1 && $step != 2 ) &&
         ( strlen(trim($forum_name)) == 0 || strlen(trim($forum_desc)) == 0 ) ) )

  {
    /* Give them an error if they are, and send them back to step 1 */
    echo "            <CENTER class=\"error_message\">Malformed request detected!</CENTER><BR>\n";
    $step = 1;
  }

/* There are different actions that can be performed, figure out which one */
if ($action == "Edit Forum")
  $step = 2;
else if ($action == "Edit")
  {
    $step = 2;
    $type = "existing";
  }
else if ($action == "Submit Forum")
  $step = 4;
else if ($action == "Delete")
  $step = 6;

/* If the user is submitting an existing forum for editting, then do to step 5 */
if ( $step == 4 && $type != "" )
  $step = 5;

/* Strip out all escape characters */
if ( $step == 2 || $step == 3 )
  {
    $forum_name = stripslashes(strip_tags($forum_name));
    $forum_desc = stripslashes(strip_tags($forum_desc));
    $old_name   = stripslashes(strip_tags($old_name));
  }

/* Execute the requested step */
switch ($step)
  {
    /* Show the forum list */
    default:
    case 1:
      ShowForums();
      break;

    /* Display the new forum page */
    case 2:
      ShowForumForm( $forum_name, $forum_desc, $forum_order, $forum_id, $type );
      break;

    /* Show preview */
    case 3:
      echo "            <FORM action=\"?pid=forum_admin\" method=\"POST\" name=\"forum_admin\">\n"
         . "              <TABLE cellpadding=\"5\" cellspacing=\"0\" width=\"100%\" border class=\"table_border\">\n"
         . "                <TR>\n"
         . "                  <TD class=\"table_header\" colspan=\"2\">Forum Preview</TD>\n"
         . "                </TR>\n"
         . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
         . "                  <TD width=\"25%\" valign=\"top\"><B>Forum Name:</B></TD>\n"
         . "                  <TD width=\"50%\">\n"
         . "                    $forum_name\n"
         . "                    <INPUT type=\"hidden\" name=\"forum_name\" value=\"$forum_name\">\n"
         . "                  </TD>\n"
         . "                </TR>\n"
         . "                <TR bgcolor=\"" . TABLE_COLOR_2 . "\" class=\"regular_text\">\n"
         . "                  <TD width=\"25%\" valign=\"top\"><B>Forum Description:</B></TD>\n"
         . "                  <TD width=\"50%\">\n"
         . "                    $forum_desc\n"
         . "                    <INPUT type=\"hidden\" name=\"forum_desc\" value=\"$forum_desc\">\n"
         . "                  </TD>\n"
         . "                </TR>\n"
         . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
         . "                  <TD width=\"25%\" valign=\"top\"><B>Forum Order:</B></TD>\n"
         . "                  <TD width=\"50%\">\n"
         . "                    $forum_order\n"
         . "                    <INPUT type=\"hidden\" name=\"forum_order\" value=\"$forum_order\">\n"
         . "                  </TD>\n"
         . "                </TR>\n"
         . "              </TABLE>\n"
         . "              <INPUT type=\"hidden\" name=\"forum_id\" value=\"$forum_id\">\n"
         . "              <INPUT type=\"hidden\" name=\"type\" value=\"$type\">\n"
         . "              <INPUT type=\"hidden\" name=\"old_name\" value=\"$old_name\">\n"
         . "              <CENTER>\n"
         . "                <BR>\n"
         . "                <INPUT type=\"submit\" value=\"Edit Forum\" name=\"action\">\n"
         . "                &nbsp;\n"
         . "                <INPUT type=\"submit\" value=\"Submit Forum\" name=\"action\">\n"
         . "              </CENTER>\n"
         . "            </FORM>\n";
      break;

    /* Add the new forum to the database */
    case 4:
      /* If the form was posted, then analyze it and add it */
      if ( $REQUEST_METHOD == "POST" )
        {
          /* Set the error to zero */
          $no_err = 0;

          /* Pull the number of forums with the same name */
          $SQL     = "SELECT COUNT(*) as forum_exists FROM " . TABLE_PREFIX . "forums WHERE forum_name='$forum_name';";
          $results = ExeSQL($SQL);

          /* Grab the data, and tell the user if the forum already exists */
          while ($row = mysql_fetch_array($results))
            {
              if ($row["forum_exists"] != 0)
                {
                  echo "            <CENTER class=\"error_message\">A forum by that name already exists!</CENTER><BR>\n";
                  $no_err++;
                }
            }

          /* If there were no errors */
          if ($no_err == 0)
            {
              /* Add the new forum to the database */
              $SQL     = "INSERT INTO " . TABLE_PREFIX . "forums (forum_name, forum_desc, forum_order) VALUES ('$forum_name', '$forum_desc', '$forum_order');";
              $results = ExeSQL($SQL);

              /* Let the user know everything went fine, and show the forum list */
              echo "            <CENTER class=\"normal_message\">The new forum has successfully been added!</CENTER><BR>\n";
              ShowForums();
              return;
            }
          else
            ShowForumForm( $forum_name, $forum_desc, $forum_order, $forum_id, $type );
        }
      else
        {
          /* If it wasn't posted, then give the user an error, and send them back */
          echo "            <CENTER class=\"error_message\">Malformed request detected!</CENTER><BR>\n";
          ShowForumForm( $forum_name, $forum_desc, $forum_order, $forum_id, $type );
        }
      break;

    /* Update an existing forum */
    case 5:
      /* Check if the form is posted */
      if ( $REQUEST_METHOD == "POST" )
        {
          /* Set the errors to zero */
          $no_err = 0;

          /* If the old name and new name don't match then ... */
          if ($forum_name != $old_name)
            {
              /* Pull the number of forums with the same name */
              $SQL     = "SELECT COUNT(*) as forum_exists FROM " . TABLE_PREFIX . "forums WHERE forum_name='$forum_name';";
              $results = ExeSQL($SQL);

              /* Grab the data and sit an error if the forum exists */
              while ($row = mysql_fetch_array($results))
                {
                  if ($row["forum_exists"] != 0)
                    {
                      echo "            <CENTER class=\"error_message\">A forum by that name already exists!</CENTER><BR>\n";
                      $no_err++;
                    }
                }
            }

          /* If there were no errors */
          if ($no_err == 0)
            {
              /* Add the new forum to the database */
              $SQL     = "UPDATE " . TABLE_PREFIX . "forums SET forum_name='$forum_name', forum_desc='$forum_desc', forum_order='$forum_order' WHERE forum_id='$forum_id';";
              $results = ExeSQL($SQL);

              /* Let the user know it went fine, and default to the forum list */
              echo "            <CENTER class=\"normal_message\">The forum has successfully been updated!</CENTER><BR>\n";
              ShowForums();
              return;
            }
          else
            ShowForumForm( $forum_name, $forum_desc, $forum_order, $forum_id, $type );
        }
      else
        {
          /* If it wasn't posted, then give an error, and show the forum form */
          echo "            <CENTER class=\"error_message\">Malformed request detected!</CENTER><BR>\n";
          ShowForumForm( $forum_name, $forum_desc, $forum_order, $forum_id, $type );
        }
      break;
 
      /* Delete the forum, and all it's associated threads and replies */
      case 6:
        /* The forum from the database */
        $SQL     = "DELETE FROM " . TABLE_PREFIX . "forums WHERE forum_id='$forum_id';";
        $results = ExeSQL($SQL);

        /* Delete the threads associated with the forum */
        $SQL     = "DELETE FROM " . TABLE_PREFIX . "threads WHERE forum_id='$forum_id';";
        $results = ExeSQL($SQL);

        /* Delete the replies associated with the forum */
        $SQL     = "DELETE FROM " . TABLE_PREFIX . "replies WHERE forum_id='$forum_id';";
        $results = ExeSQL($SQL);

        /* Give the user feedback */
        echo "            <CENTER class=\"normal_message\">The forum has successfully been removed!</CENTER><BR>\n";
        ShowForums();
        return;        
        break;
  }

/*
 * Show the current functions
 */

function
ShowForums()
{
  echo "            <TABLE cellpadding=\"5\" cellspacing=\"0\" width=\"100%\" border class=\"table_border\">\n"
     . "              <TR class=\"table_header\">\n"
     . "                <TD colspan=\"2\">\n"
     . "                  <TABLE cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\" class=\"table_header\">\n"
     . "                    <TR>\n"
     . "                      <TD>\n"
     . "                        Forum Administration&nbsp;\n"
     . "                      </TD>\n"
     . "                      <TD align=\"right\">\n"
     . "                        [ <A href=\"?pid=forum_admin&step=2\" class=\"table_header\">Add New Forum</A> ]\n"
     . "                      </TD>\n"
     . "                    </TR>\n"
     . "                  </TABLE>\n"
     . "                </TD>\n"
     . "              </TR>\n";

  /* Set the active color */
  $the_color = TABLE_COLOR_2;

  /* Pull the forums */
  $SQL     = "SELECT * FROM " . TABLE_PREFIX . "forums ORDER BY forum_order, forum_name;";
  $results = ExeSQL($SQL);

  /* Grab the data, and display the stuff */
  while ($row = mysql_fetch_array($results))
    {
      /* Grab the specific columns */
      $forum_id    = $row["forum_id"];
      $forum_name  = $row["forum_name"];
      $forum_order = $row["forum_order"];
      $forum_desc  = $row["forum_desc"];

      /* Swap the color */
      if ($the_color == TABLE_COLOR_2)
        $the_color = TABLE_COLOR_1;
      else
        $the_color = TABLE_COLOR_2;

      /* Display the data */
      echo "              <TR bgcolor=\"$the_color\">\n"
         . "                <TD>\n"
         . "                  <TABLE cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\">\n"
         . "                    <TR>\n"
         . "                      <TD>\n"
         . "                        <FONT class=\"regular_text\">$forum_order. $forum_name</FONT><BR>\n"
         . "                        <FONT class=\"small_text\">$forum_desc</FONT>\n"
         . "                      </TD>\n"
         . "                      <TD align=\"right\">\n"
         . "                        <FORM action=\"?pid=forum_admin\" method=\"POST\">\n"
         . "                          <INPUT type=\"hidden\" name=\"forum_id\" value=\"$forum_id\">\n"
         . "                          <INPUT type=\"hidden\" name=\"forum_name\" value=\"$forum_name\">\n"
         . "                          <INPUT type=\"hidden\" name=\"forum_desc\" value=\"$forum_desc\">\n"
         . "                          <INPUT type=\"hidden\" name=\"forum_order\" value=\"$forum_order\">\n"
         . "                          <INPUT type=\"submit\" name=\"action\" value=\"Edit\">\n"
         . "                          <INPUT type=\"submit\" name=\"action\" value=\"Delete\" onClick=\"return Confirm('Are you sure you want to delete this forum, and all of it\'s associated posts?');\">\n"
         . "                        </FORM>\n"
         . "                      </TD>\n"
         . "                    </TR>\n"
         . "                  </TABLE>\n"
         . "                </TD>\n"
         . "              </TR>\n";
    }

  /* Close off the table */
  echo "            </TABLE>\n";
}

/*
 * Display the form to add a forum
 */ 

function
ShowForumForm( $forum_name, $forum_desc, $forum_order, $forum_id, $type )
{
  /* Display the stuff in the form! */
  echo "            <FORM action=\"?pid=forum_admin&step=3\" method=\"POST\" name=\"forum_admin\">\n"
     . "              <TABLE cellpadding=\"5\" cellspacing=\"0\" width=\"100%\" border class=\"table_border\">\n"
     . "                <TR class=\"table_header\"><TD colspan=\"2\">Forum Administration</TD></TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" valign=\"top\"><B>Forum Name:</B></TD>\n"
     . "                  <TD width=\"50%\">\n"
     . "                    <INPUT type=\"text\" name=\"forum_name\" value=\"$forum_name\" size=\"50\" max=\"64\">\n"
     . "                  </TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_2 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" valign=\"top\"><B>Forum Description:</B></TD>\n"
     . "                  <TD width=\"50%\">\n"
     . "                    <TEXTAREA name=\"forum_desc\" rows=\"5\" cols=\"40\">$forum_desc</TEXTAREA>\n"
     . "                  </TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" valign=\"top\"><B>Forum Order:</B></TD>\n"
     . "                  <TD width=\"50%\">\n"
     . "                    <INPUT type=\"text\" name=\"forum_order\" value=\"$forum_order\" size=\"50\" max=\"64\">\n"
     . "                  </TD>\n"
     . "                </TR>\n"
     . "              </TABLE>\n"
     . "              <INPUT type=\"hidden\" name=\"forum_id\" value=\"$forum_id\">\n"
     . "              <INPUT type=\"hidden\" name=\"type\" value=\"$type\">\n"
     . "              <INPUT type=\"hidden\" name=\"old_name\" value=\"$forum_name\">\n"
     . "              <CENTER><BR><INPUT type=\"submit\" value=\"Preview Information\" name=\"action\"></CENTER>\n"
     . "            </FORM>\n";
}

?>
