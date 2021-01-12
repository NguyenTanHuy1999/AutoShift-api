<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute phải được chấp nhận.',
    'active_url' => ':attribute không phải là một URL hợp lệ.',
    'after' => ':attribute phải sau ngày :date.',
    'after_or_equal'   => ':attribute phải sau hoặc bằng ngày :date.',
    'alpha' => ':attribute chỉ có thể chứa ký tự chữ',
    'alpha_dash' => ':attribute chỉ có thể chứa ký tự chữ, số và dấu gạch ngang (-)',
    'alpha_num' => ':attribute chỉ có thể chứa ký tự chữ và số',
    'array'                => ':attribute phải là mảng.',
    'before' => ':attribute phải trước ngày :date.',
    'between' => array(
        'numeric' => ':attribute phải có giá trị trong khoản :min - :max.',
        'file' => ':attribute phải có kích thước trong khoản :min - :max kilobytes.',
        'string' => ':attribute phải có từ :min đến :max ký tự.',
        'array'   => ':attribute phải có từ :min đến :max phần tử.',
    ),
    'boolean'              => ':attribute phải là giá trị đúng/sai.',
    'confirmed' => 'Giá trị xác nhận :attribute không trùng khớp.',
    'date' => ':attribute không phải là một ngày hợp lệ.',
    'date_format' => ':attribute không phù hợp với định dạng :format.',
    'different' => ':attribute và :other phải khác nhau.',
    'digits' => ':attribute phải có :digits chữ số.',
    'digits_between' => ':attribute phải nằm trong khoản :min và :max chữ số.',
    'email' => 'Định dạng :attribute không hợp lệ.',
    'exists' => ':attribute đã chọn không hợp lệ.',
    'file'                 => ':attribute phải là một tập tin.',
    'filled'               => ':attribute bắt buộc phải có giá trị.',
    'image' => ':attribute phải là một tập tin ảnh.',
    'in' => ':attribute đã chọn không hợp lệ.',
    'integer' => ':attribute phải là một số nguyên.',
    'ip' => ':attribute phải là một địa chỉ IP hợp lệ.',
    'json'                 => ':attribute phải là chuỗi JSON.',
    'max' => array(
        'numeric' => ':attribute không được lớn hơn :max.',
        'file' => ':attribute không được lớn hơn :max kilobytes.',
        'string' => ':attribute không được dài hơn :max ký tự.',
        'array'   => ':attribute phải có ít nhất :min phần tử.',
    ),
    'mimes' => ':attribute phải là một tập tin có định dạng: :values.',
    'mimetypes'            => ':attribute phải là tập tin định dạng: :values.',
    'min' => array(
        'numeric' => ':attribute nhỏ nhất là :min.',
        'file' => ':attribute nhỏ nhất là :min kilobytes.',
        'string' => ':attribute ngắn nhất là :min ký tự.',
        'array'   => ':attribute phải có nhiều nhất :max phần tử.',
    ),
    'not_in' => 'Giá trị :attribute đã chọn không hợp lệ.',
    'numeric' => ':attribute phải là một giá trị số.',
    'present'              => ':attribute phải tồn tại.',
    'regex' => ':attribute không hợp lệ.',
    'required' => ':attribute bắt buộc phải có giá trị.',
    'required_if'          => ':attribute bắt buộc khi :other là :value.',
    'required_unless'      => ':attribute bắt buộc nhỏ hơn :other khi trong :values.',
    'required_with' => ':attribute bắt buộc phải nhập khi :values có giá trị.',
    'required_with_all'    => 'The :attribute field is required when :values is present.',
    'required_without' => ':attribute bắt buộc phải nhập khi :values không có giá trị.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same' => ':attribute và :other phải có giá trị giống nhau.',
    'size' => array(
        'numeric' => ':attribute phải bằng :size.',
        'file' => ':attribute phải bằng :size kilobytes.',
        'string' => ':attribute phải dài :size ký tự.',
        'array'   => 'The :attribute must contain :size items.',
    ),
    'string' => ':attribute phải là một chuỗi',
    'timezone' => 'The :attribute must be a valid zone.',
    'unique' => ':attribute đã bị chọn.',
    'url' => ':attribute không hợp lệ.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
        'emails' => [
            'emails' => 'Một trong những email của bạn không đúng định dạng.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'email' => 'Email',
        'captcha' => 'Mã bảo mật',
        'phone' => 'Số điện thoại',
        'name' => 'Tên',
        // Register Shop
        'shop_name' => 'Tên cửa hàng/trụ sở',
        'shop_username' => 'Tên đăng nhập cửa hàng/trụ sở',
        // Group
        'client_role' => 'Mã nhóm',
        'place' => 'Địa điểm',
        // KPI
        'unit' => 'Đơn vị',
        'password' => 'Mật khẩu',
    ],
    //Captcha
    'captcha' => 'Mã bảo mật không đúng, vui lòng nhập lại'
];
