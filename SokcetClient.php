<?php

/**
 * 1. 创建socket客户端连接
 * 2. 获取客户端输入
 * 3. 发送客户端请求
 * 3. 得到结果并输出
 */


$client = stream_socket_client("tcp://127.0.0.1:8765", $errNo, $errStr);


if (!$client) {
    die(sprintf("socket客户端创建失败:%s,%s\n", $errNo, $errStr));
}

while (true) {

    echo "\n请输入命令:\n";
    // 得到用户输入信息
    $message = trim(fgets(STDIN));

    if (empty($message)) {
        echo "输入命令不能为空，请重新输入\n";
    }

    if ($message == "exit") {
        break;
    }

    // 向服务器端发送请求
    fwrite($client, $message, strlen($message));

    //获取服务返回信息
    $responseMessage = fread($client, 1024);
    if ($responseMessage == "unknown command") {
        echo $responseMessage . "\n";
    } else {
        if ($message == "conv_tree") {
            echo $responseMessage . "\n";
        } else {
            printf("%s 计算结果为：%s", $message, $responseMessage);

        }
    }

}

fclose($client);

