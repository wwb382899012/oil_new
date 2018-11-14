<?php
/**
 * Created by youyi000.
 * DateTime: 2017/11/25 15:07
 * Describe：
 */

Mod::import('zii.widgets.grid.CDataColumn');

class ZDataColumn extends CDataColumn
{
    public function __construct(CGridView $grid)
    {
        parent::__construct($grid);
        $this->type="raw";
    }

    /*public function renderDataCell($row)
    {
        $data=$this->grid->dataProvider->data[$row];

        if ($this->htmlOptions instanceof Closure) {
            $options = call_user_func($this->htmlOptions, $data, $this);
        } else {
            $options=$this->htmlOptions;
        }
        if($this->cssClassExpression!==null)
        {
            $class=$this->evaluateExpression($this->cssClassExpression,array('row'=>$row,'data'=>$data));
            if(!empty($class))
            {
                if(isset($options['class']))
                    $options['class'].=' '.$class;
                else
                    $options['class']=$class;
            }
        }
        echo CHtml::openTag('td',$options);
        $start=microtime(true);
        $this->renderDataCellContent($row,$data);
        $n=microtime(true)-$start;
        echo "#".$n."#";
        echo '</td>';
    }*/


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
            $value=CHtml::value($data,$this->name);
        echo $value===null ? $this->grid->nullDisplay : $this->grid->getFormatter()->format($value,$this->type);
    }
}