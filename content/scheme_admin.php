<?

/******************************************************************************\
 * Copyright (C) 2002 B Squared (b^2) by Josh Sherman <josh@cleancode.org>    *
 *                                                                            *
 * This script displays the contents for the 'Scheme Administration' page.    *
 * Don't forget the 12 space indent for all content pages.                    *
 *                                                                            *
 *                                 Last modified : September 24th, 2002 (JJS) *
\******************************************************************************/

/* Redirect possible hack attempts */
$file_name = "scheme_admin.php";

/* Get the negative length of $file_name */
$file_name_length = -(strlen($file_name));

/* Check if the values match, if so, redirect */
if (substr($_SERVER['SCRIPT_NAME'], $file_name_length) == $file_name)
  header("Location: ../index.php");

/* Grab the variables held by superglobals */
$old_name           = GetVars("old_name");
$scheme_id          = GetVars("scheme_id");
$scheme_name        = GetVars("scheme_name");
$scheme_desc        = GetVars("scheme_desc");
$background_color   = GetVars("background_color");
$table_border_size  = GetVars("table_border_size");
$table_border_color = GetVars("table_border_color");
$table_header_background = GetVars("table_header_background");
$table_header_text_color = GetVars("table_header_text_color");
$text_color         = GetVars("text_color");
$text_font          = GetVars("text_font");
$text_regular       = GetVars("text_regular");
$text_small         = GetVars("text_small");
$table_color_1      = GetVars("table_color_1");
$table_color_2      = GetVars("table_color_2");
$link_color         = GetVars("link_color");
$error_message      = GetVars("error_message");
$header_background  = GetVars("header_background");
$menu_background    = GetVars("menu_background");
$active_scheme      = GetVars("active_scheme");
$forum_exists       = GetVars("forum_exists");
$action             = GetVars("action");
$step               = GetVars("step");
$type               = GetVars("type");

/* Parse any user input */
CheckVars(&$step, 1);
CheckVars(&$old_name, 64);
CheckVars(&$scheme_id, 10);
CheckVars(&$scheme_name, 64);
CheckVars(&$scheme_desc, 255);
CheckVars(&$background_color, 7);
CheckVars(&$table_border_size, 2);
CheckVars(&$table_border_color, 7);
CheckVars(&$table_header_background, 7);
CheckVars(&$table_header_text_color, 7);
CheckVars(&$text_color, 7);
CheckVars(&$text_font, 64);
CheckVars(&$text_regular, 2);
CheckVars(&$text_small, 2);
CheckVars(&$table_color_1, 7);
CheckVars(&$table_color_2, 7);
CheckVars(&$link_color, 7);
CheckVars(&$error_message, 7);
CheckVars(&$header_background, 7);
CheckVars(&$menu_background, 7);
CheckVars(&$active_scheme, 2);

/* Check that the user isn't trying to mess with the $step variable */
if ( $step == "" || ( $step != 1 && $step != 2 && $step != 3 && $step != 4 && $step != 5 && $step != 6 ) )
  $step = 1;

/* Make sure the user isn't feeding information via the query string, thwart all attempts!! */
if ( ( ( $scheme_name == "" ) && ( $step == 3 || $step == 4 ) ) || 
     ( ( $step == 1 && $QUERY_STRING != "pid=scheme_admin" ) || 
       ( $step == 2 && $QUERY_STRING != "pid=scheme_admin&step=2" ) || 
       ( $step == 3 && $QUERY_STRING != "pid=scheme_admin" ) || 
       ( $step == 4 && $QUERY_STRING != "pid=scheme_admin" ) || 
       ( $step == 5 && $QUERY_STRING != "pid=scheme_admin" ) || 
       ( $step == 6 && $QUERY_STRING != "pid=scheme_admin" ) ) ||
     ( ( $step != 1 && $step != 2 ) && ( strlen(trim($forum_name)) == 0 || strlen(trim($forum_desc)) == 0 ) ) )
  {
    echo "            <CENTER class=\"error_message\">Malformed request detected!</CENTER><BR>\n";
    $step = 1;
  }

/* Determine which step to use */
if ($action == "Edit Scheme")
  $step = 2;
else if ($action == "Edit")
  {
    $step = 2;
    $type = "existing";
  }
else if ($action == "Preview Information")
  $step = 3;
else if ($action == "Submit Scheme")
  $step = 4;
else if ($action == "Delete")
  $step = 6;

/* If the user is submitting an existing forum for editting, then go to step 5 */
if ( $step == 4 && $type != "" )
  $step = 5;

/* Strip out all escape characters */

/*

I'll unREM this eventually

if ($step == 2)
  {
    $forum_name = stripslashes(strip_tags($forum_name));
    $forum_desc = stripslashes(strip_tags($forum_desc));
    $old_name   = stripslashes(strip_tags($old_name));
  }

if ($step == 3)
  {

    $forum_name = stripslashes(strip_tags($forum_name));
    $forum_desc = stripslashes(strip_tags($forum_desc));
    $old_name   = stripslashes(strip_tags($old_name));
  }
*/

/* What to do, oh what to do ... */
switch ($step)
  {
    /* Show the forum list */
    default:
    case 1:
      ShowSchemes();
      break;

    /* Display the new forum page */
    case 2:
      ShowSchemeForm( $scheme_id, $scheme_name, $scheme_desc, $background_color, $table_border_size, $table_border_color, $table_header_background, $table_header_text_color, $text_color, $text_font, $text_regular, $text_small, $table_color_1, $table_color_2, $link_color, $error_message, $header_background, $menu_background, $active_scheme, $type );
      break;

    /* Show preview */
    case 3:
      echo "            <FORM action=\"?pid=scheme_admin\" method=\"POST\" name=\"scheme_admin\">\n"
         . "              <TABLE cellpadding=\"5\" cellspacing=\"0\" width=\"100%\" border class=\"table_border\">\n"
         . "                <TR>\n"
         . "                  <TD class=\"table_header\" colspan=\"2\">Forum Preview</TD>\n"
         . "                </TR>\n"
         . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
         . "                  <TD width=\"25%\" valign=\"top\"><B>Scheme Name:</B></TD>\n"
         . "                  <TD width=\"50%\">\n"
         . "                    $scheme_name\n"
         . "                    <INPUT type=\"hidden\" name=\"scheme_name\" value=\"$scheme_name\">\n"
         . "                  </TD>\n"
         . "                </TR>\n"
         . "                <TR bgcolor=\"" . TABLE_COLOR_2 . "\" class=\"regular_text\">\n"
         . "                  <TD width=\"25%\" valign=\"top\"><B>Scheme Description:</B></TD>\n"
         . "                  <TD width=\"50%\">\n"
         . "                    $scheme_desc\n"
         . "                    <INPUT type=\"hidden\" name=\"scheme_desc\" value=\"$scheme_desc\">\n"
         . "                  </TD>\n"
         . "                </TR>\n"
         . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
         . "                  <TD width=\"25%\" valign=\"top\"><B>Background Color:</B></TD>\n"
         . "                  <TD width=\"50%\">\n"
         . "                    <TABLE cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"regular_text\">\n"
         . "                      <TR>\n"
         . "                        <TD><TABLE bgcolor=\"$background_color\" height=\"15\" width=\"15\" border=\"1\" bordercolor=\"#000000\" cellpadding=\"0\" cellspading=\"0\"><TR><TD></TD></TR></TABLE></TD>\n"
         . "                        <TD>&nbsp;$background_color</TD>\n"
         . "                      </TR>\n"
         . "                    </TABLE>\n"
         . "                    <INPUT type=\"hidden\" name=\"background_color\" value=\"$background_color\">\n"
         . "                  </TD>\n"
         . "                </TR>\n"
         . "                <TR bgcolor=\"" . TABLE_COLOR_2 . "\" class=\"regular_text\">\n"
         . "                  <TD width=\"25%\" valign=\"top\"><B>Table Border Color:</B></TD>\n"
         . "                  <TD width=\"50%\">\n"
         . "                    <TABLE cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"regular_text\">\n"
         . "                      <TR>\n"
         . "                        <TD><TABLE bgcolor=\"$table_border_color\" height=\"15\" width=\"15\" border=\"1\" bordercolor=\"#000000\" cellpadding=\"0\" cellspading=\"0\"><TR><TD></TD></TR></TABLE></TD>\n"
         . "                        <TD>&nbsp;$table_border_color</TD>\n"
         . "                      </TR>\n"
         . "                    </TABLE>\n"
         . "                    <INPUT type=\"hidden\" name=\"table_border_color\" value=\"$table_border_color\">\n"
         . "                  </TD>\n"
         . "                </TR>\n"
         . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
         . "                  <TD width=\"25%\" valign=\"top\"><B>Table Border Size:</B></TD>\n"
         . "                  <TD width=\"50%\">\n"
         . "                    $table_border_size\n"
         . "                    <INPUT type=\"hidden\" name=\"table_border_size\" value=\"$table_border_size\">\n"
         . "                  </TD>\n"
         . "                </TR>\n"
         . "                <TR bgcolor=\"" . TABLE_COLOR_2 . "\" class=\"regular_text\">\n"
         . "                  <TD width=\"25%\" valign=\"top\"><B>Header Background Color:</B></TD>\n"
         . "                  <TD width=\"50%\">\n"
         . "                    <TABLE cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"regular_text\">\n"
         . "                      <TR>\n"
         . "                        <TD><TABLE bgcolor=\"$header_background\" height=\"15\" width=\"15\" border=\"1\" bordercolor=\"#000000\" cellpadding=\"0\" cellspading=\"0\"><TR><TD></TD></TR></TABLE></TD>\n"
         . "                        <TD>&nbsp;$header_background</TD>\n"
         . "                      </TR>\n"
         . "                    </TABLE>\n"
         . "                    <INPUT type=\"hidden\" name=\"header_background\" value=\"$header_background\">\n"
         . "                  </TD>\n"
         . "                </TR>\n"
         . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
         . "                  <TD width=\"25%\" valign=\"top\"><B>Menu Background Color:</B></TD>\n"
         . "                  <TD width=\"50%\">\n"
         . "                    <TABLE cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"regular_text\">\n"
         . "                      <TR>\n"
         . "                        <TD><TABLE bgcolor=\"$menu_background\" height=\"15\" width=\"15\" border=\"1\" bordercolor=\"#000000\" cellpadding=\"0\" cellspading=\"0\"><TR><TD></TD></TR></TABLE></TD>\n"
         . "                        <TD>&nbsp;$menu_background</TD>\n"
         . "                      </TR>\n"
         . "                    </TABLE>\n"
         . "                    <INPUT type=\"hidden\" name=\"menu_background\" value=\"$menu_background\">\n"
         . "                  </TD>\n"
         . "                </TR>\n"
         . "                <TR bgcolor=\"" . TABLE_COLOR_2 . "\" class=\"regular_text\">\n"
         . "                  <TD width=\"25%\" valign=\"top\"><B>Text Color:</B></TD>\n"
         . "                  <TD width=\"50%\">\n"
         . "                    <TABLE cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"regular_text\"><TR><TD><TABLE bgcolor=\"$text_color\" height=\"15\" width=\"15\" border=\"1\" bordercolor=\"#000000\" cellpadding=\"0\" cellspading=\"0\"><TR><TD></TD></TR></TABLE></TD><TD>&nbsp;$text_color</TD></TR></TABLE>\n"
         . "                    <INPUT type=\"hidden\" name=\"text_color\" value=\"$text_color\">\n"
         . "                  </TD>\n"
         . "                </TR>\n"
         . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
         . "                  <TD width=\"25%\" valign=\"top\"><B>Font Face:</B></TD>\n"
         . "                  <TD width=\"50%\">\n"
         . "                    $text_font\n"
         . "                    <INPUT type=\"hidden\" name=\"text_font\" value=\"$text_font\">\n"
         . "                  </TD>\n"
         . "                </TR>\n"
         . "                <TR bgcolor=\"" . TABLE_COLOR_2 . "\" class=\"regular_text\">\n"
         . "                  <TD width=\"25%\" valign=\"top\"><B>Small Font Size:</B></TD>\n"
         . "                  <TD width=\"50%\">\n"
         . "                    $text_small\n"
         . "                    <INPUT type=\"hidden\" name=\"text_small\" value=\"$text_small\">\n"
         . "                  </TD>\n"
         . "                </TR>\n"
         . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
         . "                  <TD width=\"25%\" valign=\"top\"><B>Regular Font Size:</B></TD>\n"
         . "                  <TD width=\"50%\">\n"
         . "                    $text_regular\n"
         . "                    <INPUT type=\"hidden\" name=\"text_regular\" value=\"$text_regular\">\n"
         . "                  </TD>\n"
         . "                </TR>\n"
         . "                <TR bgcolor=\"" . TABLE_COLOR_2 . "\" class=\"regular_text\">\n"
         . "                  <TD width=\"25%\" valign=\"top\"><B>Link Color:</B></TD>\n"
         . "                  <TD width=\"50%\">\n"
         . "                    <TABLE cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"regular_text\"><TR><TD><TABLE bgcolor=\"$link_color\" height=\"15\" width=\"15\" border=\"1\" bordercolor=\"#000000\" cellpadding=\"0\" cellspading=\"0\"><TR><TD></TD></TR></TABLE></TD><TD>&nbsp;$link_color</TD></TR></TABLE>\n"
         . "                    <INPUT type=\"hidden\" name=\"link_color\" value=\"$link_color\">\n"
         . "                  </TD>\n"
         . "                </TR>\n"
         . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
         . "                  <TD width=\"25%\" valign=\"top\"><B>Table Header Background Color:</B></TD>\n"
         . "                  <TD width=\"50%\">\n"
         . "                    <TABLE cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"regular_text\"><TR><TD><TABLE bgcolor=\"$table_header_background\" height=\"15\" width=\"15\" border=\"1\" bordercolor=\"#000000\" cellpadding=\"0\" cellspading=\"0\"><TR><TD></TD></TR></TABLE></TD><TD>&nbsp;$table_header_background</TD></TR></TABLE>\n"
         . "                    <INPUT type=\"hidden\" name=\"table_header_background\" value=\"$table_header_background\">\n"
         . "                  </TD>\n"
         . "                </TR>\n"
         . "                <TR bgcolor=\"" . TABLE_COLOR_2 . "\" class=\"regular_text\">\n"
         . "                  <TD width=\"25%\" valign=\"top\"><B>Table Header Text Color:</B></TD>\n"
         . "                  <TD width=\"50%\">\n"
         . "                    <TABLE cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"regular_text\"><TR><TD><TABLE bgcolor=\"$table_header_text_color\" height=\"15\" width=\"15\" border=\"1\" bordercolor=\"#000000\" cellpadding=\"0\" cellspading=\"0\"><TR><TD></TD></TR></TABLE></TD><TD>&nbsp;$table_header_text_color</TD></TR></TABLE>\n"
         . "                    <INPUT type=\"hidden\" name=\"table_header_text_color\" value=\"$table_header_text_color\">\n"
         . "                  </TD>\n"
         . "                </TR>\n"
         . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
         . "                  <TD width=\"25%\" valign=\"top\"><B>Alternating Table Color #1:</B></TD>\n"
         . "                  <TD width=\"50%\">\n"
         . "                    <TABLE cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"regular_text\"><TR><TD><TABLE bgcolor=\"$table_color_1\" height=\"15\" width=\"15\" border=\"1\" bordercolor=\"#000000\" cellpadding=\"0\" cellspading=\"0\"><TR><TD></TD></TR></TABLE></TD><TD>&nbsp;$table_color_1</TD></TR></TABLE>\n"
         . "                    <INPUT type=\"hidden\" name=\"table_color_1\" value=\"$table_color_1\">\n"
         . "                  </TD>\n"
         . "                </TR>\n"
         . "                <TR bgcolor=\"" . TABLE_COLOR_2 . "\" class=\"regular_text\">\n"
         . "                  <TD width=\"25%\" valign=\"top\"><B>Alternating Table Color #2:</B></TD>\n"
         . "                  <TD width=\"50%\">\n"
         . "                    <TABLE cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"regular_text\"><TR><TD><TABLE bgcolor=\"$table_color_2\" height=\"15\" width=\"15\" border=\"1\" bordercolor=\"#000000\" cellpadding=\"0\" cellspading=\"0\"><TR><TD></TD></TR></TABLE></TD><TD>&nbsp;$table_color_2</TD></TR></TABLE>\n"
         . "                    <INPUT type=\"hidden\" name=\"table_color_2\" value=\"$table_color_2\">\n"
         . "                  </TD>\n"
         . "                </TR>\n"
         . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
         . "                  <TD width=\"25%\" valign=\"top\"><B>Error Message Color:</B></TD>\n"
         . "                  <TD width=\"50%\">\n"
         . "                    <TABLE cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"regular_text\"><TR><TD><TABLE bgcolor=\"$error_message\" height=\"15\" width=\"15\" border=\"1\" bordercolor=\"#000000\" cellpadding=\"0\" cellspading=\"0\"><TR><TD></TD></TR></TABLE></TD><TD>&nbsp;$error_message</TD></TR></TABLE>\n"
         . "                    <INPUT type=\"hidden\" name=\"error_message\" value=\"$error_message\">\n"
         . "                  </TD>\n"
         . "                </TR>\n"
         . "                <TR bgcolor=\"" . TABLE_COLOR_2 . "\" class=\"regular_text\">\n"
         . "                  <TD width=\"25%\" valign=\"top\"><B>Active Scheme:</B></TD>\n"
         . "                  <TD width=\"50%\"><I>";

      /* Will this be the active scheme? */
      if ($active_scheme == 1)
        echo "This will be the active scheme.";
      else
        echo "This will not be the active scheme.";

      /* Finish off the preview */
      echo "</I><INPUT type=\"hidden\" name=\"active_scheme\" value=\"$active_scheme\">\n"
         . "                  </TD>\n";
      echo "                </TR>\n"
         . "              </TABLE>\n"
         . "              <INPUT type=\"hidden\" name=\"scheme_id\" value=\"$scheme_id\">\n"
         . "              <INPUT type=\"hidden\" name=\"type\" value=\"$type\">\n"
         . "              <INPUT type=\"hidden\" name=\"old_name\" value=\"$old_name\">\n"
         . "              <CENTER><BR><INPUT type=\"submit\" value=\"Edit Scheme\" name=\"action\"> <INPUT type=\"submit\" value=\"Submit Scheme\" name=\"action\"></CENTER>\n"
         . "              </CENTER>\n"
         . "            </FORM>\n";
      break;

    /* Add the new scheme to the database */
    case 4:
      /* Make sure it was POSTed */
      if ( $REQUEST_METHOD == "POST" )
        {
          /* No errors... yet */
          $no_err = 0;

          /* Pull the number of schemes with the same name */
          $SQL     = "SELECT COUNT(*) as scheme_exists FROM " . TABLE_PREFIX . "schemes WHERE scheme_name='$scheme_name';";
          $results = ExeSQL($SQL);

          /* Grab the data, and analyze it */
          while ($row = mysql_fetch_array($results))
            {
              /* If the forum already exists ... */
              if ($row["scheme_exists"] != 0)
                {
                  /* Let the user know */
                  echo "            <CENTER class=\"error_message\">A forum by that name already exists!</CENTER><BR>\n";
                  $no_err++;
                }
            }

          /* If there were no errors, then keep going */
          if ($no_err == 0)
            {
              /* Add the new scheme to the database */
              $SQL     = "INSERT INTO " . TABLE_PREFIX . "schemes (scheme_name, scheme_desc, background_color, table_border_color, table_border_size, header_background, menu_background, text_color, text_font, text_small, text_regular, link_color, table_header_background, table_header_text_color, table_color_1, table_color_2, error_message, active_scheme) VALUES ('$scheme_name', '$scheme_desc', '$background_color', '$table_border_color', '$table_border_size', '$header_background', '$menu_background', '$text_color', '$text_font', '$text_small', '$text_regular', '$link_color', '$table_header_background', '$table_header_text_color', '$table_color_1', '$table_color_2', '$error_message', '$active_scheme');";
              $results = ExeSQL($SQL);

              /* If this is supposed to be the active scheme ... */
              if ($active_scheme == 1)
                {
                  /* Set all the other schemes to inactive */
                  $SQL     = "UPDATE " . TABLE_PREFIX . "schemes SET active_scheme='0' WHERE scheme_name!='$scheme_name';";
                  $results = ExeSQL($SQL);
                }

              /* Let the user know it went off w/o a hitch */
              echo "            <CENTER class=\"regular_text\">\n"
                 . "              <FONT class=\"normal_message\">The new scheme has successfully been added!</FONT><BR>\n"
                 . "              <A href=\"?pid=scheme_admin\">If you changed the active scheme, click here to update the page</A>\n"
                 . "            </CENTER><BR>\n";
              ShowSchemes();
              return;
            }
          else
            {
              /* If there was a problem, then display the form again */
              ShowSchemeForm( $scheme_id, $scheme_name, $scheme_desc, $background_color, $table_border_size, $table_border_color, $table_header_background, $table_header_text_color, $text_color, $text_font, $text_regular, $text_small, $table_color_1, $table_color_2, $link_color, $error_message, $header_background, $menu_background, $active_scheme, $type );
            }
        }
      else
        {
          /* Same deal */
          echo "            <CENTER class=\"error_message\">Malformed request detected!</CENTER><BR>\n";
          ShowSchemeForm( $scheme_id, $scheme_name, $scheme_desc, $background_color, $table_border_size, $table_border_color, $table_header_background, $table_header_text_color, $text_color, $text_font, $text_regular, $text_small, $table_color_1, $table_color_2, $link_color, $error_message, $header_background, $menu_background, $active_scheme, $type );
        }
      break;

    /* Update an existing scheme */
    case 5:
      /* Make sure the form is POSTed */
      if ( $REQUEST_METHOD == "POST" )
        {
          /* No errors */
          $no_err = 0;

          /* If the old and new names don't match */
          if ($scheme_name != $old_name)
            {
              /* Pull the number of schemes with the same name */
              $SQL     = "SELECT COUNT(*) as scheme_exists FROM " . TABLE_PREFIX . "schemes WHERE scheme_name='$scheme_name';";
              $results = ExeSQL($SQL);

              /* Grab the data, parse the results */
              while ($row = mysql_fetch_array($results))
                {
                  /* If the scheme name exists, then error out */
                  if ($row["scheme_exists"] != 0)
                    {
                      echo "            <CENTER class=\"error_message\">A scheme by that name already exists!</CENTER><BR>\n";
                      $no_err++;
                    }
                }
            }

          /* If there were no errors ... */
          if ($no_err == 0)
            {
              /* Update the scheme in the database */
              $SQL     = "UPDATE " . TABLE_PREFIX . "schemes SET scheme_name='$scheme_name', scheme_desc='$scheme_desc', background_color='$background_color', table_border_color='$table_border_color', table_border_size='$table_border_size', header_background='$header_background', menu_background='$menu_background', text_color='$text_color', text_font='$text_font', text_small='$text_small', text_regular='$text_regular', link_color='$link_color', table_header_background='$table_header_background', table_header_text_color='$table_header_text_color', table_color_1='$table_color_1', table_color_2='$table_color_2', error_message='$error_message', active_scheme='$active_scheme' WHERE scheme_id='$scheme_id';";
              $results = ExeSQL($SQL);

              /* If this is supposed to be the active scheme */
              if ($active_scheme == 1)
                {
                  /* Then set the other schemes to inactive */
                  $SQL     = "UPDATE " . TABLE_PREFIX . "schemes SET active_scheme='0' WHERE scheme_id!='$scheme_id';";
                  $results = ExeSQL($SQL);
                }

              /* Count how many active schemes there are */
              $SQL     = "SELECT COUNT(*) AS any_active FROM " . TABLE_PREFIX . "schemes WHERE active_scheme='1';";
              $results = ExeSQL($SQL);

              /* Grab the data and load it in a variable */
              while ($row = mysql_fetch_array($results))
                $any_active = $row["any_active"];

              /* If there are no active schemes */
              if ($any_active == 0)
                {
                  /* Set the oldest scheme as active */
                  $SQL     = "UPDATE " . TABLE_PREFIX . "schemes SET active_scheme='1' LIMIT 1;";
                  $results = ExeSQL($SQL);
                }

              /* Let the user know everything went well */
              echo "            <CENTER class=\"regular_text\">\n"
                 . "              <FONT class=\"normal_message\">The forum has successfully been updated!</FONT><BR>\n"
                 . "              <A href=\"?pid=scheme_admin\">If you changed the active scheme, click here to update the page</A>\n"
                 . "            </CENTER><BR>\n";
              ShowSchemes();
              return;
            }
          else
              ShowSchemeForm( $scheme_id, $scheme_name, $scheme_desc, $background_color, $table_border_size, $table_border_color, $table_header_background, $table_header_text_color, $text_color, $text_font, $text_regular, $text_small, $table_color_1, $table_color_2, $link_color, $error_message, $header_background, $menu_background, $active_scheme, $type );
        }
      else
        {
          /* If it wasn't POSTed, then error out */
          echo "            <CENTER class=\"error_message\">Malformed request detected!</CENTER><BR>\n";
          ShowSchemeForm( $scheme_id, $scheme_name, $scheme_desc, $background_color, $table_border_size, $table_border_color, $table_header_background, $table_header_text_color, $text_color, $text_font, $text_regular, $text_small, $table_color_1, $table_color_2, $link_color, $error_message, $header_background, $menu_background, $active_scheme, $type );
        }
      break;
 
      /* Delete the selected scheme */
      case 6:
        /* Delete the scheme */
        $SQL     = "DELETE FROM " . TABLE_PREFIX . "schemes WHERE scheme_id='$scheme_id';";
        $results = ExeSQL($SQL);

        /* Check for active schemes */
        $SQL     = "SELECT COUNT(*) AS any_active FROM " . TABLE_PREFIX . "schemes WHERE active_scheme='1';";
        $results = ExeSQL($SQL);

        /* Grab the data, and load it in a variable */
        while ($row = mysql_fetch_array($results))
          $any_active = $row["any_active"];

        /* If there are no active schemes ... */
        if ($any_active == 0)
          { 
            /* Set the oldest scheme as active */
            $SQL     = "UPDATE " . TABLE_PREFIX . "schemes SET active_scheme='1' WHERE scheme_name='default';";
            $results = ExeSQL($SQL);
          }

        /* Let the user know what's up */
        echo "            <CENTER class=\"regular_text\">\n"
           . "              <FONT class=\"normal_message\">The scheme has successfully been removed!</FONT><BR>\n"
           . "              <A href=\"?pid=scheme_admin\">If you changed the active scheme, click here to update the page</A>\n"
           . "            </CENTER><BR>\n";
        ShowSchemes();
        return;        
        break;
  }

/*
 * Show the schemes that are currently in the database
 */

function
ShowSchemes()
{
  /* Stop your yappin' and start showing the schemes */
  echo "            <TABLE cellpadding=\"5\" cellspacing=\"0\" width=\"100%\" border class=\"table_border\">\n"
     . "              <TR class=\"table_header\">\n"
     . "                <TD colspan=\"2\">\n"
     . "                  <TABLE cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\" class=\"table_header\">\n"
     . "                    <TR>\n"
     . "                      <TD>\n"
     . "                        Scheme Administration&nbsp;\n"
     . "                      </TD>\n"
     . "                      <TD align=\"right\">\n"
     . "                        [ <A href=\"?pid=scheme_admin&step=2\" class=\"table_header\">Add New Scheme</A> ]\n"
     . "                      </TD>\n"
     . "                    </TR>\n"
     . "                  </TABLE>\n"
     . "                </TD>\n"
     . "              </TR>\n";

  /* Set the active color */
  $the_color = TABLE_COLOR_2;

  /* Pull the schemes */
  $SQL     = "SELECT * FROM " . TABLE_PREFIX . "schemes ORDER BY scheme_id;";
  $results = ExeSQL($SQL);

  /* Grab the data, parse the results */
  while ($row = mysql_fetch_array($results))
    {
      /* Load up all the variables */
      $scheme_id               = $row["scheme_id"];
      $scheme_name             = $row["scheme_name"];
      $scheme_desc             = $row["scheme_desc"];
      $background_color        = $row["background_color"];
      $table_border_size       = $row["table_border_size"];
      $table_border_color      = $row["table_border_color"];     
      $table_header_background = $row["table_header_background"];
      $table_header_text_color = $row["table_header_text_color"];
      $text_color              = $row["text_color"];
      $text_font               = $row["text_font"];
      $text_regular            = $row["text_regular"];
      $text_small              = $row["text_small"];
      $table_color_1           = $row["table_color_1"];
      $table_color_2           = $row["table_color_2"];
      $link_color              = $row["link_color"];
      $error_message           = $row["error_message"];
      $header_background       = $row["header_background"];
      $menu_background         = $row["menu_background"];
      $active_scheme           = $row["active_scheme"];

      /* Swap the colors */
      if ($the_color == TABLE_COLOR_2)
        $the_color = TABLE_COLOR_1;
      else
        $the_color = TABLE_COLOR_2;

      /* Keep showing the data */
      echo "              <TR bgcolor=\"$the_color\">\n"
         . "                <TD>\n"
         . "                  <TABLE cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\">\n"
         . "                    <TR>\n"
         . "                      <TD valign=\"top\"width=\"400\">\n"
         . "                        <FONT class=\"regular_text\">";

      /* If the current scheme is active, then bold the name */    
      if ($active_scheme != 1)
        echo "<A href=\"?preview_scheme=$scheme_id\" target=\"new\">$scheme_name</A>";
      else
        echo "<B><A href=\"?preview_scheme=$scheme_id\" target=\"new\">$scheme_name</A></B>";

      /* Finish displaying */      
      echo "</FONT><BR>\n"
         . "                        <FONT class=\"small_text\">$scheme_desc</FONT><BR>\n"
         . "                      </TD>\n";

/* 

I couldn't get this shit to look right, so it's been replaced... I think this code might get resurrected someday, hence why it's still here!!

      echo "                      <TD align=\"center\" valign=\"top\">\n"
         . "                        <TABLE border class=\"table_border\" bgcolor=\"$background_color\" cellspacing=\"0\" cellpadding=\"10\" width=\"200\" height=\"150\">\n"
         . "                          <TR>\n"
         . "                            <TD align=\"center\" valign=\"middle\">\n"
         . "                              <FONT face=\"$text_font\" color=\"$error_message\" style=\"font-size: $text_regular;\"><B>error message</B></FONT>\n"
         . "                              <TABLE width=\"100%\" border=\"$table_border_size=\" bordercolor=\"$table_border_color\" cellspacing=\"0\" cellpadding=\"5\">\n"
         . "                                <TR bgcolor=\"$table_header_background\">\n"
         . "                                  <TD><FONT face=\"$text_font\" style=\"font-size: $text_small; color=\"$table_header_text_color;\"><B>table header</B></FONT></TD>\n"
         . "                                </TR>\n"
         . "                                <TR bgcolor=\"$table_color_1\">\n"
         . "                                  <TD><FONT face=\"$text_font\" color=\"$text_color\" style=\"font-size: $text_regular;\">regular text...</FONT></TD>\n"
         . "                                </TR>\n"
         . "                                <TR bgcolor=\"$table_color_2\">\n"
         . "                                  <TD>\n"
         . "                                    <A href=\"\"><FONT face=\"$text_font\" color=\"$link_color\" style=\"font-size: $text_regular;\">linkage...</FONT></A>\n"
         . "                                  </TD>\n"
         . "                                </TR>\n"
         . "                              </TABLE>\n"
         . "                              <FONT face=\"$text_font\" color=\"$normal_message\" style=\"font-size: $text_regular;\"><B>normal message</B></FONT>\n"
         . "                            </TD>\n"
         . "                          </TR>\n"
         . "                        </TABLE>\n"
         . "                      </TD>\n";
*/

      /* Throw all the properties into hidden fields */
      echo "                      <TD align=\"right\" valign=\"top\" nowrap>\n"
         . "                        <FORM action=\"?pid=scheme_admin\" method=\"POST\">\n"
         . "                          <INPUT type=\"hidden\" name=\"scheme_id\" value=\"$scheme_id\">\n"
         . "                          <INPUT type=\"hidden\" name=\"scheme_name\" value=\"$scheme_name\">\n"
         . "                          <INPUT type=\"hidden\" name=\"scheme_desc\" value=\"$scheme_desc\">\n"
         . "                          <INPUT type=\"hidden\" name=\"background_color\" value=\"$background_color\">\n"
         . "                          <INPUT type=\"hidden\" name=\"table_border_size\" value=\"$table_border_size\">\n"
         . "                          <INPUT type=\"hidden\" name=\"table_border_color\" value=\"$table_border_color\">\n"
         . "                          <INPUT type=\"hidden\" name=\"table_header_background\" value=\"$table_header_background\">\n"
         . "                          <INPUT type=\"hidden\" name=\"table_header_text_color\" value=\"$table_header_text_color\">\n"
         . "                          <INPUT type=\"hidden\" name=\"text_color\" value=\"$text_color\">\n"
         . "                          <INPUT type=\"hidden\" name=\"text_font\" value=\"$text_font\">\n"
         . "                          <INPUT type=\"hidden\" name=\"text_regular\" value=\"$text_regular\">\n"
         . "                          <INPUT type=\"hidden\" name=\"text_small\" value=\"$text_small\">\n"
         . "                          <INPUT type=\"hidden\" name=\"table_color_1\" value=\"$table_color_1\">\n"
         . "                          <INPUT type=\"hidden\" name=\"table_color_2\" value=\"$table_color_2\">\n"
         . "                          <INPUT type=\"hidden\" name=\"link_color\" value=\"$link_color\">\n"
         . "                          <INPUT type=\"hidden\" name=\"error_message\" value=\"$error_message\">\n"
         . "                          <INPUT type=\"hidden\" name=\"header_background\" value=\"$header_background\">\n"
         . "                          <INPUT type=\"hidden\" name=\"menu_background\" value=\"$menu_background\">\n"
         . "                          <INPUT type=\"hidden\" name=\"active_scheme\" value=\"$active_scheme\">\n"
         . "                          <INPUT type=\"submit\" name=\"action\" value=\"Edit\">\n"
         . "                          <INPUT type=\"submit\" name=\"action\" value=\"Delete\" onClick=\"return Confirm('Are you sure you want to delete this scheme?');\">\n"
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
 * Show the form to edit the scheme
 */

function
ShowSchemeForm( $scheme_id, $scheme_name, $scheme_desc, $background_color, $table_border_size, $table_border_color, $table_header_background, $table_header_text_color, $text_color, $text_font, $text_regular, $text_small, $table_color_1, $table_color_2, $link_color, $error_message, $header_background, $menu_background, $active_scheme, $type )
{
  echo "            <SCRIPT language=\"JavaScript\">\n"
     . "              function\n"
     . "              CheckForm()\n"
     . "              {\n"
     . "                if (document.scheme_admin.scheme_name.value == '')\n"
     . "                  {\n"
     . "                    alert('The \'Scheme Name\' field is mandatory!');\n"
     . "                    document.scheme_admin.scheme_name.focus();\n"
     . "                    return false;\n"
     . "                  }\n"
     . "                if (document.scheme_admin.scheme_desc.value == '')\n"
     . "                  {\n"
     . "                    alert('The \'Scheme Description\' field is mandatory!');\n"
     . "                    document.scheme_admin.scheme_desc.focus();\n"
     . "                    return false;\n"
     . "                  }\n"
     . "                if (document.scheme_admin.background_color.value == '')\n"
     . "                  {\n"
     . "                    alert('The \'Background Color\' field is mandatory!');\n"
     . "                    document.scheme_admin.background_color.focus();\n"
     . "                    return false;\n"
     . "                  }\n"
     . "                if (document.scheme_admin.table_border_color.value == '')\n"
     . "                  {\n"
     . "                    alert('The \'Table Border Color\' field is mandatory!');\n"
     . "                    document.scheme_admin.table_border_color.focus();\n"
     . "                    return false;\n"
     . "                  }\n"
     . "                if (document.scheme_admin.table_border_size.value == '')\n"
     . "                  {\n"
     . "                    alert('The \'Table Border Size\' field is mandatory!');\n"
     . "                    document.scheme_admin.table_border_size.focus();\n"
     . "                    return false;\n"
     . "                  }\n"
     . "                if (document.scheme_admin.header_background.value == '')\n"
     . "                  {\n"
     . "                    alert('The \'Header Background Color\' field is mandatory!');\n"
     . "                    document.scheme_admin.header_background.focus();\n"
     . "                    return false;\n"
     . "                  }\n"
     . "                if (document.scheme_admin.menu_background.value == '')\n"
     . "                  {\n"
     . "                    alert('The \'Menu Background Color\' field is mandatory!');\n"
     . "                    document.scheme_admin.menu_background.focus();\n"
     . "                    return false;\n"
     . "                  }\n"
     . "                if (document.scheme_admin.text_color.value == '')\n"
     . "                  {\n"
     . "                    alert('The \'Text Color\' field is mandatory!');\n"
     . "                    document.scheme_admin.text_color.focus();\n"
     . "                    return false;\n"
     . "                  }\n"
     . "                if (document.scheme_admin.text_font.value == '')\n"
     . "                  {\n"
     . "                    alert('The \'Font Face\' field is mandatory!');\n"
     . "                    document.scheme_admin.text_font.focus();\n"
     . "                    return false;\n"
     . "                  }\n"
     . "                if (document.scheme_admin.text_small.value == '')\n"
     . "                  {\n"
     . "                    alert('The \'Small Font Size\' field is mandatory!');\n"
     . "                    document.scheme_admin.text_small.focus();\n"
     . "                    return false;\n"
     . "                  }\n"
     . "                if (document.scheme_admin.text_regular.value == '')\n"
     . "                  {\n"
     . "                    alert('The \'Regular Font Size\' field is mandatory!');\n"
     . "                    document.scheme_admin.text_regular.focus();\n"
     . "                    return false;\n"
     . "                  }\n"
     . "                if (document.scheme_admin.link_color.value == '')\n"
     . "                  {\n"
     . "                    alert('The \'Link Color\' field is mandatory!');\n"
     . "                    document.scheme_admin.link_color.focus();\n"
     . "                    return false;\n"
     . "                  }\n"
     . "                if (document.scheme_admin.table_header_background.value == '')\n"
     . "                  {\n"
     . "                    alert('The \'Table Header Background Color\' field is mandatory!');\n"
     . "                    document.scheme_admin.table_header_background.focus();\n"
     . "                    return false;\n"
     . "                  }\n"
     . "                if (document.scheme_admin.table_header_text_color.value == '')\n"
     . "                  {\n"
     . "                    alert('The \'Table Header Text Color\' field is mandatory!');\n"
     . "                    document.scheme_admin.table_header_text_color.focus();\n"
     . "                    return false;\n"
     . "                  }\n"
     . "                if (document.scheme_admin.table_color_1.value == '')\n"
     . "                  {\n"
     . "                    alert('The \'Alternating Table Color #1\' field is mandatory!');\n"
     . "                    document.scheme_admin.table_color_1.focus();\n"
     . "                    return false;\n"
     . "                  }\n"
     . "                if (document.scheme_admin.table_color_2.value == '')\n"
     . "                  {\n"
     . "                    alert('The \'Alternating Table Color #2\' field is mandatory!');\n"
     . "                    document.scheme_admin.table_color_2.focus();\n"
     . "                    return false;\n"
     . "                  }\n"
     . "                if (document.scheme_admin.error_message.value == '')\n"
     . "                  {\n"
     . "                    alert('The \'Error Message Color\' field is mandatory!');\n"
     . "                    document.scheme_admin.error_message.focus();\n"
     . "                    return false;\n"
     . "                  }\n"
     . "                return true;\n"
     . "              }\n"
     . "            </SCRIPT>\n"
     . "            <FORM action=\"?pid=scheme_admin\" method=\"POST\" name=\"scheme_admin\">\n"
     . "              <TABLE cellpadding=\"5\" cellspacing=\"0\" width=\"100%\" border class=\"table_border\">\n"
     . "                <TR class=\"table_header\"><TD colspan=\"2\">Scheme Administration</TD></TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" valign=\"top\"><B>Scheme Name:</B></TD>\n"
     . "                  <TD width=\"50%\">\n"
     . "                    <INPUT type=\"text\" name=\"scheme_name\" value=\"$scheme_name\" size=\"50\" maxlength=\"64\">\n"
     . "                  </TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_2 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" valign=\"top\"><B>Scheme Description:</B></TD>\n"
     . "                  <TD width=\"50%\">\n"
     . "                    <TEXTAREA name=\"scheme_desc\" rows=\"5\" cols=\"40\">$scheme_desc</TEXTAREA>\n"
     . "                  </TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" valign=\"top\"><B>Background Color:</B></TD>\n"
     . "                  <TD width=\"50%\">\n"
     . "                    <INPUT type=\"text\" name=\"background_color\" value=\"$background_color\" size=\"10\" maxlength=\"7\">\n"
     . "                  </TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_2 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" valign=\"top\"><B>Table Border Color:</B></TD>\n"
     . "                  <TD width=\"50%\">\n"
     . "                    <INPUT type=\"text\" name=\"table_border_color\" value=\"$table_border_color\" size=\"10\" maxlength=\"7\">\n"
     . "                  </TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" valign=\"top\"><B>Table Border Size:</B></TD>\n"
     . "                  <TD width=\"50%\">\n"
     . "                    <INPUT type=\"text\" name=\"table_border_size\" value=\"$table_border_size\" size=\"4\" maxlength=\"2\">\n"
     . "                  </TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_2 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" valign=\"top\"><B>Header Background Color:</B></TD>\n"
     . "                  <TD width=\"50%\">\n"
     . "                    <INPUT type=\"text\" name=\"header_background\" value=\"$header_background\" size=\"10\" maxlength=\"7\">\n"
     . "                  </TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" valign=\"top\"><B>Menu Background Color:</B></TD>\n"
     . "                  <TD width=\"50%\">\n"
     . "                    <INPUT type=\"text\" name=\"menu_background\" value=\"$menu_background\" size=\"10\" maxlength=\"7\">\n"
     . "                  </TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_2 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" valign=\"top\"><B>Text Color:</B></TD>\n"
     . "                  <TD width=\"50%\">\n"
     . "                    <INPUT type=\"text\" name=\"text_color\" value=\"$text_color\" size=\"10\" maxlength=\"7\">\n"
     . "                  </TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" valign=\"top\"><B>Font Face:</B></TD>\n"
     . "                  <TD width=\"50%\">\n"
     . "                    <INPUT type=\"text\" name=\"text_font\" value=\"$text_font\" size=\"50\" maxlength=\"64\">\n"
     . "                  </TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_2 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" valign=\"top\"><B>Small Font Size:</B></TD>\n"
     . "                  <TD width=\"50%\">\n"
     . "                    <INPUT type=\"text\" name=\"text_small\" value=\"$text_small\" size=\"4\" maxlength=\"2\">\n"
     . "                  </TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" valign=\"top\"><B>Regular Font Size:</B></TD>\n"
     . "                  <TD width=\"50%\">\n"
     . "                    <INPUT type=\"text\" name=\"text_regular\" value=\"$text_regular\" size=\"4\" maxlength=\"2\">\n"
     . "                  </TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_2 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" valign=\"top\"><B>Link Color:</B></TD>\n"
     . "                  <TD width=\"50%\">\n"
     . "                    <INPUT type=\"text\" name=\"link_color\" value=\"$link_color\" size=\"10\" maxlength=\"7\">\n"
     . "                  </TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" valign=\"top\"><B>Table Header Background Color:</B></TD>\n"
     . "                  <TD width=\"50%\">\n"
     . "                    <INPUT type=\"text\" name=\"table_header_background\" value=\"$table_header_background\" size=\"10\" maxlength=\"7\">\n"
     . "                  </TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_2 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" valign=\"top\"><B>Table Header Text Color:</B></TD>\n"
     . "                  <TD width=\"50%\">\n"
     . "                    <INPUT type=\"text\" name=\"table_header_text_color\" value=\"$table_header_text_color\" size=\"10\" maxlength=\"7\">\n"
     . "                  </TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" valign=\"top\"><B>Alternating Table Color #1:</B></TD>\n"
     . "                  <TD width=\"50%\">\n"
     . "                    <INPUT type=\"text\" name=\"table_color_1\" value=\"$table_color_1\" size=\"10\" maxlength=\"7\">\n"
     . "                  </TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_2 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" valign=\"top\"><B>Alternating Table Color #2:</B></TD>\n"
     . "                  <TD width=\"50%\">\n"
     . "                    <INPUT type=\"text\" name=\"table_color_2\" value=\"$table_color_2\" size=\"10\" maxlength=\"7\">\n"
     . "                  </TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" valign=\"top\"><B>Error Message Color:</B></TD>\n"
     . "                  <TD width=\"50%\">\n"
     . "                    <INPUT type=\"text\" name=\"error_message\" value=\"$error_message\" size=\"10\" maxlength=\"7\">\n"
     . "                  </TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_2 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" valign=\"top\"><B>Active Scheme:</B></TD>\n"
     . "                  <TD width=\"50%\">\n";

  /* If it's the active scheme, then put a check in the box */
  if ($active_scheme == 1)
    $checked = " checked";
  else
    $checked = "";

  /* An finish off displaying the page */
  echo "                    <INPUT type=\"checkbox\" name=\"active_scheme\" value=\"1\"$checked> Scheme is active?\n"
     . "                  </TD>\n"
     . "                </TR>\n"
     . "              </TABLE>\n"
     . "              <INPUT type=\"hidden\" name=\"scheme_id\" value=\"$scheme_id\">\n"
     . "              <INPUT type=\"hidden\" name=\"type\" value=\"$type\">\n"
     . "              <INPUT type=\"hidden\" name=\"old_name\" value=\"$scheme_name\">\n"
     . "              <CENTER><BR><INPUT type=\"submit\" value=\"Preview Information\" name=\"action\" onClick=\"return CheckForm();\"></CENTER>\n"
     . "            </FORM>\n";
}

?>
