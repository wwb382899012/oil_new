<template>
  <div class="main" v-if="quotaInfo">
    <mt-navbar v-model="active">
      <mt-tab-item id="1" v-if="quotaInfo.sellContract">销售占用额度</mt-tab-item>
      <mt-tab-item id="2" v-if="quotaInfo.buyContract">采购占用额度</mt-tab-item>
    </mt-navbar>
    <div class="toolbar">
        <div class="btn-text" @click.stop="add">+新增</div>
    </div>
    <mt-tab-container class="tab-container" v-model="active">
      <mt-tab-container-item id="1" v-if="quotaInfo.sellContract">
        <transition-group name="fade">
          <div v-for="(p,i) in price1" :key="p.id" v-auto-focus>
              <add-price v-model="price1[i]" :options="quotaInfo.downManagers"></add-price>
              <div class="btn-del" ><span @click.stop="del(i, 1)">{{'删除'}}</span></div>
          </div>
        </transition-group>
      </mt-tab-container-item>
      <mt-tab-container-item id="2" v-if="quotaInfo.buyContract">
        <transition-group name="fade">
          <div v-for="(p,i) in price2" :key="p.id" v-auto-focus>
              <add-price v-model="price2[i]" :options="quotaInfo.upManagers"></add-price>
              <div class="btn-del" ><span @click.stop="del(i, 2)">{{'删除'}}</span></div>
          </div>
        </transition-group>
      </mt-tab-container-item>
    </mt-tab-container>
    <submit-btn okText="提交" @okHandle="ok" @cancelHandle="cancel" cancelText="清除"  />
  </div>
</template>

<script>
import { Indicator, MessageBox } from 'mint-ui'
import {mapBase, mapStatePlus} from '@/utils/mapVmodel'
import SubmitBtn from '@/components/SubmitBtn'
import addPrice from '@/components/addPrice'
import {post} from '@/utils/request'

const emptyRow = (type) => {
  return {
    // type: type === 2 ? '上游合作方' : '下游合作方',
    type: '',
    price: '',
    comment: '',
    id: new Date().getTime(),
    error: ''
  }
}

export default {
  components: {
    addPrice,
    SubmitBtn
  },
  data () {
    return {
      popupVisibele: false,
      price1: [],
      price2: [],
      price1Error: -1,
      price2Error: -1,
      active: -1,
      detailId: ''
    }
  },
  beforeRouteEnter (to, from, next) {
    next(async vm => {
      await vm.$store.dispatch(`${vm.moduleName}/getQuotaInfo`, {contractId: to.query.id})
      vm.defaultActive()
      vm.detailId = to.query.detail_id
    })
  },
  computed: {
    ...mapBase(),
    ...mapStatePlus('quotaInfo')
  },
  methods: {
    defaultActive () {
      let active = -1
      let {quotaInfo} = this
      if (quotaInfo) {
        active = quotaInfo.sellContract ? '1' : '2'
      }
      this.$nextTick(() => {
        this.active = active
      })
    },
    add () {
      this['price' + this.active].push(emptyRow(this.active * 1))
    },
    del (index, active) {
      // let price =
      // if (price.length > 1) {
      // this.$delete(price, index)
      this['price' + active].splice(index, 1)
      // }
    },
    async ok () {
      // Indicator.open()
      let failed = false
      let upQuotaItems = []
      let downQuotaItems = []
      let {moduleName} = this
      let tmpcb = (active, item, index) => {
        if (failed) {
          return true
        }
        // let {type, price, comment} = item
        let msgs = {
          type: '请选择占用对象',
          price: '请输入占用额度'
        }
        let msg = ''
        for (let key in item) {
          if (key !== 'comment' && item[key] === '') {
            msg = msgs[key]
            failed = true
            break
          }
        }
        if (failed) {
          this.$toptips(msg)
          item.error = new Date().getTime()
          this.active = active
          this['price' + active].splice(index, 1, item)
          failed = true
        } else {
          let pItem = {user_id: item.type.user_id, quota: item.price * 10000 * 100, remark: item.comment}
          active === '1' ? downQuotaItems.push(pItem) : upQuotaItems.push(pItem)
        }
      }
      // if(this.price1.length<=0 && this.price2.length<=0){
      //   this.$toptips('')
      // }
      this.price1.find((...args) => tmpcb('1', ...args))
      this.price2.find((...args) => tmpcb('2', ...args))
      if (!failed) {
        let {contract} = this.quotaInfo
        let submitData = {
          project_id: contract.project_id,
          contract_id: contract.contract_id,
          is_main: contract.is_main
        }
        if (downQuotaItems.length) {
          submitData.downQuotaItems = downQuotaItems
        }
        if (upQuotaItems.length) {
          submitData.upQuotaItems = upQuotaItems
        }
        Indicator.open()
        let ret = await post(`/quota/save`, submitData)
        Indicator.close()
        if (!ret.success) {
          this.$toptips(ret.msg)
        } else {
          await MessageBox.alert('提交成功!')
          // location.href = `/quota/detail?contract_id=${contract.contract_id}&is_main=${contract.is_main}`
          this.$router.replace({path: `/${moduleName}/detail`, query: {detail_id: this.detailId, __force: true}})
        }
      }
    },
    cancel () {
      // this['price' + this.active] = [emptyRow(this.active * 1)]
      this['price' + this.active] = []
    }
  },
  directives: {
    autoFocus: {
      inserted (el) {
        // console.log(el)
        el.scrollIntoView()
      }
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: all .3s;
}
.fade-enter-active {
  transform: translate3d(0,0,0);
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
  opacity: 0;
  transform: translate3d(-100%,0,0);
}
.main{
    position: absolute;
    top:0;
    left:0;
    right:0;
    bottom:0;
    width:100%;
    height:100%;
    font-size: 14px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    .category {
      padding: 0 0 20px 0;
    }
    .tab-title{
      display: flex;
      justify-content: center;
      padding: 10px;
      ul{
        display: flex; 
        border-radius: 8px;
        border: 1px solid #19B491;
        overflow: hidden;
        font-size: 14px;
        li{
          padding: 5px 15px;
          border-right: 1px solid  #19B491;
          color: #19B491;
        }
        li:last-child{
          border-right:none; 
        }
        li.active{
          background-color: #19B491; 
          color: #ffffff;
        }
      } 
    }
}
.toolbar{
  padding: 5px 10px;
  font-size: 14px;
  z-index: 100;
  width: 100%;
  background-color: #F5F6F8;
  border-bottom: 1px solid #e2e2e2;
  margin-top:2px;
}
.btn-del{
  padding: 5px 10px;
  text-align:right;
}
.tab-container{
  flex: 1;
  overflow-y: auto;
}
</style>
