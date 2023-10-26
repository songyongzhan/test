<?php

/**
 * 这是我的一个开发大体步骤
 *
 *
 * 1. 创建一个sokcet服务器
 * 2. 监听客户端的连接
 * 3. 获取客户端请求命令，并做出相应的业务处理，得到结果
 * 4. 输出 返回给客户端
 */

// 1. 创建socket服务器 监听 8675
$socket = stream_socket_server("tcp://0.0.0.0:8765", $errNo, $errStr);

if (!$socket) {
    die(sprintf("创建socket服务器失败:%s,错误原因:%s", $errNo, $errStr));
}

echo "sokcet服务器创建成功 \n";

// 2. 接收客户端连接
while ($conn = stream_socket_accept($socket)) {

    if (!$conn) {
        die(sprintf("创建客户端连接失败"));
    }

    //为了可以支持多次数据发送，故此 需要一个while
    while (true) {
        // 3. 获取客户端请求命令，并做出相应的业务处理，得到结果
        $command = trim(fread($conn, 100));

        if (empty($command)) {
            continue;
        }

        printf("客户端命令：%s \n", $command);

        //如果是 exit 则程序退出
        if ($command == "exit") {
            break;
        }
        // 程序需要执行的命令是  mul incr div 分别都是通过 空格分割
        // 执行前，先判断是否为空
        if (empty($command)) {
            $errMessage = "请输入相应的命令";
            fwrite($conn, $errMessage, strlen($errMessage));
        }
        $commandParams = explode(" ", $command);
        // 第一个参数是 命令，下面将提供一个 switch

        $operator = $commandParams[0];
        $result = "unknown command";
        switch ($operator) {
            case "mul":
                $firstNum = isset($commandParams[1]) ? $commandParams[1] : 0;
                $secondNum = isset($commandParams[2]) ? $commandParams[2] : 0;
                //这里本身有函数的，就不使用了，直接使用 * 代替
                if (is_numeric($firstNum) && is_numeric($secondNum)) {
                    $result = $firstNum * $secondNum;
                }
                break;
            case "incr":
                $firstNum = isset($commandParams[1]) ? $commandParams[1] : 0;
                if (is_numeric($firstNum)) {
                    $result = ++$firstNum;
                }
                break;
            case "div":
                $firstNum = isset($commandParams[1]) ? $commandParams[1] : 0;
                $secondNum = isset($commandParams[2]) ? $commandParams[2] : 0;
                //这里本身有函数的，就不使用了，直接使用 * 代替
                if (empty($secondNum) || $secondNum == 0) {
                    $result = "除数不能为0";
                } elseif (is_numeric($firstNum) && is_numeric($secondNum)) {
                    $result = $firstNum / $secondNum;
                }
                break;
            default:
                // 什么都不做
        }

        // 4. 输出 返回给客户端
        fwrite($conn, $result, strlen($result));
    }

    fclose($conn);
}



