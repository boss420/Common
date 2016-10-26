# Common
Some important common libraries,like aysn process,encription and so on...

Github:https://github.com/boss420/Common

##Install
github:

```
git clone https://github.com/boss420/Common.git

```
Or you can use **composer**

```
composer require boss420/common

```

##usage

###AsynHandle

Use multi-process to simulate multi-threads.

```
$oop = new \boss420\common\AsynHandle();
$result = $oop->Request("xxxxxx");
echo $result;

```
The code will return soon,and the result only shows that if the code works.You can not get the response from this code segment.Because the real URL request will still be proceessed backend.

So it's easy that you can send multi-requests to more than 1 URL without waiting the processing result.

The method also contains some default parameters,you can change to what you need.

```
 public function Request($url, $cookie = array(), $post = array(), $timeout = 3){
 //***
 }

```

Also,the ``AsynHandle`` also contains a synchronous request method called ``Get``,you can get the real response after you call this method.

###sample code

```
header('Content-type: application/json');
require_once "../src/AsynHandle.php";
$oop = new \boss420\common\AsynHandle();
$result = $oop->Get("https://www.udopay.com/index.php/Gateway/securepay");
echo $result;

```
This method also contains some default parameters,and they are easy to undertand.

```
 public function Get($url, $cookie = array(), $post = array(), $timeout = 30){
 //***
 }
 

```