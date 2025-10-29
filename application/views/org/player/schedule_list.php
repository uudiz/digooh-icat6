<div class="table-responsive">
<table class="table table-striped">
   <thead>
    <tr>
        <th >
        	<?php echo lang('playlist');?>

		</th>

        <th >
        	<?php echo lang('desc');?>
		</th>

        <th>
        <?php echo lang('template');?>
		</th>
		<th >
			<?php echo lang('update.time');?>
		</th>

		<th>
			<?php echo lang('date.range');?>
		</th>


    </tr>

	

	<?php if(!isset($data)): ?>

	<tr>

		<td colspan="7">

			<?php echo lang("empty");?>

		</td>

	</tr>
	</thead>
	<tbody>
	<?php else:

	  $index = 0;

	?>

		<?php foreach($data as $row):?>
			
		<tr >
			
		  	<td>
		  	 	<a  href="javascript:void(0);" onclick="player.goScreen(<?php echo $row->id;?>);" title="<?php echo lang('edit.playlist');?>"><?php echo $row->name?></a>
		  	</td>

		 	<td>
		  		<?php if(mb_strlen($row->descr) > 64){echo mb_substr($row->descr, 0, 64).'...';}else{echo $row->descr;}?>

		  	</td>

		 


		  <td> 

    		<img width="48" alt="" src="<?php echo $row->template_preview ?>" class="img-thumbnail">
		
		  </td>
		  
		  <td><?php echo $row->update_time; ?></td>

		<td>
			  	<?php
			  		if(date('Y-m-d') > $row->end_date)
			  			echo '<font style="color: red;">'.$row->start_date." ~ ".$row->end_date."</font>"; 
			  		else 
			  			echo $row->start_date." ~ ".$row->end_date;
			  	?>
			  
			  
		 <td>
		 

		</tr>

		<?php

		 	$index++; 

			endforeach; 

		?>
	 
	<?php endif;?>
	 </tbody>
</table>
</div>

