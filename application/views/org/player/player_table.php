<?php
$sort_formater='<a href="#">%s</a>';
?>
<?php if($total == 0){
	$sort_formater='%s';
}?>
<?php if($gid==0):?>
	<select name="gid" id="gid">
		<?php foreach($groups as $row):?>
		<option value="<?php echo $row->id;?>"><?php echo $row->name;?></option>
		<?php endforeach;?>
	</select>
<?php endif;?>
<button id="create-<?php echo $gid;?>"><?php echo lang('create');?></button>

<table id="sorttable-<?php echo $gid;?>" class="yui" >
	<thead>
		<th><?php echo sprintf($sort_formater,lang('id'));?></th>
		<th><?php echo sprintf($sort_formater,lang('player'));?></th>
		<th><?php echo sprintf($sort_formater,lang('group'));?></th>
		<th><?php echo sprintf($sort_formater,lang('desc'));?></th>
		<th><?php echo sprintf($sort_formater,lang('status'));?></th>
		<th><?php echo sprintf($sort_formater,lang('update.time'));?></th>
	</thead>
	<tbody>
		<?php foreach( $data as $row):?>
		<tr>
		  <td><?php echo $row->id ?></td>
		  <td><?php echo $row->name ?></td>
		  <td><?php echo $row->group_name ?></td>
		  <td><?php echo $row->descr ?></td>
		  <td><?php echo $row->status ?></td>
		  <td><?php echo $row->add_time ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
	<tfoot>
		<tr id="pagerOne">
	        <td style="border-right: 3px solid rgb(127, 127, 127);" colspan="6">
		        <img class="first" src="/static/css/sorttable/first.png">
		        <img class="prev" src="/static/css/sorttable/prev.png">
		        <input type="text" class="pagedisplay">
		        <img class="next" src="/static/css/sorttable/next.png">
		        <img class="last" src="/static/css/sorttable/last.png">
		        <select class="pagesize">
			        <option value="10" selected="selected">10</option>
			        <option value="20">20</option>
			        <option value="30">30</option>
			        <option value="40">40</option>
		        </select>
		    </td>
	    </tr>
	</tfoot>
</table>
<script type="text/javascript" charset="UTF-8">
	player.list.gid = <?php echo $gid;?>;
	player.list.totalLine = <?php echo $total?>; 
	player.list.init();
</script>
