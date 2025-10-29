<?php
$lang['publish.status'] = 'Status';
$lang['text'] = 'Text';
$lang['campaign.new'] = 'New Campaign';
$lang['choose.template'] = 'Choose Template';
$lang['playtime.default'] = 'Default';
$lang['playtime.custom'] = 'Custom';
$lang['rotate'] = 'Rotate';
$lang['file.size'] = 'Size';
$lang['language'] = 'Language';
$lang['play_method'] = 'Play Method';
$lang['fromat.mm.ss'] = 'Format:(MM:SS)';

$lang['playlist.error.param.empty'] = 'Parameter error!';
$lang['playlist.error.not.exist'] = 'Campaign not exists!';
$lang['campaign.error.not.assigned'] = 'Warning: Some campaigns have no player assigned. Please check!';
$lang['campaign.ob.too.big'] = 'Overbooking!  Please change time or reduce play count for either one and publish again';
$lang['campaign.ob.confict'] = 'Time conflict! Campaign "%s" already uses %02d:%02d-%02d:%02d. Please change time or reduce play count for either one and publish again.';


$lang['warn.playcount.invalid'] = 'Play Count can\'t be empty';
$lang['warn.playweight.invalid'] = 'Invalid play count. must be 1%-100%';
$lang['warn.playtotal.invalid'] = 'Invalid total views.';
$lang['warn.campaign.name'] = 'Campaign name can\'t be empty';
$lang['warn.campaign.template'] = 'Must choose one template before save';
$lang['warn.campaign.playtime'] = 'Play time can\'t be empty';
$lang['warn.area.media.limit'] = 'Area[%s] only allowed one image!';
$lang['warn.campaign.invalidtime'] = 'Start Time must be less than End Time!';
$lang['edit.campaign'] = 'Edit Campaign';

$lang['text.rss'] = 'RSS';
$lang['text.speed'] = 'Speed';
$lang['text.speed.list'] = array('1' => 'Slow', '2' => 'Normal', '3' => 'Fast', '4' => 'Very Fast');
$lang['direction'] = 'Direction';
$lang['np200.text.direction.list'] = array('0' => 'Static', '1' => 'Left', '2' => 'Right', '3' => 'Up', '4' => 'Down');
$lang['text.direction.list'] = array('0' => 'Static', '1' => 'Left', '3' => 'Up');
$lang['text.transparent'] = 'Transparent';
$lang['text.transparent.list'] = array('0' => 'OFF', '25' => '25%', '50' => '50%', '75' => '75%', '100' => '100%');
$lang['text.duration'] = 'Duration';

$lang['text.setting.default'] = array(
    'font_size' => 60,
    'color' => '#FFFFFF',
    'font_family' => 'Aria',
    'bg_color' => '#000000',
    'speed' => 2,
    'direction' => 1,
    'duration' => '00:00:05',
    'transparent' => '0',
    'language' => '0'
);
$lang['static.text.setting.default'] = array(
    'font_size' => 14,
    'color' => '#FFFFFF',
    'font_family' => 'Aria'
);

$lang['campaign.area.movie.add'] = 'Add media';
$lang['campaign.area.image.add'] = 'Add media';
$lang['campaign.error.media.empty'] = 'Please chose at least one media!';

$lang['edit.media.config'] = 'Edit Photo Setting';

$lang['transmode'] = array('Rectangular narrowing conversion', 'Rectangular expansion conversion', 'Circular narrowing conversion', 'Circular expansion conversion', 'Up Erase', 'Down erase', 'Right to erase', 'Wipe Left', 'Vertical blinds conversion', 'Horizontal blinds conversion', 'Chess board conversion', 'Chess board conversion', 'Random noise interference conversion', 'About closing effect conversion', 'About to open the door effect conversion', 'Up and down the closed effect conversion', 'The effect of the upper and lower door conversion', 'Cover conversion from the upper right corner to the lower left corner of jagged edges', 'Cover conversion from the lower right corner to the upper left corner of the serrated edge', 'Cover conversion from upper left to lower right corner of the serrated edge', 'Cover conversion from lower left to upper right corner of jagged edges', 'Random horizontal lines converted', 'Random vertical lines of conversion', 'Random effects', 'Fade');


$lang['media_name'] = 'Media Name';

$lang['transition_mode'] = 'Transition';
$lang['transition_time'] = 'Transition Time';

$lang['image.library'] = 'Image Library';
$lang['video.library'] = 'Video Library';
$lang['rss.library'] = 'RSS Library';
$lang['webpage.library'] = 'Webpage Library';

$lang['rss.format'] = 'RSS Format';
$lang['rss.title'] = 'Title';
$lang['rss.detail'] = 'Detailed News';

$lang['playlist.publish.success'] = 'Publish success';
$lang['playlist.publish.part.success'] = 'Publish done, but [%d] video file [%s] conversion fails';
$lang['media.name'] = 'Name';
$lang['upload.date'] = 'Upload Date';

$lang['tip.rss.limit'] = 'Only allows one per playlist';
$lang['warn.rss.limit'] = 'There already exists one rss in media list! Please remove it before add new one!';
$lang['playlist.error.rss.media.num'] = 'RSS media only support one media. Please Choose one file';
$lang['warn.area.media.not.found'] = 'Not found this media, please refresh page and try again!';
$lang['warn.publish.empty.media'] = 'Minimum one file for Video/Image/Webpage/Mask zone is required!';

$lang['warn.number'] = 'Move to order must be number!';
$lang['warn.outbound'] = 'Move to order number outof bound!';
$lang['warn.playtime.format'] = 'Time format error. Must be MM:SS';
$lang['warn.playtime.range'] = 'Time range error. Must be like MM:SS(00:00 - 59:59)';

$lang['rotate.confirm.title'] = 'Rotation Option Confirmation';
$lang['rotate.confirm'] = 'Please select rotation method for the selected medial files.<br/><br/>Fill: stretch to fill entire zone<br/>Fit: keep original aspect ratio<br/>OK: No Rotation';
$lang['rotate.fill'] = 'Fill';
$lang['rotate.fit'] = 'Fit';
$lang['rotate.nothing'] = 'OK';

$lang['playlist.name.exsit'] = 'The Playlist name "%s" already exsit ! Please create different one.';
$lang['playlist.template.black'] = 'The Template field can not be blank !';
$lang['playlist.preview'] = 'preview';
$lang['playlist.area_start'] = 'Start(HH:MM:00)';
$lang['playlist.area_end'] = 'End(HH:MM:00)';
$lang['playlist.area_forget'] = 'Exclude';
$lang['image.size.control'] = 'Image Size Control';
$lang['image.size.control.fit'] = 'Fit';
$lang['image.size.control.fill'] = 'Fill';
$lang['edit.webpage.config'] = 'Edit Webpage Setting';
$lang['fromat.hh.mm'] = 'Format:(HH:MM)';
$lang['fromat.webpage.hh.mm'] = 'Format:(HH:MM:00)';
$lang['fromat.hh.mm.ss'] = 'Format:(HH:MM:SS)';
$lang['campaign.exist'] = 'The campaign name "%s" already exsit ! Please create different one.';
$lang['priority'] = 'Priority';
$lang['priority.dedicated'] = 'Dedicate';
$lang['priority.high'] = 'Partner';
$lang['priority.low'] = 'Booking';
$lang['priority.fillin'] = 'Fill In';
$lang['campaign.length'] = 'Length';
$lang['campaign.count'] = 'Count';
$lang['campaign.count.number'] = 'Times Per Hour';
$lang['campaign.count.percent'] = 'Percentage';
$lang['campaign.count.total'] = 'Total Views';
$lang['campaign.media.expired'] = "This tag(%s)'s files are expired. Please go to media library to correct its date range.";
$lang['has.expires.files'] = 'The date range of media files are expired. Please either delete or modify new dates.';
$lang['max.cam.length'] = 'The maximum combined length of all campaigns for this creteria is one hour.';
$lang['campaign.ob.no.intersection'] = "Warning:Please modify campaign time range to match The player[%s]'s timer.";
$lang['campaign.ob.comon'] = "<p style='text-align:left'>Warning: Overbooking occurs on %s at [%s] and player[%s]. Solution below:  </p>";
$lang['campaign.ob.comon.new'] = "< style='text-align:left'>Warning: Overbooking occurs on:";
$lang['campaign.ob.item'] = "<p style='text-align:left'>%s at [%s] and player[%s]</p>";
$lang['campaign.ob.solution'] = "<p style='text-align:left'>Solution below: </p>";
$lang['campaign.ob.opiton1'] = "<p style='text-align:left'>1) Decrease Percentage or Total Views.</p>";
$lang['campaign.ob.opiton2'] = "<p style='text-align:left'>2) Increase campaign time range and/or increase Criteria’s(players) timer range.</p>";
$lang['campaign.ob.opiton3'] = "<p style='text-align:left'>3) Add more players to the selected criteria.</p>";
$lang['campaign.ob.opiton4'] = "<p style='text-align:left'>4) De-select conflicted campaign(%s).</p>";
$lang['campaign.total.too.small'] = "The total view number of [%s] is too small for all players to share. Minimum should be %d. ";
$lang['campaign.ob.not.match'] = "The campaign[%s]'s time range and player[%s]'s timer have no overlapping on %s, Please check player's timer and set right time range.";
$lang['campaign.refresh.msg'] = '<p style="text-align:left">"Refresh" will re-publish all campaigns to update their playlists when add, delete or change to criteria(player) or timer. It will take time to do till "Refresh Success" appears.</p></br>';
$lang['campaign.refresh.success'] = 'Successfully refreshed.';
$lang['campaign.need.same.time'] = '<p>If Play Method=Percentage or Type=Fill-in, all files must have same length by definition.  Or change to "Total View".</p></br>';
$lang['campaign.ob.tv.too.big'] = '<p>Total View value of [%s] is overbooking. Suggested maximum # is [%s].</p></br>';
$lang['campaign.expired.date'] = "<p>Invalid date range! (must either include today's date or future date range)</p></br>";
$lang['upload.medias'] = 'Upload Medias';

$lang['player.selected'] = 'Player selected';
$lang['avarage.usage'] = 'Avarage usage';
$lang['total.free'] = 'Total free ad time';
$lang['least.common'] = 'Least common ad times per hour';
$lang['btn.calculate'] = 'Calculate';
$lang['cal_ob_msg'] = '<p>Based on your input data above, it will cause overbooking. Please decrease your input value then calculate again.</p></br>
';
$lang['total.times'] = 'Total play times';
$lang['cost'] = 'Cost for campaign';
$lang['save.tips'] = 'Click "Save" button to display times and cost.';
$lang['dest.player'] = 'Destinated Player';

$lang['criteria_player'] = 'Criteria/Player';
$lang['priority.trail'] = 'Trial';
$lang['trail.warn'] = 'Trial campaign may be taken out for regular campaigns if overbooking occurs.';

$lang['campaign.exclusive.intersection'] = "<p>The campaign[%s] has already published with exclusive tags[%s].</p></br>";
$lang['campaign.exclusive.intersection2'] = "<p>The exclusive campaign[%s] has already published with tags[%s].</p></br>";
$lang['warn.play.length'] = '<p>Warning: Time of the file is not exactly 10 seconds; this could affect the loop structure.</p></br>';
$lang['weather.placeholder'] = "Weather placeholder";
$lang['priority.reservation'] = 'Reservation';

$lang['with.expired'] = "With Expired";
$lang['priority.simple'] = 'Simple';
$lang['customerid'] = 'Customer ID';
$lang['contractid'] = 'Contract ID';
$lang['agencyid'] = 'Agency ID';
$lang['customername'] = 'Customer Name';
$lang['grouped'] = 'Bundled playback';
$lang['exclude.players'] = 'Exclude Players';
$lang['campaign.percentage.too.small'] =  "<p style='text-align:left'>Warning: Percentage value is too samll. </p>";
$lang['locked'] = 'Lock it';
$lang['contactname'] = 'Contact Name';
$lang['customertype'] = 'Customer Type';
$lang['campaignvalue'] = 'Campaign value';
$lang['customertype.local'] = 'Local Customer';
$lang['customertype.national'] = 'National Customer';
$lang['customertype.external'] = 'External Customer';
$lang['customertype.own'] = 'Own';
$lang['dyna_dclp'] = 'Dynamic DCLP criteria for selection';
$lang['individual_dclp'] = 'Individual DCLP selection with different criteria';
$lang['internal_features'] = 'Internal features';
$lang['days.per.week'] = 'Days Per Week';
$lang['days.in.campaign'] = 'Days in Campaign';
$lang['selected.players'] = 'Selected Devices';
$lang['excluded.players'] = 'Excluded Devices';
$lang['refresh'] = 'Refresh';
$lang['minimum.player'] = 'Please choose at least one criterion or player';
$lang['ob.player.cnt'] = 'Overbooked device count';
$lang['cal.result'] = 'Result of calculating';
$lang['device.count'] = 'Count of devices';
$lang['whole.day'] = "All day";
$lang['video.limit.warning'] = "Due to the video content on %d displays the campaign can not be saved or published";
$lang['with.partners'] = "With Partners";
$lang['update.frequency'] = "Update frequency";
$lang['booked'] = "Booked";
$lang['Programmatic.fillIn'] = 'Programmatic Fill In Booking';
$lang['master.zone'] = "Master guiding zone";
$lang['master.zone.help'] = "Campaign playtime will depend on master guiding zone for all other zones. ";
$lang['regular'] = "Regular";
$lang['offline'] = "Offline";
$lang['save_and_publish'] = "Save & Publish";
$lang['save_and_pause'] = "Save & Pause";
$lang['ask_for_approve'] = "Ask for approval";
$lang['approve_and_publish'] = "Approve & Publish";
$lang['deny_and_save'] = "Deny & Save";
$lang['unproven'] = "Unproven";
$lang['approved'] = "Approved";
$lang['approval'] = "Approval";

$lang['comments'] = "Comments";
$lang['last.comment'] = "Last Comment";
$lang['seeking.approval'] = "Seeking Approval";
$lang['lack.integrity'] = "Lack of motif or data";
$lang['cash.resigter.help'] = "*Only ONE cash regsiter price can be shown in a campaign";
$lang['priority.extension'] = "Extension";
$lang['main.campaign'] = "Main Campaign";
$lang['extended.campaigns'] = "extended campaigns";
$lang['item_number'] = "Item no.";
$lang['replace_main'] = "Replace Media from the Main Campaign";
$lang['with_replaced_media'] = "With replaced media";
