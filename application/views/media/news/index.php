
<div class="clear"></div>
<h1 class="tit-01"><?php echo lang('news');?><span></span></h1>
<table class="table-list" width="100%" >
        <tr>
        	<th width="120" >
				<a href="javascript:void(0);"  ><?php echo lang('title');?></a>
			</th>
            <th width="120">
				<a href="javascript:void(0);"  ><?php echo lang('desc');?></a>
			</th>
			<th width="120">
				<a href="javascript:void(0);"  ><?php echo lang('name');?></a>
			</th>
            <th width="120">
				<a href="javascript:void(0);"  ><?php echo lang('pubdate');?></a>
			</th>
		
        </tr>
		
		<?php if(!$data): ?>
		<tr>
			<td colspan="4">
				<?php echo lang("empty");?>
			</td>
		</tr>
		<?php else:
		  $index = 0;
		?>
			<?php foreach($data as $row):?>
			<tr <?php if($index%2 != 0):?>class="even" onmouseout="this.className='even'" <?php else:?>onmouseout="this.className=''"<?php endif;?>  onmouseover="this.className='onSelected'">
			  <td>
			  	<?php {echo $row->title;}?>
			  </td>
			  <td >
			  	<?php {echo $row->description;}?>
			  </td>
			  <td>
			  	<?php 
					echo $row->url;
				?>
			  </td>
			  <td><?php echo $row->pub_date; ?></td>

			</tr>
			<?php
			 	$index++; 
				endforeach; 
			?>
		<?php endif;?>

</table>


