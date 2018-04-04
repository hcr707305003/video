<?php
namespace app\Transformer;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/2 0002
 * Time: 上午 10:29
 */
class VodsTransformer extends Transformer {
    /**
     * @param $rows
     * @return array
     */
    public function transform($rows)
    {
        return [
            'id' => $rows['id'],
            'name' => $rows['name'],
            'subname' => $rows['subname'],
            'enname' => $rows['enname'],
            'letter' => $rows['letter'],
            'type_name' => $rows['type_name'],
            'pic' => $rows['pic'],
            'lang' => $rows['lang'],
            'area' => $rows['area'],
            'score' => $rows['score'],
            'year' => $rows['year'],
            'last' => $rows['last'],
            'state' => $rows['state'],
            'note' => $rows['note'],
            'actor' => $rows['actor'],
            'director' => $rows['director'],
            'playfrom' => $rows['playfrom'],
            'dd' => $rows['dd'],
            'des' => $rows['des'],
            'downfrom' => $rows['downfrom'],
            'downurl' => $rows['downurl'],
            'tuisong' => $rows['tuisong'],
        ];
    }
}