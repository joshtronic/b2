<?

/******************************************************************************\
 * Copyright (C) 2002 B Squared (b^2) by Josh Sherman <josh@cleancode.org>    *
 *                                                                            *
 * This script displays the contents for the 'General Administration' page.   *
 * Don't forget the 12 space indent for all content pages.                    *
 *                                                                            *
 *                                 Last modified : September 13th, 2002 (JJS) *
\******************************************************************************/

/* As always, don't let them access the file directly */
$file_name = "general_admin.php";

/* Get the negative length of $file_name */
$file_name_length = -(strlen($file_name));

/* Check if the values match, if so, redirect */
if (substr($_SERVER['SCRIPT_NAME'], $file_name_length) == $file_name)
  header("Location: ../index.php");

/* Grab the veriables held by superglobals */
$board_name  = GetVars("board_name");
$title_image = GetVars("title_image");
$action      = GetVars("action");

/* Parse any user input */
CheckVars(&$step, 1);
CheckVars(&$board_name, 64);
CheckVars(&$title_image, 128);

/* Make sure someone isn't trying to feed the step number via the querystring */
if ( $step == "" || ( $step != 1 && $step != 2 && $step != 3 && $step != 4 ) )
  $step = 1;

/* Make sure the user isn't feeding information via the query string, thwart all attempts!! */
if ( ( ( $board_name == "" || $title_image == "" ) && ( $step == 3 || $step == 4 ) ) ||
     ( ( $step == 1 && $QUERY_STRING != "pid=general_admin" ) ||
       ( $step == 2 && $QUERY_STRING != "pid=general_admin" ) ||
       ( $step == 3 && $QUERY_STRING != "pid=general_admin" ) ||
       ( $step == 4 && $QUERY_STRING != "pid=general_admin" ) ) || 
       ( ( $step != 1 && $step != 2 ) &&
         ( strlen(trim($board_name)) == 0 || strlen(trim($title_image)) == 0 ) ) )

  {
    /* Give them an error if they are */
    echo "            <CENTER class=\"error_message\">Malformed request detected!</CENTER><BR>\n";
    $step = 1;
  }

/* Determine which step to use */
if ($action == "Edit Properties")
  $step = 2;
else if ($action == "Preview Properties")
  $step = 3;
else if ($action == "Submit Properties")
  $step = 4;

/* Strip out all escape characters */
if ( $step == 3 || $step == 4 )
  $board_name  = stripslashes(strip_tags($board_name));

/* Display the desired step */
switch ($step)
  {
    /* Show the forum list */
    default:
    case 1:
      ShowProperties();
      break;

    /* Show edit form */
    case 2:
      ShowPropertyForm( $board_name, $title_image );
      break;

    /* Show preview */
    case 3:
      echo "            <FORM action=\"index.php?pid=general_admin\" method=\"POST\" name=\"general_admin\">\n"
         . "              <TABLE cellpadding=\"5\" cellspacing=\"0\" width=\"100%\" border class=\"table_border\">\n"
         . "                <TR class=\"table_header\">\n"
         . "                  <TD colspan=\"2\">Properties Preview</TD>\n"
         . "                </TR>\n"
         . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
         . "                  <TD width=\"25%\" valign=\"top\"><B>Board Name:</B></TD>\n"
         . "                  <TD width=\"50%\">\n"
         . "                    $board_name\n"
         . "                    <INPUT type=\"hidden\" name=\"board_name\" value=\"$board_name\">\n"
         . "                  </TD>\n"
         . "                </TR>\n"
         . "                <TR bgcolor=\"" . TABLE_COLOR_2 . "\" class=\"regular_text\">\n"
         . "                  <TD width=\"25%\" valign=\"top\"><B>Title Image:</B></TD>\n"
         . "                  <TD width=\"50%\">\n"
         . "                    $title_image<BR><BR>\n"
         . "                    <TABLE border class=\"table_border\" cellspacing=\"0\" cellpadding=\"0\">\n"
         . "                      <TR>\n"
         . "                        <TD><IMG src=\"$title_image\" border=\"0\"></TD>\n"
         . "                      </TR>\n"
         . "                    </TABLE>\n"
         . "                    <INPUT type=\"hidden\" name=\"title_image\" value=\"$title_image\">\n"
         . "                  </TD>\n"
         . "                </TR>\n"
         . "              </TABLE>\n"
         . "              <CENTER>\n"
         . "                <BR>\n"
         . "                <INPUT type=\"submit\" value=\"Edit Properties\" name=\"action\">\n"
         . "                &nbsp;\n"
         . "                <INPUT type=\"submit\" value=\"Submit Properties\" name=\"action\">\n"
         . "              </CENTER>\n"
         . "            </FORM>\n";
      break;

    /* Add the new forum to the database */
    case 4:
      /* Check if the page was POSTed */
      if ( $REQUEST_METHOD == "POST" )
        {
          /* Set the error to zero */
          $no_err = 0;

          /* Delete the existing properties */
          $SQL     = "DELETE FROM " . TABLE_PREFIX . "properties;";
          $results = ExeSQL($SQL);

          /* Add the new ones in */
          $SQL     = "INSERT INTO " . TABLE_PREFIX . "properties (board_name, title_image) VALUES ('$board_name', '$title_image');";
          $results = ExeSQL($SQL);
 
          /* Let the user know what's up, then show the properties */
          echo "            <CENTER class=\"normal_message\">The properties have successfully been modified!</CENTER><BR>\n";
          ShowProperties();
          return;
        }
      else
        ShowMessageForm( $message_name, $message_body, $message_id );
      break;
  }

/*
 * Show the existing properties and values
 */

function
ShowProperties()
{
  /* Spit out the top part of the HTML */
  echo "            <FORM action=\"?pid=general_admin\" method=\"POST\">\n"
     . "              <TABLE cellpadding=\"5\" cellspacing=\"0\" width=\"100%\" border class=\"table_border\">\n"
     . "                <TR class=\"table_header\">\n"
     . "                  <TD colspan=\"2\">\n"
     . "                    <TABLE cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\">\n"
     . "                      <TR>\n"
     . "                        <TD class=\"table_header\">\n"
     . "                          General Administration</B>&nbsp;\n"
     . "                        </TD>\n"
     . "                      </TR>\n"
     . "                    </TABLE>\n"
     . "                  </TD>\n"
     . "                </TR>\n";

  /* Set the color */
  $the_color = TABLE_COLOR_2;

  /* Pull the properties */
  $SQL = "SELECT * FROM " . TABLE_PREFIX . "properties;";
  $results = ExeSQL($SQL);

  /* Grab the data, and assign it to variables */
  while ($row = mysql_fetch_array($results))
    {
      $board_name  = $row["board_name"];
      $title_image = $row["title_image"];
    }

  /* Display the properties */
  echo "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\">\n"
     . "                  <TD class=\"regular_text\" width=\"25%\">\n"
     . "                    <B>Board Name:</B><BR>\n"
     . "                  </TD>\n"
     . "                  <TD class=\"regular_text\" width=\"50%\">\n"
     . "                    $board_name<BR>\n"
     . "                  </TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_2 . "\">\n"
     . "                  <TD class=\"regular_text\" width=\"25%\" valign=\"top\">\n"
     . "                    <B>Title Image:</B><BR>\n"
     . "                  </TD>\n"
     . "                  <TD class=\"regular_text\" width=\"50%\">\n"
     . "                    $title_image<BR><BR>\n"
     . "                    <TABLE border class=\"table_border\" cellspacing=\"0\" cellpadding=\"0\">\n"
     . "                      <TR>\n"
     . "                        <TD><IMG src=\"$title_image\" border=\"0\"></TD>\n"
     . "                      </TR>\n"
     . "                    </TABLE>\n"
     . "                  </TD>\n"
     . "                </TR>\n"
     . "              </TABLE>\n"
     . "              <CENTER><BR>\n"
     . "                <INPUT type=\"hidden\" name=\"board_name\" value=\"$board_name\">\n"
     . "                <INPUT type=\"hidden\" name=\"title_image\" value=\"$title_image\">\n"
     . "                <INPUT type=\"submit\" name=\"action\" value=\"Edit Properties\">\n"
     . "              </CENTER>\n"
     . "            </FORM>\n";
}

/*
 * Show the form to edit the properties
 */

function
ShowPropertyForm( $board_name, $title_image )
{
  /* What are you waiting for, show it already! */
  echo "            <FORM action=\"index.php?pid=general_admin\" method=\"POST\" name=\"general_admin\">\n"
     . "              <TABLE cellpadding=\"5\" cellspacing=\"0\" width=\"100%\" border class=\"table_border\">\n"
     . "                <TR class=\"table_header\">\n"
     . "                  <TD colspan=\"2\">General Administration</TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" valign=\"top\"><B>Board Name:</B></TD>\n"
     . "                  <TD width=\"50%\">\n"
     . "                    <INPUT type=\"text\" name=\"board_name\" value=\"$board_name\" size=\"50\" maxlength=\"64\">\n"
     . "                  </TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_2 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" valign=\"top\"><B>Title Image:</B></TD>\n"
     . "                  <TD width=\"50%\">\n"
     . "                    <INPUT type=\"text\" name=\"title_image\" value=\"$title_image\" size=\"50\" maxlength=\"128\">\n"
     . "                  </TD>\n"
     . "                </TR>\n"
     . "              </TABLE>\n"
     . "              <CENTER><BR><INPUT type=\"submit\" value=\"Preview Properties\" name=\"action\"></CENTER>\n"
     . "            </FORM>\n";
}

?>
