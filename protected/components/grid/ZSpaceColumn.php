<?php
/**
 * Created by youyi000.
 * DateTime: 2018/1/29 17:44
 * Describe：
 */

class ZSpaceColumn extends ZDataColumn
{
    public function __construct(CGridView $grid)
    {
        parent::__construct($grid);
        $this->type="raw";
    }

    public function init()
    {
        $this->sortable=false;
    }

    protected function renderDataCellContent($row, $data)
    {
        echo "&nbsp;";
    }
}