webpackJsonp([6],{w5gX:function(t,r,n){(t.exports=n("FZ+f")(!0)).push([t.i,"\n.panel-container[data-v-ece85760]{\r\n  background-color: #f5f5f9;\r\n  position: absolute;\r\n  top:0;\r\n  left:0;\r\n  width: 100%;\r\n  height: 100%;\n}\r\n","",{version:3,sources:["C:/Users/alaba/codes/oil_new/protected/h5/src/router/audit/src/router/audit/contract.vue"],names:[],mappings:";AAiFA;EACA,0BAAA;EACA,mBAAA;EACA,MAAA;EACA,OAAA;EACA,YAAA;EACA,aAAA;CACA",file:"contract.vue",sourcesContent:['<template>\r\n<div class="panel-container">\r\n  <panel type=\'accordion\' title="项目信息">\r\n    <card>\r\n       <field-item label="项目编码" content="KY11ZN171122N01" />\r\n       <field-item label="项目编码" content="：预付货款申请表进行审批，并授权进行网上汇款。出纳网上汇款：根据批准后汇款申请单付款并在汇完款后将网上汇款单发送给采购..." contentStyle="em2"/>\r\n       <field-item label="项目编码" content="KY11ZN171122N01" contentStyle="important"/>\r\n       <field-item label="项目编码" content="KY11ZN171122N01" contentStyle="normal" />\r\n    </card>\r\n  </panel>\r\n  <panel>\r\n    <card type="link" href="login">\r\n       123123\r\n    </card>\r\n    <card type="link" href="login">\r\n       123123\r\n    </card>\r\n  </panel>\r\n  <panel type="link" href="login" title="登录"/>\r\n  <panel title="审核">\r\n    <card>\r\n      <audit-item />\r\n    </card>\r\n    <card :topBorder="1" >\r\n      <audit-item type="comment"/>\r\n    </card>\r\n  </panel>\r\n  <panel title="商品信息">\r\n    <card>\r\n      <audit-goods-item />\r\n    </card>\r\n  </panel>\r\n  <panel>\r\n     <card>\r\n        <audit-table >\r\n          <template slot="header">\r\n              <td>预计时间</td>\r\n              <td>付款类别</td>\r\n              <td>金额</td>\r\n              <td>备注</td>\r\n          </template>\r\n          <template slot="body">\r\n            <tr>\r\n              <td>预计时间</td>\r\n              <td>付款类别</td>\r\n              <td>金额</td>\r\n              <td>\r\n                 <router-link to="">查看</router-link>\r\n              </td>\r\n            </tr>\r\n          </template>\r\n        </audit-table>\r\n     </card>\r\n  </panel>\r\n  <panel>\r\n    <audit-history-item :isLatest="true" ></audit-history-item>\r\n    <audit-history-item></audit-history-item>\r\n  </panel>\r\n</div>\r\n</template>\r\n<script>\r\nimport Panel from \'@/components/Panel\'\r\nimport Card from \'@/components/Card\'\r\nimport FieldItem from \'@/components/FieldItem\'\r\nimport AuditItem from \'@/components/AuditItem\'\r\nimport AuditGoodsItem from \'@/components/AuditGoodsItem\'\r\nimport AuditTable from \'@/components/AuditTable\'\r\nimport AuditHistoryItem from \'@/components/AuditHistoryItem\'\r\n\r\nexport default {\r\n  components: {\r\n    Panel,\r\n    Card,\r\n    FieldItem,\r\n    AuditItem,\r\n    AuditGoodsItem,\r\n    AuditTable,\r\n    AuditHistoryItem\r\n  }\r\n}\r\n<\/script>\r\n<style scoped>\r\n.panel-container{\r\n  background-color: #f5f5f9;\r\n  position: absolute;\r\n  top:0;\r\n  left:0;\r\n  width: 100%;\r\n  height: 100%;\r\n}\r\n</style>\r\n'],sourceRoot:""}])},wRXX:function(t,r,n){var e=n("w5gX");"string"==typeof e&&(e=[[t.i,e,""]]),e.locals&&(t.exports=e.locals);n("rjj0")("09901954",e,!1)},xBQA:function(t,r,n){"use strict";Object.defineProperty(r,"__esModule",{value:!0});var e=n("JhKW"),a=n("rhdv"),i=n("YIB9"),o=n("GC8e"),l=n("QjXM"),d=n("XDRT"),c=n("RaI2"),s={components:{Panel:e.a,Card:a.a,FieldItem:i.a,AuditItem:o.a,AuditGoodsItem:l.a,AuditTable:d.a,AuditHistoryItem:c.a}},p=function(){var t=this,r=t.$createElement,n=t._self._c||r;return n("div",{staticClass:"panel-container"},[n("panel",{attrs:{type:"accordion",title:"项目信息"}},[n("card",[n("field-item",{attrs:{label:"项目编码",content:"KY11ZN171122N01"}}),t._v(" "),n("field-item",{attrs:{label:"项目编码",content:"：预付货款申请表进行审批，并授权进行网上汇款。出纳网上汇款：根据批准后汇款申请单付款并在汇完款后将网上汇款单发送给采购...",contentStyle:"em2"}}),t._v(" "),n("field-item",{attrs:{label:"项目编码",content:"KY11ZN171122N01",contentStyle:"important"}}),t._v(" "),n("field-item",{attrs:{label:"项目编码",content:"KY11ZN171122N01",contentStyle:"normal"}})],1)],1),t._v(" "),n("panel",[n("card",{attrs:{type:"link",href:"login"}},[t._v("\r\n       123123\r\n    ")]),t._v(" "),n("card",{attrs:{type:"link",href:"login"}},[t._v("\r\n       123123\r\n    ")])],1),t._v(" "),n("panel",{attrs:{type:"link",href:"login",title:"登录"}}),t._v(" "),n("panel",{attrs:{title:"审核"}},[n("card",[n("audit-item")],1),t._v(" "),n("card",{attrs:{topBorder:1}},[n("audit-item",{attrs:{type:"comment"}})],1)],1),t._v(" "),n("panel",{attrs:{title:"商品信息"}},[n("card",[n("audit-goods-item")],1)],1),t._v(" "),n("panel",[n("card",[n("audit-table",[n("template",{slot:"header"},[n("td",[t._v("预计时间")]),t._v(" "),n("td",[t._v("付款类别")]),t._v(" "),n("td",[t._v("金额")]),t._v(" "),n("td",[t._v("备注")])]),t._v(" "),n("template",{slot:"body"},[n("tr",[n("td",[t._v("预计时间")]),t._v(" "),n("td",[t._v("付款类别")]),t._v(" "),n("td",[t._v("金额")]),t._v(" "),n("td",[n("router-link",{attrs:{to:""}},[t._v("查看")])],1)])])],2)],1)],1),t._v(" "),n("panel",[n("audit-history-item",{attrs:{isLatest:!0}}),t._v(" "),n("audit-history-item")],1)],1)};p._withStripped=!0;var m={render:p,staticRenderFns:[]},u=m;var A=!1;var v=n("VU/8")(s,u,!1,function(t){A||n("wRXX")},"data-v-ece85760",null);v.options.__file="src\\router\\audit\\contract.vue";r.default=v.exports}});
//# sourceMappingURL=6.7017e0990313a08ad284.js.map