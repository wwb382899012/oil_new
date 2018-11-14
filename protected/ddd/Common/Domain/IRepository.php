<?php
/**
 * Created by youyi000.
 * DateTime: 2018/4/8 16:01
 * Describe：
 */

namespace ddd\Common\Domain;


use ddd\Common\IAggregateRoot;

interface IRepository
{
    function findByPk($id,$condition='',$params=array());
    function find($condition='',$params=array());
    function findAll($condition='',$params=array());
    function store(IAggregateRoot $entity);
    //function remove(IAggregateRoot $entity);
}