<div id="appq" class="my-progress-bar">
  <i class="btn-left icon icon-31fanhui1" @click="toUp"></i>
  <span class="btn-right icon icon-31fanhui2" @click="toDown" /></span>
  <ul :style="{width:ulWidth,marginLeft: ulLeft}" style="height: 136px;">
    <!-- <li v-for="(item,index) of comData.data" :key="index" :class="item.done?'done':''" :style="{width:liWidth}" @mouseover="showPopover=true" @mouseleave="showPopover=false"> -->
    <li v-for="(item,index) of comData.data" :key="index" class="done" :style="{width:liWidth}" @mouseover="showPopover=true" @mouseleave="showPopover=false">
      <p class="progress-node-name">{{ item.node_name }}</p>
      <!-- 1:通过 -100发起人  -101:待处理  其他：驳回 -->
      <p v-if="item.check_status==1 || item.check_status==-100" class="status-yes"><i class="icon icon-check-circle-fill"></i></p>
      <p v-else-if="item.check_status==-101" class="dot"></p>
      <p v-else class="status-no"><i class="icon icon-close-circle-fill"></i></p>
      <p class="bar" :class="(index+currentPage)/currentPage==5?'end':((index+currentPage)%5==0 && index !== 0)?'front':''"></p>
      <p class="progress-name">{{ item.name }}</p>
      <p style="font-size: 12px; color: #999;line-height: 12px;">{{(item.check_status !== 1 && item.check_status!== -101) ? item.check_time : '正在处理中'}}</p>
      <div v-if="(item.check_status!==-100) && (item.check_status!==-101)" class="my-popover-com pop">
        <span class="sanjiao-com bottom"></span>
        <div v-show="showPopover" class="pop-content">
          <div style="text-align:left;font-size:12px;font-weight:500;">审核意见：{{item.remark}}</div>
          <ul style="text-align:left;">
            <li v-if="item.item.length" v-for="(item1,index1) of item.item" :key="index1" style="margin-bottom:6px;">
              <p style="font-weight:500;">{{index1+1 + '：' + item1.name}}</p>
              <p><span style="color:#FF3030;">{{item1.value == 1 ? '是' : '否'}}</span><span style="color:#999;margin-left:10px;">{{item1.remark}}</span></p>
            </li>
          </ul>
        </div>
      </div>
    </li>
  </ul>
</div>
<script>
    var checkLogs=<?php echo json_encode($checkLogs); ?>;
    console.log(checkLogs)
  new Vue ({
    el:'#appq',
    name: 'MyProgress',
    data() {
      return {
        showPopover: false,
        currentPage: 1,
        totalPage: 0,
        ulLeft: 0,
        popoverData: {
          type: 'bottom'
        },
        comData: {
          data:checkLogs
          // data: [
          // {
          //   title: "xx创建项目", 
          //   name: "7业务", 
          //   time: "2018-01-01 11:29", 
          //   done: true, 
          //   remarks: [
          //     { text: "1、7业务", done: true },
          //     { text: "2、付款要求操作", done: true }, 
          //     { text: "3、付款计划求操作", done: true }, 
          //     { text: "4、付款计划要求操作", done: false }, 
          //     { text: "5、付款计划要求操作", done: false }
          //   ]
          // }, 
          // {
          //   title: "xx创建项目",
          //   name: "8业务", time: "2018-01-01 11:29", done: true, remarks: [{ text: "1、8业务", done: false }, {
          //     text: "2、付款计划求操作", done:
          //       false
          //   }, { text: "3、付款计否按要求操作", done: false }, { text: "4、付款计划否按要求操作", done: false }]
          // }, 
          // {
          //   title: "xx创建项目", name: "9业务",
          //   time: "2018-01-01 11:29", done: false, remarks: [{ text: "1、9业务", done: true }, { text: "2、付款计按要求操作", done: true }, {
          //     text:
          //       "3、付款按要求操作", done: true
          //   }, { text: "4、付款计是否按要求操作", done: true }]
          // },
          // {
          //   title: "xx创建项目", name: "11业务", time: "2018-01-01 11:29",
          //   done: false, remarks: [{ text: "1、11业务", done: true }, { text: "2、付款计按要求操作", done: true }, {
          //     text: "3、付否按要求操作", done: true
          //   }, { text: "4、付划是否按要求操作", done: true }]
          // },
          // {
          //   title: "xx创建项目", name: "11业务", time: "2018-01-01 11:29",
          //   done: false, remarks: [{ text: "1、11业务", done: true }, { text: "2、付款计按要求操作", done: true }, {
          //     text: "3、付否按要求操作", done: true
          //   }, { text: "4、付划是否按要求操作", done: true }]
          // }, 
          // {
          //   title: "xx创建项目", name: "11业务", time: "2018-01-01 11:29",
          //   done: false, remarks: [{ text: "1、11业务", done: true }, { text: "2、付款计按要求操作", done: true }, {
          //     text: "3、付否按要求操作", done: true
          //   }, { text: "4、付划是否按要求操作", done: true }]
          // }, 
          // {
          //   title: "xx创建项目", name: "11业务", time: "2018-01-01 11:29",
          //   done: false, remarks: [{ text: "1、11业务", done: true }, { text: "2、付款计按要求操作", done: true }, {
          //     text: "3、付否按要求操作", done: true
          //   }, { text: "4、付划是否按要求操作", done: true }]
          // }]
        }
      };
    },
    watch: {
      currentPage: function (val) {
        this.ulLeft = -0.8 * (val - 1) * 100 + '%';
      }
    },
    created() {
      this.totalPage = Math.floor((this.comData.data.length - 1) / 5) + 1;
      this.liWidth = 100 / (this.totalPage * 5) + '%';
      this.ulWidth = this.totalPage * 100 + '%';
    },
    methods: {
      toUp() {
        if (this.currentPage > 1) {
          this.currentPage--;
        }
      },
      toDown() {
        console.log(this.currentPage,this.totalPage)
        if (this.currentPage < this.totalPage) {
          this.currentPage++;
        }
      }
    }
  });
</script>