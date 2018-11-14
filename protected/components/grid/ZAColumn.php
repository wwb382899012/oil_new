<?php
/**
 * Created by youyi000.
 * DateTime: 2017/12/4 11:44
 * Describe：
 */

class ZAColumn extends ZDataColumn
{
    /**
     * 链接模板
     * @var
     */
    public $template;
    /**
     * 模板中的参数，对应顺序字段名
     * @var
     */
    public $params;

    public function __construct(CGridView $grid)
    {
        parent::__construct($grid);
        $this->type="html";
    }

    protected function renderDataCellContent($row, $data)
    {
        if($this->value!==null)
            $value=$this->evaluateExpression($this->value,array('data'=>$data,'row'=>$row,'controller'=>$this->grid->getOwner()));
        else if($this->name!==null)
        {
            $value=CHtml::value($data,$this->name);
            if(!empty($value) && !empty($this->template))
            {
                $value=$this->template;
                if(is_array($this->params))
                {
                    foreach ($this->params as $i=>$n)
                    {
                        $value=str_replace("{".$i."}",$data[$n],$value);
                    }
                }
            }
        }

        echo $value===null ? $this->grid->nullDisplay : $this->grid->getFormatter()->format($value,$this->type);
    }


}