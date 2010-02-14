<?

/******************************************************************************\
 * Copyright (C) 2002 B Squared (b^2) by Josh Sherman <josh@cleancode.org>    *
 *                                                                            *
 * This script displays the contents for the 'Registation' page.  Don't       *
 * forget the 12 space indent for all content pages.                          *
 *                                                                            *
 *                                 Last modified : September 14th, 2002 (JJS) *
\******************************************************************************/

/* Call this file directly, get sent back */
$file_name = "register.php";

/* Get the negative length of $file_name */
$file_name_length = -(strlen($file_name));

/* Check if the values match, if so, redirect */
if (substr($_SERVER['SCRIPT_NAME'], $file_name_length) == $file_name)
  header("Location: ../index.php");

/* Grab the veriables held by super globals */
$username    = GetVars("username");
$password    = GetVars("password");
$email       = GetVars("email");
$location    = GetVars("location");
$occupation  = GetVars("occupation");
$homepage    = GetVars("homepage");
$picture     = GetVars("picture");
$interests   = GetVars("interests");
$aim         = GetVars("aim");
$icq         = GetVars("icq");
$yahoo       = GetVars("yahoo");
$signature   = GetVars("signature");
$include_sig = GetVars("include_sig");
$action      = GetVars("action");
$step        = GetVars("step");

/* Parse any user input */
CheckVars(&$step, 1);
CheckVars(&$username, 64);
CheckVars(&$password, 64);
CheckVars(&$confirm_password, 64);
CheckVars(&$email, 128);
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

/* Strip &nbsp; from the username */
$username = str_replace("&nbsp;", "", $username);

/* Check that the user isn't trying to mess with the $step variable */
if ( $step == "" || ( $step != 1 && $step != 2 && $step != 3 && $step != 4 ) )
  $step = 1;

/* Make sure the user isn't feeding information via the query string, thwart all attempts!! */
if ( ( ( $username == "" || $password == "" || $email == "" ) && ( $step == 3 || $step == 4 ) ) || 
     ( ( $step == 1 && $QUERY_STRING != "pid=register" ) ||
       ( $step == 2 && $QUERY_STRING != "pid=register&step=2" ) || 
       ( $step == 3 && $QUERY_STRING != "pid=register&step=3" ) || 
       ( $step == 4 && $QUERY_STRING != "pid=register" ) ) ||
       ( ( $step != 1 && $step != 2 ) &&
         ( strlen(trim($username)) == 0 || strlen(trim($password)) == 0 || strlen(trim($email)) == 0 ) ) )

  {
    /* If so, give them an error */
    echo "            <CENTER class=\"error_message\">Malformed request detected!</CENTER><BR>\n";
    $step = 1;
  }

/* Determine which step to go to */
if ($action == "Edit Information")
  $step = 2;
else if ($action == "Submit Information")
  $step = 4;

/* Parse some of the variables to ensure accurate values */
if ( $step == 2 && $homepage == "" )
  $homepage = "http://";

if ( $step == 2 && $picture == "" )
  $picture = "http://";

/* Strip out all escape characters */
if ($step == 2)
  {
    $username   = stripslashes(strip_tags($username));
    $password   = stripslashes(strip_tags($password));
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

/* Again, with some sig clean up */
if ($step == 3)
  {
    $username   = stripslashes(strip_tags($username));
    $password   = stripslashes(strip_tags($password));
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

/* This time, just signature clean up */
if ($step == 4)
  {
    $signature = htmlspecialchars($signature);
    $signature = str_replace("&lt;BR&gt;", "<BR>", $signature);
  }

/* To step, or not to step! */
switch ($step)
  {
    /* Display the TOS */
    default:
    case 1:
      /* Start displaying the TOS */
      echo "            <TABLE cellpadding=\"5\" cellspacing=\"0\" width=\"100%\" border class=\"table_border\">\n"
         . "              <TR class=\"table_header\">\n"
         . "                <TD>Usage Policy</TD>\n"
         . "              </TR>\n"
         . "              <TR>\n"
         . "                <TD bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
         . "                  Registration for our community is 100% free! If you agree to abide by our rules below, you should press the \"Agree\" button, which will enable you to register. If you do not agree, press the \"Cancel\" button.\n"
         . "                </TD>\n"
         . "              </TR>\n"
         . "              <TR>\n"
         . "                <TD bgcolor=\"" . TABLE_COLOR_2 . "\" class=\"regular_text\">\n";

      /* Grab the TOS */
      require("language/tos.php");

      /* Display the TOS */
      echo "                    " . TERMS_OF_SERVICE . "\n";

      /* Finish off the page */
      echo "                </TD>\n"
         . "              </TR>\n"
         . "            </TABLE>\n"
         . "            <CENTER class=\"regular_text\">\n"
         . "              <B><A href=\"?pid=register&step=2\">Agree</A> | <A href=\"?pid=view_forums\">Cancel</A></B>\n"
         . "            </CENTER>\n";
      break;

    /* Display the form for the user to fill out */
    case 2:
      ShowRegistrationForm( $username, $password, $confirm_password, $email, $location, $occupation, $homepage, $picture, $interests, $aim, $icq, $yahoo, $signature, $include_sig );
      break;

    /* Display the info the user supplied and prompt them to continue or edit */
    case 3:
      /* Line starts here, no cutting [or pasting ;)] */
      echo "            <FORM action=\"index.php?pid=register\" method=\"POST\" name=\"registration\">\n"
         . "              <TABLE cellpadding=\"5\" cellspacing=\"0\" width=\"100%\" border class=\"table_border\">\n"
         . "                <TR class=\"table_header\">\n"
         . "                  <TD colspan=\"2\">Registration Preview</TD>\n"
         . "                </TR>\n";

      /* Set the active color */      
      $the_color = TABLE_COLOR_2;

      /* Display the mandatory fields */
      PreviewSection ( $username, "Username", &$the_color );
      PreviewSection ( $password, "Password", &$the_color ); 
      PreviewSection ( $email, "Email", &$the_color );
     
      /* Display the optional fields, if they were filled in */
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
          /* Swap the colors */
          if ($the_color == TABLE_COLOR_1)
            $the_color = TABLE_COLOR_2;
          else
            $the_color = TABLE_COLOR_1;

          /* Start the section */
          echo "                <TR bgcolor=\"$the_color\" class=\"regular_text\">\n"
             . "                  <TD width=\"25%\" valign=\"top\"><B>Signature:</B></TD>\n"
             . "                  <TD width=\"50%\">\n"
             . "                    $signature<BR><BR>\n"
             . "                    <I>\n";

          /* Display if the signature will be added by default */
          if ($include_sig == 1)
            echo "                      You have chosen to include this signature on new posts.\n";
          else
            echo "                      You have chosen to not include this signature on new posts.\n";

          /* Finish off the section */
          echo "                    </I>\n"
             . "                    <INPUT type=\"hidden\" name=\"signature\" value=\"$signature\">\n"
             . "                    <INPUT type=\"hidden\" name=\"include_sig\" value=\"$include_sig\">\n"
             . "                  </TD>\n"
             . "                </TR>\n";
        }

      /* And then we finish off the form */
      echo "              </TABLE>\n"
         . "              <CENTER><BR><INPUT type=\"Submit\" value=\"Edit Information\" name=\"action\"> <INPUT type=\"Submit\" value=\"Submit Information\" name=\"action\"></CENTER>\n"
         . "            </FORM>\n";
      break;

    /* Check the user's input, add the user to the database, and display the results */
    case 4:
      /* Make sure it was POSTed, if it wasn't they are trying to be slick */
      if ( $REQUEST_METHOD == "POST" )
        {
          /* No errors... yet */
          $no_err = 0;

          /* Pull the number of accounts with the same username */
          $SQL     = "SELECT COUNT(*) as user_exists FROM " . TABLE_PREFIX . "users WHERE user_name='$username';";
          $results = ExeSQL($SQL);

          /* Grab the data, parse the results */
          while ($row = mysql_fetch_array($results))
            {
              /* If the username exists, error out */
              if ($row["user_exists"] != 0)
                {
                  echo "            <CENTER class=\"error_message\">That username already exists!</CENTER><BR>\n";
                  $no_err++;
                }
            }

          /* Pull the number of accounts with the same email */
          $SQL     = "SELECT COUNT(*) as email_exists FROM " . TABLE_PREFIX . "users WHERE user_email='$email';";
          $results = ExeSQL($SQL);

          /* Grab the data, parse the results */
          while ($row = mysql_fetch_array($results))
            {
              /* If the email exists, then error out */
              if ($row["email_exists"] != 0)
                {
                  echo "            <CENTER class=\"error_message\">Someone has already registered using that email address!</CENTER><BR>\n";
                  $no_err++;
                }
            }

          /* If there are no errors, then proceed with the registration */
          if ($no_err == 0)
            {
              /* Clear out the URL variables if they still contain 'http://' */
              if ($homepage == "http://") { $homepage = ""; }
              if ($picture == "http://") { $picture = ""; }
        
              /* Crypt the password to a random salt */
              $password = crypt($password); 
  
              /* Determine if the sig will be added by default */
              if ($include_sig != 1)
                $include_sig == 0;

              /* Insert the user into the database */
              $SQL     = "INSERT INTO " . TABLE_PREFIX . "users (user_name, user_email, user_pass, user_location, user_occupation, user_homepage, user_picture, user_interests, user_aim, user_icq, user_yahoo, user_signature, user_usesig) VALUES ('$username', '$email', '$password', '$location', '$occupation', '$homepage', '$picture', '$interests', '$aim', '$icq', '$yahoo', '$signature', '$include_sig');";
              $results = ExeSQL($SQL);

              /* Log the new user in */
              SetCookie("user_name", $username, time() + 86400, '', $_SERVER['HTTP_HOST']);
              SetCookie("user_pass", $password, time() + 86400, '', $_SERVER['HTTP_HOST']);

              /* Set their login status */
              $logged_in = 1;
 
              /* Finish off the registration */
              echo "            <CENTER class=\"regular_text\">\n"
                 . "              <B>Thanks for registering!</B><BR>\n"
                 . "              <A href=\"index.php\">Click here to log in!</A>\n"
                 . "            </CENTER>\n"
                 . "            <BR>\n";
              require("./content/view_forums.php");
              return;
            }
          else
            ShowRegistrationForm( $username, $password, $confirm_password, $email, $location, $occupation, $homepage, $picture, $interests, $aim, $icq, $yahoo, $signature, $include_sig );
        }
      else
        {
          /* If they didn't POST it, then error out */
          echo "            <CENTER class=\"error_message\">Malformed request detected!</CENTER><BR>\n";
          ShowRegistrationForm( $username, $password, $confirm_password, $email, $location, $occupation, $homepage, $picture, $interests, $aim, $icq, $yahoo, $signature, $include_sig );
        }
      break;
  }

/*
 * Show the registration form
 */

function
ShowRegistrationForm( $username, $password, $confirm_password, $email, $location, $occupation, $homepage, $picture, $interests, $aim, $icq, $yahoo, $signature, $include_sig )
{
  /* Start displaying the damned thing */
  echo "            <SCRIPT language=\"JavaScript\">\n"
     . "              function\n"
     . "              CheckForm()\n"
     . "              {\n"
     . "                if (document.registration.username.value == '')\n"
     . "                  {\n"
     . "                    alert('The \'Username\' field is mandatory!');\n"
     . "                    document.registration.username.focus();\n"
     . "                    return false;\n"
     . "                  }\n"
     . "                if (document.registration.password.value == '')\n"
     . "                  {\n"
     . "                    alert('The \'Password\' field is mandatory!');\n"
     . "                    document.registration.password.focus();\n"
     . "                    return false;\n"
     . "                  }\n"
     . "                if (document.registration.confirm_password.value == '')\n"
     . "                  {\n"
     . "                    alert('The \'Confirm Password\' field is mandatory!');\n"
     . "                    document.registration.confirm_password.focus(1);\n"
     . "                    return false;\n"
     . "                  }\n"
     . "                if (document.registration.password.value != document.registration.confirm_password.value)\n"
     . "                  {\n"
     . "                    alert('The \'Password\' and \'Confirm Password\' fields must be the same!');\n"
     . "                    document.registration.password.focus();\n"
     . "                    document.registration.password.select();\n"
     . "                    return false;\n"
     . "                  }\n"
     . "                if (document.registration.password.value.length < 6)\n"
     . "                  {\n"
     . "                    alert('The \'Password\' field must be at least 6 characters!');\n"
     . "                    document.registration.password.focus();\n"
     . "                    document.registration.password.select();\n"
     . "                    return false;\n"
     . "                  }\n"
     . "                if (document.registration.email.value == '')\n"
     . "                  {\n"
     . "                    alert('The \'Email\' field is mandatory!');\n"
     . "                    document.registration.email.focus();\n"
     . "                    return false;\n"
     . "                  }\n"
     . "                if (!ValidateEmail(document.registration.email.value))\n"
     . "                  {\n"
     . "                    alert('You must supply a valid email address.');\n"
     . "                    document.registration.email.focus();\n"
     . "                    document.registration.email.select();\n"
     . "                    return false;\n"
     . "                  }\n"
     . "                if (document.registration.signature.value.length > 255)\n"
     . "                  {\n"
     . "                    alert('The \'Signature\' field cannot exceed 255 characters!');\n"
     . "                    document.registration.signature.focus();\n"
     . "                    document.registration.signature.select();\n"
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
     . "            <FORM action=\"index.php?pid=register&step=3\" method=\"POST\" name=\"registration\">\n"
     . "              <TABLE cellpadding=\"5\" cellspacing=\"0\" width=\"100%\" border class=\"table_border\">\n"
     . "                <TR class=\"table_header\">\n"
     . "                  <TD colspan=\"2\">Required Information</TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" nowrap><B>Username:</B></TD>\n"
     . "                  <TD width=\"50%\" nowrap><INPUT type=\"text\" name=\"username\" value=\"$username\" maxlength=\"64\" size=\"50\"> <FONT class=\"small_text\">Max: 64 characters</FONT></TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_2 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" nowrap><B>Password:</B></TD>\n"
     . "                  <TD width=\"50%\" nowrap><INPUT type=\"password\" name=\"password\" value=\"$password\" maxlength=\"64\" size=\"50\"> <FONT class=\"small_text\">Min 6 characters - Max: 64 characters</FONT></TD>\n"
     . "                </TR>\n"
     . "                <TR bgcolor=\"" . TABLE_COLOR_1 . "\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" nowrap><B>Confirm Password:</B></TD>\n"
     . "                  <TD width=\"50%\" nowrap><INPUT type=\"password\" name=\"confirm_password\" value=\"$password\" maxlength=\"64\" size=\"50\"> <FONT class=\"small_text\">Min: 6 characters - Max: 64 characters</FONT></TD>\n"
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
     . "                  <TD width=\"50%\" nowrap><INPUT type=\"text\" name=\"icq\" value=\"$icq\" maxlength=\"16\" size=\"50\"> <FONT class=\"small_text\" size=\"1\">Max: 16 characters</FONT></TD>\n"
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
     . "                        <TD><TEXTAREA name=\"signature\" rows=\"5\" cols=\"40\" maxlength=\"255\">$signature</TEXTAREA></TD>\n"
     . "                        <TD valign=\"top\" nowrap>&nbsp;<FONT class=\"small_text\">Max: 255 characters</FONT></TD>\n"
     . "                      </TR>\n"
     . "                      <TR>\n"
     . "                        <TD colspan=\"2\" class=\"regular_text\">\n";
  
  /* Check the box if the signature is to be included */
  if ($include_sig == 1)
    $checked = " checked";
  else
    $checked = "";

  /* Display the rest of the form */
  echo "                          <INPUT type=\"checkbox\" name=\"include_sig\" value=\"1\"$checked> Include Signature on New Posts?\n"
     . "                        </TD>\n"
     . "                      </TR>\n"
     . "                    </TABLE>\n"
     . "                  </TD>\n"
     . "                </TR>\n"
     . "              </TABLE>\n"
     . "              <CENTER><BR><INPUT type=\"Submit\" value=\"Preview Information\" onClick=\"return CheckForm();\"></CENTER>\n"
     . "            </FORM>\n";
}

/*
 * Display the portion that is being previewed
 */

function
PreviewSection ( $section_value, $section_title, $the_color )
{
  /* Swap the colors */
  if ($the_color == TABLE_COLOR_1)
    $the_color = TABLE_COLOR_2;
  else
    $the_color = TABLE_COLOR_1;

  /* Display the start of the section */
  echo "                <TR bgcolor=\"$the_color\" class=\"regular_text\">\n"
     . "                  <TD width=\"25%\" valign=\"top\"><B>$section_title:</B></FONT></TD>\n"
     . "                  <TD width=\"50%\">\n";

  /* Don't display the password, for security reasons and all */
  if ($section_title == "Password")
    echo "                    <I>Password is hidden for security purposes.</I>\n";
  else
    echo "                    $section_value\n";

  /* If it's the AIM section, then swap out the variables to make sure everything is okay */
  if ($section_title == "AOL Instant Messenger")
    $section_title = "aim";
  else
    $section_title = strtolower($section_title);

  /* And, we're out */
  echo "                    <INPUT type=\"hidden\" name=\"$section_title\" value=\"$section_value\">\n"
     . "                  </TD>\n"
     . "                </TR>\n";
}

?>
