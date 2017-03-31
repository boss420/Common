<?php
/**
 * 测试多进程类的同步处理
 *@author Jan
 *@email  popmyjoshion@gmail.com
 */
header('Content-type: application/json');
require_once "../src/AsynHandle.php";
$oop = new \boss420\common\AsynHandle();
$result = $oop->Get("...");
echo $result;