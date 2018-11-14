<section class="content-header menu-path is-fixed-bread">
    <div class="col flex-grid">
        <a href="javascript: void 0"  onclick="back()">
            <img src="/img/cc-arrow-left-circle.png" class="back-icon" alt="">
            返回
        </a>
        <?php echo empty($this->pageTitle)?$this->moduleName:$this->pageTitle ?>
    </div>
    <!-- 提交保存 -->
    <div class="flex-grid col">
        <?php if($this->checkIsCanEdit($contract["status"])){ ?>
        <button type="button" id="saveButton" onclick="submitForCheck()">提交</button>
        <button type="button" onclick="edit()">修改</button>
        <?php } ?>
        <?php if(!$this->isExternal){ ?>
        <button type="button" onclick="back()">返回</button>
        <?php } ?>
    </div>
</section>
<!-- 进口渠道 -->
<div class="content-wrap">
  <div class="content-wrap-title">
    <div>
      <p>
        <span>进口渠道</span>
        <span>
          <a style="color:#3E8CF7!important;margin-left:10px;" target="_blank">项目编号：</a>
        </span>
        <span style="color:#FF6E34;font-size:14px;margin-left:10px;">复制</span>
      </p>
    </div>
  </div>
  <ul class="item-com">
    <li>
      <label>交易主体</label>
      <span>车有邦科技服务（深圳）有限公司</span>
    </li>
    <li>
      <label>采购合同类型</label>
      <span>代理进口合同</span>
    </li>
    <li>
      <label>销售合同类型</label>
      <span>国内销售合同</span>
    </li>
    <li>
      <label>合同状态</label>
      <span>业务审核通过</span>
    </li>
    <li>
      <label>采购代理商</label>
      <a href="#">中石化西藏销售有限公司</a>
    </li>
    <li>
      <label>代理模式</label>
      <span>购销模式</span>
    </li>
    <li>
      <label>采购价格方式</label>
      <span>活价（价格为暂估价）</span>
    </li>
    <li>
      <label>销售价格方式</label>
      <span>活价（价格为暂估价）</span>
    </li>
  </ul>
</div>

<!-- 交易明细 -->
<div class="content-wrap">
  <div class="content-wrap-title">
    <div>
      <p>交易明细</p>
      <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
    </div>
  </div>
  <div>
    <ul class="item-com item-com-4">
      <li>
        <label>上游合作方</label>
        <span>西藏智鸿石油化工有限公司</span>
      </li>
      <li>
        <label>采购币种</label>
        <span>人民币</span>
      </li>
      <li>
        <label>负责人</label>
        <span>朱飞</span>
      </li>
      <li>
        <label>合同签订日期</label>
        <span>业务审核通过</span>
      </li>
    </ul>
    <ul class="table-com">
      <li>
        <span>采购品名</span>
        <span>计价标的</span>
        <span>溢短装比</span>
        <span>数量</span>
        <span>采购单价</span>
        <span>采购总价</span>
        <span>采购人民币总价</span>
      </li>
      <li>
        <span>MTBE</span>
        <span>1</span>
        <span>0.00%</span>
        <span>100.0000 吨</span>
        <span>￥1.00</span>
        <span>￥ 100.00</span>
        <span>￥ 100.00</span>
      </li>
      <li>
    </ul>
    <ul class="item-com item-com-4" style="margin-top:20px;">
      <li>
        <label>下游合作方</label>
        <span>苏州华中石化有限公司</span>
      </li>
      <li>
        <label>销售币种</label>
        <span>人民币</span>
      </li>
      <li>
        <label>负责人</label>
        <span>朱飞</span>
      </li>
      <li>
        <label>合同签订日期</label>
        <span>业务审核通过</span>
      </li>
    </ul>
    <ul class="table-com">
      <li>
        <span>销售品名</span>
        <span>计价标的</span>
        <span>溢短装比</span>
        <span>数量</span>
        <span>销售单价</span>
        <span>销售总价</span>
        <span>销售人民币总价</span>
      </li>
      <li>
        <span>MTBE</span>
        <span>1</span>
        <span>0.00%</span>
        <span>100.0000 吨</span>
        <span>￥1.00</span>
        <span>￥ 100.00</span>
        <span>￥ 100.00</span>
      </li>
      <li>
    </ul>
    <ul class="item-com item-com-1" style="margin-top:20px;">
      <li>
        <label>*采购计价公式</label>
        <span>1</span>
      </li>
      <li>
        <label>*销售计价公式</label>
        <span>1</span>
      </li>
    </ul>
  </div>
</div>

<!-- 代理手续费 -->
<div class="content-wrap">
  <div class="content-wrap-title">
    <div>
      <p>代理手续费</p>
      <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
    </div>
  </div>
  <ul class="table-com">
    <li>
      <span>采购品名</span>
      <span>计价标的</span>
      <span>溢短装比</span>
      <span>数量</span>
      <span>采购单价</span>
      <span>采购总价</span>
      <span>采购人民币总价</span>
    </li>
    <li>
      <span>MTBE</span>
      <span>1</span>
      <span>0.00%</span>
      <span>100.0000 吨</span>
      <span>￥1.00</span>
      <span>￥ 100.00</span>
      <span>￥ 100.00</span>
    </li>
    <li>
  </ul>
</div>

<!-- 收付款计划 -->
<div class="content-wrap">
  <div class="content-wrap-title">
    <div>
      <p>收付款计划</p>
      <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
    </div>
  </div>
  <div>
    <p>上游付款计划</p>
    <ul class="table-com">
      <li>
        <span>预计日期</span>
        <span>付款类别</span>
        <span>币种</span>
        <span>金额</span>
        <span>备注</span>
      </li>
      <li>
        <span>2018-06-20</span>
        <span>履约保证金</span>
        <span>人民币</span>
        <span>￥ 10.00</span>
        <span>-</span>
      </li>
      <li>
    </ul>
    <p>下游收款计划</p>
    <ul class="table-com">
      <li>
        <span>预计日期</span>
        <span>付款类别</span>
        <span>币种</span>
        <span>金额</span>
        <span>备注</span>
      </li>
      <li>
        <span>2018-06-20</span>
        <span>履约保证金</span>
        <span>人民币</span>
        <span>￥ 10.00</span>
        <span>-</span>
      </li>
      <li>
    </ul>
  </div>
</div>

<!-- 额度占用情况 -->
<div class="content-wrap">
  <div class="content-wrap-title">
    <div>
      <p>额度占用情况</p>
      <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
    </div>
  </div>
  <ul class="table-com">
    <li>
      <span>占用对象</span>
      <span>授信额度</span>
      <span>实际占用额度</span>
      <span>合同占用额度</span>
    </li>
    <li>
      <span>西藏智鸿石油化工有限公司</span>
      <span>￥10,000,000.00</span>
      <span>￥-1,500,000.00</span>
      <span>￥10,000,000.00</span>
    </li>
    <li>
      <span>西藏智鸿石油化工有限公司</span>
      <span>￥10,000,000.00</span>
      <span>￥-1,500,000.00</span>
      <span>￥10,000,000.00</span>
    </li>
    <li>
  </ul>
</div>

<!-- 额度占用情况 -->
<div class="content-wrap">
  <div class="content-wrap-title">
    <div>
      <p>合同条款</p>
      <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
    </div>
  </div>
  <div>
    <ul class="item-com item-com-1">
      <li>  
        <label>起运港</label>
        <span>目的港</span>
      </li>
      <li>
        <label>目的港</label>
        <span>1</span>
      </li>
      <li>
        <label>运输方式</label>
        <span>1</span>
      </li>
      <li>
        <label>报关口岸</label>
        <span>1</span>
      </li>
      <li>
        <label>交货期限</label>
        <span>1</span>
      </li>
      <li>
        <label>交货方式</label>
        <span>1</span>
      </li>
      <li>
        <label>付款方式</label>
        <span>1</span>
      </li>
      <li>
        <label>结算方式</label>
        <span>1</span>
      </li>
      <li>
        <label>保费承担方</label>
        <span>1</span>
      </li>
      <li>
        <label>其他事项说明</label>
        <span>1</span>
      </li>
    </ul>
    <ul class="item-com item-com-1">
      <li>  
        <label>交货期限</label>
        <span></span>
      </li>
      <li>
        <label>交货方式</label>
        <span>1</span>
      </li>
      <li>
        <label>结算方式</label>
        <span>1</span>
      </li>
      <li>
        <label>保费承担方</label>
        <span>1</span>
      </li>
      <li>
        <label>运费承担方</label>
        <span>1</span>
      </li>
      <li>
        <label>质量验收依据</label>
        <span>1</span>
      </li>
      <li>
        <label>数量结算依据</label>
        <span>1</span>
      </li>
      <li>
        <label>其他事项说明</label>
        <span>1</span>
      </li>
    </ul>
  </div>
</div>

<!-- 创建人信息 -->
<div class="content-wrap">
  <div class="content-wrap-title">
    <div>
      <p>创建人信息</p>
      <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
    </div>
  </div>
  <ul class="item-com">
    <li>
      <label>采购合同创建人/时间</label>
      <span>朱飞 / 2018-06-20 20:49:35</span>
    </li>
    <li>
      <label>采购合同修改人/时间</label>
      <span>朱飞 / 2018-06-20 20:49:35</span>
    </li>
    <li>
      <label>销售合同创建人/时间</label>
      <span>朱飞 / 2018-06-20 20:49:35</span>
    </li>
    <li>
      <label>销售合同修改人/时间</label>
      <span>朱飞 / 2018-06-20 20:49:35</span>
    </li>
  </ul>
</div>