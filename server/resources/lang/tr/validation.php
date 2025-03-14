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
    'unique' => ':attribute daha önce kullanılmış, tekrar edemez.',
    'size' => ':attribute alanı tam olarak :size uzunluğunda olmalıdır.',

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
            'required' => 'Yıl alanı zorunludur.',
            'integer' => 'Yıl geçerli bir tam sayı olmalıdır.',
            'max' => 'Yıl alanı :max karakterden uzun olamaz.',
            'min' => 'Yıl alanı :min karakterden kısa olamaz.',
            'sometimes' => 'Yıl belirtilmişse geçerli olmalıdır.',
        ],

        'group_id' => [
            'required' => 'Grup ID alanı zorunludur.',
            'exists' => 'Seçilen grup ID geçerli değil.',
            'integer' => 'Grup ID geçerli bir tam sayı olmalıdır.',
            'sometimes' => 'Grup ID belirtilmişse geçerli olmalıdır.',
        ],

        'gender' => [
            'required' => 'Cinsiyet alanı zorunludur.',
            'boolean' => 'Cinsiyet alanı sadece true veya false olabilir.',
            'sometimes' => 'Cinsiyet belirtilmişse geçerli olmalıdır.',
        ],

        'works_on_accident_day' => [
            'required' => 'Kaza günü çalışan kişi sayısı zorunludur.',
            'integer' => 'Bu alan tam sayı olmalıdır.',
            'min' => 'Kaza günü çalışan kişi sayısı 0 veya daha büyük olmalıdır.',
            'sometimes' => 'Kaza günü çalışan kişi sayısı belirtilmişse geçerli olmalıdır.',
        ],

        'occupational_disease_cases' => [
            'required' => 'Meslek hastalığı vakaları alanı zorunludur.',
            'integer' => 'Bu alan tam sayı olmalıdır.',
            'max' => 'Meslek hastalığı vakaları en fazla :max olabilir.',
            'sometimes' => 'Meslek hastalığı vakaları belirtilmişse geçerli olmalıdır.',
        ],

        'work_accident_fatalities' => [
            'required' => 'İş kazası sonucu ölenler alanı zorunludur.',
            'integer' => 'İş kazası sonucu ölenler değeri tam sayı olmalıdır.',
            'min' => 'İş kazası sonucu ölenler alanı 0 veya daha büyük olmalıdır.',
            'sometimes' => 'İş kazası sonucu ölenler belirtilmişse geçerli olmalıdır.',
        ],

        'occupational_disease_fatalities' => [
            'required' => 'Meslek hastalığı sonucu ölenler alanı zorunludur.',
            'integer' => 'Meslek hastalığı sonucu ölenler değeri tam sayı olmalıdır.',
            'min' => 'Meslek hastalığı sonucu ölenler alanı 0 veya daha büyük olmalıdır.',
            'sometimes' => 'Meslek hastalığı sonucu ölenler belirtilmişse geçerli olmalıdır.',
        ],

        'is_outpatient' => [
            'required' => 'Ayakta tedavi durumu zorunludur.',
            'boolean' => 'Ayakta tedavi durumu sadece true veya false olabilir.',
            'sometimes' => 'Ayakta tedavi durumu belirtilmişse geçerli olmalıdır.',
        ],

        'is_inpatient' => [
            'required' => 'Yatarak tedavi durumu zorunludur.',
            'boolean' => 'Yatarak tedavi durumu sadece true veya false olabilir.',
            'sometimes' => 'Yatarak tedavi durumu belirtilmişse geçerli olmalıdır.',
        ],

        'one_day_unfit' => [
            'required' => '1 gün iş göremez alanı zorunludur.',
            'integer' => '1 gün iş göremez değeri tam sayı olmalıdır.',
            'min' => '1 gün iş göremez alanı 0 veya daha büyük olmalıdır.',
            'sometimes' => '1 gün iş göremez belirtilmişse geçerli olmalıdır.',
        ],

        'two_days_unfit' => [
            'required' => '2 gün iş göremez alanı zorunludur.',
            'integer' => '2 gün iş göremez değeri tam sayı olmalıdır.',
            'min' => '2 gün iş göremez alanı 0 veya daha büyük olmalıdır.',
            'sometimes' => '2 gün iş göremez belirtilmişse geçerli olmalıdır.',
        ],

        'three_days_unfit' => [
            'required' => '3 gün iş göremez alanı zorunludur.',
            'integer' => '3 gün iş göremez değeri tam sayı olmalıdır.',
            'min' => '3 gün iş göremez alanı 0 veya daha büyük olmalıdır.',
            'sometimes' => '3 gün iş göremez belirtilmişse geçerli olmalıdır.',
        ],

        'four_days_unfit' => [
            'required' => '4 gün iş göremez alanı zorunludur.',
            'integer' => '4 gün iş göremez değeri tam sayı olmalıdır.',
            'min' => '4 gün iş göremez alanı 0 veya daha büyük olmalıdır.',
            'sometimes' => '4 gün iş göremez belirtilmişse geçerli olmalıdır.',
        ],

        'five_or_more_days_unfit' => [
            'required' => '5 ve daha fazla gün iş göremez alanı zorunludur.',
            'integer' => '5 ve daha fazla gün iş göremez değeri tam sayı olmalıdır.',
            'min' => '5 ve daha fazla gün iş göremez alanı 0 veya daha büyük olmalıdır.',
            'sometimes' => '5 ve daha fazla gün iş göremez belirtilmişse geçerli olmalıdır.',
        ],

        'sector_code' => [
            'required' => 'Sektör kodu alanı zorunludur.',
            'string' => 'Sektör kodu geçersiz. Sadece metin olabilir.',
            'max' => 'Sektör kodu en fazla :max karakter olabilir.',
            'unique' => 'Bu sektör kodu zaten kullanılıyor.',
            'sometimes' => 'Sektör kodu belirtilmişse geçerli olmalıdır.',
        ],

        'diagnosis_code' => [
            'required' => 'Tanı kodu alanı zorunludur.',
            'string' => 'Tanı kodu geçersiz. Sadece metin olabilir.',
            'max' => 'Tanı kodu en fazla :max karakter olabilir.',
            'unique' => 'Bu tanı kodu zaten mevcut.',
            'sometimes' => 'Tanı kodu belirtilmişse geçerli olmalıdır.',
        ],

        'group_code' => [
            'required' => 'Grup kodu alanı zorunludur.',
            'string' => 'Grup kodu geçersiz. Sadece metin olabilir.',
            'max' => 'Grup kodu en fazla :max karakter olabilir.',
            'sometimes' => 'Grup kodu belirtilmişse geçerli olmalıdır.',
        ],

        'group_name' => [
            'required' => 'Grup adı alanı zorunludur.',
            'string' => 'Grup adı geçersiz. Sadece metin olabilir.',
            'max' => 'Grup adı en fazla :max karakter olabilir.',
            'sometimes' => 'Grup adı belirtilmişse geçerli olmalıdır.',
        ],

        'sub_group_code' => [
            'required' => 'Alt grup kodu alanı zorunludur.',
            'string' => 'Alt grup kodu geçersiz. Sadece metin olabilir.',
            'max' => 'Alt grup kodu en fazla :max karakter olabilir.',
            'sometimes' => 'Alt grup kodu belirtilmişse geçerli olmalıdır.',
        ],

        'sub_group_name' => [
            'required' => 'Alt grup adı alanı zorunludur.',
            'string' => 'Alt grup adı geçersiz. Sadece metin olabilir.',
            'max' => 'Alt grup adı en fazla :max karakter olabilir.',
            'sometimes' => 'Alt grup adı belirtilmişse geçerli olmalıdır.',
        ],

        'pure_code' => [
            'required' => 'Saf kod alanı zorunludur.',
            'max' => 'Saf kod en fazla :max karakter olabilir.',
            'sometimes' => 'Saf kod belirtilmişse geçerli olmalıdır.',
        ],

        'pure_name' => [
            'required' => 'Saf adı alanı zorunludur.',
            'max' => 'Saf adı en fazla :max karakter olabilir.',
            'sometimes' => 'Saf adı belirtilmişse geçerli olmalıdır.',
        ],

        'province_code' => [
            'required' => 'İl kodu alanı zorunludur.',
            'size' => 'İl kodu tam olarak :size karakter olmalıdır.',
            'unique' => 'Bu il kodu zaten kullanılıyor.',
            'sometimes' => 'İl kodu belirtilmişse geçerli olmalıdır.',
        ],

        'province_name' => [
            'required' => 'İl adı alanı zorunludur.',
            'max' => 'İl adı en fazla :max karakter olabilir.',
            'sometimes' => 'İl adı belirtilmişse geçerli olmalıdır.',
        ],

        'age' => [
            'required' => 'Yaş alanı zorunludur.',
            'integer' => 'Yaş, geçerli bir tam sayı olmalıdır.',
            'min' => 'Yaş en az :min olmalıdır.',
            'max' => 'Yaş en fazla :max olabilir.',
            'unique' => 'Bu yaş kodu zaten mevcut.',
            'sometimes' => 'Yaş kodu belirtilmişse geçerli olmalıdır.',
        ],

        'month_name' => [
            'required' => 'Ay adı alanı zorunludur!',
            'string' => 'Ay adı geçersiz. Sadece metin olabilir.',
            'max' => 'Ay adı en fazla :max karakter olabilir.',
            'unique' => 'Bu ay adı zaten mevcut.',
            'sometimes' => 'Ay adı belirtilmişse geçerli olmalıdır.',
        ],

        'code' => [
            'required' => 'Kod alanı zorunludur.',
            'string' => 'Kod geçersiz. Sadece metin olabilir.',
            'max' => 'Kod en fazla :max karakter olabilir.',
            'unique' => 'Bu kod zaten kullanılıyor.',
            'sometimes' => 'Kod belirtilmişse geçerli olmalıdır.',
        ],

        'occupation_code' => [
            'required' => 'Meslek kodu alanı zorunludur.',
            'string' => 'Meslek kodu geçersiz. Sadece metin olabilir.',
            'max' => 'Meslek kodu en fazla :max karakter olabilir.',
            'sometimes' => 'Meslek kodu belirtilmişse geçerli olmalıdır.',
        ],

        'occupation_name' => [
            'required' => 'Meslek adı alanı zorunludur.',
            'string' => 'Meslek adı geçersiz. Sadece metin olabilir.',
            'max' => 'Meslek adı en fazla :max karakter olabilir.',
            'sometimes' => 'Meslek adı belirtilmişse geçerli olmalıdır.',
        ],

        'injury_code' => [
            'required' => 'Yaralanma kodu alanı zorunludur.',
            'string' => 'Yaralanma kodu geçersiz. Sadece metin olabilir.',
            'max' => 'Yaralanma kodu en fazla :max karakter olabilir.',
            'unique' => 'Bu yaralanma kodu zaten kullanılıyor.',
            'sometimes' => 'Yaralanma kodu belirtilmişse geçerli olmalıdır.',
        ],

        'injury_location_code' => [
            'required' => 'Yaralanma yeri kodu alanı zorunludur.',
            'string' => 'Yaralanma yeri kodu geçersiz. Sadece metin olabilir.',
            'max' => 'Yaralanma yeri kodu en fazla :max karakter olabilir.',
            'unique' => 'Bu yaralanma yeri kodu zaten kullanılıyor.',
            'sometimes' => 'Yaralanma yeri kodu belirtilmişse geçerli olmalıdır.',
        ],

        'injury_cause_code' => [
            'required' => 'Yaralanma sebebi kodu alanı zorunludur.',
            'string' => 'Yaralanma sebebi kodu geçersiz. Sadece metin olabilir.',
            'max' => 'Yaralanma sebebi kodu en fazla :max karakter olabilir.',
            'unique' => 'Bu yaralanma sebebi kodu zaten kullanılıyor.',
            'sometimes' => 'Yaralanma sebebi kodu belirtilmişse geçerli olmalıdır.',
        ],

        'general_activity_code' => [
            'required' => 'Genel faaliyet kodu alanı zorunludur.',
            'string' => 'Genel faaliyet kodu geçersiz. Sadece metin olabilir.',
            'max' => 'Genel faaliyet kodu en fazla :max karakter olabilir.',
            'unique' => 'Bu genel faaliyet kodu zaten kullanılıyor.',
            'sometimes' => 'Genel faaliyet kodu belirtilmişse geçerli olmalıdır.',
        ],

        'special_activity_code' => [
            'required' => 'Özel faaliyet kodu alanı zorunludur.',
            'string' => 'Özel faaliyet kodu alanı metin formatında olmalıdır.',
            'max' => 'Özel faaliyet kodu en fazla :max karakter olabilir.',
            'unique' => 'Bu özel faaliyet kodu zaten kullanılıyor.',
            'sometimes' => 'Özel faaliyet kodu belirtilmişse geçerli olmalıdır.',
        ],

        'deviation_code' => [
            'required'  => 'Sapma kodu alanı zorunludur.',
            'string'    => 'Sapma kodu alanı metin formatında olmalıdır.',
            'max'       => 'Sapma kodu en fazla :max karakter olabilir.',
            'unique'    => 'Bu sapma kodu daha önce kullanılmış, tekrar edemez.',
            'sometimes' => 'Sapma kodu belirtilmişse geçerli olmalıdır.',
        ],

        'material_code' => [
            'required'  => 'Malzeme kodu alanı zorunludur.',
            'string'    => 'Malzeme kodu alanı metin formatında olmalıdır.',
            'max'       => 'Malzeme kodu en fazla :max karakter olabilir.',
            'unique'    => 'Bu malzeme kodu daha önce kullanılmış, tekrar edemez.',
            'sometimes' => 'Malzeme kodu belirtilmişse geçerli olmalıdır.',
        ],

        'environment_code' => [
            'required'  => 'Çalışma ortamı kodu alanı zorunludur.',
            'string'    => 'Çalışma ortamı kodu metin formatında olmalıdır.',
            'max'       => 'Çalışma ortamı kodu en fazla :max karakter olabilir.',
            'unique'    => 'Bu çalışma ortamı kodu daha önce kullanılmış, tekrar edemez.',
            'sometimes' => 'Çalışma ortamı kodu belirtilmişse geçerli olmalıdır.',
        ],

        'workstation_code' => [
            'required'  => 'İş istasyonu kodu alanı zorunludur.',
            'string'    => 'İş istasyonu kodu metin formatında olmalıdır.',
            'max'       => 'İş istasyonu kodu en fazla :max karakter olabilir.',
            'unique'    => 'Bu iş istasyonu kodu daha önce kullanılmış, tekrar edemez.',
            'sometimes' => 'İş istasyonu kodu belirtilmişse geçerli olmalıdır.',
        ],

        'workstation_name' => [
            'required'  => 'İş istasyonu adı alanı zorunludur.',
            'string'    => 'İş istasyonu adı metin formatında olmalıdır.',
            'max'       => 'İş istasyonu adı en fazla :max karakter olabilir.',
            'sometimes' => 'İş istasyonu adı belirtilmişse geçerli olmalıdır.',
        ],

        'time_interval' => [
            'required'  => 'Zaman aralığı alanı zorunludur.',
            'string'    => 'Zaman aralığı metin formatında olmalıdır.',
            'max'       => 'Zaman aralığı en fazla :max karakter olabilir.',
            'sometimes' => 'Zaman aralığı belirtilmişse geçerli olmalıdır.',
        ],

        'employee_count' => [
            'required'  => 'Çalışan sayısı alanı zorunludur.',
            'string'    => 'Çalışan sayısı alanı metin formatında olmalıdır.',
            'max'       => 'Çalışan sayısı en fazla :max karakter olabilir.',
            'sometimes' => 'Çalışan sayısı belirtilmişse geçerli olmalıdır.',
        ],

        'employment_duration' => [
            'required'  => 'Çalışma süresi alanı zorunludur.',
            'string'    => 'Çalışma süresi alanı metin formatında olmalıdır.',
            'max'       => 'Çalışma süresi en fazla :max karakter olabilir.',
            'sometimes' => 'Çalışma süresi belirtilmişse geçerli olmalıdır.',
        ],

        'age_id' => [
            'required'  => 'Yaş kodu alanı zorunludur.',
            'integer'   => 'Yaş kodu tam sayı olmalıdır.',
            'exists'    => 'Seçilen yaş kodu geçerli değil.',
            'sometimes' => 'Yaş kodu belirtilmişse geçerli olmalıdır.',
        ],

        'work_accident_fatalities' => [
            'required'  => 'İş kazası sonucu ölenler alanı zorunludur.',
            'integer'   => 'İş kazası sonucu ölenler alanı tam sayı olmalıdır.',
            'sometimes' => 'İş kazası sonucu ölenler belirtilmişse geçerli olmalıdır.',
        ],

        'occupational_disease_fatalities' => [
            'required'  => 'Meslek hastalığı sonucu ölenler alanı zorunludur.',
            'integer'   => 'Meslek hastalığı sonucu ölenler alanı tam sayı olmalıdır.',
            'sometimes' => 'Meslek hastalığı sonucu ölenler belirtilmişse geçerli olmalıdır.',
        ],

        'diagnosis_code' => [
            'required'  => 'Tanı kodu alanı zorunludur.',
            'integer'   => 'Tanı kodu alanı tam sayı olmalıdır.',
            'exists'    => 'Seçilen tanı kodu geçerli değil.',
            'sometimes' => 'Tanı kodu belirtilmişse geçerli olmalıdır.',
        ],

        'month_id' => [
            'required'  => 'Ay ID alanı zorunludur.',
            'integer'   => 'Ay ID alanı tam sayı olmalıdır.',
            'exists'    => 'Seçilen ay ID geçerli değil.',
            'sometimes' => 'Ay ID belirtilmişse geçerli olmalıdır.',
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
        'sector_code' => 'Sektör Kodu',
        'group_code' => 'Grup Kodu',
        'group_name' => 'Grup Adı',
        'sub_group_code' => 'Alt Grup Kodu',
        'sub_group_name' => 'Alt Grup Adı',
        'pure_code' => 'Saf Kod',
        'pure_name' => 'Saf Adı',
        'province_code' => 'İl Kodu',
        'province_name' => 'İl Adı',
        'age' => 'Yaş',
        'month_name' => 'Ay',
        'code' => 'Kod',
        'occupation_code' => 'Meslek Kodu',
        'occupation_name' => 'Meslek Adı',
        'injury_code' => 'Yaralanma Kodu',
        'injury_location_code' => 'Yaralanma Yeri Kodu',
        'injury_cause_code' => 'Yaralanma Sebebi Kodu',
        'general_activity_code' => 'Genel Faaliyet Kodu',
        'special_activity_code' => 'Özel Faaliyet Kodu',
        'deviation_code' => 'Sapma Kodu',
        'material_code' => 'Malzeme Kodu',
        'environment_code' => 'Çalışma Ortamı Kodu',
        'workstation_code' => 'İş İstasyonu Kodu',
        'workstation_name' => 'İş İstasyonu Adı',
        'time_interval' => 'Zaman Aralığı',
        'employee_count' => 'Çalışan Sayısı',
        'employment_duration' => 'Çalışma Süresi',
        'age_id'              => 'Yaş Kodu',
        'diagnosis_code' => 'Tanı Kodu',
        'month_id' => 'Ay ID',
    ],
];
