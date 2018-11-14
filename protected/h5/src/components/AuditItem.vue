<template>
  <section class="audit-item" :class="{[type]:true}">
  <template v-if="type=='default'">
    <div class="title">{{title}}</div>
    <mt-radio class="checklist g-inline-checklist" title="" :options="options" v-model="mainValue"/>
  </template>
  <template v-if="type=='input'">
    <div class="comment">
      <span>{{label}}:</span>
      <input type="text" :placeholder="placeholder" v-model="mainValue" />
    </div>
  </template>
  <template v-if="type=='comment'">
    <textarea rows="2" :placeholder="placeholder" v-model="mainValue" :maxlength="maxlength"></textarea>
    <label class="comment-label">{{mainValue.length}}/{{maxlength}}</label>
  </template>
</section>
</template>
<script>

export default {
  components: {
  },
  props: {
    type: {
      default: 'default'
    },
    title: {
      default: '标题'
    },
    label: {
      default: '备注'
    },
    placeholder: {
      default: '请填写备注内容'
    },
    maxlength: {
      default: 100
    },
    value: {
    }
  },
  computed: {
  },
  watch: {
    mainValue: 'emitInput'
  },
  data () {
    let value = this.value || ''
    return {
      options: [
        {label: '是', value: '1'},
        {label: '否', value: '0'}
      ],
      mainValue: value
    }
  },
  methods: {
    emitInput (v) {
      this.$emit('input', v)
    }
  }
}
</script>
<style scoped>
input,textarea{
  border: none;
  background-color: transparent;
}
.audit-item {
  display: flex;
  font-size: 14px;
  flex-direction: column;
  position: relative;
  .title{
    /* font-weight: bold; */
    /* font-size: 14px; */
    color: #000;
    font-size: 16px;
  }
  .checklist{
   padding: 10px 0; 
  }
  .comment {
    display: flex;
    span{
      padding-right: 5px;
      line-height: normal;
    }
    input {
      border: none;
      background-color: transparent;
      flex: 1;
      font-size: 14px;
    }
  }
  textarea{
    padding: 0;
  }
  .comment-label{
    text-align: right;
    font-size: 12px;
    color: #ccc;
  }
}

</style>
