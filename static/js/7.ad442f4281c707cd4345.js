webpackJsonp([7],{UcDP:function(t,e,r){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var i=r("Dd8w"),n=r.n(i),s=r("RaI2"),o=r("WUkm"),c={components:{AuditHistoryItem:s.a},data:function(){return{}},computed:n()({},Object(o.b)(),Object(o.c)("pageData",["checkHistory"]))},a=function(){var t=this.$createElement,e=this._self._c||t;return e("div",{staticClass:"history"},[e("div",[this._v("审核节点")]),this._v(" "),this._l(this.checkHistory,function(t,r){return e("audit-history-item",{key:t.id,attrs:{isLatest:0==r,member:t.name,time:t.time,desc:t.comment,status:t.status,name:t.nodeName,checkChoices:t.checkChoices?t.checkChoices:null}})})],2)};a._withStripped=!0;var m={render:a,staticRenderFns:[]},u=m;var d=!1;var p=r("VU/8")(c,u,!1,function(t){d||r("uqHC")},"data-v-978cea56",null);p.options.__file="src\\router\\check13\\history.vue";e.default=p.exports},gSO2:function(t,e,r){(t.exports=r("FZ+f")(!0)).push([t.i,"\n.history[data-v-978cea56]{\r\n  padding: 15px;\n}\r\n","",{version:3,sources:["C:/www/oil_new/protected/h5/src/router/check13/src/router/check13/history.vue"],names:[],mappings:";AA2BA;EACA,cAAA;CACA",file:"history.vue",sourcesContent:['<template>\r\n  <div class="history">\r\n    <div>审核节点</div>\r\n    <audit-history-item :isLatest="index==0" v-for="(item, index) in checkHistory" :key="item.id" :member="item.name" :time="item.time" :desc="item.comment" :status="item.status" :name="item.nodeName" :checkChoices="(item.checkChoices)?item.checkChoices:null"></audit-history-item>\r\n  </div>\r\n</template>\r\n\r\n<script>\r\nimport AuditHistoryItem from \'@/components/AuditHistoryItem\'\r\nimport {mapBase, mapStatePlus} from \'@/utils/mapVmodel\'\r\n\r\nexport default {\r\n  components: {\r\n    AuditHistoryItem\r\n  },\r\n  data () {\r\n    return {\r\n    }\r\n  },\r\n  computed: {\r\n    ...mapBase(),\r\n    ...mapStatePlus(\'pageData\', [\'checkHistory\'])\r\n  }\r\n}\r\n<\/script>\r\n\r\n\x3c!-- Add "scoped" attribute to limit CSS to this component only --\x3e\r\n<style scoped>\r\n.history{\r\n  padding: 15px;\r\n}\r\n</style>\r\n'],sourceRoot:""}])},uqHC:function(t,e,r){var i=r("gSO2");"string"==typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);r("rjj0")("135e9e9f",i,!1)}});
//# sourceMappingURL=7.ad442f4281c707cd4345.js.map