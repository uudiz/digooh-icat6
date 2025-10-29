<div id="validateTips">
	<div >
		<div id="formMsgContent"></div>
	</div>
</div>
<table cellspacing="0" cellpadding="0" border="0" class="from-panel">
    <tbody>
        <tr>
            <td width="100">
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
                <?php echo lang('interaction'); ?>
            </td>
            <td>
            	<select id="interactionId" name="interactionId" style="width: 200px;">
            		<?php
            		foreach($interaction as $inter):
            		?>
            		<option value="<?php echo $inter->id;?>"><?php echo $inter->name;?></option>
            		<?php
            		endforeach;
            		?>
            	</select>
            </td>
            <td>
                <div class="attention" id="errorTemplate" style="display:none;">
                    <?php echo lang('warn.playlist.template'); ?>
                </div>
            </td>
        </tr>
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
<p class="btn-center">
    <!--
    <a class="btn-01" href="javascript:void(0);" onclick="interactionpls.goList();"><span><?php echo lang('button.back'); ?></span></a>
    -->
	<a class="btn-01" href="javascript:void(0);" onclick="interactionpls.doCreate();"><span><?php echo lang('button.next'); ?></span></a>
</p>
<!--
<script>
    playlist.initDatePicker();
</script>
-->
