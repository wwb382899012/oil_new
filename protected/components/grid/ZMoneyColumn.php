<?php
/**
 * Created by youyi000.
 * DateTime: 2017/11/25 15:57
 * Describe：
 */

class ZMoneyColumn extends ZDataColumn
{
    public $currency='￥';

    /**
     * 要保留的小数位数
     * @var int
     */
    public $scale=2;

    public function __construct(CGridView $grid)
    {
        parent::__construct($grid);
        $this->headerHtmlOptions=array("style"=>"width:120px;text-align:right;");
        $this->htmlOptions=array("style"=>"text-align:right;");
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
            if ($this->currency instanceof Closure)
                $ico = call_user_func($this->currency, array('data'=>$data,'row'=>$row,'controller'=>$this->grid->getOwner()));
            else
                $ico = $this->currency;

//            $value=number_format(intval($value)/100,$this->scale);
            $value=number_format(sprintf("%.2f", $value / 100), $this->scale);


            $value=$ico." ".$value;
        }

        echo $value===null ? $this->grid->nullDisplay : $value;
    }
}