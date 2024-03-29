<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    "accepted"=> "この項目の承認が必要です。",
    "accepted_if"=> ":Otherが:valueの場合、この項目の承認が必要です。",
    "active_url"=> "有効なURLではありません。",
    "after"=> ":Dateより後の日付を指定してください。",
    "after_or_equal"=> ":Date以降の日付を指定してください。",
    "alpha"=> "アルファベットのみ使用可能です。",
    "alpha_dash"=> "英数字、ハイフン、アンダースコアのみ使用可能です。",
    "alpha_num"=> "英数字のみ使用可能です。",
    "array"=> "配列でなければいけません。",
    "ascii"=> "半角英数字と記号のみ使用可能です。",
    "attached"=> "すでに添付されています。",
    "before"=> ":Date より前の日付を指定してください。",
    "before_or_equal"=> ":Date 以前の日付を指定してください。",
    "between" => [
        "array"=> ":Min ~ :max 個でなければいけません。",
        "file"=> "ファイルサイズは :min ~ :max KBの間でなければいけません。",
        "numeric"=> ":Min ~ :max の間でなければいけません。",
        "string"=> ":Min ~ :max 文字の間でなければいけません。",
    ],
    "boolean"=> "trueまたはfalseを指定してください。",
    "confirmed"=> "確認の内容が一致しません。",
    "country"=> "This field is not a valid country.",
    "date"=> "有効な日付ではありません。",
    "date_equals"=> ":Date 同じ日付を指定してください。",
    "date_format"=> ":Format の形と一致しません。",
    "decimal"=> "This field must have :decimal decimal places.",
    "declined"=> "拒否する必要があります。",
    "declined_if"=> ":Otherが:valueである場合、拒否する必要があります。",
    "different"=> ":Otherとは異なる値を指定する必要があります。",
    "digits"=> ":Digits桁でなければいけません。",
    "digits_between"=> ":Min ~ :max桁でなければいけません。",
    "dimensions"=> "画像の寸法が無効です。",
    "distinct"=> "値が重複しています。",
    "doesnt_end_with"=> "次のいずれかで終わってはいけません。: :values",
    "doesnt_start_with"=> "次のいずれかで始まってはいけません。: :values",
    "email"=> "無効なメールアドレスです。",
    "ends_with"=> "次のいずれかで終わる必要があります: :values",
    "enum"=> "選択された値は無効です。",
    "exists"=> "選択した値が無効です。",
    "file"=> "ファイルである必要があります。",
    "filled"=> "値がありません。",
    "gt" => [
        "array"=> "項目数は、:Value個より多い必要があります。",
        "file"=> "ファイルサイズは、:value KBより大きい必要があります。",
        "numeric"=> ":Value より大きい必要があります。",
        "string"=> ":Value文字より多い必要があります。",
    ],
    "gte" => [
        "array"=> ":Value 個以上でなければいけません。",
        "file"=> "ファイルサイズが :value KB以上でなければいけません。",
        "numeric"=> ":Value 以上でなければいけません。",
        "string"=> ":Value文字以上必要です。",
    ],
    "image"=> "画像でなければいけません。",
    "in"=> "選択した値が無効です。",
    "in_array"=> "この値は:otherに存在しません。",
    "integer"=> "数字でなければいけません。",
    "ip"=> "有効なIPアドレスである必要があります。",
    "ipv4"=> "有効なIPv4アドレスである必要があります。",
    "ipv6"=> "有効なIPv6アドレスである必要があります。",
    "json"=> "有効なJSON文字列である必要があります。",
    "lowercase"=> "小文字で入力してください。",
    "lt" => [
        "array"=> ":Value 個より少なくなければいけません。",
        "file"=> "ファイルサイズが :value KBより小さくなければいけません。",
        "numeric"=> ":Value より小さくなければいけません。",
        "string"=> ":Value文字より少なければいけません。",
    ],
    "lte" => [
        "array"=> ":Value 個以下でなければいけません。",
        "file"=> "ファイルサイズが :value KB以下でなければいけません。",
        "numeric"=> ":Value 以下でなければいけません。",
        "string"=> ":Value文字以下でなければいけません。",
    ],
    "mac_address"=> "この値は有効なMACアドレスでなければならない。",
    "max" => [
        "array"=> "項目数は、:max個以下でなければいけません。",
        "file"=> "ファイルサイズは、:max KB以下でなければいけません。",
        "numeric"=> ":Max以下の数字でなければいけません。",
        "string"=> "文字数は、:max文字以下でなければいけません。",
    ],
    "max_digits"=> ":Max桁以下の数字でなければいけません。",
    "mimes"=> ":Valuesのファイルである必要があります。",
    "mimetypes"=> ":Valuesのファイルである必要があります。",
    "min" => [
        "array"=> "項目数は、:min個以上でなければいけません。",
        "file"=> "ファイルサイズが :min KB以上でなければいけません。",
        "numeric"=> ":Min以上の数字でなければいけません。",
        "string"=> "文字数は、:min文字以上必要です。",
    ],
    "min_digits"=> ":Max桁以上の数字でなければいけません。",
    "multiple_of"=> ":Valueの倍数でなければいけません。",
    "not_in"=> "選択した値が無効です。",
    "not_regex"=> "この形式は無効です。",
    "numeric"=> "数字でなければいけません。",
    "password" => [
        "letters"=> "この項目は文字を1文字以上含む必要があります。",
        "mixed"=> "この項目は大文字と小文字をそれぞれ1文字以上含む必要があります。",
        "numbers"=> "この項目は数字を1文字以上含む必要があります。",
        "symbols"=> "この項目は記号を1文字以上含む必要があります。",
        "uncompromised"=> "この項目は情報漏洩した可能性があります。他の項目を選んでください。",
    ],
    "present"=> "この項目は必須です。",
    "prohibited"=> "この項目の入力は禁止されています。",
    "prohibited_if"=> ":Otherが:valueの場合、この項目の入力は禁止されています。",
    "prohibited_unless"=> ":Otherが:valuesでない限り、この項目の入力は禁止されています。",
    "prohibits"=> ":Otherが存在している場合、この項目の入力は禁止されています。",
    "regex"=> "この形式は無効です。",
    "relatable"=> "このリソースと関連づけられません。",
    "required"=> "この項目は必須です。",
    "required_array_keys"=> ":Valuesのエントリを含める必要があります。",
    "required_if"=> ":Otherが:valueの場合、この項目は必須です。",
    "required_if_accepted"=> ":Otherを承認した場合、この項目は必須です。",
    "required_unless"=> ":Otherが:valuesでない限り、この項目は必須です。",
    "required_with"=> ":Valuesが存在する場合、この項目は必須です。",
    "required_with_all"=> ":Valuesが存在する場合、この項目は必須です。",
    "required_without"=> ":Valuesが存在しない場合、この項目は必須です。",
    "required_without_all"=> ":Valuesのいずれも存在しない場合、この項目は必須です。",
    "same"=> ":Otherの値と一致しません。",
    "size" => [
        "array"=> ":Size 個含まれていないといけません。",
        "file"=> ":Size KBでないといけません。",
        "numeric"=> ":Size でないといけません。",
        "string"=> ":Size 文字でないといけません。",
    ],
    "starts_with"=> "次のいずれかから始まる必要があります: :values",
    "string"=> "文字列でなければいけません。",
    "timezone"=> "有効なタイムゾーンである必要があります。",
    "ulid"=> "有効なULIDである必要があります。",
    "unique"=> "既に使用されています。",
    "uploaded"=> "アップロードに失敗しました。",
    "uppercase"=> "大文字で入力してください。",
    "url"=> "有効なURL形式を指定する必要があります。",
    "uuid"=> "有効なUUIDである必要があります。",
    "Whoops! Something went wrong." => "エラーの内容を確認してください。",

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
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes'           => [
        'name'  => '名前',
        'email'  => 'メールアドレス',
        'password'  => 'パスワード',
    ],

];
