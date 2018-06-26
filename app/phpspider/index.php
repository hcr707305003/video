<?php
header("Content-Type:text/html;charset=gbk2312");
require_once __DIR__ . './autoloader.php';
use phpspider\core\phpspider;
use phpspider\core\selector;
/* Do NOT delete this comment */
/* 不要删除这段注释 */

$content = file_get_contents('http://tv.2345.com/---.html');
//获取class为pic的内容
$data = selector::select($content, '@<div class="pic">(.*?)</div>@', 'regex');

var_dump($data);