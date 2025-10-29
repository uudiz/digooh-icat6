<style type="text/css">
	.contain {
 		 object-fit: contain;
	}
</style>

<table border="0" cellspacing="5" cellpadding="5">
  <tr>
    <td valign="top" class="content">
    	<div>
    		<?php if($ccode == 0):?>

				<div style="display:block;width:425px;height:300px;">
					<video id='preview-video' autoplay controls class='contain' width=100% height=100%  >
						<source src="<?php echo $video;?>" type='video/mp4' >
					</video>
				</div>
			<?php else:?>
	    		<?php if($media->preview_status > 0):?>
	        		<img src="<?php echo $media->main_url;?>" width="640" height="320" />
				<?php else:?>
					<img src="/images/media/video.gif" width="120" height="80" />
				<?php endif;?>
			<?php endif;?>
        </div>
    </td>
	<td width="20px">&nbsp;&nbsp;&nbsp;</td>
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
<h1><?php echo $media->name;?></h1>
<p><?php echo lang('url');?>: <a href="/media/download?full_path=<?php echo $media->full_path;?>&name=<?php echo $media->orig_name;?>"><?php echo 'http://'.$_SERVER['HTTP_HOST'].'/resources/'.$media->company_id.'/'.$media->orig_name;?></a></p>
<p>
	<?php echo $media->descr;?> 
</p>