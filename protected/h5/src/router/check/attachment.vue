<template>
  <div>
    <template v-if="attachments" v-for="att in attachments"  >
      <panel :title="att.typeName" v-if="att.status == '1'">
        <a :href="att.download_path" class="link">{{att.name}}</a>

        <!-- 暂无不需要复杂图片预览 -->
        <!--<a v-if="!att.isImg" :href="att.download_path" class="link">{{att.name}}</a>-->
        <!--<viewer v-else :options="options" :images="[att.download_path]"-->
                <!--@inited="inited"-->
                <!--class="viewer" ref="viewer"-->
        <!--&gt;-->
          <!--<template slot-scope="scope">-->
            <!--<img v-for="src in scope.images" :src="src" :key="src">-->
          <!--</template>-->
        <!--</viewer>-->
      </panel>
    </template>
  </div>
</template>

<script>
import Panel from '@/components/Panel'
import Viewer from 'v-viewer/src/component.vue'
import { mapBase, mapStatePlus } from '@/utils/mapVmodel'

export default {
  components: {
    Panel,
    Viewer
  },
  data () {
    return {
      options: {
        // 'inline': true,
        // 'button': false,
        'navbar': false,
        'title': false,
        'toolbar': false,
        'tooltip': false
        // 'movable': false,
        // 'zoomable': true,
        // 'rotatable': true,
        // 'scalable': true,
        // 'transition': true,
        // 'fullscreen': true,
        // 'keyboard': false,
        // 'url': 'data-source'
      }
    }
  },
  methods: {
    inited (viewer) {
      this.$viewer = viewer
    },
    show () {
      this.$viewer.show()
    }
  },
  computed: {
    ...mapBase(),
    ...mapStatePlus('pageData', ['attachments'])
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
img{
width: 100%;
height: auto;
}
.link{
display: block;
margin: 10px;
}

</style>
