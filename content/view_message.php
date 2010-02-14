<?

/******************************************************************************\
 * Copyright (C) 2002 B Squared (b^2) by Josh Sherman <josh@cleancode.org>    *
 *                                                                            *
 * This script displays the contents for the 'View Message' page.  Don't      *
 * forget the 12 space indent for all content pages.                          *
 *                                                                            *
 *                                 Last modified : September 24th, 2002 (JJS) *
\******************************************************************************/

/* Redirect the person if they call this file directly */
$file_name = "view_message.php";

/* Get the negative length of $file_name */
$file_name_length = -(strlen($file_name));

/* Check if the values match, if so, redirect */
if (substr($_SERVER['SCRIPT_NAME'], $file_name_length) == $file_name)
  header("Location: ../index.php");

/* Pull the named message */
if ($message == "faq")
  {
    require("./language/faq.php");
    $message_name = FAQ_TITLE;
    $message_body = FREQUENTLY_ASKED_QUESTIONS;
  }
else
  header("Location: ../index.php");

/* Display the message */
echo "            <TABLE cellspacing=\"0\" cellpadding=\"5\" width=\"100%\" border class=\"table_border\">\n"
   . "              <TR>\n"
   . "                <TD class=\"table_header\">$message_name</TD>\n"
   . "              </TR>\n"
   . "              <TR bgcolor=\"" . TABLE_COLOR_1 . "\">\n"
   . "                <TD class=\"regular_text\">\n"
   . "                  $message_body\n"
   . "                </TD>\n"
   . "              </TR>\n"
   . "            </TABLE>\n";

?>
