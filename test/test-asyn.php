<?php
/**
 * 测试多进程类的异步步处理
 *@author Jan
 *@email  popmyjoshion@gmail.com
 */
//header('Content-type: application/json');
require_once "../src/AsynHandle.php";
$oop = new \boss420\common\AsynHandle();
$result = $oop->Request("http://127.0.0.1/github-class/Common/test/asy-write.php");
echo $result;