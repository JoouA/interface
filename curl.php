<?php
    //初始化
    $curl = curl_init();
    //设置url
    curl_setopt($curl, CURLOPT_URL, 'https://www.baidu.com');
    //设置返回获取的输出为文本流
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    //执行命令
    $data = curl_exec($curl);
    //关闭URL请求
    curl_close($curl);
    //显示获得的数据
    print_r($data);
?>