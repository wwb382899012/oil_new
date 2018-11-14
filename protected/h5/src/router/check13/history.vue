<template>
  <div class="history">
    <div>审核节点</div>
    <audit-history-item :isLatest="index==0" v-for="(item, index) in checkHistory" :key="item.id" :member="item.name" :time="item.time" :desc="item.comment" :status="item.status" :name="item.nodeName" :checkChoices="(item.checkChoices)?item.checkChoices:null"></audit-history-item>
  </div>
</template>

<script>
import AuditHistoryItem from '@/components/AuditHistoryItem'
import {mapBase, mapStatePlus} from '@/utils/mapVmodel'

export default {
  components: {
    AuditHistoryItem
  },
  data () {
    return {
    }
  },
  computed: {
    ...mapBase(),
    ...mapStatePlus('pageData', ['checkHistory'])
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.history{
  padding: 15px;
}
</style>
