<table border="0" cellspacing="5" cellpadding="5">
	<tr>
		<td valign="top" class="content">
			<div>
			<?php if($media->preview_status > 0 || $media->source == 2):?>
			<?php if($media->width/$media->height >= 640/410) {
				$width = 680;
				$height = 680*$media->height/$media->width;	        
				}else {	        		
					$width = 410*$media->width/$media->height;	        		
					$height = 410;	        	
				}    			
			?>        		
			<img src="<?php if($media->main_url) echo $media->main_url; else echo $media->tiny_url;?>" width="<?php echo $width;?>" height="<?php echo $height;?>" />			
			<?php else:?>				
			<img src="/images/media/video.gif" width="120"/>			
			<?php endif;?>        
			</div>    
		</td>	
		<td>&nbsp;&nbsp;</td>	
		<td valign="top" >		
			<table border="0" cellspacing="0" cellpadding="0">			
				<tr>				
					<td><b><?php echo lang('author');?></b></td>				
					<td>
						<?php
							if($media->author == NULL || $media->author == '') {
								echo 'N/A';
							}else {
								echo $media->author;
							}
						?>
					</td>			
				</tr>			
				<tr>				
					<td><b><?php echo lang('upload.date');?></b></td>				
					<td><?php echo $media->add_time;?></td>			
				</tr>			
				<tr>				
					<td><b><?php echo lang('file.size');?></b></td>				
					<td><?php echo $media->file_size;?></td>			
				</tr>			
				<tr>				
					<td><b><?php echo lang('file.ext');?></b></td>				
					<td><?php echo $media->ext;?></td>			
				</tr>			
				<tr>				
					<td><b><?php echo lang('source');?></b></td>				
					<td>					
					<?php
						switch($media->source){							
							case 0:								
								echo lang('local');								
								break;							
							case 1:								
								echo lang('ftp');								
								break;							
							case 2:								
								echo lang('http');								
								break;						
						} 					
					?>				
					</td>			
				</tr>			
				<tr>				
					<td><b><?php echo lang('dimension');?></b></td>				
					<td>					
						<?php if($media->width > 0){echo $media->width.'X'.$media->height;}?>				
					</td>			
				</tr>			
				<tr>				
					<td><b><?php echo lang('folder');?></b></td>				
					<td><?php if($media->folder_id > 0){echo $media->folder->name;}else{echo lang('folder.default');}?></td>			
				</tr>		
			</table>	
		</td>  
	</tr>
</table>
&nbsp;&nbsp;
<h1><?php echo $media->name;?></h1>

<p>
<?php echo lang('url');?>: 
<?php
	if($media->source == 2):
?>
	<a href="<?php echo $media->full_path;?>" target="_blank">
		<?php echo $media->full_path;?>
	</a>
<?php else:?>
	<a href="/media/download?full_path=<?php echo $media->full_path;?>&name=<?php echo $media->orig_name;?>"><?php echo 'http://'.$_SERVER['HTTP_HOST'].'/resources/'.$media->company_id.'/'.$media->orig_name;?></a>
<?php endif;?>
</p>
<p>	<?php echo $media->descr;?> </p>