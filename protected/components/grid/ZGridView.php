<?php
/**
 * Created by youyi000.
 * DateTime: 2017/11/7 15:41
 * Describe：
 */
Mod::import('zii.widgets.grid.CGridView');

class ZGridView extends CGridView
{
    public $pagerCssClass="navigation";
    public $cssFile=false;
    //public $template="{items}\n{summary}\n{pager}";
    public $itemsCssClass="table table-condensed table-hover table-bordered table-layout";

    public $rowOptions=array();
    /**
     * 表格属性
     * @var array
     */
    public $tableOptions=array();

    public $emptyText="暂时没有数据";

    public $isShowSummary=true;
    public $isShowPager=true;

    /**
     * 默认列的类名
     * @var string
     */
    public $defaultColumnClass="ZDataColumn";

    public $pager=array(
        'header'=>'',
        'cssFile'=>false,
        'htmlOptions'=>array('class'=>'pagination'),
        'internalPageCssClass'=>'',
        'selectedPageCssClass'=>'active',
        'firstPageLabel'=>'<span class="glyphicon glyphicon-fast-backward"></span>',
        'prevPageLabel'=>'<span class="glyphicon glyphicon-backward"></span>',
        'nextPageLabel'=>'<span class="glyphicon glyphicon-forward"></span>',
        'lastPageLabel'=>'<span class="glyphicon glyphicon-fast-forward"></span>',
    );

    /*public $templates=array(
        "default"=>"<div class=\"box\">
                        <div class=\"box-body no-padding\">
                                {items}
                        </div>
                        <div class=\"box-footer container-fluid\">
                            <div class='row'>
                                    <div class='col-sm-4'>{summary}</div>
                                    <div class='col-sm-8'>{pager}</div>
                            </div>
                        </div>
                    </div>",
        "noSummary"=>"<div class=\"box\">
                        <div class=\"box-body no-padding\">
                                {items}
                        </div>
                        <div class=\"box-footer\">
                            {pager}
                        </div>
                    </div>",
        "noPager"=>"<div class=\"box\">
                        <div class=\"box-body no-padding\">
                                {items}
                        </div>
                        <div class=\"box-footer\">
                            {summary}
                        </div>
                    </div>",
        "content"=>"<div class=\"box\">
                        <div class=\"box-body no-padding\">
                                {items}
                        </div>
                    </div>",
    );*/

    public $template="<div class=\"box box-solid\">
                        <div class=\"box-body no-padding\">
                                {items}
                        </div>
                        <div class=\"box-footer container-fluid\">
                            <div class='row'>
                                    <div class='col-sm-4'>{summary}</div>
                                    <div class='col-sm-8'>{pager}</div>
                            </div>
                        </div>
                    </div>";

    public function getTemplate()
    {
        $footer="";
        if($this->isShowSummary && $this->isShowPager)
        {
            $footer="<div class=\"box-footer container-fluid\">
                            <div class='row'>
                                    <div class='col-sm-4'>{summary}</div>
                                    <div class='col-sm-8'>{pager}</div>
                            </div>
                        </div>";
        }
        else
        {
            if($this->isShowPager)
            {
                $footer="<div class=\"box-footer\">
                            {pager}
                        </div>";
            }
            else if($this->isShowSummary)
            {
                $footer="<div class=\"box-footer\">
                            {summary}
                        </div>";
            }
        }
        $this->template="<div class=\"box box-solid\">
                        <div class=\"box-body no-padding\">
                                {items}
                        </div>
                        ".$footer."
                    </div>";

    }

    /**
     * Renders the view.
     * This is the main entry of the whole view rendering.
     * Child classes should mainly override {@link renderContent} method.
     */
    public function run()
    {
        $this->registerClientScript();

        echo CHtml::openTag($this->tagName,$this->htmlOptions)."\n";

        $this->renderContent();
        //$this->renderKeys();

        echo CHtml::closeTag($this->tagName);
    }

    public function init()
    {
        if(!isset($this->htmlOptions['class']))
            $this->htmlOptions['class']='grid-view';
        if(empty($this->itemsCssClass))
            $this->itemsCssClass="table table-condensed table-hover table-bordered table-layout";

        $this->getTemplate();
        $this->baseScriptUrl="#";

        parent::init();

        //$this->initColumns();
    }

    public function registerClientScript()
    {

    }


    /**
     * Creates column objects and initializes them.
     */
	protected function initColumns()
    {
        if($this->columns===array())
        {
            if($this->dataProvider instanceof CActiveDataProvider)
                $this->columns=$this->dataProvider->model->attributeNames();
            else if($this->dataProvider instanceof IDataProvider)
            {
                // use the keys of the first row of data as the default columns
                $data=$this->dataProvider->getData();
                if(isset($data[0]) && is_array($data[0]))
                    $this->columns=array_keys($data[0]);
            }
        }
        $id=$this->getId();
        foreach($this->columns as $i=>$column)
        {
            if(is_string($column))
                $column=$this->createDataColumn($column);
            else
            {
                if(!isset($column['class']))
                    $column['class']=$this->defaultColumnClass;
                else if($column['class']=="enum")
                    $column['class']="ZEnumColumn";

                $column=Mod::createComponent($column, $this);
            }
            if(!$column->visible)
            {
                unset($this->columns[$i]);
                continue;
            }
            if($column->id===null)
                $column->id=$id.'_c'.$i;
            $this->columns[$i]=$column;
        }

        foreach($this->columns as $column)
            $column->init();
    }

    protected function createDataColumn($text)
    {
        if($text=="#space#")
        {
            return new ZSpaceColumn($this);
        }

        if(!preg_match('/^([\w\.]+)(:(\w*))?(:([^:]*))?(:(.*))?$/',$text,$matches))
            throw new CException(Mod::t('zii','The column must be specified in the format of "Name:Type:Label:Css", where "Type", "Label" and "Css" are optional.'));
        //var_dump($matches);
        if($matches[3]=="amount")
            $column=new ZMoneyColumn($this);
        else
            $column=new ZDataColumn($this);
        $column->name=$matches[1];
        if(isset($matches[3]) && $matches[3]!=='')
            $column->type=$matches[3];
        if(isset($matches[5]))
            $column->header=$matches[5];
        if(isset($matches[7]))
        {
            $column->htmlOptions["style"]=$matches[7];
            $column->headerHtmlOptions["style"]=$matches[7];
        }

        return $column;
    }


    /**
     * Renders the data items for the grid view.
     */
    public function renderItems()
    {
        if($this->dataProvider->getItemCount()>0 || $this->showTableOnEmpty)
        {
            if ($this->tableOptions instanceof Closure) {
                $options = call_user_func($this->tableOptions, $this);
            } else {
                $options = $this->tableOptions;
            }

            if(isset($options['class']))
                $options['class']=$this->itemsCssClass." ".$options['class'];
            else
                $options['class']=$this->itemsCssClass;

            echo CHtml::openTag('table',$options);
            //echo "<table class=\"{$this->itemsCssClass}\">\n";
            $this->renderTableHeader();
            ob_start();
            $this->renderTableBody();
            $body=ob_get_clean();
            $this->renderTableFooter();
            echo $body; // TFOOT must appear before TBODY according to the standard.
            echo "</table>";
        }
        else
            $this->renderEmptyText();
    }

    /**
     * Renders the table body.
     */
    public function renderTableBody()
    {
        $data=$this->dataProvider->getData();
        $n=count($data);
        echo "<tbody>\n";

        if($n>0)
        {
            for($row=0;$row<$n;++$row)
                $this->renderTableRow($row);
        }
        else
        {
            echo '<tr><td colspan="'.count($this->columns).'" class="empty">';
            $this->renderEmptyText();
            echo "</td></tr>\n";
        }
        echo "</tbody>\n";
    }

    /**
     * Renders a table body row.
     * @param integer $row the row number (zero-based).
     */
    public function renderTableRow($row)
    {
        $data=$this->dataProvider->data[$row];
        if($this->rowCssClassExpression!==null)
        {
            $class=$this->evaluateExpression($this->rowCssClassExpression,array('row'=>$row,'data'=>$data));
        }
        else if(is_array($this->rowCssClass) && ($n=count($this->rowCssClass))>0)
            $class=$this->rowCssClass[$row%$n];
        else
            $class='';

        if ($this->rowOptions instanceof Closure) {
            $options = call_user_func($this->rowOptions, $data, $this);
        } else {
            $options = $this->rowOptions;
        }
        $key=$this->dataProvider->keys[$row];
        $options['data-key'] = is_array($key) ? json_encode($key) : (string) $key;
        $options['class']=$class;

        echo CHtml::openTag('tr',$options);
        //echo empty($class) ? '<tr>' : '<tr class="'.$class.'">';
        foreach($this->columns as $column)
            $column->renderDataCell($row);
        echo "</tr>\n";
    }


}