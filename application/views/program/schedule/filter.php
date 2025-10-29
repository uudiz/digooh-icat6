<html xmlns="http://www.w3.org/1999/xhtml"><head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link type="text/css" rel="stylesheet" href="/static/english/listpage.css">
	<link type="text/css" rel="stylesheet" href="/static/css/tooltip/tooltip.css">
	<link type="text/css" href="/static/css/fullcalendar/fullcalendar.css" rel="stylesheet">
	<link type="text/css" href="/static/css/schedule.css" rel="stylesheet">
	<script type="text/javascript" src="/static/js/jquery/jquery-1.5.2.min.js"></script>
	<!--
	<script type="text/javascript" src="/static/js/jquery/jquery.tools.min.js"></script>
	-->
	<script type="text/javascript" src="/static/js/jquery/includeMany12m.js"></script>
	<script type="text/javascript" src="/static/js/jquery/simpletip131p.js"></script>
	<script type="text/javascript" src="/static/js/uid/core.js"></script>
	<!--
	<script type="text/javascript" src="/static/js/listpage.js"></script>
	-->
	<script type="text/javascript" src="/static/js/jquery/thickbox31.js"></script>
	
	<script type="text/javascript" src="/static/js/common.js" charset="UTF-8"></script>
	<script language="JavaScript" type="text/javascript" src="/static/js/fullcalendar/fullcalendar.js?t=16276" charset="UTF-8"></script><script language="JavaScript" type="text/javascript" src="/static/js/schedule.js?t=16276" charset="UTF-8"></script>	
	<script type="text/javascript">
		function bindHover(){
			$('.table-list a, .operate a').hover(function(){
				var cur = $(this);
				var child = cur.children('img');
				var src = child.attr('src');
				var id = child.attr('id');
				if(src != null){
					var pos = src.lastIndexOf('.');
					if (pos != -1) {
						MM_swapImage(id, '', src.substr(0, pos)+'-hover'+src.substr(pos), 1)
					}
				}
			},function(){
				MM_swapImgRestore();
			});
		}
		requestFail='Request Fail, Please try again!';
		$(document).ready(function(){
			MM_preloadImages('/images/icons/24-edit-hover.png','/images/icons/24-del-hover.png','/images/icons/24-add-hover.png','/images/icons/24-date-hover.png','/images/icons/24-weather-hover.png','/images/icons/24-image-hover.png','/images/icons/24-export-hover.png','/images/icons/24-bg-hover.png','/images/icons/24-video-hover.png','/images/icons/24-down-hover.png','/images/icons/24-up-hover.png');
			bindHover();
		});
	</script>
	<title>iCAT5 System	</title>
</head>
<body>
<div id="loadingLayer" style="display:none;">
	<div class="TB_overlayBG"></div>
	<div class="loading-01" id="loading" style="top: 40%; left:30%; z-index:999;">Loading ......</div>
</div>
<div id="msgLayer" style="display:none; margin-top: 8px;">
	<div>
      <div id="msgContent"></div>
    </div>	
</div>
<div class="wrap">
<div class="schedule" style="padding-top:0px;">
	<div class="title">
		<span class="fc-button fc-button-all fc-state-default fc-state-active">
			<span class="fc-button-inner">
				<span class="fc-button-content"><?php echo lang('all');?></span>
				<span class="fc-button-effect"><span></span></span>
			</span>
		</span>
		<span class="fc-button fc-button-day fc-state-default">
			<span class="fc-button-inner">
				<span class="fc-button-content"><?php echo lang('day');?></span>
				<span class="fc-button-effect"><span></span></span>
			</span>
		</span>
		<span class="fc-button fc-button-week fc-state-default">
			<span class="fc-button-inner">
				<span class="fc-button-content"><?php echo lang('week');?></span>
				<span class="fc-button-effect"><span></span></span>
			</span>
		</span>
		<span class="fc-button fc-button-month fc-state-default">
			<span class="fc-button-inner">
				<span class="fc-button-content"><?php echo lang('month');?></span>
				<span class="fc-button-effect"><span></span></span>
			</span>
		</span>
	</div>
	<div id="list">
		<?php if($auth > 0):?>
		<div class="add-panel">
			<a href="/schedule/add?width=400&height=300" id="create" class="thickbox" title="<?php echo lang('schedule.new');?>"><?php echo lang('create');?></a>
		</div>
		<?php endif;?>
		<div class="clear"></div>
		<h1 class="tit-01"><?php echo lang('scheduler');?><span></span>
		<div class="filter" style="width:84%;">
		<?php echo lang('filter.by');?>:
		<select class="input-large" id="filter_type" name="filter_type">
			<option value="group"><?php echo lang('filter.group');?></option>
			<option value="playlist"><?php echo lang('filter.playlist');?></option>
		</select>
		<input type="text" name="filter_name" id="filter_name" style="width:120px; margin-left:4px;" value="<?php echo $value; ?>">
		<a href="javascript:void(0);" style="margin-left:20px; " class="btn-go" onclick="schedule.index.filter();"><label><?php echo lang('filter');?></label></a>
		</div>
		</h1>
		<div id="layoutContent">
		<table class="table-list"  width="100%" >
			<thead>
				<tr>
					<th>
						<a href="javascript:void(0);" onclick="schedule.index.filter(<?php echo $curpage;?>,'name','<?php if($order_item == 'name' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('name');?></a>
						<img alt="" src="/images/icons/<?php if($order_item == 'name' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'name' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
					</th>
					<th width="100">
						<a href="javascript:void(0);" onclick="schedule.index.filter(<?php echo $curpage;?>,'descr','<?php if($order_item == 'descr' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('desc');?></a>
						<img alt="" src="/images/icons/<?php if($order_item == 'descr' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'descr' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
					</th>
					<th width="40"><?php echo lang('player.type');?></th>
					<th>
						<?php echo lang('group');?>
					</th>
					<th>
						<?php echo lang('playlist');?>
					</th>
					<th>
						<a href="javascript:void(0);" onclick="schedule.index.filter(<?php echo $curpage;?>,'status','<?php if($order_item == 'status' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('status');?></a>
						<img alt="" src="/images/icons/<?php if($order_item == 'status' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'status' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
					</th>
					<th>
						<a href="javascript:void(0);" onclick="schedule.index.filter(<?php echo $curpage;?>,'start_date','<?php if($order_item == 'start_date' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('schedule.range');?></a>
						<img alt="" src="/images/icons/<?php if($order_item == 'start_date' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'start_date' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
					</th>
					<th>
						<a href="javascript:void(0);" onclick="schedule.index.filter(<?php echo $curpage;?>,'publish_time','<?php if($order_item == 'publish_time' && $order == 'desc'):?>asc<?php else:?>desc<?php endif;?>');" ><?php echo lang('publish.time');?></a>
						<img alt="" src="/images/icons/<?php if($order_item == 'publish_time' && $order == 'desc'):?>dir-down.gif<?php elseif($order_item == 'publish_time' && $order == 'asc'):?>dir-up.gif<?php else:?>dir-blank.gif<?php endif;?>" />
					</th>
					<th width="80">
						<?php echo lang('operate');?>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php if($total == 0): ?>
			<tr>
				<td colspan="8">
					<?php echo lang("empty");?>
				</td>
			</tr>
			<?php else:
			  $index = 0;
			?>
				
				<?php foreach($data as $row):?>
				<tr <?php if($index%2 != 0):?>class="even" onmouseout="this.className='even'" <?php else:?>onmouseout="this.className=''"<?php endif;?>  onmouseover="this.className='onSelected'">
					<td>
						<a href="/schedule/view?id=<?php echo $row->id;?>&width=720&height=600"  class="thickbox" title="<?php echo lang('schedule.properties');?>">
							<?php if(mb_strlen($row->name) > 120){echo mb_substr($row->name, 0, 120).'..';}else{echo $row->name;}?>
						</a>
					</td>
					<td width="100">
						<?php if(mb_strlen($row->descr) > 60){echo mb_substr($row->descr, 0, 60).'...';}else{echo $row->descr;}?>
					</td>
					<td>
					  	<?php 
						if($row->sch_type):
						?>
						<img src="/images/icons/android.png"  title="<?php echo lang('type.1');?>" />
						<?php else:?>
						<img src="/images/icons/windows.png"  title="<?php echo lang('type.0');?>" />
						<?php endif;?>
					  </td>
					<td>
						<?php if($row->groups):?>
							<?php
							$i = 0; 
							foreach($row->groups as $g):?>
							<label>
								<?php if(mb_strlen($g->name) > 24){echo mb_substr($g->name, 0, 24).'..';}else{echo $g->name;}?>
							</label>
							<?php if($i > 0 && $i % 4 == 0):?>
							<br />
							<?php endif;?>
							<?php
							$i++; 
							endforeach;?>
						<?php endif;?>
					</td>
					<td>
						<?php if($row->playlists):?>
						<?php 
							$i = 1;
							foreach($row->playlists as $p):?>
							<label>
								<?php if($auth > $VIEW):?>
								<a href="/campaign//screen?id=<?php echo $p->playlist_id;?>" class="sch_pl"><?php echo $p->name;?></a>
								<?php else:?>
								<?php echo $p->name;?>
								<?php endif;?>
							</label>
							<?php if($i > 1 && $i % 2 == 0):?>
							<br />
							<?php endif;?>
							<?php 
							$i++;
							endforeach;?>
						<?php endif;?>
					</td>
					<td>
						<?php if($row->status == 0):?>
							<img src="/images/icons/led_off.png" alt="<?php echo lang('activity.status.saved');?>" title="<?php echo lang('activity.status.saved');?>"/>
						<?php elseif($row->status == 1):?>							<?php if(date('Y-m-d') > $row->end_date):?>							<img src="/images/icons/led_on.png" alt="<?php echo lang('activity.status.published');?>" title="<?php echo lang('activity.status.published');?>"/>&nbsp;<font style="color: red;">Expired</font>							<?php else:?>							<img src="/images/icons/led_on.png" alt="<?php echo lang('activity.status.published');?>" title="<?php echo lang('activity.status.published');?>"/>							<?php endif;?>
						<?php endif;?>
					</td>
					<td>
						<?php if(!empty($row->start_date)){echo $row->start_date.'~'.$row->end_date;}?>
					</td>
					<td>
						<?php if($row->status == 1):?>
							<?php echo $row->publish_time;?>
						<?php endif;?>
					</td>
					<td>
						<?php if($auth > 0):?>
					  	<a href="/schedule/edit?id=<?php echo $row->id;?>&type=<?php echo $type;?>&name=<?php echo $value;?>" title="<?php echo lang('edit.schedule');?>"><img id="edit_<?php echo $row->id;?>" src="/images/icons/24-edit.png" /></a>
					  	<a href="javascript:void(0);" onclick="schedule.index.remove(<?php echo $row->id;?>,'<?php echo lang('tip.remove.item');?>');" title="<?php echo lang('delete');?>"><img id="del_<?php echo $row->id;?>" src="/images/icons/24-del.png" /></a>
						<?php endif;?>
					</td>
				</tr>
				<?php $index++;endforeach;?>
				<?php endif;?>
			</tbody>
			<tbody>
				<tr>
					<td colspan="8">
						<?php
							$totalPage = intval(($total + ($limit - 1)) / $limit);
							$startIndex = 1;
							$endIndex   = $totalPage;
							$midIndex   = intval(($curpage + 5) / 2); 
							if($midIndex - 2 > $startIndex){
								$startIndex = $midIndex - 2;
							}
							if($midIndex + 2 < $endIndex){
								$endIndex = $midIndex + 2;
							}
						?>
						<div class="pagination pagination-right">
							<ul>
								<?php if($totalPage > 1):?>
									<?php if($curpage>1):?>
										<li><a href="/schedule/index/1/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.first');?></a></li>
										<li><a href="/schedule/index/<?php echo $curpage-1;?>/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.prev');?></a></li>	
									<?php endif;?>
									<?php for($i = $startIndex; $i <= $endIndex; $i++):?>
									<li <?php if($i == $curpage):?>class="active"<?php endif;?>>
										<?php if($i == $curpage):?>
											<?php echo $i;?>
										<?php else:?>
											<a href="/schedule/index/<?php echo $i;?>/<?php echo $order_item.'/'.$order;?>"><?php echo $i;?></a>
										<?php endif;?>
									</li>
									<?php endfor;?>
									<?php if($curpage<$totalPage):?>
										<li><a href="/schedule/index/<?php echo $curpage+1;?>/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.next');?></a></li>
										<li><a href="/schedule/index/<?php echo $totalPage;?>/<?php echo $order_item.'/'.$order;?>"><?php echo lang('page.last');?></a></li>	
									<?php endif;?>
								<?php endif;?>
								
							</ul>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
		<script type="text/javascript">
		$(function() {
			$(".sch_pl").click(function() {
				parent.$('#sch').removeClass("on");
				parent.$('#play').addClass("on");
			});
		});
		</script>
		</div>
	</div>
	<div id="calendar" style="display:none;">
		
	</div>
</div>


<script type="text/javascript">
	$('.btn-go').click(function() {
		var type = $('#filter_type').val();
		var name = $('#filter_name').val();
		$('#calendar').html('');
		setTimeout(function() {
			schedule.index.init(type, name);
			schedule.index.initCalendar(type, name);	
		},2000);
	});
</script>
<div class="globalMask" style="display:none;">
</div>
</div>
</body>
</html>