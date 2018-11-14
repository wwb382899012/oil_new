<?php $maxPageSize = 30; ?>
<div class="main-content_footer flex-grid align-center pagination-wrapper">
    <?php if ($data["total"] > - 1) { ?>
        <div>共<span><?php echo $data['total'] ?></span>条</div>
        <div>&emsp;  <span><?php echo $data['pageCount'] ?></span>页</div>
        <div class="margin-lr-15 vertical-align">
            <span>每页</span>
            <select style="width: 80px;" class="selectpicker show-menu-arrow w-spec" id="pageSizeSelect" onchange="changePageSize(this.value)">
                <?php
                $pageSize = $this->getSearchPageSize();
                $pageSize = !empty($pageSize) ? $pageSize : '10';
                for ($i = 10; $i <= $maxPageSize; $i = $i + 10)
                {
                    $select = ('' . $i) === $pageSize ? 'selected' : '';
                    echo "<option value='" . $i . "' $select>$i</option>";
                }
                ?>
            </select>
            <span>条</span>
        </div>
    <?php } ?>
    <div class="el-pagination is-background pagination-theme-list" align="left" id="pagination">
        <?php
        //开始分页逻辑
//        $page = $this->getSearchPage();
        $curPage = !empty($data['page']) ? $data['page'] : 1;
//        $curPage = array_key_exists('page',$_GET) && !empty($_GET['page']) ?$_GET[page]:1;
        $totalPage = empty($data["pageCount"]) ? 0 : $data["pageCount"];
        if (substr_count($_SERVER["REQUEST_URI"], '&page=') > 1)
        {
            Mod::Log(sprintf("[E] [%s] %s | [E] %s\n", date("m/d H:i:s"), 'BaseController->load_page', $_SERVER["REQUEST_URI"]));
            exit();
        }
        if (substr_count($_SERVER["REQUEST_URI"], '&pageSize=') > 1)
        {
            Mod::Log(sprintf("[E] [%s] %s | [E] %s\n", date("m/d H:i:s"), 'BaseController->load_page', $_SERVER["REQUEST_URI"]));
            exit();
        }
        $pageUrl = $_SERVER['REQUEST_URI'];
        $pageUrl = substr($pageUrl, 0, strpos($pageUrl, "?"));
        $queryString = $_SERVER['QUERY_STRING'];
        if (!empty($queryString))
        {
            $patterns = "/&*page=(\d)+/";
            $queryString = preg_replace($patterns, "", $queryString);
            if (!empty($queryString) && substr($queryString, -1) != '&')
            {
                $queryString .= "&";
            }
        }
        if (!empty($queryString))
        {
            $patterns = "/&*pageSize=(\d)+&/";
            $queryString = preg_replace($patterns, "", $queryString);
            if (!empty($queryString) && substr($queryString, -1) != '&')
            {
                $queryString .= "&";
            }
        }
        $pageUrl = $pageUrl . "?" . $queryString;
        ?>
    </div>
    <div class="route-to margin-lr-15 vertical-align">
        前往第
        <input id='gotoPageInput' type="text" size="1" style="width: 60px; text-align: center" value="<?php echo $curPage > $data['pageCount'] ? $data['pageCount'] : $curPage ?>" onchange="gotoPage(this.value)">
        页
    </div>
</div>
<script>
    page.autoWidth()

	page.initPagination('', {
		totalPages: <?php echo $totalPage ?>,
		currentPage: <?php echo $curPage > $data['pageCount'] ? $data['pageCount'] : $curPage ?>,
		onPageChange: function (n, type) {
			if(type == 'change') {
				location.href = "<?php echo $pageUrl ?>page=" + n;
			}
		}
	});

	//调整每页条数
	function changePageSize(val) {
		location.href = "<?php echo $pageUrl ?>pageSize=" + val;
	}

	//跳转到某一页
	function gotoPage(val) {
		if(val > <?php echo $data['pageCount'] ?>) {
			val = <?php echo $data['pageCount'] ?>;
        }
		location.href = "<?php echo $pageUrl ?>page=" + val;
    }
</script>