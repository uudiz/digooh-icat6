<?php if(isset($data) && !empty($data)):?>
<div class="template-panel">
	<ul>
		<?php foreach($data as $row):?>
		<li>
			<table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td valign="top" class="content">
                	<div class="pic">
                		<?php if(!empty($row->preview_url)):?>
							
                    			<img id="<?php echo $row->id;?>" name="<?php echo $row->name;?>" src="<?php echo $row->preview_url;?>?t=<?php if(empty($row->update_time)){echo 0;}else{echo $row->update_time;}?>"/>
						<?php else:?>
								<img id="<?php echo $row->id;?>" name="<?php echo $row->name;?>" src="/images/media/video.gif" width="385" height="256" />
						<?php endif;?>
                    </div>
                </td>
              </tr>
            </table>
			<h1>
				<?php 
					if($row->template_type):
				?>
					<img src="/images/icons/android.png"  title="<?php echo lang('type.1');?>" />
				<?php else:?>
					<img src="/images/icons/windows.png"  title="<?php echo lang('type.0');?>" />
				<?php endif;?>
				&nbsp;&nbsp;
				<?php echo $row->name;?>  (<?php echo $row->width.'X'.$row->height;?>)
			</h1>
			<p>
				<?php echo $row->descr;?>
 			</p>
		</li>
		<?php endforeach;?>
	</ul>
</div>
<?php else:?>
<div style="color:#c0c0c0; margin-top:10px;">
	<b><?php echo lang('empty.template');?></b>
</div>
<?php endif;?>