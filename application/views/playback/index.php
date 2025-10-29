<h1 class="tit-01"><?php echo lang('playback');?>
	<div class="filter" style="width:84%;">
		<?php echo lang('post.date');?>:
		<input type="text" readonly="readonly" id="postStartDate" class="date-input" style="width:90px;" value="<?php echo $start_date;?>"/> -
		<input type="text" readonly="readonly" id="postEndDate" class="date-input" style="width:90px;" value="<?php echo $end_date;?>"/>
		
		<!--
		<select id="filtertype"  style="width:120px;" onchange="filterchange(this);">
			<option value="0"><?php echo lang('campaign');?></option>
			<option value="1"><?php echo lang('player');?></option>
			<option value="2"><?php echo lang('media');?></option>
		</select>
		-->
		

		<lable for="player"><?php echo lang('player');?></lable>
		<span>
		<select id="player" class="select2_ajax" style="width:200px;" >
		</select>
		</span>

		<span>
		<lable><?php echo lang('campaign');?></lable>
		<select id="campaign" class="select2_ajax" style="width:200px;">
		</select>
		</span>
		<span>
		<lable><?php echo lang('media');?></lable>
		<select id="media" class="select2_ajax" style="width:200px">
		</select>
		</span>
		
		<a href="javascript:void(0);" class="btn-go" style="margin-left:20px;" onclick="playback.query();"><label><?php echo lang('button.query');?></label></a>
	</div>
    <span></span>
</h1>
<div class="clear"></div>
<div id="playbackContent" width="100%">

</div>
<script>
	playback.initCalendar();

	function filterchange(obj){
		if(obj.value==0){
	
			$('.media-select').hide();
			$('.player-select').hide();
			$('.campaign-select').show();
		}
		else if(obj.value==1){
		
			$('.media-select').hide();
			$('.campaign-select').hide();
			$('.player-select').show();
		}else{
			$('.media-select').show();
			$('.campaign-select').hide();
			$('.player-select').hide();
		}
	}	

	$('#filtertype').select2({
		minimumResultsForSearch: -1
	});

	$('.select2_ajax').each(function(){
		var id ='#'+this.id;
		var thisID = this.id;
    	$(this).select2({
			ajax: {
				url: "/playback/get_select_data",
				type: 'post',
				dataType: 'json',
				delay: 150,
				cache: true,
				data: function (params) {
					return {
						type: thisID,  
						q: params.term, // search term
						page: params.page
					};
				},
				processResults: function (data, params) {
					params.page = params.page || 1;
					return {
						results: data.items,
						pagination: 
						{
							more: (params.page * 20) < data.total_count
						}
					};
				},	
			},
			placeholder: 'Search for '+thisID,
			minimumInputLength: 1,
			allowClear: true,
		});
  	});

		
</script>