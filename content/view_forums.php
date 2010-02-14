<?

/******************************************************************************\
 * Copyright (C) 2002 B Squared (b^2) by Josh Sherman <josh@cleancode.org>    *
 *                                                                            *
 * This script displays the contents for the 'View Forums' page.  Don't       *
 * forget the 12 space indent for all content pages.                          *
 *                                                                            *
 *                                 Last modified : September 21st, 2002 (JJS) *
\******************************************************************************/

/* Deter hackers */
$file_name = "view_forums.php";

/* Get the negative length of $file_name */
$file_name_length = -(strlen($file_name));

/* Check if the values match, if so, redirect */
if (substr($_SERVER['SCRIPT_NAME'], $file_name_length) == $file_name)
  header("Location: ../index.php");

/* Pull the total number of users */
$SQL     = "SELECT COUNT(*) as total_users FROM " . TABLE_PREFIX . "users;";
$results = ExeSQL($SQL);

/* Start off the table to divide everything */
echo "            <TABLE border=\"0\" width=\"100%\">\n"
   . "              <TR class=\"small_text\">\n"
   . "                <TD>\n";

/* Grab the data, and display it */
while ($row = mysql_fetch_array($results))
  echo "                    Registered Members: <B>" . $row["total_users"] . "</B><BR>\n";

/* Start the number of posts at zero */
$total_posts = 0;

/* Pull the total number of threads */
$SQL     = "SELECT COUNT(*) AS total_posts FROM " . TABLE_PREFIX . "threads;";
$results = ExeSQL($SQL);

/* Grab the data and load it in a variable */
while ($row = mysql_fetch_array($results))
  $total_posts = $row["total_posts"];

/* Pull the total number of replies */
$SQL     = "SELECT COUNT(*) AS total_posts FROM " . TABLE_PREFIX . "replies;";
$results = ExeSQL($SQL);

/* Grab the data, and load it in a variable */
while ($row = mysql_fetch_array($results))
  $total_posts = $total_posts + $row["total_posts"];

/* Display the total number of posts */
echo "                  Total Posts: <B>$total_posts</B><BR>\n";

/* Pull the most recent user added to the database */
$SQL     = "SELECT * FROM " . TABLE_PREFIX . "users ORDER BY user_id DESC LIMIT 1;";
$results = ExeSQL($SQL);

/* Grab the data, and throw it on the screen */
while ($row = mysql_fetch_array($results))
  echo "                  Welcome to our newest member, <B><A href=\"?pid=view_profile&user=" . $row["user_name"] . "\">" . $row["user_name"] . "</A></B>.<P>\n";

/* Show the current date / time, then close out the table */
echo "                </TD>\n"
   . "                <TD align=\"right\" valign=\"bottom\">\n"
   . "                  " . date("l, F jS, Y\<\B\R\>g:i:s A T") . "\n"
   . "                </TD>\n"
   . "              </TR>\n"
   . "            </TABLE>\n";

/* Pull the forum list */
$SQL     = "SELECT DISTINCT(forum_id) FROM " . TABLE_PREFIX . "forums;";
$results = ExeSQL($SQL);

/* Grab the data, and load it in an array */
while ($row = mysql_fetch_array($results))
  $forum_list[] = $row["forum_id"];

/* Loop through the forum list and count the number of threads and replies, loading both into their respective arrays */
for ( $i = 0; $i < count($forum_list); $i++ )
  {
    /* Set the current forum in the loop */
    $current_forum = $forum_list[$i];

    /* Pull the total number of threads for the forum */
    $SQL     = "SELECT COUNT(*) AS total_threads FROM " . TABLE_PREFIX . "threads WHERE forum_id='$current_forum';";
    $results = ExeSQL($SQL);

    /* Grab the data, and load it in an array */
    while ($row = mysql_fetch_array($results))
      $total_threads[] = $row["total_threads"];

    /* Pull the total number of replies for the forum */
    $SQL     = "SELECT COUNT(*) AS total_replies FROM " . TABLE_PREFIX . "replies WHERE forum_id='$current_forum';";
    $results = ExeSQL($SQL);

    /* Grab the data, and load it in an array */
    while ($row = mysql_fetch_array($results))
      $total_replies[] = $row["total_replies"];
  }

/* Build the HTML table (column headings) */
echo "            <TABLE cellspacing=\"0\" cellpadding=\"5\" width=\"100%\" border class=\"table_border\">\n"
   . "              <TR class=\"table_header\">\n"
   . "                <TD>Forum</TD>\n"
   . "                <TD align=\"center\" width=\"1\">Threads</TD>\n"
   . "                <TD align=\"center\" width=\"1\">Replies</TD>\n"
   . "                <TD align=\"center\" width=\"\" nowrap>Latest Post</TD>\n"
   . "                <TD align=\"center\" width=\"\">Moderator</TD>\n"
   . "              </TR>\n";

/* Pull each forum name in alpabetical order */
$SQL     = "SELECT * FROM " . TABLE_PREFIX . "forums ORDER BY forum_order, forum_name;";
$results = ExeSQL($SQL);

/* Grab the data, do crap to it and and display it in the table */
while ($row = mysql_fetch_array($results))
  {
    /* Determine how many posts there are for that forum */
    $forum_key   = array_search($row["forum_id"], $forum_list);
    $all_threads = $total_threads[$forum_key];
    $all_replies = $total_replies[$forum_key];

    /* If there are no posts, then just set the value to "--" instead of "0" which I find unpleasant to the eye */
    if ( $all_threads == "" || $all_threads == 0 )
      $all_threads = "--";

    /* If there are no posts, then just set the value to "--" instead of "0" which I find unpleasant to the eye */
    if ( $all_replies == "" || $all_replies == 0 )
      $all_replies = "--";

    /* Null out these variables */
    $moderator_id = "";
    $moderators   = "";

    /* Grab the moderators */
    $SQL      = "SELECT * FROM " . TABLE_PREFIX . "moderators WHERE forum_id=" . $row["forum_id"] . ";";
    $results2 = ExeSQL($SQL);

    /* Give the arrays default values */
    $moderator_id[] = "";
    //$moderators[]   = "";

    /* Grab the data, and add it to an array */
    while ($row2 = mysql_fetch_array($results2))
      $moderator_id[] = $row2["user_id"];

    /* Loop through the array */
    for ( $i = 0; $i < sizeof($moderator_id); $i++ )
      {
        /* Grab the moderators */
        $SQL      = "SELECT * FROM " . TABLE_PREFIX . "users WHERE user_id='" . $moderator_id[$i] . "';";
        $results2 = ExeSQL($SQL);

        /* Grab the data, and add it to an array */
        while ($row2 = mysql_fetch_array($results2))
          $moderators[] = $row2["user_name"];
      }

    /* Clear out the variables before we determine the most recent post for the forum */
    $latest_post = "";
    $latest_user = "";
    $thread_time = "";
    $thread_user = "";
    $reply_time  = "";
    $reply_user  = "";

    /* Grab the most recent thread */
    $SQL      = "SELECT *, DATE_FORMAT(thread_time, '%W, %M %e, %Y<BR>%r') AS nice_time FROM " . TABLE_PREFIX . "threads WHERE forum_id=" . $row["forum_id"] . " ORDER BY thread_id DESC LIMIT 1 ;";
    $results2 = ExeSQL($SQL);

    /* Grab the data, and add it to variables */
    while ($row2 = mysql_fetch_array($results2))
      {
        $thread_time = $row2["nice_time"];
        $thread_user = $row2["user_id"];
      }

    /* Grab the most recent replies */
    $SQL      = "SELECT *, DATE_FORMAT(reply_time, '%W, %M %e, %Y<BR>%r') AS nice_time FROM " . TABLE_PREFIX . "replies WHERE forum_id=" . $row["forum_id"] . " ORDER BY reply_id DESC LIMIT 1 ;";
    $results2 = ExeSQL($SQL);

    /* Grab the data, and load it into variables */
    while ($row2 = mysql_fetch_array($results2))
      {
        $reply_time = $row2["nice_time"];
        $reply_user = $row2["user_id"];
      }
  
    /* If the thread is more recent than the reply */
    if ($thread_time > $reply_time)
      {
        /* Set the thread as the most recent */
        $latest_post = $thread_time;
        $latest_user = $thread_user;
      }
    else
      {
        /* Set the reply as the most recent */
        $latest_post = $reply_time;
        $latest_user = $reply_user;
      }

    /* Grab the most recent user */
    $SQL      = "SELECT * FROM " . TABLE_PREFIX . "users WHERE user_id='" . $latest_user . "';";
    $results2 = ExeSQL($SQL);

    /* Grab the data, and load it in a variable */
    while ($row2 = mysql_fetch_array($results2))
      $latest_user = $row2["user_name"];

    /* Display more stuff on the screen */
    echo "              <TR>\n"
       . "                <TD bgcolor=\"" . TABLE_COLOR_1 . "\"><FONT class=\"regular_text\"><A href=\"?pid=view_threads&forum_id=" . $row["forum_id"] . "\">" . $row["forum_name"] . "</A></FONT><BR><FONT class=\"small_text\">" . $row["forum_desc"] . "</FONT></TD>\n"
       . "                <TD align=\"center\" valign=\"middle\" bgcolor=\"" . TABLE_COLOR_2 . "\">\n"
       . "                  <FONT class=\"regular_text\">" . $all_threads . "</FONT>\n"
       . "                </TD>\n"
       . "                <TD align=\"center\" valign=\"middle\" bgcolor=\"" . TABLE_COLOR_1 . "\">\n"
       . "                  <FONT class=\"regular_text\">" . $all_replies . "</FONT>\n"
       . "                </TD>\n"
       . "                <TD valign=\"middle\" align=\"center\" bgcolor=\"" . TABLE_COLOR_2 . "\" nowrap>\n";

    /* If the latest post exists then display it */
    if ($latest_post != "")
      echo "                  <FONT class=\"small_text\">$latest_post by <B><A href=\"?pid=view_profile&user=$latest_user\">$latest_user</A></B></FONT><BR>\n";
    else 
      echo "                  <FONT class=\"regular_text\">--</FONT>\n";
   
    /* Finish off this section */
    echo "                </TD>\n"
       . "                <TD align=\"center\" valign=\"middle\" bgcolor=\"" . TABLE_COLOR_1 . "\" width=\"150\">\n"
       . "                  <FONT class=\"small_text\">";

    /* If there are moderators then show them */
    if (@isset($moderators[0]))
      { 
        /* Sort the list in alphabetical order */
        sort($moderators);

        /* Sort through the array */
        for ( $i = 0; $i < sizeof($moderators); $i++ )
          {
            /* Display the moderators */
            echo "<A href=\"?pid=view_profile&user={$moderators[$i]}\">{$moderators[$i]}</A>";
        
            /* Comma deliminate them */
            if ($i != (sizeof($moderators)) - 1)
              echo ", ";
          }

        /* Throw in a line break for good measure */
        echo "<BR>";
      }
    else
      echo "<FONT class=\"regular_text\">--</FONT>";

    /* Finish off this page! */
    echo "                  </FONT>\n"
       . "                </TD>\n"
       . "              </TR>\n";
  }

echo "            </TABLE>\n";

?>
