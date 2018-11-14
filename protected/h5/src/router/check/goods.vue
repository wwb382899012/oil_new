<template>
  <div>
  <audit-goods title="采购交易明细" name="采购" v-if="buyGoods&&buyGoods.length>0" :goods="buyGoods"></audit-goods>
  <audit-goods title="销售交易明细" name="销售" v-if="sellGoods&&sellGoods.length>0" :goods="sellGoods"></audit-goods>
  </div>
</template>

<script>
import AuditGoods from '@/components/AuditGoods'
import {mapBase} from '@/utils/mapVmodel'
import { mapState } from 'vuex'

export default {
  components: {
    AuditGoods
  },
  computed: {
    ...mapBase(),
    ...mapState({
      buyGoods (state) {
        let pageData = state[this.moduleName].pageData
        return pageData ? pageData.buyGoods : null
      },
      sellGoods (state) {
        let pageData = state[this.moduleName].pageData
        return pageData ? pageData.sellGoods : null
      }
    })
  }
}
</script>

<style scoped>

.split{
  height: 1px;
  border-top: 1px dotted #e2e2e2;
  width: 100%;
  margin: 5px auto;
}
</style>
