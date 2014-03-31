/**
 * Suitable to run from a cron-job, this file regularly
 * polls a mailbox for incoming items.
 * Using the ImapMailbox project from https://github.com/barbushin/php-imap/

 !PONDER-UPON: Make the 'reply-to' tag act as comment?
 !PONDER-UPON: Use gnupg instead of openssl?

*/
define('USER_KEYSTORE', '/users/');
define('USER_KEYSTORE_SALT', 'n3w7QZDBkaETPqDP97CNTLjH');
define('SIG_FILE_NAME', "signature.asc");

include_once "ImapMailbox.php";


function qsps_listen(){
	/*
	  You should have a dedicated mailbox with a random password
	  eg. qsps@example.com
	*/
	$server=NULL; //! eg. imap.gmail.com
	$port  =993;
	$user  =NULL;
	$pass  =NULL;
	$attach=NULL; //! Where attachments end up.
	$enc   ="utf-8";

	$query ="{".$server.":".$port."}INBOX";
	$mbox  =new ImapMalbox($query, $user, $pass, $attach, $enc);
	$msgs  =$mbox->searchMailBox("UNSEEN");

	if(count($msgs)==0)
		return NULL; //no new emails

	$ret=qsps_verify_mails($msgs, $attach);

	//Mark the rest of the emails as "read"
	$mbox->markMailsAsRead($ret);
}


/**
 * Validity checks on each unread mail.
 * Mails which failed the test are removed
 * from the return array.
 * @param $m Array containing email IDs
 * @param $att Attachments folder
 * @return array of mail IDs which passed the test
 */
function qsps_verify_mails($m, $att){
	$c=count($m);
	$ret=array();
	for($i=0; $i<$c; $i++){
		if(!in_array(SIG_FILE_NAME, $m->getAttachments()))
			continue;
		$sig=@file_get_contents($att.SIG_FILE_NAME);
		if($sig===NULL)
			die("Sig not found in ".$att.SIG_FILE_NAME);

		$dt=$m[$i]->textPlain;

		//get user key. Cert finelame is hash(salt+user email)
		$pkey=openssl_pkey_get_public(
			USER_KEYSTORE.sha1(USER_KEYSTORE_SALT.$m->fromAddress)."crt");

		if($pkey===NULL)
			die("No such user ".$m->fromAddress);

		/*!
		  QSPS uses sign-then-encrypt so the signature actually
		  validates to the encrypted text. Presently the file gets
		  decrypted twice, once here and once in processing. A
		  more civilized way is needed.
		*/

		//TODO: Handle encryption
		
		//moment of truth
		if(openssl_verify($dt, $sig, $pkey)==1)
			array_push($ret, $m[$i]);
	}
	return $ret;
}