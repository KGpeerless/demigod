<?php
namespace GKL\Helper;

class Mobile
{
    private static $device;
    private static $httpAccept;
    private static $defaultMobiles = [
        'mobileexplorer'       => 'Mobile Explorer',
        'palmsource'           => 'Palm',
        'palmscape'            => 'Palmscape',
        'motorola'             => "Motorola",
        'nokia'                => "Nokia",
        'palm'                 => "Palm",

        // Apple
        'ipad'                 => "iPad",
        'iphone'               => "Apple iPhone",
        'ipod'                 => "Apple iPod Touch",

        // Samsung Tabs
        'SPH-P100'             => "Samsung SPH-P100 (Galaxy Tab Tablet on Sprint)",
        'GT-P1000'             => "Samsung Tab",

        // Outros
        'sony'                 => "Sony Ericsson",
        'ericsson'             => "Sony Ericsson",
        'blackberry'           => "BlackBerry",
        'cocoon'               => "O2 Cocoon",
        'blazer'               => "Treo",
        'lg'                   => "LG",
        'amoi'                 => "Amoi",
        'xda'                  => "XDA",
        'mda'                  => "MDA",
        'vario'                => "Vario",
        'htc_tattoo'           => "HTC Android",
        'samsung'              => "Samsung",
        'sharp'                => "Sharp",
        'sie-'                 => "Siemens",
        'alcatel'              => "Alcatel",
        'benq'                 => "BenQ",
        'ipaq'                 => "HP iPaq",
        'mot-'                 => "Motorola",
        'playstation portable' => "PlayStation Portable",
        'hiptop'               => "Danger Hiptop",
        'nec-'                 => "NEC",
        'panasonic'            => "Panasonic",
        'philips'              => "Philips",
        'sagem'                => "Sagem",
        'sanyo'                => "Sanyo",
        'spv'                  => "SPV",
        'zte'                  => "ZTE",
        'sendo'                => "Sendo",

        // Operating Systems
        'symbian'              => "Symbian",
        'SymbianOS'            => "SymbianOS",
        'elaine'               => "Palm",
        'palm'                 => "Palm",
        'series60'             => "Symbian S60",
        'windows ce'           => "Windows CE",
        'android'              => "Google OS",

        // Browsers
        'obigo'                => "Obigo",
        'netfront'             => "Netfront Browser",
        'openwave'             => "Openwave Browser",
        'mobilexplorer'        => "Mobile Explorer",
        'operamini'            => "Opera Mini",
        'opera mini'           => "Opera Mini",

        // Other
        'digital paths'        => "Digital Paths",
        'avantgo'              => "AvantGo",
        'xiino'                => "Xiino",
        'novarra'              => "Novarra Transcoder",
        'vodafone'             => "Vodafone",
        'docomo'               => "NTT DoCoMo",
        'o2'                   => "O2",

        // Fallback
        'mobile'               => "Generic Mobile",
        'wireless'             => "Generic Mobile",
        'j2me'                 => "Generic Mobile",
        'midp'                 => "Generic Mobile",
        'cldc'                 => "Generic Mobile",
        'up.link'              => "Generic Mobile",
        'up.browser'           => "Generic Mobile",
        'smartphone'           => "Generic Mobile",
        'cellphone'            => "Generic Mobile"
    ];

    public function __construct()
    {
        self::$httpAccept = $_SERVER["HTTP_ACCEPT"];
    }

    public static function setDevice($device)
    {
        self::$device = $device;
    }

    public static function getDevice()
    {
        return self::$device;
    }

    /**
     * @return bool
     */
    public static function check()
    {
        if (is_array(self::$defaultMobiles)) {
            foreach (self::$defaultMobiles as $key => $val) {

                if (preg_match("/" . $key . "/i", strtolower($_SERVER["HTTP_USER_AGENT"])) == 1) {
                    self::setDevice($val);

                    return true;
                }
            }
        }

        return false;
    }
}