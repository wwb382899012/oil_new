<?php
/**
 * Created by youyi000.
 * DateTime: 2017/11/25 15:45
 * Describe：
 */

class ZEnumColumn extends ZDataColumn
{
    public $key;

    public function __construct(CGridView $grid)
    {
        parent::__construct($grid);
        $this->htmlOptions=array("style"=>"text-align:center;");
        $this->headerHtmlOptions=array("style"=>"width:80px;text-align:center;");
    }

    /**
     * Renders the data cell content.
     * This method evaluates {@link value} or {@link name} and renders the result.
     * @param integer $row the row number (zero-based)
     * @param mixed $data the data associated with the row
     */
    protected function renderDataCellContent($row,$data)
    {
        if($this->value!==null)
            $value=$this->evaluateExpression($this->value,array('data'=>$data,'row'=>$row,'controller'=>$this->grid->getOwner()));
        else if($this->name!==null)
        {
            $value=CHtml::value($data,$this->name);
            if(!empty($this->key))
                $value=$this->grid->owner->map[$this->key][$value];
        }

        echo $value===null ? $this->grid->nullDisplay : $this->grid->getFormatter()->format($value,$this->type);
    }

}