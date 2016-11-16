<?php

namespace App\Tools\Transformer;

/**
 * 数据结构转化
 *
 * @author 杨立景
 * Class MyTransFormer
 * @package App\Tools\TransFormer
 */
abstract class TransFormerBase
{
    public function transformCollection($items)
    {
        return array_map([$this,'transform'],$items);
    }
    public abstract function transform($item);
}