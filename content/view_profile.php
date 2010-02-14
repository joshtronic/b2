<?

/******************************************************************************\
 * Copyright (C) 2002 B Squared (b^2) by Josh Sherman <josh@cleancode.org>    *
 *                                                                            *
 * This script displays the contents for the 'View Profile' page.  Don't      *
 * forget the 12 space indent for all content pages.                          *
 *                                                                            *
 *                                 Last modified : September 24th, 2002 (JJS) *
\******************************************************************************/

/* Stop all direct access to this file!!! */
$file_name = "view_profile.php";

/* Get the negative length of $file_name */
$file_name_length = -(strlen($file_name));

/* Check if the values match, if so, redirect */
if (substr($_SERVER['SCRIPT_NAME'], $file_name_length) == $file_name)
  header("Location: ../index.php");

/* Grab the veriables held by superglobals */
$user  = $_GET['user'];

/* Parse any user input */
CheckVars(&$user, 64);

/* Pull the number of accounts with the specified username */
$SQL     = "SELECT COUNT(*) AS user_exists FROM " . TABLE_PREFIX . "users WHERE user_name='$user';";
$results = ExeSQL($SQL);

/* Grab the data and add it to a variable */
while ($row = mysql_fetch_array($results))
  $user_exists = $row["user_exists"];

/* If the user doesn't exist then ... */
if ($user_exists == 0)
  {
    /* Let the user know what's up, then redirect to the view forums page */
    echo "            <CENTER class=\"normal_message\">Sorry, there are no users by that name!</CENTER><BR><BR>\n";
    require("view_forums.php");
  }
else
  {
    /* Pull the information for the specified username */
    $SQL     = "SELECT * FROM " . TABLE_PREFIX . "users WHERE user_name='$user';";
    $results = ExeSQL($SQL);

    /* Grab the data, and add it to variables */
    while ($row = mysql_fetch_array($results))
      {
        $username   = $row["user_name"];
        $email      = $row["user_email"];
        $location   = $row["user_location"];
        $occupation = $row["user_occupation"];
        $homepage   = $row["user_homepage"];
        $picture    = $row["user_picture"];
        $interests  = $row["user_interests"];
        $aim        = $row["user_aim"];
        $icq        = $row["user_icq"];
        $yahoo      = $row["user_yahoo"];
      }

    /* Display the table header */
    echo "            <TABLE cellpadding=\"5\" cellspacing=\"0\" width=\"100%\" border class=\"table_border\">\n"
       . "              <TR class=\"table_header\">\n"
       . "                <TD colspan=\"2\">$username's Profile</TD>\n"
       . "              </TR>\n";

    /* Set the active color to the second color */
    $the_color = TABLE_COLOR_2;

    /* Preview the email section */
    PreviewSection ( $email, "Email", &$the_color );

    /* If the location isn't NULL, then preview it */
    if ( $location != "" )
      PreviewSection( $location, "Location", &$the_color );

    /* same with the occupation */
    if ( $occupation != "" )
      PreviewSection( $occupation, "Occupation", &$the_color );

    /* and the homepage */
    if ( $homepage != "" && $homepage != "http://" )
      PreviewSection( $homepage, "Homepage", &$the_color );

    /* AND the picture */
    if ( $picture != "" && $picture != "http://" )
      PreviewSection ( $picture, "Picture", &$the_color );

    /* Can't forget the interests */
    if ( $interests != "" )
      PreviewSection ( $interests, "Interests", &$the_color );

    /* And of course, the AIM name */
    if ( $aim != "" )
      PreviewSection ( $aim, "AOL Instant Messenger", &$the_color );

    /* Along with the ICQ UIN */
    if ( $icq != "" )
      PreviewSection ( $icq, "ICQ", &$the_color );

    /* And last, and IMHO least, the Yahoo! Pager */
    if ( $yahoo != "" )
      PreviewSection ( $yahoo, "Yahoo Pager", &$the_color );

    /* Close out the fuggin' table */
    echo "            </TABLE>\n";
  }

/*
 * This function lets you preview sections, and 
 * kills a lot of repetative, and messy code
 */
function
PreviewSection ( $section_value, $section_title, $the_color )
{
  /* Swap the colors */
  if ($the_color == TABLE_COLOR_1)
    $the_color = TABLE_COLOR_2;
  else
    $the_color = TABLE_COLOR_1;

  /* Display the section name */
  echo "              <TR bgcolor=\"$the_color\" class=\"regular_text\">\n"
     . "                <TD width=\"25%\" valign=\"top\"><B>$section_title:</B></TD>\n"
     . "                <TD width=\"50%\">\n"
     . "                  ";

  /* Jump to the section for the appropriate section */
  switch ($section_title)
    {
      /* Email section */
      case "Email":
        echo "<A href=\"mailto:$section_value\">$section_value</A>";
        break;

      /* Homepage section */
      case "Homepage":
        echo "<A href=\"$section_value\" target=\"_blank\">$section_value</A>";
        break;

      /* AIM Section*/
      case "AOL Instant Messenger":
        echo "$section_value ";
        $section_value = str_replace(" ", "", $section_value); 
      
        /* Add the cool links instead of just the AIM name */
        echo "(<A href=\"aim:addbuddy?screenname=$section_value\">Add Buddy</A>, <A href=\"aim:goim?screenname=$section_value&message=\">Send Message</A>)";
        break;

      /* Picture section */
      case "Picture":
        /* Grab the image size */
        $profile_img = @getimagesize($section_value);

        /* Set the caption */
        $image_caption = "Image size";

        /* If the width is larger than 320, then rectify the situation */
        if ($profile_img[0] > 320)
          $profile_img[0] = 320;

        /* Same with the height, but set it to 240 */
        if ($profile_img[1] > 240) 
          $profile_img[1] = 240; 

        /* If height's larger, then use the height, width larger, then use the width */
        if ($profile_img[0] > $profile_img[1])
          $scale_img = "height=\"$profile_img[1]\"";
        else
          $scale_img = "width=\"$profile_img[0]\"";

        /* Show the image!! */
        echo "                  <TABLE cellspacing=\"0\" cellpadding=\"0\" border class=\"table_border\"><TR><TD><A href=\"$section_value\" target=\"_blank\"><IMG src=\"$section_value\" $scale_img border=\"0\"></A></TD></TR></TABLE>\n";
        break;

      /* Not specified, then just display the value */
      default:
        echo "$section_value";
        break;
    }
  
  /* Finish it off */
  echo "</FONT>\n"
     . "                </TD>\n"
     . "              </TR>\n";
}

?>
