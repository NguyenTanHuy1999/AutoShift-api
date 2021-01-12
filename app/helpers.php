<?php

use App\Api\Entities\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request as HttpRequest;
use App\Api\Entities\Element;
use App\Api\Entities\EmployeeShift;
use App\Api\Entities\ShopActivity;
use App\Api\Entities\Department;

if (!function_exists('auth_user')) {
    /**
     * Get the auth_user.
     *
     * @return mixed
     */
    function auth_user()
    {
        return app('Dingo\Api\Auth\Auth')->user();
    }
}

if (!function_exists('dingo_route')) {
    /**
     * Get Dingo Route.
     *
     * @param string $version
     * @param string $name
     * @param string $params
     *
     * @return string
     */
    function dingo_route($version, $name, $params = [])
    {
        return app('Dingo\Api\Routing\UrlGenerator')
            ->version($version)
            ->route($name, $params);
    }
}

if (!function_exists('trans')) {
    /**
     * Translate the given message.
     *
     * @param string $id
     * @param array  $parameters
     * @param string $domain
     * @param string $locale
     *
     * @return string
     */
    function trans($id = null, $parameters = [], $domain = 'messages', $locale = null)
    {
        if (is_null($id)) {
            return app('translator');
        }

        return app('translator')->trans($id, $parameters, $domain, $locale);
    }
}

if (!function_exists('app_path')) {
    /**
     * Get the app_path.
     *
     * @return mixed
     */
    function app_path($path = '')
    {
        $app_path = app()->basePath() . ($path ? '/' . $path : '/app/' . $path);

        return $app_path;
    }
}
if (!function_exists('build_slug')) {
    /**
     * Remove all Vietnamese Characters and build slugs.
     */
    function build_slug($str)
    {
        $result = '';
        $isSpace = 0;
        $arr_unicode = getArraycompositeUnicodeToLatin();

        $str = mb_strtolower($str, 'utf-8');
        $len = mb_strlen($str, 'utf-8');
        for ($i = 0; $i < $len; $i++) {
            $char = mb_substr($str, $i, 1, 'utf-8');
            if ($char == ' ' || $char == '-') {
                if ($isSpace == 0) {
                    $result .= "-";
                }
                $isSpace = 1;
            } else if (array_key_exists($char, $arr_unicode)) {

                $result .= $arr_unicode[$char];
                $isSpace = 0;
            }
        }
        return $result;
    }
}
/**
 * rename dang ha duc trung
 * */
if (!function_exists('remove_sign')) {
    function remove_sign($str)
    {
        $result = '';
        $isSpace = 0;
        //echo 'tesst';return;
        $arr_unicode = getArraycompositeUnicodeToLatin();
        $len = mb_strlen($str, 'utf-8');
        for ($i = 0; $i < $len; $i++) {
            $char = mb_substr($str, $i, 1, 'utf-8');
            if ($char == ' ') {
                if ($isSpace == 0) {
                    $result .= " ";
                }
                $isSpace = 1;
            } else if (array_key_exists($char, $arr_unicode)) {
                $result .= $arr_unicode[$char];
                $isSpace = 0;
            }
        }
        return $result;
    }
}
//List unicode charactor
if (!function_exists('getArraycompositeUnicodeToLatin')) {
    function getArraycompositeUnicodeToLatin()
    {
        return array(
            'a' => 'a', 'á' => 'a', 'à' => 'a', 'â' => 'a', 'ă' => 'a', 'ã' => 'a', 'ấ' => 'a', 'ầ' => 'a', 'ắ' => 'a', 'ằ' => 'a', 'ẫ' => 'a', 'ẵ' => 'a',
            'ả' => 'a', 'ẩ' => 'a', 'ẳ' => 'a', 'ạ' => 'a', 'ậ' => 'a', 'ặ' => 'a', 'b' => 'b', 'c' => 'c', 'e' => 'e', 'f' => 'f', 'g' => 'g', 'h' => 'h',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ẽ' => 'e', 'ế' => 'e', 'ề' => 'e', 'ễ' => 'e', 'ẻ' => 'e', 'ể' => 'e', 'ẹ' => 'e', 'ệ' => 'e',
            'i' => 'i', 'í' => 'i', 'ì' => 'i', 'ĩ' => 'i', 'ỉ' => 'i', 'ị' => 'i', 'j' => 'j', 'k' => 'k', 'l' => 'l', 'm' => 'm', 'n' => 'n', 'o' => 'o',
            'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o', 'ố' => 'o', 'ồ' => 'o', 'ỗ' => 'o', 'ỏ' => 'o', 'ơ' => 'o', 'ổ' => 'o', 'ọ' => 'o',
            'ớ' => 'o', 'ờ' => 'o', 'ỡ' => 'o', 'ộ' => 'o', 'ở' => 'o', 'ợ' => 'o', 'u' => 'u', 'q' => 'q', 'r' => 'r', 's' => 's', 't' => 't', 'z' => 'z', 'v' => 'v', 'p' => 'p',
            'ú' => 'u', 'ù' => 'u', 'ũ' => 'u', 'ủ' => 'u', 'ư' => 'u', 'ụ' => 'u', 'ứ' => 'u', 'ừ' => 'u', 'ữ' => 'u', 'ử' => 'u', 'ự' => 'u',
            'ý' => 'y', 'ỳ' => 'y', 'ỹ' => 'y', 'ỷ' => 'y', 'ỵ' => 'y', 'y' => 'y', 'd' => 'd', '-' => '-', 'w' => 'w', 'x' => 'x', 'đ' => 'd',
            'A' => 'A', 'Á' => 'A', 'À' => 'A', 'Â' => 'A', 'Ă' => 'A', 'Ã' => 'A', 'Ấ' => 'A', 'Ầ' => 'A', 'Ắ' => 'A', 'Ằ' => 'A', 'Ẫ' => 'A', 'Ẵ' => 'A',
            'Ả' => 'A', 'Ẩ' => 'A', 'Ẳ' => 'A', 'Ạ' => 'A', 'Ậ' => 'A', 'Ặ' => 'A', 'B' => 'B', 'C' => 'C', 'E' => 'E', 'F' => 'F', 'G' => 'G', 'H' => 'H',
            'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ẽ' => 'E', 'Ế' => 'E', 'Ề' => 'E', 'Ễ' => 'E', 'Ẻ' => 'E', 'Ể' => 'E', 'Ẹ' => 'E', 'Ệ' => 'E',
            'I' => 'I', 'Í' => 'I', 'Ì' => 'I', 'Ĩ' => 'I', 'Ỉ' => 'I', 'Ị' => 'I', 'J' => 'J', 'K' => 'K', 'L' => 'L', 'M' => 'M', 'N' => 'N', 'O' => 'O',
            'Ó' => 'O', 'Ò' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ố' => 'O', 'Ồ' => 'O', 'Ỗ' => 'O', 'Ỏ' => 'O', 'Ơ' => 'O', 'Ổ' => 'O', 'Ọ' => 'O',
            'Ớ' => 'O', 'Ờ' => 'O', 'Ỡ' => 'O', 'Ộ' => 'O', 'Ở' => 'O', 'Ợ' => 'O', 'U' => 'U', 'Q' => 'Q', 'R' => 'R', 'S' => 'S', 'T' => 'T', 'Z' => 'Z', 'V' => 'V', 'P' => 'P',
            'Ú' => 'U', 'Ù' => 'U', 'Ũ' => 'U', 'Ủ' => 'U', 'Ư' => 'U', 'Ụ' => 'U', 'Ứ' => 'U', 'Ừ' => 'U', 'Ữ' => 'U', 'Ử' => 'U', 'Ự' => 'U',
            'Ý' => 'Y', 'Ỳ' => 'Y', 'Ỹ' => 'Y', 'Ỷ' => 'Y', 'Y' => 'Y', 'D' => 'D', '-' => '-', 'W' => 'W', 'X' => 'X', ',' => ',', "." => ".", ":" => ":", "%" => "%", "\r" => "\r", "\n" => "\n", "\"" => "\"", "/" => "/", "\\" => "\\", "}" => "}", "{" => "{", "[" => "[", "]" => "]", "(" => "(", ")" => ")",
            'Đ' => 'D', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '0' => '0'
        );
    }
}

if (!function_exists('is_mongo_id')) {
    /*
    * Generate MongoDB\BSON\ObjectID class
    */
    function is_mongo_id($id)
    {
        return ($id instanceof \MongoDB\BSON\ObjectID
            || preg_match('/^[a-f\d]{24}$/i', $id));
    }
}


if (!function_exists('mongo_id')) {
    /*
    * Generate MongoDB\BSON\ObjectID class
    */
    function mongo_id($id)
    {
        if (empty($id)) return null;
        return new MongoDB\BSON\ObjectID($id);
    }
}

if (!function_exists('mongo_id_string')) {
    /*
    * Generate MongoDB\BSON\ObjectID class
    */
    function mongo_id_string($id)
    {
        return strval($id);
    }
}

if (!function_exists('format_get_date')) {
    /*
    * Generate MongoDB\BSON\ObjectID class
    */
    function format_get_date($date, $format = 'full_date')
    {
        if (empty($date)) {
            return null;
        }
        if ($format == 'date') {
            return $date->toDateString();
        } else if ($format == 'time') {
            return $date->toTimeString();
        } else if ($format == 'full_date') {
            return $date->toDateTimeString();
        } else {
            return $date->getTimeStamp();
        }
    }
}

if (!function_exists('format_datetimepicker')) {
    /**
     * Generate MongoDB\BSON\ObjectID class.
     *
     * @param $datetime: input: 20/02/2017 1:00 AM
     *
     * @return datetime 02/20/2017 1:00 AM
     **/
    function format_datetimepicker($datetime)
    {
        $tmpDate = explode(' ', $datetime);
        $dmy = explode('/', $tmpDate[0]);
        $tmpDate[0] = $dmy[1] . '/' . $dmy[0] . '/' . $dmy[2];
        $datetime = implode(' ', $tmpDate);

        return Carbon::parse($datetime)->format('Y-m-d H:i:s');
    }
}

if (!function_exists('asset')) {
    /**
     * Generate an asset path for the application.
     *
     * @param string $path
     * @param bool   $secure
     *
     * @return string
     */
    function asset($path, $secure = null)
    {
        return app('url')->asset($path, $secure);
    }
}

if (!function_exists('public_path')) {
    /**
     * Return the path to public dir.
     *
     * @param null $path
     *
     * @return string
     */
    function public_path($path = null)
    {
        return rtrim(app()->basePath('public/' . $path), '/');
    }
}

if (!function_exists('base_path')) {
    /**
     * Return the path to public dir.
     *
     * @param null $path
     *
     * @return string
     */
    function base_path($path = null)
    {
        return rtrim(app()->basePath($path), '/');
    }
}

if (!function_exists('get_client_ip')) {
    /**
     * Return IP of client.
     *
     * @param null
     *
     * @return string $ip
     */
    function get_client_ip()
    {
        $ip;
        if (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('REMOTE_ADDR')) {
            $ip = getenv('REMOTE_ADDR');
        } else {
            $ip = 'UNKNOWN';
        }

        return $ip;
    }
}

if (!function_exists('get_closest_number')) {
    function get_closest_number($search, $arr)
    {
        $closest = null;
        foreach ($arr as $item) {
            if ($closest === null || abs($search - $closest) > abs($item - $search)) {
                $closest = $item;
            }
        }

        return $closest;
    }
}

if (!function_exists('get_image_size')) {
    function get_image_size($image)
    {
        list($width, $height, $type, $attr) = getimagesize($image);

        return array('width' => $width, 'height' => $height);
    }
}

if (!function_exists('add_http')) {
    function add_http($url)
    {
        if (!preg_match('~^(?:f|ht)tps?://~i', $url)) {
            $url = 'http://' . $url;
        }

        return $url;
    }
}

if (!function_exists('charset_convert_utf8')) {
    function charset_convert_utf8($content)
    {
        return mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
    }
}
if (!function_exists('convert_pdf_to_image')) {
    function convert_pdf_to_image($pdfPath, $imagePath)
    {
        if (file_exists($pdfPath)) {
            $pdf = new Spatie\PdfToImage\Pdf($pdfPath);
            return $pdf->saveImage($imagePath);
        }
        return false;
    }
}

if (!function_exists('getStartDayOfWeek')) {
    function getStartDayOfWeek($weekOfYeear)
    {
        $currDay = Carbon::now(); // or $date = new Carbon();
        $currYear = $currDay->year;
        $isoDay = $currDay->setISODate($currYear, $weekOfYeear);
        $startDay = $isoDay->startOfWeek();
        return $startDay;
    }
}

if (!function_exists('createDayByIndexDay')) {
    function createDayByIndexDay($date)
    {
        $daysOfWeek = [];
        // Create list day of week
        for ($i = 0; $i <= 6; $i++) {
            $daysOfWeek[] = (clone $date)->addDays($i);
        }
        return $daysOfWeek;
    }
}

if (!function_exists('subDateTime')) {
    function subDateTime($minusDate, $subDate, $type = 'hour')
    {
        $response = [];
        $tsMinusDate = Carbon::parse($minusDate)->timestamp;
        $tsSubDate = Carbon::parse($subDate)->timestamp;
        if ($type == 'hour') {
            $minutes = round(($tsMinusDate - $tsSubDate) / 60);
            $response['hour'] = (int)($minutes / 60);
            $response['minute'] = $minutes - $response['hour'] * 60;
            return $response;
        } elseif ($type == 'minute') {
            return ['minute' => round(($tsMinusDate - $tsSubDate) / 60)];
        }
        return $tsMinusDate - $tsSubDate;
    }
}

if (!function_exists('listProvider')) {
    function listProvider()
    {
        $arrVietTel = array(
            '098', '097', '096', '016', '086', '032', '033', '034', '035', '036',
            '037', '038', '039'
        );

        $arrVMS = array(
            '090', '093', '0120', '0121', '0122', '0126', '0128', '089',
            '070', '079', '077', '076', '078'
        );

        $arrVNP = array(
            '091', '094', '0123', '0124', '0125', '0127', '0129', '088',
            '083', '084', '085', '081', '082'
        );

        $arrSFONE = array('095');

        $arrVNM = array('0186', '0188', '092', '056', '058', '059', '052');

        $arrGTEL = array('099', '0199', '058');

        $arrOther = array('022', '025', '038', '051', '063');

        $arrTanca = array('011', '0111');

        $arrReturn = array(
            'VIETTEL' => $arrVietTel, 'VMS' => $arrVMS, 'VNP' => $arrVNP, 'SFONE' => $arrSFONE,
            'GTEL' => $arrGTEL, 'VNMOBILE' => $arrVNM, 'UNKOWN' => $arrOther, 'TANCA' => $arrTanca
        );
        return $arrReturn;
    }
}

if (!function_exists('getProviderByPhone')) {
    function getProviderByPhone($phone)
    {
        $arrListProvider = listProvider();
        $prePhone4Num = substr($phone, 0, 4);
        $prePhone3Num = substr($phone, 0, 3);
        foreach ($arrListProvider as $p => $iTems) {
            foreach ($iTems as $key => $proV) {
                if ($proV === $prePhone4Num) {
                    return $p;
                }
            }
        }
        foreach ($arrListProvider as $p => $iTems) {
            foreach ($iTems as $key => $proV) {
                if ($proV === $prePhone3Num) {
                    return $p;
                }
            }
        }
        return false;
    }
}
/**
 * Get the configuration path.
 *
 * @param  string $path
 * @return string
 */
if (!function_exists('config_path')) {
    function config_path($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}

/**
 * Get Normal day, saturday and sunnday
 * @param $month
 * @param $year
 **/
if (!function_exists('list_normal_saturday_sunday_in_month')) {
    function list_normal_saturday_sunday_in_month($month, $year)
    {
        $firstDay = Carbon::create(
            $year,
            $month,
            1,
            0,
            0,
            0
        );
        $lastDay =  new Carbon('last day of ' . $firstDay);
        $listDayOfMonth = [
            'normal' => 0,
            'saturday' => 0,
            'sunday' => 0,
        ];
        while ($firstDay <= $lastDay) {
            // echo $firstDay->dayOfWeek,"----";
            if ($firstDay->format('l') == 'Saturday') {
                $listDayOfMonth['saturday'] += 1;
            } elseif ($firstDay->format('l') == 'Sunday') {
                $listDayOfMonth['sunday'] += 1;
            } else {
                $listDayOfMonth['normal'] += 1;
            }
            $firstDay->addDay();
        }
        return $listDayOfMonth;
    }
}

/**
 * Foramt number
 *
 **/
if (!function_exists('format_number')) {
    function format_number($number, $afterPoin = 0)
    {
        return number_format($number, $afterPoin, ".", ",");
    }
}

/**
 * Build build_meta_paging
 *
 **/
if (!function_exists('build_meta_paging')) {
    function build_meta_paging($entityPaging)
    {
        return [
            'total' => $entityPaging->total(),
            'count' => $entityPaging->count(),
            'per_page' => $entityPaging->perPage(),
            'current_page' => $entityPaging->currentPage(),
            'total_pages' => ceil($entityPaging->total() / $entityPaging->perPage())
        ];
    }
}

/**
 * Create alias
 * @param $repository Repository
 * @param $id: Id to check new or update
 * @param $content: Content to Alias
 * @param $type: By shop or all (Defaul: Shop)
 * @param $params: shop_id, without_char: not replace in alias
 **/
if (!function_exists('create_alias')) {
    function create_alias($repository, $content, $id = '', $type = 'shop', $field = 'alias', $params = [])
    {
        if (!empty($params['without_char'])) {
            $content = str_replace($params['without_char'], 'hu-hu-hu', $content);
            // dd($content);
        }
        $alias = build_slug($content);
        // Revert lại ký tự đặc biệt
        if (!empty($params['without_char'])) {
            $alias = str_replace('hu-hu-hu', $params['without_char'], $alias);
        }
        // Nếu có upper case thì uppercase lại
        if (!empty($params['is_upper_case'])) {
            $alias = strtoupper($alias);
        }
        $dataRes = $repository->findByField($field, $alias);
        if (!empty($id)) {
            $dataRes = $dataRes->where('_id', '<>', mongo_id($id));
        }
        if ($type == 'shop') {
            if (!empty($params['shop_id'])) {
                $shop_id = $params['shop_id'];
            } else {
                $shop_id = Auth::getPayload()->get('shop_id');
            }
            $dataRes = $dataRes->where('shop_id', mongo_id($shop_id));
        }
        if ($type == 'shop-or-null') {
            if (!empty($params['shop_id'])) {
                $shop_id = $params['shop_id'];
            } else {
                $shop_id = Auth::getPayload()->get('shop_id');
            }
            $dataRes = $dataRes->whereIn('shop_id', [mongo_id($shop_id), null]);
        }
        if ($type == 'element') {
            if (!empty($params['type'])) {
                $eType = $params['type'];
                $dataRes = $dataRes->where('type', $eType);
            }
        }
        $data = $dataRes->first();
        if (empty($data)) {
            return $alias;
        } else {
            return $alias . '-' . time();
        }
    }
}

/**
 * CHECK VALID STRING
 * @param $string
 * @return array|mixed
 */
if (!function_exists('is_valid_key')) {
    function is_valid_key($str)
    {
        $validate_key = $str;
        if (
            !preg_match('/^[A-Z0-9_]+$/', $validate_key) ||
            preg_match('/^[0-9_]+$/', $validate_key)
        ) {
            return null;
        }
        return $str;
    }
}

//Validate Image Extention
if (!function_exists('validate_image_extention')) {
    function validate_image_extention($ext)
    {
        $imgExt = [
            'image/jpeg',
            'image/pjpeg',
            'image/png',
            'image/x-png',
            'image/gif'
        ];
        if (in_array(strtolower($ext), $imgExt)) {
            $tmpExt = explode("/", strtolower($ext));
            return $tmpExt[1];
        }
        return false;
    }
}

/**
 * Get client OS
 * @param $user_agent null
 * @return string
 */
if (!function_exists('get_client_platform')) {
    function get_client_platform($user_agent = null)
    {
        $detect = new \Detection\MobileDetect();
        $detect->setUserAgent($user_agent);
        if ($detect->isMobile() || $detect->isTablet()) {
            return 'Mobile-Web';
        } else {
            return 'Web';
        }
        return 'Unknown OS';
    }
}


/**
 * Get client OS
 * @param $user_agent null
 * @return string
 */
if (!function_exists('get_client_os')) {
    function get_client_os($user_agent = null)
    {
        if (!isset($user_agent) && isset($_SERVER['HTTP_USER_AGENT'])) {
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
        }

        // https://stackoverflow.com/questions/18070154/get-operating-system-info-with-php
        $os_array = [
            'windows nt 10'                              =>  'Windows 10',
            'windows nt 6.3'                             =>  'Windows 8.1',
            'windows nt 6.2'                             =>  'Windows 8',
            'windows nt 6.1|windows nt 7.0'              =>  'Windows 7',
            'windows nt 6.0'                             =>  'Windows Vista',
            'windows nt 5.2'                             =>  'Windows Server 2003/XP x64',
            'windows nt 5.1'                             =>  'Windows XP',
            'windows xp'                                 =>  'Windows XP',
            'windows nt 5.0|windows nt5.1|windows 2000'  =>  'Windows 2000',
            'windows me'                                 =>  'Windows ME',
            'windows nt 4.0|winnt4.0'                    =>  'Windows NT',
            'windows ce'                                 =>  'Windows CE',
            'windows 98|win98'                           =>  'Windows 98',
            'windows 95|win95'                           =>  'Windows 95',
            'win16'                                      =>  'Windows 3.11',
            'mac os x 10.1[^0-9]'                        =>  'Mac OS X Puma',
            'macintosh|mac os x'                         =>  'Mac OS X',
            'mac_powerpc'                                =>  'Mac OS 9',
            'linux'                                      =>  'Linux',
            'ubuntu'                                     =>  'Linux - Ubuntu',
            'iphone'                                     =>  'iPhone',
            'ipod'                                       =>  'iPod',
            'ipad'                                       =>  'iPad',
            'android'                                    =>  'Android',
            'blackberry'                                 =>  'BlackBerry',
            'webos'                                      =>  'Mobile',

            '(media center pc).([0-9]{1,2}\.[0-9]{1,2})' => 'Windows Media Center',
            '(win)([0-9]{1,2}\.[0-9x]{1,2})' => 'Windows',
            '(win)([0-9]{2})' => 'Windows',
            '(windows)([0-9x]{2})' => 'Windows',

            // Doesn't seem like these are necessary...not totally sure though..
            //'(winnt)([0-9]{1,2}\.[0-9]{1,2}){0,1}'=>'Windows NT',
            //'(windows nt)(([0-9]{1,2}\.[0-9]{1,2}){0,1})'=>'Windows NT', // fix by bg

            'Win 9x 4.90' => 'Windows ME',
            '(windows)([0-9]{1,2}\.[0-9]{1,2})' => 'Windows',
            'win32' => 'Windows',
            '(java)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2})' => 'Java',
            '(Solaris)([0-9]{1,2}\.[0-9x]{1,2}){0,1}' => 'Solaris',
            'dos x86' => 'DOS',
            'Mac OS X' => 'Mac OS X',
            'Mac_PowerPC' => 'Macintosh PowerPC',
            '(mac|Macintosh)' => 'Mac OS',
            '(sunos)([0-9]{1,2}\.[0-9]{1,2}){0,1}' => 'SunOS',
            '(beos)([0-9]{1,2}\.[0-9]{1,2}){0,1}' => 'BeOS',
            '(risc os)([0-9]{1,2}\.[0-9]{1,2})' => 'RISC OS',
            'unix' => 'Unix',
            'os/2' => 'OS/2',
            'freebsd' => 'FreeBSD',
            'openbsd' => 'OpenBSD',
            'netbsd' => 'NetBSD',
            'irix' => 'IRIX',
            'plan9' => 'Plan9',
            'osf' => 'OSF',
            'aix' => 'AIX',
            'GNU Hurd' => 'GNU Hurd',
            '(fedora)' => 'Linux - Fedora',
            '(kubuntu)' => 'Linux - Kubuntu',
            '(ubuntu)' => 'Linux - Ubuntu',
            '(debian)' => 'Linux - Debian',
            '(CentOS)' => 'Linux - CentOS',
            '(Mandriva).([0-9]{1,3}(\.[0-9]{1,3})?(\.[0-9]{1,3})?)' => 'Linux - Mandriva',
            '(SUSE).([0-9]{1,3}(\.[0-9]{1,3})?(\.[0-9]{1,3})?)' => 'Linux - SUSE',
            '(Dropline)' => 'Linux - Slackware (Dropline GNOME)',
            '(ASPLinux)' => 'Linux - ASPLinux',
            '(Red Hat)' => 'Linux - Red Hat',
            // Loads of Linux machines will be detected as unix.
            // Actually, all of the linux machines I've checked have the 'X11' in the User Agent.
            //'X11'=>'Unix',
            '(linux)' => 'Linux',
            '(amigaos)([0-9]{1,2}\.[0-9]{1,2})' => 'AmigaOS',
            'amiga-aweb' => 'AmigaOS',
            'amiga' => 'Amiga',
            'AvantGo' => 'PalmOS',
            //'(Linux)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3}(rel\.[0-9]{1,2}){0,1}-([0-9]{1,2}) i([0-9]{1})86){1}'=>'Linux',
            //'(Linux)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3}(rel\.[0-9]{1,2}){0,1} i([0-9]{1}86)){1}'=>'Linux',
            //'(Linux)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3}(rel\.[0-9]{1,2}){0,1})'=>'Linux',
            '[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3})' => 'Linux',
            '(webtv)/([0-9]{1,2}\.[0-9]{1,2})' => 'WebTV',
            'Dreamcast' => 'Dreamcast OS',
            'GetRight' => 'Windows',
            'go!zilla' => 'Windows',
            'gozilla' => 'Windows',
            'gulliver' => 'Windows',
            'ia archiver' => 'Windows',
            'NetPositive' => 'Windows',
            'mass downloader' => 'Windows',
            'microsoft' => 'Windows',
            'offline explorer' => 'Windows',
            'teleport' => 'Windows',
            'web downloader' => 'Windows',
            'webcapture' => 'Windows',
            'webcollage' => 'Windows',
            'webcopier' => 'Windows',
            'webstripper' => 'Windows',
            'webzip' => 'Windows',
            'wget' => 'Windows',
            'Java' => 'Unknown',
            'flashget' => 'Windows',

            // delete next line if the script show not the right OS
            //'(PHP)/([0-9]{1,2}.[0-9]{1,2})'=>'PHP',
            'MS FrontPage' => 'Windows',
            '(msproxy)/([0-9]{1,2}.[0-9]{1,2})' => 'Windows',
            '(msie)([0-9]{1,2}.[0-9]{1,2})' => 'Windows',
            'libwww-perl' => 'Unix',
            'UP.Browser' => 'Windows CE',
            'NetAnts' => 'Windows',
        ];

        $arch = '';
        // https://github.com/ahmad-sa3d/php-useragent/blob/master/core/user_agent.php
        if (strpos($user_agent, 'windows') !== false) {
            $arch_regex = '/\b(x86_64|x86-64|Win64|WOW64|x64|ia64|amd64|ppc64|sparc64|IRIX64)\b/ix';
            $arch = preg_match($arch_regex, $user_agent) ? ' x64' : ' x32';
        }
        foreach ($os_array as $regex => $value) {
            if (preg_match('{\b(' . $regex . ')\b}i', $user_agent)) {
                return $value . $arch;
            }
        }

        return 'Unknown OS';
    }
}
/**
 * Get client OS
 * @param $user_agent null
 * @return string
 */
if (!function_exists('get_client_browser')) {
    function get_client_browser($user_agent = null)
    {

        $browser = "Unknown Browser";

        $browser_array = array(
            '/msie/i' => 'Internet Explorer',
            '/firefox/i' => 'Firefox',
            '/safari/i' => 'Safari',
            '/chrome/i' => 'Chrome',
            '/edge/i' => 'Edge',
            '/opera/i' => 'Opera',
            '/netscape/i' => 'Netscape',
            '/maxthon/i' => 'Maxthon',
            '/konqueror/i' => 'Konqueror',
            '/mobile/i' => 'Mobile Browser'
        );

        foreach ($browser_array as $regex => $value)
            if (preg_match($regex, $user_agent))
                $browser = $value;

        return $browser;
    }
}
/**
 * BUILD A VALIDATE CODE FROM STRING
 * @param $string
 * @return string
 */
if (!function_exists('build_code')) {

    function build_code($string = '')
    {
        if (empty($string)) {
            return '';
        }
        $slugStr = preg_replace('/_/', ' ', $string);
        $slugStr = build_slug($slugStr);
        $slugStr = preg_replace('/-/', '_', $slugStr);
        return strtoupper(preg_replace('/[^a-zA-Z0-9_]/', '', $slugStr));
    }
}



/**
 * Computes the distance between two coordinates.
 *
 * Implementation based on reverse engineering of
 * <code>google.maps.geometry.spherical.computeDistanceBetween()</code>.
 *
 * @param float $lat1 Latitude from the first point.
 * @param float $lng1 Longitude from the first point.
 * @param float $lat2 Latitude from the second point.
 * @param float $lng2 Longitude from the second point.
 * @param float $radius (optional) Radius in meters.
 *
 * @return float Distance in meters.
 */
if (!function_exists('compute_distance')) {
    function compute_distance($lat1, $lng1, $lat2, $lng2, $radius = 6378137)
    {
        static $x = M_PI / 180;
        $lat1 *= $x;
        $lng1 *= $x;
        $lat2 *= $x;
        $lng2 *= $x;
        $distance = 2 * asin(sqrt(pow(sin(($lat1 - $lat2) / 2), 2) + cos($lat1) * cos($lat2) * pow(sin(($lng1 - $lng2) / 2), 2)));

        return $distance * $radius;
    }
}

/**
 * get_device_info
 * @param device
 * @return array
 **/
if (!function_exists('get_device_info')) {
    function get_device_info($request)
    {
        $device_info = [];
        $clientName = '';
        $clientId = '';
        $clientPlatform = '';
        $clientGaId = '';
        $clientGaRef = 'admin';
        $clientIp = get_client_ip();
        // Lấy DEVICE từ header hoặc params
        $deviceEncoded = '';
        $device = '';
        if (!empty($request->get('device'))) {
            $deviceEncoded = $request->get('device');
        }
        if (!empty($request->header('device'))) {
            $deviceEncoded = $request->header('device');
        }
        if (!empty($deviceEncoded)) {
            $device = base64_decode($deviceEncoded);
            $device = json_decode($device, true);
        }
        if (!empty($device)) {
            // Mobile
            if (empty($device['userAgent'])) {
                if (!empty($device['id']) && !empty($device['name'])) {
                    $clientName = $device['id'] . '/' . $device['name'];
                }
                if (!empty($device['imei'])) {
                    $clientId = $device['imei'];
                }
                if (!empty($device['os'])) {
                    $clientPlatform = $device['os'];
                }
            }
            // Web: Web hoặc Mobile Web. 
            else {
                $userAgent = $device['userAgent'];
                $clientName = get_client_os($userAgent);
                if (!empty($device['uuid'])) {
                    $clientId = $device['uuid'];
                }
                $clientPlatform = get_client_platform($userAgent);
                if (!empty($device['_ga'])) {
                    $clientGaId = $device['_ga'];
                }
            }
        }
        $device_info['client_name'] = $clientName;
        $device_info['client_id'] = $clientId;
        $device_info['client_platform'] = $clientPlatform;
        if (!empty($clientGaId)) {
            $device_info['client_ga_id'] = $clientGaId;
            $device_info['client_ga_referer'] = $clientGaRef;
        }
        $device_info['client_ip'] = $clientIp;

        return $device_info;
    }
}
