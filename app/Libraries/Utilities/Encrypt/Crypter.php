<?php 

/* Tutorial by AwesomePHP.com -> www.AwesomePHP.com */ 
/* Function: Encode or Decode anything based on a string */  
//echo strlen('Please meet me at 05:44 time.'),'-',strlen('5541c0ce3e64d9ad65e0440dd0952a5d48ac4c2f68cc2432d5b5018cf1');
//$secretPass = 'kljhflk73#OO#*U$O(*YO'; 
//$encodeThis = 'testtest'; 

/* Regular Encoding */ 
//$encoded = crypter($encodeThis,$secretPass); 
/* Another pass to decode */ 
//$decoded = crypter($encoded,$secretPass); 

//echo 'Encoded String: '.$encoded; 
//echo '<br />Decoded String: '.$decoded; 

/* Important: If passing this value via URL you might want to make it
explorer friendler */ 
//$encoded = bin2hex(crypter($encodeThis,$secretPass)); 
/* Another pass to decode */ 
//$decoded = crypter(hex2bin($encoded),$secretPass); 

//echo '<br /><br />Encoded String: '.$encoded; 
//echo '<br />Decoded String: '.$decoded;
//echo zendCrypter($secretPass,$encodeThis,'encrypt'),'-',zendCrypter($secretPass,'7148d6db39648ab4','decrypt'); 
//---------------------------------
namespace Libraries\Utilities\Encrypt;
use Libraries\Utilities\Encrypt\Base32;
class Crypter
{
    public static function cmsCrypter($key,$string,$action='encrypt')
    {
        if($action == 'encrypt')
        {
            return bin2hex(self::crypter($key,$string));
        }
        else
        {
            return self::crypter($key,self::hex2bin($string));
        }
    }
    static function crypter($pwd,$data) 
    { 
        $x = $a = $j = $k  = 0;
        $Zcrypt = "";
        $pwd_length = strlen($pwd); 
        for ($i = 0; $i < 255; $i++) { 
            $key[$i] = ord(substr($pwd, ($i % $pwd_length)+1, 1)); 
            $counter[$i] = $i; 
        } 
        for ($i = 0; $i < 255; $i++) { 
            $x = ($x + $counter[$i] + $key[$i]) % 256; 
            $temp_swap = $counter[$i];     
            if(!isset($counter[$x]))
                $counter[$x] = 0;
            if(!isset($counter[$i]))
                $counter[$i] = 0;
            
            $counter[$i] = $counter[$x]; 
            $counter[$x] = $temp_swap; 
        } 
        for ($i = 0; $i < strlen($data); $i++) { 
            $a = ($a + 1) % 256; 
            $j = ($j + $counter[$a]) % 256; 
            $temp = $counter[$a]; 
            $counter[$a] = $counter[$j]; 
            $counter[$j] = $temp; 
            $k = $counter[(($counter[$a] + $counter[$j]) % 256)]; 
            $Zcipher = ord(substr($data, $i, 1)) ^ $k; 
            $Zcrypt .= chr($Zcipher); 
        } 
        return $Zcrypt; 
    } 
    
    public static function hex2bin($hexdata) { 
        $bindata = "";
        for ($i=0;$i<strlen($hexdata);$i+=2) { 
            $bindata.=chr(hexdec(substr($hexdata,$i,2))); 
        }   
        return $bindata; 
    }
    
    
    public static function  random_encrypt($value,$key,$base = 64){
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        if($base == 64)
            return self::base64_url_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $value, MCRYPT_MODE_ECB, $iv)) ;
        else if($base == 32) 
        {
            $base32 = new Base32();
            return $base32->fromString(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $value, MCRYPT_MODE_ECB, $iv)) ;
        }
        
    }
    
    static function  random_decrypt($value,$key ,$base = 64){
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        if($base == 64)
            return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, self::base64_url_decode($value), MCRYPT_MODE_ECB, $iv));
        else if($base == 32) 
        {
            $base32 = new Base32();
            return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $base32->toString($value), MCRYPT_MODE_ECB, $iv));
        }
           
    } 
    
    static function base64_url_encode($input)
    {
        return strtr(base64_encode($input), '+/=', '-_,');
    }
     
    static function base64_url_decode($input)
    {
        return base64_decode(strtr($input, '-_,', '+/='));
    }
}