<?

/******************************************************************************\
 * Copyright (C) 2002 B Squared (b^2) by Josh Sherman <josh@cleancode.org>    *
 *                                                                            *
 * This script displays the contents for the 'View Replies' page.  Don't      *
 * forget the 12 space indent for all content pages.                          *
 *                                                                            *
 *                                 Last modified : September 13th, 2002 (JJS) *
\******************************************************************************/

/* srekcah eb-dluow yna pu kcuF */
$file_name = "view_forums.php";

/* Get the negative length of $file_name */
$file_name_length = -(strlen($file_name));

/* Check if the values match, if so, redirect */
if (substr($_SERVER['SCRIPT_NAME'], $file_name_length) == $file_name)
  header("Location: ../index.php");

/* Assign a value to the array, so it doesn't freak out is the user is an admin, but not a moderator */
$moderated_forums[] = "0";

/* Pull the list of forums this user is a moderator for */
$SQL     = "SELECT * FROM " . TABLE_PREFIX . "moderators WHERE user_id='$user_id';";
$results = ExeSQL($SQL);

/* Grab the data and load it in an array */
while ($row = mysql_fetch_array($results))
  $moderated_forums[] = $row["forum_id"];

/* Pull the forum id list from the database */
$SQL     = "SELECT forum_id FROM " . TABLE_PREFIX . "forums;";
$results = ExeSQL($SQL);

/* Grab the data and load it into an array */
while ($row = mysql_fetch_array($results))
  $forum_list[] = $row["forum_id"];

/* Pull the thread id list from the database */
$SQL     = "SELECT thread_id FROM " . TABLE_PREFIX . "threads;";
$results = ExeSQL($SQL);

/* Grab the data and load it into an array */
while ($row = mysql_fetch_array($results))
  $thread_list[] = $row["thread_id"];

/* If the forum doesn't exist, then halt */
if ( !in_array($forum_id, $forum_list) || !in_array($thread_id, $thread_list) )
  {
    echo "            <CENTER class=\"error_message\"><B>Malformed request detected!</CENTER>
<BR>\n";
    require ("./content/view_forums.php");
    return;
  }

/* Start off the table */
echo "            <TABLE width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n"
   . "              <TR>\n";

/* Pull the forum name from the database */
$SQL     = "SELECT * FROM " . TABLE_PREFIX . "forums WHERE forum_id='$forum_id';";
$results = ExeSQL($SQL);

/* Grab the data and print it on the screen */
while ($row = mysql_fetch_array($results))
  echo "                <TD class=\"regular_text\"><A href=\"?pid=view_forums\">" . BOARD_NAME . "</A> > <A href=\"?pid=view_threads&forum_id=" . $row["forum_id"] . "\">" . $row["forum_name"] . "</A> > ";

/* Pull the thread name from the database */
$SQL     = "SELECT * FROM " . TABLE_PREFIX . "threads WHERE thread_id=$thread_id;";
$results = ExeSQL($SQL);

/* Grab the data and throw it on the screen */
while ($row = mysql_fetch_array($results))
  echo "            " . $row["thread_title"] . "</TD>\n";

/* Add some options for the user */
echo "            <TD align=\"right\" class=\"regular_text\"><A href=\"?pid=post_thread&forum_id=$forum_id\">Post New Thread</A> | <A href=\"?pid=post_reply&thread_id=$thread_id&forum_id=$forum_id\">Post Reply</A></TD>\n"
   . "          </TR>\n"
   . "        </TABLE>\n"
   . "        <BR>\n";

/* Pull each thread name from the database */
$SQL     = "SELECT * FROM " . TABLE_PREFIX . "threads WHERE thread_id='$thread_id';";
$results = ExeSQL($SQL);

/* Grab the data and load it into a variable */
while ($row = mysql_fetch_array($results))
  $thread_topic = $row["thread_title"];

/* Build the HTML table (column headings) */
echo "            <TABLE cellspacing=\"0\" cellpadding=\"5\" width=\"100%\" border class=\"table_border\">\n"
   . "              <TR class=\"table_header\">\n"
   . "                <TD width=\"150\">Author</TD>\n"
   . "                <TD width=\"100%\">Thread: $thread_topic</TD>\n"
   . "              </TR>\n";

/* Pull the requested thread */
$SQL     = "SELECT *, DATE_FORMAT(thread_time, '%W, %M %e, %Y %r') AS nice_time FROM " . TABLE_PREFIX . "threads WHERE thread_id='$thread_id' ORDER BY thread_title;";
$results = ExeSQL($SQL);

/* Grab the data, and parse it out and do some other shit too! */
while ($row = mysql_fetch_array($results))
  {
    /* Pull each user name from the database */
    $SQL      = "SELECT * FROM " . TABLE_PREFIX . "users WHERE user_id='" . $row["user_id"] . "';";
    $results2 = ExeSQL($SQL);

    /* Grab the data and load it into an array */
    while ($row2 = mysql_fetch_array($results2))
      {
        $user_name     = $row2["user_name"];
        $user_location = $row2["user_location"];
      }

    /* Pull the total number of threads from the database */
    $SQL      = "SELECT COUNT(*) AS total_posts FROM " . TABLE_PREFIX . "threads WHERE user_id='" . $row["user_id"] . "';";
    $results2 = ExeSQL($SQL);

    /* Grab the data and load it into a variable */
    while ($row2 = mysql_fetch_array($results2))
      $total_posts = $row2["total_posts"];

    /* Pull the total number of replies from the database */
    $SQL      = "SELECT COUNT(*) AS total_posts FROM " . TABLE_PREFIX . "replies WHERE user_id='" . $row["user_id"] . "';";
    $results2 = ExeSQL($SQL);

    /* Grab the data and load it into a variable */
    while ($row2 = mysql_fetch_array($results2))
      $total_posts = $total_posts + $row2["total_posts"];

    echo "              <TR>\n"
       . "                <TD bgcolor=\"" . TABLE_COLOR_2 . "\" width=\"200\" valign=\"top\" nowrap>\n"
       . "                  <FONT class=\"regular_text\"><B><A href=\"?pid=view_profile&user=$user_name\">$user_name</A></B></FONT><BR><BR>\n"
       . "                  <FONT class=\"small_text\">\n"
       . "                    Total Posts: $total_posts<BR>\n";
 
    /* Show the user the tree of where they are located */
    if ($user_location != "") { echo "                    Location: $user_location<BR>\n"; }

    /* Display more of the table */
    echo "                  </FONT>\n"
       . "                </TD>\n"
       . "                <TD bgcolor=\"" . TABLE_COLOR_2 . "\" width=\"100%\" valign=\"top\">\n"
       . "                  <FONT class=\"small_text\">Posted " . $row["nice_time"] . "</FONT>\n"
       . "                  <HR>\n"
       . "                  <FONT class=\"regular_text\">\n"
       . "                    " . $row["thread_body"] . "\n"
       . "                  </FONT>\n";

    /* If the user is a moderator or an admin then ... */
    if ( ( $is_moderator != 0 && in_array($forum_id, $moderated_forums) ) || $is_admin != 0 )
      {
        /* Pull each user ip from the database */
        $SQL      = "SELECT user_ip FROM " . TABLE_PREFIX . "threads WHERE thread_id=" . $row["thread_id"] . ";";
        $results2 = ExeSQL($SQL);

        /* Grab the data and load it int a variable */
        while ($row2 = mysql_fetch_array($results2))
          $user_ip = $row2["user_ip"];

        /* Display the start of the mod / admin options */
        echo "                  <HR>\n"
           . "                  <TABLE width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\n"
           . "                    <TR class=\"small_text\">\n";

        /* Is the user a mod or an admin? */
        if ($is_admin == 0)
          $which = "mod";
        else
          $which = "admin";

        /* Display the form */
        echo "                      <FORM action=\"index.php\" method=\"POST\" name=\"" . $which . "_tools\">\n"
           . "                        <TD>\n"
           . "                          <INPUT type=\"hidden\" name=\"forum_id\" value=\"$forum_id\">\n";
  
        /* Check if the value is set */
        if (isset($row["reply_id"]))
          echo "                          <INPUT type=\"hidden\" name=\"reply_id\" value=\"" . $row["reply_id"] . "\">\n";

        /* Keep on truckin' */
        echo "                          <INPUT type=\"hidden\" name=\"thread_id\" value=\"" . $row["thread_id"] . "\">\n"
           . "                          <INPUT type=\"submit\" name=\"" . $which . "_action\" value=\"Delete Entire Thread\" onClick=\"return Confirm('Are you sure you want to delete this thread, and all of the associated replies?');\">\n"
           . "                        </TD>\n"
           . "                        <TD align=\"right\">\n"
           . "                          <B>IP:</B> " . $user_ip . "\n"
           . "                        </TD>\n"
           . "                      </FORM>\n"
           . "                    </TR>\n"
           . "                  </TABLE>\n";
      } 

    /* Close off the section */   
    echo "                </TD>\n"
       . "              </TR>\n";
  }

/* Pull each reply in reverse time order */
$SQL     = "SELECT *, DATE_FORMAT(reply_time, '%W, %M %e, %Y %r') AS nice_time FROM " . TABLE_PREFIX . "replies WHERE thread_id='$thread_id' ORDER BY reply_time;";
$results = ExeSQL($SQL);

/* Grab the data, and display it in the table */
while ($row = mysql_fetch_array($results))
  {
    /* Pull each user name from the database */
    $SQL      = "SELECT * FROM " . TABLE_PREFIX . "users WHERE user_id='" . $row["user_id"] . "';";
    $results2 = ExeSQL($SQL);

    /* Grab the data and load it into variables */
    while ($row2 = mysql_fetch_array($results2))
      {
        $user_name     = $row2["user_name"];
        $user_location = $row2["user_location"];
      }

    /* Pull the total number of posts */
    $SQL      = "SELECT COUNT(*) AS total_posts FROM " . TABLE_PREFIX . "threads WHERE user_id='" . $row["user_id"] . "';";
    $results2 = ExeSQL($SQL);

    /* Grab the data and load it into a variable */
    while ($row2 = mysql_fetch_array($results2))
      $total_posts = $row2["total_posts"];

    /* Pull the total number of replies */
    $SQL      = "SELECT COUNT(*) AS total_posts FROM " . TABLE_PREFIX . "replies WHERE user_id='" . $row["user_id"] . "';";
    $results2 = ExeSQL($SQL);

    /* Grab the data and load it into a variable */
    while ($row2 = mysql_fetch_array($results2))
      $total_posts = $total_posts + $row2["total_posts"];

    /* Display the user info */
    echo "              <TR>\n"
       . "                <TD bgcolor=\"" . TABLE_COLOR_1 . "\" width=\"150\" valign=\"top\" nowrap>\n"
       . "                  <FONT class=\"regular_text\"><B><A href=\"?pid=view_profile&user=$user_name\">$user_name</A></B></FONT><BR><BR>\n"
       . "                  <FONT class=\"small_text\">\n"
       . "                    Total Posts: $total_posts<BR>\n";

    /* If the user specified their location, then display it */
    if ($user_location != "") { echo "                    Location: $user_location<BR>\n"; }

    /* Keep going ... */
    echo "                  </FONT>\n"
       . "                </TD>\n"
       . "                <TD bgcolor=\"" . TABLE_COLOR_1 . "\" width=\"100%\">\n"
       . "                  <FONT class=\"small_text\">Posted " . $row["nice_time"] . "</FONT>\n"
       . "                  <HR>\n"
       . "                  <FONT class=\"regular_text\">\n"
       . "                    " . $row["reply_body"] . "\n"
       . "                  </FONT>\n";

    /* If the user is a mod or an admin, then display the extra options */
    if ( ( $is_moderator != 0 && in_array($forum_id, $moderated_forums) ) || $is_admin != 0 )
      {
        /* Pull the user's IP address */
        $SQL      = "SELECT user_ip FROM " . TABLE_PREFIX . "replies WHERE reply_id='" . $row["reply_id"] . "';";
        $results2 = ExeSQL($SQL);

        /* Grab the data and load it into a variable */
        while ($row2 = mysql_fetch_array($results2))
          $user_ip = $row2["user_ip"];

        /* Start displaying the options */
        echo "                  <HR>\n"
           . "                  <TABLE width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\n"
           . "                    <TR class=\"small_text\">\n";

        /* Is the user an admin or a moderator? */
        if ($is_admin == 0)
          $which = "mod";
        else
          $which = "admin";

        /* Display the form */
        echo "                      <FORM action=\"index.php\" method=\"POST\" name=\"" . $which . "_tools\">\n"
           . "                        <TD>\n"
           . "                          <INPUT type=\"hidden\" name=\"forum_id\" value=\"$forum_id\">\n"
           . "                          <INPUT type=\"hidden\" name=\"thread_id\" value=\"$thread_id\">\n"
           . "                          <INPUT type=\"hidden\" name=\"reply_id\" value=\"" . $row["reply_id"] . "\">\n"
           . "                          <INPUT type=\"submit\" name=\"" . $which . "_action\" value=\"Delete Reply\" onClick=\"return Confirm('Are you sure you want to delete this reply?');\">\n"
           . "                        </TD>\n"
           . "                        <TD align=\"right\" valign=\"middle\">\n"
           . "                          <B>IP:</B> $user_ip\n"
           . "                        </TD>\n"
           . "                      </FORM>\n"
           . "                    </TR>\n"
           . "                  </TABLE>\n";
      }

    /* Close out the section */
    echo "                </TD>\n"
       . "              </TR>\n";
  }

/* Let's get the hell out of dodge! */
echo "            </TABLE>\n";

?>
