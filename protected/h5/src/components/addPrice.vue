<template>
  <section class="item">
  <error-focus :value="value.error && typeShowName==''" @input="emitValue">
    <mt-cell class="g-mt-cell"  title="占用对象" >
      <input type="text" readonly @focus="autoBlur"  placeholder="请选择占用对象" class="g-input g-picker-input" :value="typeShowName"  @click.stop="popupVisibele=true"/>
      <Icon>&#xe602;</Icon>
    </mt-cell>
  </error-focus>
    <!-- <mt&#45;field label="占用额度" class="g&#45;rmb&#45;label" type="number" :attr="{pattern:'[0&#45;9]*',onFocus:priceFocus}"  placeholder="请输入占用额度"></mt&#45;field> -->
  <error-focus :value="value.error && price == ''" @input="emitValue">
    <mt-cell title="占用额度" class="g-rmb-label g-unit-w-label g-mt-cell">
      <NumericInput placeholder="请输入占用额度" class="input" name="price" v-model="price" :keyboard="{entertext:'OK'}"/>
    </mt-cell>
  </error-focus>
    <audit-item class="comment" type="comment" v-model="comment" />
    <mt-popup v-model="popupVisibele" class="popup" :closeOnClickModal="false"  position="bottom" >
      <div class="toolbar" >
        <a @click.stop="popupHandler('cancel')">取消</a> 
        <a @click.stop="popupHandler('ok')">确定</a>
      </div>
      <mt-picker :slots="slots" :valueKey="nameKey" :showToolbar="true" @change="onValuesChange">
      </mt-picker>
    </mt-popup>
    </section>
</template>
<script>
import Icon from './Icon'
import AuditItem from './AuditItem'
import { NumericInput } from 'numeric-keyboard'
import ErrorFocus from '@/components/ErrorFocus'

export default {
  components: {
    Icon,
    AuditItem,
    NumericInput,
    ErrorFocus
  },
  props: {
    value: {
      default () {
        return {
        }
      }
    },
    options: {
      default: () => ([])
    },
    nameKey: {
      default: 'name'
    },
    valueKey: {
      default: 'value'
    }
  },
  data () {
    return {
      popupVisibele: false,
      keyboardVisible: false,
      slots: [{
        // flex: 1,
        values: this.options || []
      }],
      type: this.value.type || '',
      price: '',
      comment: '',
      prevType: '上游合作方',
      tmpType: '',
      picker: null
    }
  },
  computed: {
    defaultIndex () {
      return this.options.indexOf(this.value.type)
    },
    typeShowName () {
      return this.type ? this.type[this.nameKey] : ''
    }
  },
  watch: {
    price: 'emitValue',
    comment: 'emitValue'
  },
  methods: {
    onValuesChange (picker, values) {
      this.picker = picker
      this.tmpType = values[0]
      // console.log(arguments)
      // if (values[0] > values[1]) {
        // console.log(values)
      // picker.setSlotValue(0, values[0])
      // }
    },
    press () {
    },
    priceFocus () {
    },
    autoBlur (e) {
      e.target.blur()
    },
    popupHandler (type) {
      // console.log(this.tmpType)
      if (type === 'cancel' && this.picker && this.tmpType !== this.type) {
        // this.type = this.prevType
        this.tmpType = this.type
      } else if (type === 'ok') {
        // console.log(this.tmpType)
        this.type = this.tmpType
        this.emitValue()
        this.picker.setSlotValue(0, this.type)
      }
      this.popupVisibele = false
    },
    emitValue () {
      this.$emit('input', Object.assign(this.value, {
        type: this.type,
        price: this.price,
        comment: this.comment,
        error: false
      }))
    }
  }

}
</script>
<style scoped>
.comment{
  padding: 10px;
  background: #ffffff;
  font-size: 16px!important;
  border-top: 1px solid #f0f0f0;
}
.popup{
 width: 100%;
}
.toolbar{
 display: flex;
 justify-content: space-between;
 padding: 10px;
 border-bottom: 1px solid #f0f0f0;

 a{
  color: #005cff;
  font-size: 16px;
 }
}
.input{
  color: #666!important;
}
</style>

