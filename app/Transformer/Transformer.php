<?php
namespace app\Transformer;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/2 0002
 * Time: 上午 10:24
 */
abstract class Transformer {
    /**
     * @param $items
     * @return array
     */
    public function transformCollection($items)
    {
        return array_map([$this,'transform'],$items);
    }

    /**
     * @param $items
     * @return mixed
     */
    public abstract function transform($items);
}