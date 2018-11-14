<link href="/newUI/css/index/index.css" rel="stylesheet" type="text/css"/>
<main id="site_body" style="margin:20px;">
    <div>
        <div class="backlog">
            <div class="div-title" id="app1">
                <span>待办事项</span>
                <div class="pull-right" style="width:200px" >
                    <select class="form-control"   data-bind="
                                selectpicker:selectedAction,
                                selectPickerOptions:actions,
                                optionsText:function(data){
                                    return data.action_name+' ('+data.n+')';
                                },
                                    optionsValue: 'action_id'

                                ">
                    </select>
                </div>
            </div>
            <!-- ko if:selectedTasks().length > 0 -->
                <ul class="ul-backlog-list">
                    <!-- ko foreach:{ data: displayTasks(), as: 'task' } -->
                    <li>
                        <span class='backlog-time' data-bind="text:task.create_time.substr(0, 10)"></span>
                        <p style="width:0;flex:1;">
                            <span class='backlog-content' data-bind="text:'您有一个新任务 : ' + task.action_name + ' ( ' + task.title +' ) '"></span>
                            <a class="backlog-operation task_link no-padding" href="javascript:void(0);" data-bind="attr: { href:'/site/task?id='+ task.task_id}">点击这里确认操作</a>
                        </p>
                    </li>
                    <!-- /ko -->
                </ul>
            <!-- /ko -->
            <!-- ko if:selectedTasks().length == 0 -->
            <div style="display:flex;justify-content:center;align-items:center;flex-direction:column;margin-top:80px;">
                <img style="width:150px;height:150px;margin-bottom:20px;" src="/newUI/img/empty.png">
                <p>暂时没有数据哦～</p>
            </div>
            <!-- /ko -->
            <div class="el-pagination is-background" align="center" id="pagination">
            </div>
        </div>
        <div class="fast-entry" style="position:relative;flex-shrink:0;">
            <p class="p-title">快捷入口</p>
            <!-- ko foreach:{ data: shortCuts(), as: 'shortCut' } -->
                <div data-bind="click:$parent.doQuickLink">
                    <p>
                        <i class="icon" data-bind="css:shortCut.ico"></i>
                        <span class="short_cut_name" data-bind="text:shortCut.name"></span>
                        <!-- ko if:shortCut.subMenu -->
                            <!-- ko if:shortCut.subMenu.length > 0 -->
                            <i class="icon icon-xiala" style="font-size: 16px!important;margin-left: 6px;"></i>
                            <!-- /ko -->
                        <!-- /ko -->
                    </p>
                   
                </div>
                <ul class="dropdown-quick-link popover-has-arrow" style="display:none;" >
                    <!-- ko foreach:shortCut.subMenu -->
                    <li>
                        <a data-bind="attr:{href:url}, text:name"></a>
                    </li>
                    <!-- /ko -->
                </ul>
            <!-- /ko -->

        </div>
    </div>
</main>
<script>
    var view;
    $(function () {
        view=new ViewModel(<?php echo json_encode(array(
                'actions'=>$actions,
                'tasks'=>$tasks,
                'shortCuts'=>$shortCuts
            )
        )?>);
        ko.applyBindings(view);
        // 快捷入口处点击空白处收起二级入口
        $('body').click(function(e){
            if(!$(e.target).parents('.fast-entry').length){
                $('.dropdown-quick-link').hide()
            }
        })
        $('.fast-entry>div').click(function(){
            $(this).next().toggle()
            $(this).prevAll('.dropdown-quick-link').hide()
            $(this).next().nextAll('.dropdown-quick-link').hide()
        })
        $('.fast-entry>ul').mouseenter(function(){
            $(this).prev().children('p').css('color','#FF6E34');
            $(this).prev().children('p').children('i').css('color','#FF6E34');
        }).mouseleave(function(){
            $(this).prev().children('p').css('color','#666');
            $(this).prev().children('p').children('i').css('color','#666');
        })
        view.initPagination();
    });

    function ViewModel(option){
        var defaults={
            actions:[],
            tasks:[],
            shortCuts:[],
            openAllActions:false,
            selectedAction:0
        };

        var o = $.extend(defaults, option);
        var self = this;
        self.actions = ko.observableArray(o.actions);
        self.tasks = ko.observable(o.tasks);
        self.selectedTasks = ko.observable(o.tasks);

        self.openAllActions = ko.observable(o.openAllActions);
        /*if(o.actions!=null && o.actions.length>0)
            o.selectedAction=o.actions[0]["action_id"];*/
        self.selectedAction = ko.observable(o.selectedAction);
        self.actionLength = ko.observable(o.selectedAction.length);
        self.openStatus = ko.observable(false);
        self.openStatusStr = ko.computed(function() {
            return self.openStatus()?'<i class="fa fa-angle-double-up"></i>&nbsp;收起':'<i class="fa fa-angle-double-down"></i>&nbsp;展开';
        }, self);
        self.openAllActions = function() {
            self.openStatus(!self.openStatus());
        };

        self.page=ko.observable(1);
        self.pageSize=9;

        var n=0;
        ko.utils.arrayForEach(self.actions(),function (item) {
            n+=parseInt(item.n);
        });

        self.actions.splice(0, 0, {"action_id":"0","action_name":"全部待办","n":n});
        self.allDisplayTasks=ko.computed(function () {
            var items=[];
            if(self.selectedAction()==0)
                items= self.tasks();
            else
            {
                items=ko.utils.arrayFilter(self.tasks(),function (item) {
                    return item.action_id==self.selectedAction();
                });
            }
            return items;
        },self);

        self.displayTasks=ko.computed(function () {
            var items=self.allDisplayTasks();

            if(self.page()>0)
            {
                var start=(self.page()-1)*self.pageSize;
                items=items.slice(start,start+self.pageSize);
            }
            return items;
        },self);

        self.pages=ko.computed(function(){
            var n=self.allDisplayTasks().length;
            return Math.ceil(n/self.pageSize);
        });
        self.page.subscribe(function (v) {
            if(v>self.pages())
                self.page(self.pages());
        });

        self.initPagination=function(){
            if(self.pages()>0)
            {
                page.initPagination("#pagination",{
                    totalPages: self.pages(),
                    currentPage: self.page(),
                    onPageChange: function(n) {
                        self.page(n);
                    }
                });
            }
        };
        self.pages.subscribe(function (v) {
            // if(self.page()>v)
            //     self.page(v);
            self.page(1);
            $("#pagination").jqPaginator('option', {
                totalPages: v,
                currentPage:self.page()
            });
        });



        self.timeLine = ko.computed(function() {
            var timeLine = [];
            var time = {
                name : '全部',
                height: 44,
                href:'#allTasks',
                showIcon:1
            }
            timeLine.push(time);
            var today = new Date();
            var todayCount = 0;
            var weekCount = 0;
            var fornightCount = 0;
            var extraCount = 0;
            for(var ind = 0; ind < self.selectedTasks().length; ind ++) {
                var thisTask = self.selectedTasks()[ind];
                var thisDate = new Date(thisTask.create_time),
                timeDiff = (today - thisDate) / (24 * 3600 * 1000);
                // 今天
                if(timeDiff < 1) {
                    todayCount ++;
                }
                // 一周
                if(timeDiff >= 1 && timeDiff < 7) {
                    weekCount ++;
                }
                // 两周
                if(timeDiff >= 7 && timeDiff < 14) {
                    fornightCount ++;
                }
                // 多于两周
                if(timeDiff >= 14) {
                    extraCount ++;
                }
            }
            if(todayCount > 0) {
                timeLine.push({
                    name : '今天',
                    height: 44 * todayCount,
                    href:'#todayTasks',
                    showIcon:1
                });
            }
            if(weekCount > 0) {
                timeLine.push({
                    name : '近一周',
                    height: 44 * weekCount,
                    href:'#weekTasks',
                    showIcon:1
                });
            }
            if(fornightCount > 0) {
                timeLine.push({
                    name : '近两周',
                    height: 44 * fornightCount,
                    href:'#weekTasks',
                    showIcon:1
                });
            }
            if(extraCount > 0) {
                timeLine.push({
                    name : '',
                    height: 44 * extraCount,
                    href:'#extraTasks',
                    showIcon:0
                });
            }
            return timeLine;
        }, self);


        self.shortCuts = ko.observableArray(o.shortCuts);
        ko.utils.arrayForEach(self.shortCuts(),function (item) {
           item.subMenuVisible=ko.observable(false);
        });

        self.doQuickLink=function(data)
        {
            if(!data.subMenu)
                location.href=data.url;
            else
            {
                data.subMenuVisible(!data.subMenuVisible());
            }
        }
    }


</script>
