<?php

return [
        'useful_time' => 5, //驗證碼有效時間（分鐘）
        'captcha_characters' => '123456789',
        'sensitive' => false, //驗證碼是否判斷大小寫
        'login'   => [ //驗證碼樣式
            'length'    => 4, //驗證碼字數
            'width'     => 120, //圖片寬度
            'height'    => 36, //字體大小和圖片高度
            'angle'     => 10, //字體傾斜度
            'lines'     => 2, //橫線數
            'quality'   => 90, //品質
            'invert'    =>false, //反相
            'bgImage'   =>true, //背景圖
            'bgColor'   =>'#ffffff',
            'fontColors'=>['#339900','#ff3300','#9966ff','#3333ff'],//字體顏色
        ],
    ];
?>