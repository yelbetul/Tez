<?php

return [
    'required' => ':attribute alanı zorunludur.',
    'max' => ':attribute alanı en fazla :max karakter olabilir.',
    'min' => ':attribute alanı en az :min karakter olmalıdır.',
    'integer' => ':attribute alanı tam sayı olmalıdır.',
    'boolean' => ':attribute alanı sadece true veya false olabilir.',
    'string' => ':attribute alanı metin formatında olmalıdır.',
    'exists' => 'Seçilen :attribute geçerli değil.',
    'regex' => ':attribute formatı geçersiz.',
    'numeric' => ':attribute alanı sayısal olmalıdır.',

    /*
    |--------------------------------------------------------------------------
    | Özelleştirilmiş Doğrulama Mesajları
    |--------------------------------------------------------------------------
    |
    | "custom" anahtarı ile spesifik alanlar için özelleştirilmiş doğrulama
    | mesajları belirleyebilirsiniz. Bu, belirli bir alanın doğrulama mesajını
    | değiştirmek için kullanılır.
    |
    */
    'custom' => [
        'year' => [
            'required' => 'Yıl alanı zorunludur!',
            'max' => 'Yıl alanı :max karakterden uzun olamaz.',
        ],
        'group_id' => [
            'required' => 'Grup ID alanı zorunludur!',
            'exists' => 'Seçilen grup ID geçerli değil.',
        ],
        'gender' => [
            'required' => 'Cinsiyet alanı zorunludur!',
            'boolean' => 'Cinsiyet alanı sadece true veya false olabilir.',
        ],
        'works_on_accident_day' => [
            'required' => 'Kaza günü çalışan kişi sayısı zorunludur!',
            'integer' => 'Bu alan tam sayı olmalıdır.',
        ],
        'occupational_disease_cases' => [
            'required' => 'Meslek hastalığı vakaları alanı zorunludur!',
            'integer' => 'Bu alan tam sayı olmalıdır.',
            'max' => 'Meslek hastalığı vakaları en fazla :max olabilir.',
        ],
        'work_accident_fatalities' => [
            'required' => 'İş kazası sonucu ölenler alanı zorunludur!',
            'integer' => 'İş kazası sonucu ölenler değeri tam sayı olmalıdır.',
            'min' => 'İş kazası sonucu ölenler alanı 0 veya daha büyük olmalıdır.',
        ],
        'occupational_disease_fatalities' => [
            'required' => 'Meslek hastalığı sonucu ölenler alanı zorunludur!',
            'integer' => 'Meslek hastalığı sonucu ölenler değeri tam sayı olmalıdır.',
            'min' => 'Meslek hastalığı sonucu ölenler alanı 0 veya daha büyük olmalıdır.',
        ],
        'is_outpatient' => [
            'required' => 'Ayakta tedavi durumu zorunludur!',
            'boolean' => 'Ayakta tedavi durumu sadece true veya false olabilir.',
        ],
        'is_inpatient' => [
            'required' => 'Yatarak tedavi durumu zorunludur!',
            'boolean' => 'Yatarak tedavi durumu sadece true veya false olabilir.',
        ],
        'one_day_unfit' => [
            'required' => '1 gün iş göremez alanı zorunludur!',
            'integer' => '1 gün iş göremez değeri tam sayı olmalıdır.',
            'min' => '1 gün iş göremez alanı 0 veya daha büyük olmalıdır.',
        ],
        'two_days_unfit' => [
            'required' => '2 gün iş göremez alanı zorunludur!',
            'integer' => '2 gün iş göremez değeri tam sayı olmalıdır.',
            'min' => '2 gün iş göremez alanı 0 veya daha büyük olmalıdır.',
        ],
        'three_days_unfit' => [
            'required' => '3 gün iş göremez alanı zorunludur!',
            'integer' => '3 gün iş göremez değeri tam sayı olmalıdır.',
            'min' => '3 gün iş göremez alanı 0 veya daha büyük olmalıdır.',
        ],
        'four_days_unfit' => [
            'required' => '4 gün iş göremez alanı zorunludur!',
            'integer' => '4 gün iş göremez değeri tam sayı olmalıdır.',
            'min' => '4 gün iş göremez alanı 0 veya daha büyük olmalıdır.',
        ],
        'five_or_more_days_unfit' => [
            'required' => '5 ve daha fazla gün iş göremez alanı zorunludur!',
            'integer' => '5 ve daha fazla gün iş göremez değeri tam sayı olmalıdır.',
            'min' => '5 ve daha fazla gün iş göremez alanı 0 veya daha büyük olmalıdır.',
        ],
    ],

    // Alan isimlerini Türkçeleştirme
    'attributes' => [
        'year' => 'Yıl',
        'group_id' => 'Grup ID',
        'gender' => 'Cinsiyet',
        'works_on_accident_day' => 'Kaza günü çalışan kişi sayısı',
        'occupational_disease_cases' => 'Meslek hastalığı vakaları',
        'work_accident_fatalities' => 'İş kazası sonucu ölenler',
        'occupational_disease_fatalities' => 'Meslek hastalığı sonucu ölenler',
        'is_outpatient' => 'Ayakta tedavi',
        'is_inpatient' => 'Yatarak tedavi',
        'one_day_unfit' => '1 gün iş göremez',
        'two_days_unfit' => '2 gün iş göremez',
        'three_days_unfit' => '3 gün iş göremez',
        'four_days_unfit' => '4 gün iş göremez',
        'five_or_more_days_unfit' => '5 ve daha fazla gün iş göremez',
    ],
];
