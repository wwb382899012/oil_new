webpackJsonp([17],{"0T5k":function(n,e,t){var r=t("2T3E");"string"==typeof r&&(r=[[n.i,r,""]]),r.locals&&(n.exports=r.locals);t("rjj0")("c7159242",r,!1)},"2T3E":function(n,e,t){(n.exports=t("FZ+f")(!0)).push([n.i,"\ni[data-v-71953609]{\r\nfont-family: 'iconfont';\n}\r\n","",{version:3,sources:["C:/www/oil_new/protected/h5/src/components/src/components/Icon.vue"],names:[],mappings:";AASA;AACA,wBAAA;CACA",file:"Icon.vue",sourcesContent:["<template>\r\n  <i><slot /></i>\r\n</template>\r\n<script>\r\nexport default {\r\n  props: {\r\n  }\r\n}\r\n<\/script>\r\n<style scoped>\r\ni{\r\nfont-family: 'iconfont';\r\n}\r\n</style>\r\n"],sourceRoot:""}])},"4/BI":function(n,e,t){"use strict";e.a=function(n,e,t,r){n=(n+"").replace(/[^0-9+\-Ee.]/g,"");var a=isFinite(+n)?+n:0,i=isFinite(+e)?Math.abs(e):0,o=void 0===r?",":r,c=void 0===t?".":t,A="";(A=(i?function(n,e){var t=Math.pow(10,e);return""+(Math.round(n*t)/t).toFixed(e)}(a,i):""+Math.round(a)).split("."))[0].length>3&&(A[0]=A[0].replace(/\B(?=(?:\d{3})+(?!\d))/g,o));(A[1]||"").length<i&&(A[1]=A[1]||"",A[1]+=new Array(i-A[1].length+1).join("0"));return A.join(c)}},"44/k":function(n,e){},"8sNj":function(n,e,t){(n.exports=t("FZ+f")(!0)).push([n.i,"\n.fade-enter-active[data-v-06dfb5c8], .fade-leave-active[data-v-06dfb5c8] {\r\n  -webkit-transition: opacity .5s;\r\n  transition: opacity .5s;\n}\n.fade-enter[data-v-06dfb5c8], .fade-leave-to[data-v-06dfb5c8] {\r\n  opacity: 0;\n}\n.toptips[data-v-06dfb5c8]{\r\n  position: fixed;\r\n  top: 0;\r\n  left:0;\r\n  right: 0;\r\n  padding: 5px 10px;\r\n  display: -webkit-box;\r\n  display: -webkit-flex;\r\n  display: flex;\r\n  -webkit-box-align: center;\r\n  -webkit-align-items: center;\r\n          align-items: center;\r\n  color: #ffffff;\r\n  font-size: 14px;\n}\n.toptips span[data-v-06dfb5c8]{\r\n    -webkit-box-flex:1;\r\n    -webkit-flex:1;\r\n            flex:1;\n}\n.toptips.error[data-v-06dfb5c8]{\r\n    background-color: rgba(255,0,0, 0.8);\n}\n.toptips-msg[data-v-06dfb5c8] {\r\n  display:inline\n}\n.toptips-close[data-v-06dfb5c8] {\r\n  position:fixed;\r\n  top:10px;\r\n  right:10px;\n}\r\n","",{version:3,sources:["C:/www/oil_new/protected/h5/src/plugins/toptips/src/plugins/toptips/component.vue","C:/www/oil_new/protected/h5/src/plugins/toptips/<no source>"],names:[],mappings:";AAqDA;EACA,gCAAA;EAAA,wBAAA;CACA;AACA;EACA,WAAA;CACA;AACA;EACA,gBAAA;EACA,OAAA;EACA,OAAA;EACA,SAAA;EACA,kBAAA;EACA,qBAAA;EAAA,sBAAA;EAAA,cAAA;EACA,0BAAA;EAAA,4BAAA;UAAA,oBAAA;EACA,eAAA;EACA,gBAAA;CAOA;AANA;IACA,mBAAA;IAAA,eAAA;YAAA,OAAA;CACA;AACA;ICxEA,qCAAA;CD0EA;AAEA;EACA,cAAA;CACA;AACA;EACA,eAAA;EACA,SAAA;EACA,WAAA;CACA",file:"component.vue",sourcesContent:['<template>\r\n   <transition name="fade">\r\n   <div class="toptips" :class="type" v-if="showed">\r\n      <div class="toptips-msg"  v-html="msg" :style="(inlineWidth)?\'width:\'+inlineWidth+\'px\':\'\'"></div>\r\n        <icon @click.native.stop="hide" class="toptips-close">&#xe63c;</icon>\r\n      </div>\r\n   </transition>\r\n</template>\r\n<script>\r\nimport Icon from \'@/components/Icon\'\r\n\r\nexport default {\r\n  components: {\r\n    Icon\r\n  },\r\n  props: {\r\n    msg: {\r\n      default: \'\'\r\n    },\r\n    type: {\r\n      default: \'error\'\r\n    },\r\n    closeTime: {\r\n      default: 2000\r\n    }\r\n  },\r\n  data () {\r\n    return {\r\n      showed: false,\r\n      inlineWidth: 0\r\n    }\r\n  },\r\n  methods: {\r\n    show () {\r\n      this.showed = true\r\n    },\r\n    hide () {\r\n      this.showed = false\r\n    }\r\n  },\r\n  mounted () {\r\n    if (this.closeTime) {\r\n      this.inlineWidth = window.innerWidth - 35\r\n      this.timer = setTimeout(() => {\r\n        this.hide()\r\n      }, this.closeTime)\r\n    }\r\n  },\r\n  beforeDestory () {\r\n    clearTimeout(this.timer)\r\n  }\r\n}\r\n<\/script>\r\n<style scoped>\r\n.fade-enter-active, .fade-leave-active {\r\n  transition: opacity .5s;\r\n}\r\n.fade-enter, .fade-leave-to {\r\n  opacity: 0;\r\n}\r\n.toptips{\r\n  position: fixed;\r\n  top: 0;\r\n  left:0;\r\n  right: 0;\r\n  padding: 5px 10px;\r\n  display: flex;\r\n  align-items: center;\r\n  color: #ffffff;\r\n  font-size: 14px;\r\n  span{\r\n    flex:1;\r\n  }\r\n  &.error{\r\n    background-color: rgba(#ff0000, 0.8);\r\n  }\r\n}\r\n.toptips-msg {\r\n  display:inline\r\n}\r\n.toptips-close {\r\n  position:fixed;\r\n  top:10px;\r\n  right:10px;\r\n}\r\n</style>\r\n',null],sourceRoot:""}])},Cwy8:function(n,e,t){var r=t("LgZj");"string"==typeof r&&(r=[[n.i,r,""]]),r.locals&&(n.exports=r.locals);t("rjj0")("79959608",r,!1)},Cz6C:function(n,e){},GQ9g:function(n,e,t){var r=t("8sNj");"string"==typeof r&&(r=[[n.i,r,""]]),r.locals&&(n.exports=r.locals);t("rjj0")("15110f4c",r,!1)},LgZj:function(n,e,t){(n.exports=t("FZ+f")(!0)).push([n.i,"\nbody{\r\n  background-color: #F5F6F8;\n}\n*{padding:0;margin:0;list-style:none;box-sizing: border-box;font-style: normal;\n}\ninput,textarea,select{\r\n  outline: none;\r\n  -webkit-tap-highlight-color: rgba(0,0,0,0);  \r\n  font-size: inherit;\n}\n#app {\r\n  font-family: 'Avenir', Helvetica, Arial, sans-serif;\r\n  -webkit-font-smoothing: antialiased;\r\n  -moz-osx-font-smoothing: grayscale;\r\n  color: #2c3e50;\n}\n.g-inline-checklist{\r\n  display: -webkit-box;\r\n  display: -webkit-flex;\r\n  display: flex;\r\n  border: none;\n}\n.g-inline-checklist .mint-checklist-title, .g-inline-checklist .mint-radiolist-title{\r\n    display: none;\n}\n.g-inline-checklist .mint-cell{\r\n    -webkit-box-flex: 1;\r\n    -webkit-flex: 1;\r\n            flex: 1; \r\n    background: none;\r\n    border: none;\n}\n.g-inline-checklist .mint-cell:last-child{\r\n      background: none;\n}\n.g-inline-checklist .mint-cell .mint-cell-left,.g-inline-checklist .mint-cell .mint-cell-right{\r\n     display: none;\n}\n.g-inline-checklist .mint-cell-wrapper{\r\n    padding: 0; \r\n    background: none;\n}\n.g-inline-checklist .mint-radiolist-label{\r\n    padding: 0; \r\n    display: -webkit-box; \r\n    display: -webkit-flex; \r\n    display: flex;\r\n    -webkit-box-align: center;\r\n    -webkit-align-items: center;\r\n            align-items: center;\n}\n.g-inline-checklist .mint-radio-label{\r\n    font-size: 14px;\r\n    padding: 2px 0 0 0;\r\n    vertical-align: inherit;\n}\n.g-table{\r\n    width: 100%;\r\n    border: 1px solid #e2e2e2;\r\n    border-collapse: collapse;\r\n    text-align: center;\n}\n.g-table td{\r\n      padding: 5px; \r\n      border: 1px solid #e2e2e2;\n}\n.g-table tbody td{\r\n      font-size: 13px;\n}\n.g-rmb-label .mint-cell-value{\r\n    position: relative;\n}\n.g-rmb-label .mint-cell-value:before{\r\n     content: \"\\FFE5\"; \r\n     position: absolute;\r\n     left: -20px;\r\n     height: 100%;\r\n     display: -webkit-box;\r\n     display: -webkit-flex;\r\n     display: flex;\r\n     -webkit-box-pack: center;\r\n     -webkit-justify-content: center;\r\n             justify-content: center;\r\n     -webkit-box-align: center;\r\n     -webkit-align-items: center;\r\n             align-items: center;\n}\n.g-rmb-label.g-unit-w-label .mint-cell-value:after {\r\n      content: '\\4E07\\5143';\r\n      position: absolute;\r\n      right: 0;\r\n      height: 100%;\r\n      display: -webkit-box;\r\n      display: -webkit-flex;\r\n      display: flex;\r\n      -webkit-box-pack: center;\r\n      -webkit-justify-content: center;\r\n              justify-content: center;\r\n      -webkit-box-align: center;\r\n      -webkit-align-items: center;\r\n              align-items: center;\n}\n.g-mt-cell .mint-cell-title{\r\n    -webkit-box-flex: 0;\r\n    -webkit-flex: none;\r\n            flex: none; \r\n    min-width: 105px;\n}\n.g-mt-cell .mint-cell-value{\r\n    -webkit-box-flex: 1;\r\n    -webkit-flex: 1;\r\n            flex: 1; \r\n    display: -webkit-box; \r\n    display: -webkit-flex; \r\n    display: flex;\n}\n.g-mt-cell .g-input{\r\n    -webkit-box-flex:1;\r\n    -webkit-flex:1;\r\n            flex:1;\n}\n.g-input{\r\n  border: none;\n}\n.g-picker-input:disabled{\r\n   color: inherit;\n}\n.numeric-input-placeholder{\r\n  color: #ccc!important;\n}\n::-webkit-input-placeholder { /* WebKit browsers */\r\n    color: #ccc!important;\n}\n.g-dotted-split{\r\n  margin: 10px 0 5px 0;\r\n  border-top: 1px dotted #e2e2e2;\n}\n.mint-tab-item-label{\r\n  font-size: 14px;\n}\n.picker-toolbar{\r\ndisplay: none;\n}\r\n","",{version:3,sources:["C:/www/oil_new/protected/h5/src/src/App.vue"],names:[],mappings:";AAaA;EACA,0BAAA;CACA;AACA,EAAA,UAAA,SAAA,gBAAA,uBAAA,mBAAA;CAAA;AACA;EACA,cAAA;EACA,2CAAA;EACA,mBAAA;CACA;AACA;EACA,oDAAA;EACA,oCAAA;EACA,mCAAA;EACA,eAAA;CACA;AACA;EACA,qBAAA;EAAA,sBAAA;EAAA,cAAA;EACA,aAAA;CA6BA;AA5BA;IACA,cAAA;CACA;AACA;IACA,oBAAA;IAAA,gBAAA;YAAA,QAAA;IACA,iBAAA;IACA,aAAA;CAOA;AANA;MACA,iBAAA;CACA;AACA;KACA,cAAA;CACA;AAEA;IACA,WAAA;IACA,iBAAA;CACA;AACA;IACA,WAAA;IACA,qBAAA;IAAA,sBAAA;IAAA,cAAA;IACA,0BAAA;IAAA,4BAAA;YAAA,oBAAA;CACA;AACA;IACA,gBAAA;IACA,mBAAA;IACA,wBAAA;CACA;AAEA;IACA,YAAA;IACA,0BAAA;IACA,0BAAA;IACA,mBAAA;CAQA;AAPA;MACA,aAAA;MACA,0BAAA;CACA;AACA;MACA,gBAAA;CACA;AAGA;IACA,mBAAA;CACA;AACA;KACA,iBAAA;KACA,mBAAA;KACA,YAAA;KACA,aAAA;KACA,qBAAA;KAAA,sBAAA;KAAA,cAAA;KACA,yBAAA;KAAA,gCAAA;aAAA,wBAAA;KACA,0BAAA;KAAA,4BAAA;aAAA,oBAAA;CACA;AAEA;MACA,sBAAA;MACA,mBAAA;MACA,SAAA;MACA,aAAA;MACA,qBAAA;MAAA,sBAAA;MAAA,cAAA;MACA,yBAAA;MAAA,gCAAA;cAAA,wBAAA;MACA,0BAAA;MAAA,4BAAA;cAAA,oBAAA;CACA;AAIA;IACA,oBAAA;IAAA,mBAAA;YAAA,WAAA;IACA,iBAAA;CACA;AACA;IACA,oBAAA;IAAA,gBAAA;YAAA,QAAA;IACA,qBAAA;IAAA,sBAAA;IAAA,cAAA;CACA;AACA;IACA,mBAAA;IAAA,eAAA;YAAA,OAAA;CACA;AAEA;EACA,aAAA;CACA;AAEA;GACA,eAAA;CACA;AAEA;EACA,sBAAA;CACA;AACA,8BAAA,qBAAA;IACA,sBAAA;CACA;AACA;EACA,qBAAA;EACA,+BAAA;CACA;AACA;EACA,gBAAA;CACA;AACA;AACA,cAAA;CACA",file:"App.vue",sourcesContent:["<template>\r\n  <div v-min-height id=\"app\">\r\n    <router-view/>\r\n  </div>\r\n</template>\r\n\r\n<script>\r\nimport '@/assets/font/iconfont/iconfont.css'\r\nexport default {\r\n  name: 'app'\r\n}\r\n<\/script>\r\n\r\n<style>\r\nbody{\r\n  background-color: #F5F6F8;\r\n}\r\n*{padding:0;margin:0;list-style:none;box-sizing: border-box;font-style: normal;}\r\ninput,textarea,select{\r\n  outline: none;\r\n  -webkit-tap-highlight-color: rgba(0,0,0,0);  \r\n  font-size: inherit; \r\n}\r\n#app {\r\n  font-family: 'Avenir', Helvetica, Arial, sans-serif;\r\n  -webkit-font-smoothing: antialiased;\r\n  -moz-osx-font-smoothing: grayscale;\r\n  color: #2c3e50;\r\n}\r\n.g-inline-checklist{\r\n  display: flex;\r\n  border: none;\r\n  .mint-checklist-title, .mint-radiolist-title{\r\n    display: none; \r\n  }\r\n  .mint-cell{\r\n    flex: 1; \r\n    background: none;\r\n    border: none;\r\n    &:last-child{\r\n      background: none;\r\n    }\r\n    .mint-cell-left,.mint-cell-right{\r\n     display: none; \r\n    }\r\n  }\r\n  .mint-cell-wrapper{\r\n    padding: 0; \r\n    background: none;\r\n  }\r\n  .mint-radiolist-label{\r\n    padding: 0; \r\n    display: flex;\r\n    align-items: center;\r\n  }\r\n  .mint-radio-label{\r\n    font-size: 14px;\r\n    padding: 2px 0 0 0;\r\n    vertical-align: inherit;\r\n  }\r\n}\r\n.g-table{\r\n    width: 100%;\r\n    border: 1px solid #e2e2e2;\r\n    border-collapse: collapse;\r\n    text-align: center;\r\n    td{\r\n      padding: 5px; \r\n      border: 1px solid #e2e2e2;\r\n    }\r\n    tbody td{\r\n      font-size: 13px;\r\n    }\r\n}\r\n.g-rmb-label{\r\n  .mint-cell-value{\r\n    position: relative; \r\n  }\r\n  .mint-cell-value:before{\r\n     content: \"￥\"; \r\n     position: absolute;\r\n     left: -20px;\r\n     height: 100%;\r\n     display: flex;\r\n     justify-content: center;\r\n     align-items: center;\r\n  }\r\n  &.g-unit-w-label{\r\n    .mint-cell-value:after {\r\n      content: '万元';\r\n      position: absolute;\r\n      right: 0;\r\n      height: 100%;\r\n      display: flex;\r\n      justify-content: center;\r\n      align-items: center;\r\n    }\r\n  }\r\n}\r\n.g-mt-cell{\r\n  .mint-cell-title{\r\n    flex: none; \r\n    min-width: 105px;\r\n  }\r\n  .mint-cell-value{\r\n    flex: 1; \r\n    display: flex;\r\n  }\r\n  .g-input{\r\n    flex:1; \r\n  }\r\n}\r\n.g-input{\r\n  border: none;\r\n}  \r\n.g-picker-input{\r\n  &:disabled{\r\n   color: inherit; \r\n  }\r\n}\r\n.numeric-input-placeholder{\r\n  color: #ccc!important;\r\n}\r\n::-webkit-input-placeholder { /* WebKit browsers */\r\n    color: #ccc!important;\r\n}\r\n.g-dotted-split{\r\n  margin: 10px 0 5px 0;\r\n  border-top: 1px dotted #e2e2e2;\r\n}\r\n.mint-tab-item-label{\r\n  font-size: 14px;\r\n}\r\n.picker-toolbar{\r\ndisplay: none;\r\n}\r\n</style>\r\n"],sourceRoot:""}])},Lrbi:function(n,e){},NHnr:function(n,e,t){"use strict";Object.defineProperty(e,"__esModule",{value:!0});t("Cz6C");var r=t("dU8H"),a=t.n(r),i=(t("Lrbi"),t("aMxQ")),o=t.n(i),c=(t("44/k"),t("utzC")),A=t.n(c),s=(t("q/am"),t("4VPn")),l=t.n(s),p=(t("f1Vh"),t("ZQVe")),u=t.n(p),d=(t("UIRh"),t("uOA4")),m=t.n(d),f=(t("jayP"),t("QKkm")),h=t.n(f),g=(t("PTD1"),t("B7Ql")),C=t.n(g),b=(t("goBr"),t("IBPZ")),y=t.n(b),x=(t("lrMw"),t("7YS2")),v=t.n(x),k=t("7+uW"),w=(t("RsLH"),function(){var n=this.$createElement,e=this._self._c||n;return e("div",{directives:[{name:"min-height",rawName:"v-min-height"}],attrs:{id:"app"}},[e("router-view")],1)});w._withStripped=!0;var _={render:w,staticRenderFns:[]},B=_;var I=!1;var E=t("VU/8")({name:"app"},B,!1,function(n){I||t("Cwy8")},null,null);E.options.__file="src\\App.vue";var j=E.exports,N=t("d7EF"),P=t.n(N),q=t("/ocq"),T=function(){return Promise.all([t.e(0),t.e(12)]).then(t.bind(null,"RtFp"))},M=function(){return Promise.all([t.e(0),t.e(7)]).then(t.bind(null,"UcDP"))},O=function(){return Promise.all([t.e(0),t.e(10)]).then(t.bind(null,"PDlz"))},F=function(){return Promise.all([t.e(0),t.e(2)]).then(t.bind(null,"OZNm"))};k.default.use(q.a);var D=new q.a({mode:"history",routes:[{path:"/site/login",name:"login",component:function(){return t.e(5).then(t.bind(null,"BOGj"))}},{path:"/audit/contract",name:"AuditContract",component:function(){return Promise.all([t.e(0),t.e(6)]).then(t.bind(null,"xBQA"))}},{path:"/check13/(check|detail)",name:"Check13Check",component:function(){return Promise.all([t.e(0),t.e(1)]).then(t.bind(null,"lIq8"))}},{path:"/(check2|check3)/(check|detail)",name:"CheckCheck",component:function(){return Promise.all([t.e(0),t.e(1)]).then(t.bind(null,"SKGP"))}},{path:"/(check2|check3)/history",name:"CheckHistory",component:F},{path:"/(check2|check3)/(check|detail)/history",name:"CheckHistory",component:F},{path:"/check13/history",name:"Check13History",component:M},{path:"/check13/(check|detail)/history",name:"Check13History",component:M},{path:"/(check2|check3)/contract",name:"CheckContract",component:O},{path:"/(check2|check3)/(check|detail)/contract",name:"CheckContract",component:O},{path:"/check13/(check|detail)/contract",name:"Check13Contract",component:function(){return Promise.all([t.e(0),t.e(14)]).then(t.bind(null,"QVV9"))}},{path:"/check13/(check|detail)/attachments",name:"Check13Attachments",component:function(){return Promise.all([t.e(0),t.e(11)]).then(t.bind(null,"UpRi"))}},{path:"/check13/(check|detail)/contract-files",name:"Check13contractFiles",component:function(){return Promise.all([t.e(0),t.e(8)]).then(t.bind(null,"Qh0/"))}},{path:"/check13/(check|detail)/payments",name:"Check13Payments",component:function(){return Promise.all([t.e(0),t.e(2)]).then(t.bind(null,"3VXS"))}},{path:"/check*/agentfee",name:"AgentFee",component:T},{path:"/check*/(check|detail)/agentfee",name:"AgentFee",component:T},{path:"/check*/(check|detail)/goods",name:"CheckGoods",component:function(){return Promise.all([t.e(0),t.e(4)]).then(t.bind(null,"bj/L"))}},{path:"/check*/(check|detail)/add-price",name:"AuditAddPrice",component:function(){return Promise.all([t.e(0),t.e(3)]).then(t.bind(null,"0ep7"))}},{path:"/(check2|check3)/(check|detail)/attachment",name:"CheckAttachment",component:function(){return Promise.all([t.e(0),t.e(9)]).then(t.bind(null,"Nm8X"))}},{path:"/test",name:"test",component:function(){return t.e(15).then(t.bind(null,"11gr"))}},{path:"/",name:"404",component:function(){return t.e(13).then(t.bind(null,"OGiG"))}}],scrollBehavior:function(n,e,t){t?setTimeout(function(){window.scrollTo(t.x,t.y)},10):setTimeout(function(){window.scrollTo(0,0)},10)}});D.beforeEach(function(n,e,t){var r=D.app.$store,a=n.path.replace(/^\//,"").split("/"),i=P()(a,2),o=i[0],c=i[1];if(o.match(/^(check)/)&&c.match(/^(check|detail)$/)){"history"===c?c="detail":"detail"!==c&&(c="check");var A="detail"===c?"detail_id":"id";r.dispatch(o+"/fetchPageData",{id:n.query[A],actionName:c,idName:A,force:n.query.__force})}t()});var G=D,S=t("NYxO"),z=t("Xxa5"),R=t.n(z),H=t("exGp"),W=t.n(H),K=t("Dd8w"),Y=t.n(K),L=(t("OgVB"),t("/Lyv")),Q=t.n(L),U=t("bOdI"),V=t.n(U),Z=(t("qONS"),t("UQTY")),$=t.n(Z),X=t("pFYg"),J=t.n(X),nn=t("vLgD"),en=t("4/BI"),tn=this,rn=function(n){return Object(en.a)(n/100,2)},an=function(n,e,t){return n?n.map(function(n){return{id:n.detail_id,name:n.goods.name,quantity:n.quantity,quantityName:(1*n.quantity).toFixed(2)+(Array.isArray(n.goods)||"0"===n.goods.unit?"":e.goods_unit[n.goods.unit].name),type:n.type,avatar:"1"===n.type?"采":"销",price:e.currency[n.currency].ico+rn(n.price),rateName:1*n.more_or_less_rate!=0?"±"+Object(en.a)(100*n.more_or_less_rate,2)+"%":"",amount:e.currency[n.currency].ico+rn(n.amount),amountCNY:"￥"+rn(n.amount_cny),amountCNYValue:n.amount_cny,exchangeRate:Number(t)?Object(en.a)(t,2):"",refer_target:n.refer_target}}):{}},on=function(n,e,t){return e?e.map(function(e){return{date:e.pay_date,typeName:t[n][e.expense_type].name+(5!==Number(e.expense_type)?"":"("+e.expense_name+")"),price:t.currency[e.currency].ico+rn(e.amount),remark:e.remark}}):{}},cn=function(n,e){return n?n.map(function(n){var t=e.project_launch_attachment_type[n.type];return{id:n.id,name:n.name,status:n.status,url:n.file_url,typeName:t?t.name:"",isImg:!!n.file_url.match(/\.(jpg|png|bmp|gif)$/),download_path:n.download_path}}):{}},An=function(n,e){var t=e.project_type[n.type];return n.base&&"0"!==n.base.buy_sell_type&&(t+="-"+e.purchase_sale_order[n.base.buy_sell_type]),t},sn=function(n,e){return n?n.map(function(n){return{name:n.agentGoods.name,unitName:e.goods_unit[n.agentGoods.unit].name,payTypeName:e.agent_fee_pay_type[n.type],price:"￥"+rn(n.price),feeRate:Object(en.a)(100*n.fee_rate,2)+"%",fee:"￥"+rn(n.amount)}}):[]},ln=function(n,e,t){var r=[];return n&&"object"===(void 0===n?"undefined":J()(n))&&n.type&&n.category&&r.push(t.contract_config[n.type][n.category].name),e&&"object"===(void 0===e?"undefined":J()(e))&&e.type&&e.category&&r.push(t.contract_config[e.type][e.category].name),r.join("/")},pn=function(n,e,t){return(n=n||[]).map(function(n){var e=Array.isArray(n.quotaManager)?n.quotaPartner:n.quotaManager;return{id:n.detail_id,name:e.name||e.user_name,price:"￥"+(r=n.amount,Object(en.a)(r/1e6,2))+"万元",comment:n.remark,icon:1===t?"采":"销",isBlue:2===t};var r})},un=function(){var n=W()(R.a.mark(function n(e,t){var r,a,i,o,c,A,s,l,p,u,d,m,f,h,g,C,b,y,x,v=arguments.length>2&&void 0!==arguments[2]?arguments[2]:"check",k=arguments.length>3&&void 0!==arguments[3]?arguments[3]:"id";return R.a.wrap(function(n){for(;;)switch(n.prev=n.next){case 0:return $.a.open(),n.next=3,Object(nn.a)("/check"+e+"/"+v,V()({},k,t));case 3:return r=n.sent,$.a.close(),r.success&&r.data?(a=r.data,i=a.pageTitle,o=a.contract,c=a.relative,A=a.map,s=a.data,l=a.checkHistory,p=void 0===l?[]:l,u=a.checkLogs,d=void 0===u?[]:u,m=a.creator,f=a.updater,h=a.create_time,g=a.update_time,d.length&&(p=d),C=(o||{}).project,b="1"===o.type?o:c,y="2"===o.type?o:c,x={pageTitle:i,detailId:s.detail_id,contractId:o.contract_id,isMain:o.is_main,projectId:o.project_id,projectCode:C.project_code,projectType:1*C.type,projectTypeName:An(C,A),contractTypeName:ln(b,y,A),agentTypeName:A.buy_agent_type[o.agent_type],agentName:o.agent.name,buySellDesc:o.buy_sell_desc,corporationName:o.corporation.name,prevPartnerName:b&&b.partner?b.partner.name:"",nextPartnerName:y&&y.partner?y.partner.name:"",buyGoods:b?an(b.contractGoods,A,b.exchange_rate):"",sellGoods:y?an(y.contractGoods,A,y.exchange_rate):"",buyContent:b&&b.extra?JSON.parse(b.extra.content):"",sellContent:y&&y.extra?JSON.parse(y.extra.content):"",prevPayments:b&&b.payments?on("pay_type",b.payments,A):"",nextPayments:y&&y.payments?on("proceed_type",y.payments,A):"",attachments:cn(C.attachments,A),agentDetails:sn(o.agentDetail,A),buyFormula:b?b.formula||"":"",sellFormula:y?y.formula||"":"",checkId:s.check_id,quotas:pn(b&&b.quotas?b.quotas:null,0,1).concat(pn(y&&y.quotas?y.quotas:null,0,2)),checkHistory:p.map(function(n){return{id:n.id,nodeName:n.node_name,time:n.check_time,name:n.name,comment:n.remark,status:n.check_status}}),buyContract:b,sellContract:y,creator:m,updater:f,create_time:h,update_time:g},r.data=x):Q()({title:"",message:r.msg,confirmButtonText:"返回首页"}).then(function(n){"confirm"===n&&(window.location.href="/site/index")}),n.abrupt("return",r);case 7:case"end":return n.stop()}},n,tn)}));return function(e,t){return n.apply(this,arguments)}}(),dn=function(){var n=W()(R.a.mark(function n(e,t){var r,a,i,o;return R.a.wrap(function(n){for(;;)switch(n.prev=n.next){case 0:return $.a.open(),n.next=3,Object(nn.a)("/quota/ajaxEdit",{contract_id:e,is_main:t});case 3:return r=n.sent,$.a.close(),r.success&&r.data?(a=r.data,i=a.contract,o=a.relative,r.data.detailId=e,r.data.buyContract="1"===i.type||!!o,r.data.sellContract="2"===i.type||!!o):Q.a.alert(r.msg,"").then(function(n){window.location.href="/site/index"}),n.abrupt("return",r);case 7:case"end":return n.stop()}},n,this)}));return function(e,t){return n.apply(this,arguments)}}(),mn={namespaced:!0,state:Y()({},{audit1:"",audit1comment:"",audit2:""},{audit1Error:"",pageData:null,quotaInfo:null}),mutations:{update:function(n,e){var t=e.field,r=e.value;n[t]=r}},actions:{fetchPageData:function(n,e){var t=this,r=n.commit,a=n.state,i=e.id,o=e.actionName,c=e.idName,A=e.force;return W()(R.a.mark(function n(){var e;return R.a.wrap(function(n){for(;;)switch(n.prev=n.next){case 0:if(A||!a.pageData){n.next=2;break}return n.abrupt("return",{success:!0,data:a.pageData});case 2:return n.next=4,un(2,i,o,c);case 4:return(e=n.sent).success&&(r("update",{field:"pageData",value:e.data}),document.title=e.data.pageTitle||"石油系统"),n.abrupt("return",e);case 7:case"end":return n.stop()}},n,t)}))()},getQuotaInfo:function(n,e){var t=this,r=n.commit,a=e.contractId,i=e.isMain;return W()(R.a.mark(function n(){var e;return R.a.wrap(function(n){for(;;)switch(n.prev=n.next){case 0:return n.next=2,dn(a,i);case 2:(e=n.sent).success&&r("update",{field:"quotaInfo",value:e.data});case 4:case"end":return n.stop()}},n,t)}))()}}},fn={namespaced:!0,state:Y()({},{audit1:"",audit1comment:"",audit2:""},{audit1Error:"",pageData:null,quotaInfo:null}),mutations:{update:function(n,e){var t=e.field,r=e.value;n[t]=r}},actions:{fetchPageData:function(n,e){var t=this,r=n.commit,a=n.state,i=e.id,o=e.actionName,c=e.idName,A=e.force;return W()(R.a.mark(function n(){var e;return R.a.wrap(function(n){for(;;)switch(n.prev=n.next){case 0:if(A||!a.pageData){n.next=2;break}return n.abrupt("return",{success:!0,data:a.pageData});case 2:return n.next=4,un(3,i,o,c);case 4:return(e=n.sent).success&&(r("update",{field:"pageData",value:e.data}),document.title=e.data.pageTitle||"石油系统"),n.abrupt("return",e);case 7:case"end":return n.stop()}},n,t)}))()},getQuotaInfo:function(n,e){var t=this,r=n.commit,a=e.contractId,i=e.isMain;return W()(R.a.mark(function n(){var e;return R.a.wrap(function(n){for(;;)switch(n.prev=n.next){case 0:return n.next=2,dn(a,i);case 2:(e=n.sent).success&&r("update",{field:"quotaInfo",value:e.data});case 4:case"end":return n.stop()}},n,t)}))()}}},hn=(t("nkZn"),t("NC6I")),gn=t.n(hn),Cn=this;!function(){var n=W()(R.a.mark(function n(e){var t,r=e.username,a=e.password;return R.a.wrap(function(n){for(;;)switch(n.prev=n.next){case 0:return n.next=2,Object(nn.b)("/site/login",{"obj[username]":r,"obj[password]":(void 0)(a)});case 2:return t=n.sent,n.abrupt("return",t);case 4:case"end":return n.stop()}},n,Cn)}))}();k.default.use(S.a);new S.a.Store({modules:{check2:mn,check3:fn,check13:gn.a},actions:{login:function(n,e){return(void 0)(e)}}});var bn=t("ripP"),yn=function(){var n=this,e=n.$createElement,t=n._self._c||e;return t("transition",{attrs:{name:"fade"}},[n.showed?t("div",{staticClass:"toptips",class:n.type},[t("div",{staticClass:"toptips-msg",style:n.inlineWidth?"width:"+n.inlineWidth+"px":"",domProps:{innerHTML:n._s(n.msg)}}),n._v(" "),t("icon",{staticClass:"toptips-close",nativeOn:{click:function(e){e.stopPropagation(),n.hide(e)}}},[n._v("")])],1):n._e()])};yn._withStripped=!0;var xn={render:yn,staticRenderFns:[]},vn=xn;var kn=!1;var wn=t("VU/8")(vn,_n,!1,function(n){kn||t("GQ9g")},"data-v-06dfb5c8",null);wn.options.__file="src\\plugins\\toptips\\component.vue";var _n=wn.exports,Bn=k.default.extend(In),In={install:function(n,e){n.prototype.$toptips=function(n,e,t){var r=document.createElement("div");document.body.appendChild(r);var a=new Bn({propsData:{msg:n,type:t,closeTime:e}}).$mount(r);return console.log(a),a.show(),a}}};k.default.component(v.a.name,v.a),k.default.component(y.a.name,y.a),k.default.component(C.a.name,C.a),k.default.component(h.a.name,h.a),k.default.component(m.a.name,m.a),k.default.component(u.a.name,u.a),k.default.component(l.a.name,l.a),k.default.component(A.a.name,A.a),k.default.component(o.a.name,o.a),k.default.component(a.a.name,a.a),k.default.config.productionTip=!1,k.default.use(bn.a),k.default.use(void 0),k.default.directive("min-height",{inserted:function(n){n.style.minHeight=window.innerHeight+"px"}}),new k.default({el:"#app",router:G,template:"<App/>",components:{App:j}})},OgVB:function(n,e){},PTD1:function(n,e){},RsLH:function(n,e){},UIRh:function(n,e){},ULTG:function(n,e){},f1Vh:function(n,e){},goBr:function(n,e){},jayP:function(n,e){},joTY:function(n,e){},lrMw:function(n,e){},nkZn:function(n,e){"use strict";throw new Error("Module build failed: SyntaxError: C:/www/oil_new/protected/h5/src/service/check13.js: Unexpected token, expected , (122:17)\n\n  120 |       subContractType: mapContractType(apply.sub_contract_type, map),\n  121 |       payRemark: (apply.remark || '') + (apply.extra (apply.extra.remark || ''),\n> 122 |       subjectName: apply.subject.name,\n      |                  ^\n  123 |       // 是否对接保理\n  124 |       isFactoring: (Number(apply.type) !== 13) ? trueOrFalseFactoring(apply.is_factoring) : null,\n  125 |       // 本次保理金额\n")},"q/am":function(n,e){},qONS:function(n,e){},ripP:function(n,e,t){"use strict";var r=function(){var n=this.$createElement;return(this._self._c||n)("i",[this._t("default")],2)};r._withStripped=!0;var a={render:r,staticRenderFns:[]},i=a;var o=!1;var c=t("VU/8")({props:{}},i,!1,function(n){o||t("0T5k")},"data-v-71953609",null);c.options.__file="src\\components\\Icon.vue";e.a=c.exports},vLgD:function(n,e,t){"use strict";t.d(e,"a",function(){return v}),t.d(e,"b",function(){return k});var r=t("Xxa5"),a=t.n(r),i=t("exGp"),o=t.n(i),c=t("Dd8w"),A=t.n(c),s=t("pFYg"),l=t.n(s),p=t("mtWM"),u=t.n(p),d=t("mw3O"),m=t.n(d),f=this,h=location.origin,g=function(n){return n?"string"==typeof n?n:n.data:""},C=function(n){var e,t={success:!0,msg:"操作成功"};return!("object"===(void 0===(e=n)?"undefined":l()(e))&&0===e.state)?(t.success=!1,t.msg=g(n)):t=A()({},t,{data:n.data}),t},b=function(n,e){return{success:!1,msg:g(e)||"服务异常，请稍后重试",status:n}},y=function(){var n=o()(a.a.mark(function n(e){return a.a.wrap(function(n){for(;;)switch(n.prev=n.next){case 0:e.success||"请先登录"!==e.msg||(location.href="/site/login?redirect="+encodeURIComponent(location.href));case 1:case"end":return n.stop()}},n,f)}));return function(e){return n.apply(this,arguments)}}();function x(n,e){var t=this,r=arguments.length>2&&void 0!==arguments[2]?arguments[2]:{},i=arguments[3];return n=n.toLowerCase(),i=A()({method:n,headers:{"Content-Type":"application/x-www-form-urlencoded","X-Requested-With":"XMLHttpRequest"},params:{},data:{}},i),"get"===n||"delete"===n?(r.__ajax=1,i.params=r):i.data=m.a.stringify(r),u()(h+e,i).then(function(){var n=o()(a.a.mark(function n(e){var r;return a.a.wrap(function(n){for(;;)switch(n.prev=n.next){case 0:return r=200===e.status?C(e.data):b(e.status,e.data),n.next=3,y(r);case 3:return n.abrupt("return",r);case 4:case"end":return n.stop()}},n,t)}));return function(e){return n.apply(this,arguments)}}()).catch(function(n){var e=n.response?n.response.data:{},t=n.response?n.response.status:0;return b(t,e)})}var v=function(){for(var n=arguments.length,e=Array(n),t=0;t<n;t++)e[t]=arguments[t];return x.apply(void 0,["GET"].concat(e))},k=function(){for(var n=arguments.length,e=Array(n),t=0;t<n;t++)e[t]=arguments[t];return x.apply(void 0,["POST"].concat(e))}}},["NHnr"]);
//# sourceMappingURL=app.c8102ad6d3a7ab0b87df.js.map