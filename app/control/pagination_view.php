<div id="paginationWrapper" style="clear:both; text-align:center; margin-top:10px; margin-bottom:10px;">
	<!-- jump-to-first-page-link:start -->
	<?php if($firstPageNo!=$currentPageNo): ?>
	<a href="<?php echo $baseURL . "/number/" . $number . "/page/" . $firstPageNo; ?>">首页</a>
	<?php endif; ?>
	<!-- jump-to-first-page-link:end -->
	
	<!-- jump-to-pre-page-link:start -->
	<?php if(1!=$currentPageNo): ?>
	<a href="<?php echo $baseURL . "/number/" . $number . "/page/" . $prevPageNo; ?>">上页</a>
	<?php endif; ?>
	<!-- jump-to-pre-page-link:start -->
	
	<!-- pagination-link:start -->
	<?php if (2==3) : ?>
	<?php for ($i=1; $i<=$pageCount; $i++) : ?>
		<?php if ( $i==$currentPageNo ) : ?>
		<span><?php echo $i; ?></span>
		<?php else : ?>
		<a href="<?php echo $baseURL . "/number/" . $number . "/page/" . $i; ?>"><?php echo $i; ?></a>
		<?php endif; ?>
	<?php endfor; ?>
	<?php endif; ?>
	<?php if ($currentPageNo==1): ?>
	<span><?php echo $currentPageNo; ?></span>
	<span>....</span>	
	<?php elseif ($currentPageNo==$pageCount) : ?>
	<span>....</span>
	<span><?php echo $currentPageNo; ?></span>
	<?php else: ?>
	<span>....</span>
	<span><?php echo $currentPageNo; ?></span>
	<span>....</span>
	<?php endif; ?>
	<!-- pagination-link:end -->
	
	<!-- jump-to-next-page-link:start -->
	<?php if($currentPageNo!=$lastPageNo) : ?>
	<a href="<?php echo $baseURL . "/number/" . $number . "/page/" . $nextPageNo; ?>">下页</a>
	<?php endif; ?>
	<!-- jump-to-next-page-link:end -->
	
	<!-- jump-to-last-page-link:start -->
	<?php if ($currentPageNo!=$lastPageNo) : ?>
	<a href="<?php echo $baseURL . "/number/" . $number . "/page/" . $lastPageNo; ?>">尾页</a>
	<?php endif; ?>
	<!-- jump-to-last-page-link:end -->
</div>
<div style="clear:both; text-align:center; margin-top:10px; margin-bottom:10px;">
	<span>共<?php echo $lastPageNo; ?>页</span>
	<span>每页<?php echo $number; ?>项</span>
	<span>共<?php echo $recordCount; ?>项</span>
</div>