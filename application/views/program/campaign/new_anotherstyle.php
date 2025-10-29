<div id="validateTips">
    <div>
        <div id="formMsgContent">
        </div>
    </div>
</div>

<link rel="stylesheet" href="/static/css/shaixuan.css" />

<script type="text/javascript">
               		$(document).ready(function(e) {
                        $("#selectList").find(".more").toggle(function(){
							$(this).addClass("more_bg");
							$(".more-none").show()
                    },function(){
						$(this).removeClass("more_bg");
						$(".more-none").hide()
						});
					});

</script>
<script>
	
</script>
 

<div class="w1200">
<div class="list-screen"> 
 		<div style="padding:10px 30px 10px 10px;">
 			<div class="screen-address"> <div>
<table cellspacing="0" cellpadding="0" border="0" class="from-panel">
    <tbody>
        <tr>
            <td width="60">
                <?php echo lang('name'); ?>
            </td>
            <td>
                <input type="text" id="name" name="name" class="text ui-widget-content ui-corner-all" style="width:200px;"/>
            </td>
            <td>
                <div class="attention" id="errorName" style="display:none;">
                    <?php echo lang('warn.playlist.name'); ?>
                </div>
            </td>
        </tr>
     
        <tr>
            <td>
                <?php echo lang('template'); ?>
            </td>
            <td>
                <input type="hidden" id="templateId" name="templateId" value="0" /><input type="text" id="templateName" name="templateName" class="text ui-widget-content ui-corner-all" readonly="readonly" />
				<a href="/campaign//template?width=1024&height=500" id="search" style="margin-left:10px;" class="thickbox" title="<?php echo lang('choose.template');?>"><img src="/images/icons/search.gif" alt="<?php echo lang('choose.template');?>"></a>
            </td>
            <td>
                <div class="attention" id="errorTemplate" style="display:none;">
                    <?php echo lang('warn.playlist.template'); ?>
                </div>
            </td>
        </tr> 
		<?php if(false):?>
        <tr>
            <td>
                <?php echo lang('playtime'); ?>
            </td>
            <td>
                <input type="radio" name="playtimeType" id="default" value="0" checked="checked" /><?php echo lang('playtime.default'); ?>
                <input type="radio" name="playtimeType" id="custom" value="1" /><?php echo lang('playtime.custom'); ?>
                <input name="playtime" id="playtime" style="display:none; width:80px;" value="00:15:00"/>
            </td>
            <td>
                <div class="attention" id="errorPlaytime" style="display:none;">
                    <?php echo lang('warn.playlist.playtime'); ?>
                </div>
            </td>
        </tr>
		<?php endif;?>	
		
        <tr>
            <td>
                <?php echo lang("desc"); ?>
            </td>
            <td>
                <textarea name="descr" id="descr" class="ui-widget-content ui-corner-all" rows="2" style="width:200px;"></textarea>
            </td>
            <td>
                &nbsp;
            </td>
        </tr>
    </tbody>
</table>
</div>
  	 <div class="screen-term">
      <div class="selectNumberScreen">
        <div id="selectList" class="screenBox screenBackground">
   				<dl class="listIndex" attr="player_criteria">
            <dt><?php echo lang('criteria').": ";?></dt>
            	<dd>
              <label><a href="javascript:;" values2="" values1="" attrval="all">All</a></label>           
         
            <?php foreach($criterias as $crit):?>
								<label name="criteria">
                	<input name="" type="checkbox" value="" autocomplete="off"/>
                	<a href="javascript:;" values2="" values1="" attrval="<?php echo $crit->id;?>"><?php echo $crit->name;?></a>
                </label>
						<?php endforeach;?>
					
               </dd> 
          </dl>
          
   				<dl class="listIndex" attr="player_criteria">
            <dt><?php echo lang('exclude.criteria').": ";?></dt>
            	<dd>
              <label><a href="javascript:;" values2="" values1="" attrval="all">None</a></label>           
         
            <?php foreach($criterias as $crit):?>
								<label name="ex-criteria">
                	<input name="" type="checkbox" value="" autocomplete="off"/>
                	<a href="javascript:;" values2="" values1="" attrval="<?php echo $crit->id;?>"><?php echo $crit->name;?></a>
                </label>
						<?php endforeach;?>
					
               </dd> 
          </dl>
   
        <!--
          <dl class="listIndex" attr="player_criteria">
            <dt><?php echo lang('criteria').": ";?></dt>
            <?php if(count($criterias)>10):?>
            	<dd data-more=true>
            <?php endif; ?>
              <label><a href="javascript:;" values2="" values1="" attrval="all">All</a></label>           
         
            <?php foreach(array_slice($criterias,0,10) as $crit):?>
								<label name="criteria">
                	<input name="" type="checkbox" value="" autocomplete="off"/>
                	<a href="javascript:;" values2="" values1="" attrval="<?php echo $crit->id;?>"><?php echo $crit->name;?></a>
                </label>
						<?php endforeach;?>
					
					     <span class="more"><em class="open"></em>more</span>
               </dd> 
          </dl>
          <dl class="listIndex more-none" attr="player_criteria"  style="display:none;border:none">
            <dt style='visibility:hidden'><?php echo lang('criteria').": ";?></dt>
            <dd >
        			<?php foreach(array_slice($criterias,10) as $crit):?>
								<label name="criteria">
                	<input  type="checkbox" value="" autocomplete="off"/>
                	<a href="javascript:;" values2="" values1="" attrval="<?php echo $crit->id;?>"><?php echo $crit->name;?></a>
                </label>
               <?php endforeach;?>
             </dd>
          </dl>
          -->
          <dl class="listIndex" attr="media_tag">
            <dt><?php echo lang('tag').": ";?></dt>
            <dd>
              <label><a href="javascript:;" values2="" values1="" attrval="all">All</a></label>           
            <?php foreach($tags as $tag):?>
								<label name="tags">
                	<input type="checkbox" value="" autocomplete="off"/>
                	<a href="javascript:;" values2="" values1="" attrval="<?php echo $tag->id;?>"><?php echo $tag->name;?></a>
                </label>
						<?php endforeach;?>
						</dd>
          </dl>
          
          <dl class="listIndex" attr="media_tag">
            <dt><?php echo lang('exclude.criteria').": ";?></dt>
            <dd>
              <label><a href="javascript:;" values2="" values1="" attrval="all">None</a></label>           
            <?php foreach($tags as $tag):?>
								<label name="ex-tags">
                	<input type="checkbox" value="" autocomplete="off"/>
                	<a href="javascript:;" values2="" values1="" attrval="<?php echo $tag->id;?>"><?php echo $tag->name;?></a>
                </label>
						<?php endforeach;?>
						</dd>
          </dl>
        </div>
      </div>   
    </div>

    </div>
  
		<script type="text/javascript" src="/static/js/shaixuan.js"></script> 
		</div> 
 </div>

</div>
<input type="button" name="button" id="button" onclick="checkbox()" value="提交" />
	 <p class="btn-center">
 		 <a class="btn-01" href="javascript:void(0);" onclick="campaign.goList();"><span> <?php echo lang('button.back'); ?></span></a>
		 <a class="btn-01" href="javascript:void(0);" onclick="campaign.doCreate();"><span><?php echo lang('button.next'); ?></span></a>   
		 </p>	
<script>
    campaign.initDatePicker();
</script>
