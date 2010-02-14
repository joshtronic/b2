<?

/******************************************************************************\
 * Copyright (C) 2002 B Squared (b^2) by Josh Sherman <josh@cleancode.org>    *
 *                                                                            *
 * This script displays the contents for the 'Login' page.  Don't forget the  *
 * forget the 12 space indent for all content pages.                          *
 *                                                                            *
 *                                 Last modified : September 13th, 2002 (JJS) *
\******************************************************************************/

/* Run this stuff so people can't call this file directly */
$file_name = "login.php";

/* Get the negative length of $file_name */
$file_name_length = -(strlen($file_name));

/* Check if the values match, if so, redirect */
if (substr($_SERVER['SCRIPT_NAME'], $file_name_length) == $file_name)
  header("Location: ../index.php");

/* If the user performed a bad login, then tell them */
if ($login == "failed")
  echo "            <CENTER class=\"error_message\">Bad login credentials, try again.</CENTER><BR>";

/* Display the top part of the form */
echo "            <FORM method=\"POST\" action=\"index.php?pid=login\">\n"
   . "              <TABLE width=\"100%\" cellspacing=\"0\" cellpadding=\"5\" border class=\"table_border\">\n"
   . "                <TR class=\"table_header\">\n"
   . "                  <TD>Login</TD>\n"
   . "                </TR>\n"
   . "                <TR>\n"
   . "                  <TD align=\"center\" bgcolor=\"" . TABLE_COLOR_1 . "\">\n"
   . "                    <TABLE align=\"center\">\n"
   . "                      <TR class=\"regular_text\">\n"
   . "                        <TD align=\"right\">\n"
   . "                          Username:&nbsp;\n"
   . "                        </TD>\n"
   . "                        <TD>\n"
   . "                          <INPUT type=\"text\" name=\"username\">\n"
   . "                        </TD>\n"
   . "                      </TR>\n"
   . "                      <TR class=\"regular_text\">\n"
   . "                        <TD align=\"right\">\n"
   . "                          Password:&nbsp;\n"
   . "                        </TD>\n"
   . "                        <TD>\n"
   . "                          <INPUT type=\"password\" name=\"password\">\n"
   . "                        </TD>\n"
   . "                      </TR>\n"
   . "                      <TR>\n"
   . "                        <TD align=\"center\" colspan=\"2\">\n"
   . "                          <INPUT type=\"submit\" value=\"Login\">\n"
   . "                        </TD>\n"
   . "                      </TR>\n"
   . "                      <TR>\n"
   . "                        <TD align=\"center\" colspan=\"2\" class=\"small_text\">\n"
   . "                          <A href=\"?pid=forgot_password\">Forgot Password?</A>\n"
   . "                        </TD>\n"
   . "                    </TABLE>\n";

/* If $destination isn't NULL, then put it on the form */
if ($destination != "")
  echo "                    <INPUT type=\"hidden\" name=\"destination\" value=\"$destination\">\n";

/* Same with the $forum_id */
if ($forum_id != "")
  echo "                    <INPUT type=\"hidden\" name=\"forum_id\" value=\"$forum_id\">\n";

/* Same with the $thread_id */
if ($thread_id != "")
  echo "                    <INPUT type=\"hidden\" name=\"thread_id\" value=\"$thread_id\">\n";
 
/* Let's close off the form */
echo "                  </TD>\n"
   . "                </TR>\n"
   . "              </TABLE>\n"
   . "            </FORM>\n";

?>
