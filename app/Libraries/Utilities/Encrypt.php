<?php
namespace Libraries\Utilities;

use Libraries\Utilities\Encrypt\Crypter;

class Encrypt extends Crypter {
	public static function encrypt($string,$action='encrypt')
	{
		return self::cmsCrypter(SECRET_KEY,$string,$action);
	}
    
    public static function randomize_encrypt($string,$action='encrypt')
    {
       if($action == 'encrypt')
       {
            return self::random_encrypt($string,SECRET_KEY);
       }
       else
       {
            return self::random_decrypt($string,SECRET_KEY);    
       } 
    }
}

?>