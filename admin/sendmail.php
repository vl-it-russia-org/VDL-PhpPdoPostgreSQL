<?php

$VDLPath = "/home/vladlev/public_html/vladlev/msg_ins/ftpfiles/";


/*


 this class encapsulates the PHP mail() function.
 implements CC, Bcc, Priority headers


@version        1.3

- added ReplyTo( $address ) method
- added Receipt() method - to add a mail receipt
- added optionnal charset parameter to Body() method. this should fix charset problem on some mail clients

@example

        $m= new Mail; // create the mail
        $m->From( "leo@isp.com" );
        $m->To( "destination@somewhere.fr" );
        $m->Subject( "the subject of the mail" );

        $message= "Hello world!\nthis is a test of the Mail class\nplease ignore\nThanks.";
        $m->Body( $message);        // set the body
        $m->Cc( "someone@somewhere.fr");
        $m->Bcc( "someoneelse@somewhere.fr");
        $m->Priority(4) ;        // set the priority to Low
        $m->Attach( "/home/leo/toto.gif", "image/gif" ) ;        // attach a file of type image/gif

        //alternatively u can get the attachment uploaded from a form
        //and retreive the filename and filetype and pass it to attach methos

        $m->Send();        // send the mail
        echo "the mail below has been sent:<br><pre>", $m->Get(), "</pre>";



@author     Saravanan(winsaravanan@yahoo.com,ssaravanan@teledata-usa.com)

*/


function strstr_after($haystack, $needle, $case_insensitive = false) {
    $strpos = ($case_insensitive) ? 'stripos' : 'strpos';
    $pos = $strpos($haystack, $needle);
    if (is_int($pos)) {
        return substr($haystack, $pos + strlen($needle));
    }
    // Most likely false or null
    return $pos;
}

class Mail
{
        /*
        list of To addresses
        @var        array
        */
        var $sendto = array();
        /*
        @var        array
        */
        var $acc = array();
        /*
        @var        array
        */
        var $abcc = array();
        /*
        paths of attached files
        @var array
        */
        var $aattach = array();
        /*
        list of message headers
        @var array
        */
        var $xheaders = array();
        /*
        message priorities referential
        @var array
        */
        var $priorities = array( '1 (Highest)', '2 (High)', '3 (Normal)', '4 (Low)', '5 (Lowest)' );
        /*
        character set of message
        @var string
        */
        var $charset = "windows-1251";
        var $ctencoding = "8bit";
        var $receipt = 0;
        var $content_type='';

/*

        Mail contructor

*/

function Mail()
{
        $this->autoCheck( true );
        $this->boundary= "--" . md5( uniqid("myboundary") );
}


function Content_type($contenttype){

    $this->content_type=$contenttype;
    //echo $this->content_type;
    //echo '<br>';
    //exit();
}

/*

activate or desactivate the email addresses validator
ex: autoCheck( true ) turn the validator on
by default autoCheck feature is on

@param boolean        $bool set to true to turn on the auto validation
@access public
*/
function autoCheck( $bool )
{
        if( $bool )
                $this->checkAddress = true;
        else
                $this->checkAddress = false;
}


/*

Define the subject line of the email
@param string $subject any monoline string

*/
function Subject( $subject )
{
        $this->xheaders['Subject'] = strtr( $subject, "\r\n" , "  " );
}


/*

set the sender of the mail
@param string $from should be an email address

*/

function From( $from )
{

        if( ! is_string($from) ) {
                echo "Class Mail: error, From is not a string";
                exit;
        }
        $this->xheaders['From'] = $from;
}

/*
 set the Reply-to header
 @param string $email should be an email address

*/
function ReplyTo( $address )
{

        if( ! is_string($address) )
                return false;

        $this->xheaders["Reply-To"] = $address;

}


/*
add a receipt to the mail ie.  a confirmation is returned to the "From" address (or "ReplyTo" if defined)
when the receiver opens the message.

@warning this functionality is *not* a standard, thus only some mail clients are compliants.

*/

function Receipt()
{
        $this->receipt = 1;
}


/*
set the mail recipient
@param string $to email address, accept both a single address or an array of addresses

*/

function To( $to )
{

        // TODO : test validit� sur to
        if( is_array( $to ) )
                $this->sendto= $to;
        else
                $this->sendto[] = $to;

        if( $this->checkAddress == true )
                $this->CheckAdresses( $this->sendto );

}

/*

Clear TO address list for sending one more time for new recepients

*/

function ClearTo()
{
        $this->boundary= "--" . md5( uniqid($this->sendto[0]) );
        $this->sendto->clear();

}



/*                Cc()
 *                set the CC headers ( carbon copy )
 *                $cc : email address(es), accept both array and string
 */

function Cc( $cc )
{
        if( is_array($cc) )
                $this->acc= $cc;
        else
                $this->acc[]= $cc;

        if( $this->checkAddress == true )
                $this->CheckAdresses( $this->acc );

}



/*                Bcc()
 *                set the Bcc headers ( blank carbon copy ).
 *                $bcc : email address(es), accept both array and string
 */

function Bcc( $bcc )
{
        if( is_array($bcc) ) {
                $this->abcc = $bcc;
        } else {
                $this->abcc[]= $bcc;
        }

        if( $this->checkAddress == true )
                $this->CheckAdresses( $this->abcc );
}


/*                Body( text [, charset] )
 *                set the body (message) of the mail
 *                define the charset if the message contains extended characters (accents)
 *                default to us-ascii
 *                $mail->Body( "m�l en fran�ais avec des accents", "iso-8859-1" );
 */
function Body( $body, $charset="" )
{
        $this->body = $body;

        if( $charset != "" ) {
                $this->charset = strtolower($charset);
                if( $this->charset != "us-ascii" )
                        $this->ctencoding = "8bit";
        }
}


/*                Organization( $org )
 *                set the Organization header
 */

function Organization( $org )
{
        if( trim( $org != "" )  )
                $this->xheaders['Organization'] = $org;
}


/*                Priority( $priority )
 *                set the mail priority
 *                $priority : integer taken between 1 (highest) and 5 ( lowest )
 *                ex: $mail->Priority(1) ; => Highest
 */

function Priority( $priority )
{
        if( ! intval( $priority ) )
                return false;

        if( ! isset( $this->priorities[$priority-1]) )
                return false;

        $this->xheaders["X-Priority"] = $this->priorities[$priority-1];

        return true;

}


/*
 Attach a file to the mail

 @param string $filename : path of the file to attach
 @param string $filetype : MIME-type of the file. default to 'application/x-unknown-content-type'
 @param string $disposition : instruct the Mailclient to display the file if possible ("inline") or always as a link ("attachment") possible values are "inline", "attachment"
 */

function Attach($filename,$filetype = "",$disposition = "inline")
{

        if( $filetype == "" )
                $filetype = "application/x-unknown-content-type";
                //$filetype = "text/plain";



         $this->aattach[] = $filename;


        $this->actype[] = $filetype;
        $this->adispo[] = $disposition;

}

/*

Build the email message

@access protected

*/
function BuildMail()
{

        // build the headers
        $this->headers = "";
//        $this->xheaders['To'] = implode( ", ", $this->sendto );

        if( count($this->acc) > 0 )
                $this->xheaders['CC'] = implode( ", ", $this->acc );

        if( count($this->abcc) > 0 )
                $this->xheaders['BCC'] = implode( ", ", $this->abcc );


        if( $this->receipt ) {
                if( isset($this->xheaders["Reply-To"] ) )
                        $this->xheaders["Disposition-Notification-To"] = $this->xheaders["Reply-To"];
                else
                        $this->xheaders["Disposition-Notification-To"] = $this->xheaders['From'];
        }

        if( $this->charset != "" ) {
                //global $contenttype;
                $content_type=$this->content_type;
                $this->xheaders["Mime-Version"] = "1.0";
                $this->xheaders["Content-Type"] = "$content_type; charset=$this->charset";
                $this->xheaders["Content-Transfer-Encoding"] = $this->ctencoding;
        }

        $this->xheaders["X-Mailer"] = "RLSP Mailer";

        // include attached files
        if( count( $this->aattach ) > 0 ) {

                $this->_build_attachement();
        } else {
                $this->fullBody = $this->body;
        }

        reset($this->xheaders);
        while( list( $hdr,$value ) = each( $this->xheaders )  ) {
                if( $hdr != "Subject" )
                        $this->headers .= "$hdr: $value\n";
        }


}

/*
        fornat and send the mail
        @access public
*/

function Send()
{
        //global $filename;

        $this->BuildMail();

        $this->strTo = implode( ", ", $this->sendto );


        $res = @mail( $this->strTo, $this->xheaders['Subject'], $this->fullBody, $this->headers );

}



/*
 *                return the whole e-mail , headers + message
 *                can be used for displaying the message in plain text or logging it
 */

function Get()
{
        $this->BuildMail();
        $mail = "To: " . $this->strTo . "\n";
        $mail .= $this->headers . "\n";
        $mail .= $this->fullBody;
        return $mail;
}


/*
        check an email address validity
        @access public
        @param string $address : email address to check
        @return true if email adress is ok
 */

function ValidEmail($address)
{
       // if( ereg( ".*<(.+)>", $address, $regs ) ) {
       //         $address = $regs[1];
       // }
       //  if(ereg( "^[^@  ]+@([a-zA-Z0-9\-]+\.)+([a-zA-Z0-9\-]{2}|net|com|gov|mil|org|edu|int)\$",$address) )
                 return true;
       //  else
       //          return false;
}


/*

        check validity of email addresses
        @param        array $aad -
        @return if unvalid, output an error message and exit, this may -should- be customized

 */

function CheckAdresses( $aad )
{
        for($i=0;$i< count( $aad); $i++ ) {
                if( ! $this->ValidEmail( $aad[$i]) ) {
                        echo "Class Mail, method Mail : invalid address $aad[$i]";
                        exit;
                }
        }
}


/*
 check and encode attach file(s) . internal use only

*/

function _build_attachement()
{

        $this->xheaders["Content-Type"] = "multipart/mixed;\n boundary=\"$this->boundary\"";

        $this->fullBody = "This is a multi-part message in MIME format.\n--$this->boundary\n";
        $this->fullBody .= "Content-Type: text/html; charset=$this->charset\nContent-Transfer-Encoding: $this->ctencoding\n\n" . $this->body ."\n";

        $sep= chr(13) . chr(10);

        $ata= array();
        $k=0;
        // for each attached file, do...
        for( $i=0; $i < count( $this->aattach); $i++ ) {

                $filename = $this->aattach[$i];
                $basename = basename($filename);
                $ctype = $this->actype[$i];        // content-type
                $disposition = $this->adispo[$i];
                /*getting the original name of the file */

                //echo $original_filename;

                if( ! file_exists( $filename) ) {
                        echo "Class Mail, method attach : file $filename can't be found"; exit;
                }

               /* echo 'filename--'.$filename;
                  echo '<br>';
               */

                /*

                   the semicolon after the Content-type : $basename is important
                   since it was not there.This mail program
                   was not able to see the attachment for the past 1 month
                   --Saravanan 20/04/02

               */

                $subhdr= "--$this->boundary\nContent-Type: $ctype;\n name=\"$basename\";\nContent-Transfer-Encoding: base64\nContent-Disposition: $disposition;\n  filename=\"$basename\"\n";
                //$subhdr= "--$this->boundary\nContent-type: $ctype;\n name=\"$filename\"\nContent-Transfer-Encoding: base64\nContent-Disposition: $disposition;\n  filename=\"$filename\"\n";
                $ata[$k++] = $subhdr;
                // non encoded line length
                $linesz= filesize( $filename)+1;
                $fp= fopen( $filename, 'r' );
                $ata[$k++] = chunk_split(base64_encode(fread( $fp, $linesz)));

                fclose($fp);

        }

        $this->fullBody .= implode($sep, $ata);

        //echo $this->fullBody;
}

/*

        Read command file and send e-mail by many people
        @param command_file_name       
        @return if unvalid, output an error message and exit, this may -should- be customized

 */

function SendMailVDL( $command_file_name , $VDLPath )
{
  $this->From( "vladlev@gator411.hostgator.com" );
  $this->ReplyTo( "vladislav.levitskiy@kontaktor.ru" );
  $Msg = "";
  $this->To ( "vl@it-russia.org" );
  $handle = @fopen( $command_file_name, "r");
  if ($handle) {
    while (($buffer = fgets($handle,1900)) !== false) {
      //echo ("NewLine:". $buffer."<p>");
      
      if (strncmp ($buffer, "#", 1) == 0) {
        // Do nothing -- just comment line
      }
      elseif (strncmp ("<SetAddrList>", $buffer, strlen ("<SetAddrList>")) == 0) {
        $fn = $VDLPath . "addrlist/" . trim (strstr_after ( $buffer,"<SetAddrList>"));
        $FileOut = @fopen( $fn.".adrlist", "w");
        if($FileOut == false) {
            die("unable to create file $fn") ;
        };

        // ��������� ������
        while ( (($buffer = fgets($handle, 100)) !== false) &&
                 (strncmp ($buffer, "</SetAddrList>", strlen ("</SetAddrList>")) !== 0 )) {           
          fputs ($FileOut, trim ($buffer)."\n");
        };

        fclose ($FileOut);
      }
      elseif ( strncmp ("<AddrTo>", $buffer, strlen ("<AddrTo>")) == 0) {
        $buffer = trim ( $buffer );
        $this->To( strstr_after ( $buffer, "<AddrTo>" ) );
      }
      elseif ( strncmp ("<Subj>", $buffer, strlen ("<Subj>")) == 0 ) {
        $this->Subject ( trim (strstr_after ( $buffer, "<Subj>" ) ) );
      }
      elseif (strncmp ("<AddrFrom>", $buffer, strlen ("<AddrFrom>")) == 0) {
        $this->Subject (strstr_after ( $buffer, "<AddrFrom>") );
      }
      elseif (strncmp ("<Attach>", $buffer, strlen ("<Attach>")) == 0) {
        $fn = $VDLPath . "attach/" . trim (strstr_after ($buffer, "<Attach>")) ;
        //echo ("Attached file:". $fn ."<p>");
        $this->Attach( $fn);
      }
      elseif (strncmp ("<Message>", $buffer, strlen ("<Message>")) == 0) {
        $Msg = $Msg . strstr_after ($buffer, "<Message>")."<br>";
        //echo "Messgage:". $Msg ."<BR>";
      }
      elseif (strncmp ("</SendMail>", $buffer, strlen ("</SendMail>")) == 0) {
        $this->Body($Msg);
        $this->Send();                
        //echo ("send ok<br>");
      };
        
    }    
  }
  if (!feof($handle)) {
        echo ": unexpected fgets() fail\n";
  }
  fclose($handle);
  rename ( $command_file_name, $command_file_name . ".snd" );
}

  
  

} // class Mail



?>