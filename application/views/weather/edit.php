
<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>

<form class="form-horizontal" >
	<div class="form-group">
         <label for="name" class="col-sm-3 control-label"><?php echo lang("template.name"); ?></label>
         <div class="col-sm-8">
                  <input type="text" class="form-control" id="name" value="<?php echo $template->name;?>" >
         </div>		
	</div>
			
		<div class="form-group">
         <label for="descr" class="col-sm-3 control-label"><?php echo lang("desc"); ?></label>
         <div class="col-sm-8">
              <textarea name="descr" id="descr" class="form-control" rows="2" ><?php echo $template->descr;?></textarea>
         </div>		
	</div>
	
</form>

 <div class="modal-footer">  
 	<div class='pull-right'>
		<input type="hidden" name="id" id="id" value="<?php echo $template->id;?>"/>
		<a class="btn-01" href="javascript:void(0);" onclick="tb_remove();"><span><?php echo lang('button.cancel');?></span></a>
		<a class="btn-01" href="javascript:void(0);" onclick="weather.index.doSave();"><span><?php echo lang('button.save');?></span></a>
	  
    </div>    
</div>
	