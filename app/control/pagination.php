<?php
class Control_Pagination extends QUI_Control_Abstract {
	function render() {
		$baseURL       = $this->get("base");
		$pagination    = $this->get("pagination");

		$recordCount   = $pagination['record_count']; // 总记录数
		$baseNo        = $pagination['page_base'];    // 第一页页码
		$firstPageNo   = $pagination['first'];        // 第一页页码
		$lastPageNo    = $pagination['last'];         // 最后一页页码
		$nextPageNo    = $pagination['next'];         // 下一页页码
		$prevPageNo    = $pagination['prev'];         // 上一页页码
		$currentPageNo = $pagination['current'];      // 当前页页码
		$number        = $pagination['page_size'];    // 一页显示多少条记录
		$pageCount     = ceil($recordCount/$number);  // 一共有多少页

		if ($pageCount<=1) {
			return "";
		} else {
			$this->_view['baseURL']       = $baseURL;
			$this->_view['recordCount']   = $recordCount;
			$this->_view['baseNo']        = $baseNo;
			$this->_view['firstPageNo']   = $firstPageNo;
			$this->_view['lastPageNo']    = $lastPageNo;
			$this->_view['nextPageNo']    = $nextPageNo;
			$this->_view['prevPageNo']    = $prevPageNo;
			$this->_view['currentPageNo'] = $currentPageNo;
			$this->_view['number']        = $number;
			$this->_view['pageCount']     = $pageCount;

			return $this->_fetchView(dirname(__FILE__) . '/pagination_view.php');
		}
	}
}
?>