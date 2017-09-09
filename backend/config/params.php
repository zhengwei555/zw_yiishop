<?php
return [
    'adminEmail' => 'admin@example.com',
    'qiniuyun'=>[  //将图片上传七里云,并返回七里云地址
            'accessKey'=>'_Fj7hAZt_q8vrMbtIhCQHj5r5DhVrF_2Lkh-77ot',
            'secretKey'=>'sJk7ct2iw2vcLj_zE0t80XZfKFqSRwE2QrV5S1WD',
            'domain'=>'http://ovyfqayrd.bkt.clouddn.com/',
            'bucket'=>'zhengwei',
            'area'=>\flyok666\qiniu\Qiniu::AREA_HUADONG  //华东
        ]
];
