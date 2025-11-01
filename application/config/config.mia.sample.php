<?php
$config['mia_system_multi_language'] = TRUE; //多国语言开关
$config['encryption_key'] = 'icat';
// DST的国家 0:US 1:Gemany
$config['dst_country'] = 1;

//代码类型 NP100:1 , NP200:2, All:3
$config['mia_system_set'] = 2; //代码类型设置
$config['mia_system_np100'] = 1;
$config['mia_system_np200'] = 2;
$config['mia_system_all'] = 3;

//服务器地址设置
$config['mia_server_ip'] = '127.0.0.1';

//bandwidth
$config['bandwidth_open'] = false;
$config['auto-register_open'] = true;
$config['text.font_family'] = false;
$config['covert.office.files'] = true;

//company
$config['default.storage'] = '500MB'; //500MB

//page config
$config['page_default_size'] = 20;
$config['page_log_size'] = 20;
$config['page_group_size'] = 8;
$config['page_player_size'] = 20;
$config['page_media_size'] = 12;
$config['page_template_size'] = 6;
$config['page_template_image_size'] = 8;

$config['base_path'] = str_replace("\\", "/", realpath('.'));
$config['resources'] = './resources/';
if (!is_dir($config['resources'])) {
	mkdir($config['resources'], 777, true);
}
$config['images'] = './images/';
$config['gz_path'] = '/home/miatek/np200/'; //终端压缩包下载目录
//system media lib path
$config['system_media_path'] = $config['resources'] . 'system/';

//campaign/ path
$config['playlist_publish_path'] = $config['resources'] . 'publish/';

//YUV path
$config['yuv_publish_path'] = $config['resources'] . 'publish/';

//temp cached file
$config['cached_temp_path'] = $config['resources'] . 'cached/';

//error log file
$config['cached_errorlog_path'] = $config['resources'] . 'errorlog/';

//tmp upload file path
$config['tmp'] = $config['base_path'] . '/upload/tmp';
if (!is_dir($config['tmp'])) {
	mkdir($config['tmp'], 755, true);
}
$config['max_filesize'] = 524288000; //500MB

//rss
$config['rss_cache_path'] = './cached/';
$config['rss_cache_prefix'] = 'rsscache_';


//RSS格式设置 title only
$config['rss_format_title'] = 0;
$config['rss_format_desc'] = 1;
$config['rss_format_all'] = 2;

//server timezone setting
$now = time();
$gmt = mktime(gmdate("H", $now), gmdate("i", $now), gmdate("s", $now), gmdate("m", $now), gmdate("d", $now), gmdate("Y", $now));
$config['server.timezone'] = ($now - $gmt) / 3600;
$config['server.dst'] = date('I') ? true : false;


$config['default_passd'] = '123456';

//$config['base_path']=str_replace("\\", "/", realpath('.'));

$config['ffmpeg'] = '/usr/bin/ffmpeg';
//auth config
$config['auth_view'] = 0; //group viewer
$config['auth_franchise'] = 1; //Franchise
$config['auth_group'] = 3; //group onwer
$config['auth_staff'] = 4;
$config['auth_admin'] = 5; //admin
$config['auth_system'] = 10; //system


$config['system_users'] = array('admin', 'administrator', 'system');

//media config
$config['media_type_image'] = 1;
$config['media_type_video'] = 2;
$config['media_type_rss'] = 3;
$config['media_type_music'] = 4;
$config['media_type_webpage'] = 5;


$config['media_source_local'] = 0;
$config['media_source_ftp'] = 1;
$config['media_source_http'] = 2;

$config['media_layout_grid'] = 0;
$config['media_layout_list'] = 1;
//preview status
$config['media_preview_disable'] = 0;
$config['media_preview_enable_main'] = 1;
$config['media_preview_enable_tiny'] = 2;


$config['image_max_size'] = 533504000; //5MB
$config['image_max_width'] = 0; //no limit
$config['image_max_height'] = 0; //no limit
$config['logo_max_size'] = 2048 * 1024 * 5; //2MB
$config['logo_max_width'] = 220; //no limit
$config['logo_max_height'] = 100; //no limit

$config['image_types'] = 'jpg|jpeg';
$config['image_tiny_width'] = 320;
$config['image_tiny_height'] = 180;
$config['image_main_width'] = 1280;
$config['image_main_height'] = 720;

//flash config
$config['image_file_types'] = '*.jpg;*.jpeg;*.bmp;*.png;';
$config['image_file_types_desc'] = 'Images File';

$config['video_file_types'] = '*.mpeg;*.mpg;*.mkv;*.mp4;*.divx;*.mov;*.avi;*.flv;*.wmv;';

$config['allowed_video_types'] = array("avi", "mp4", "divx", 'mpeg', 'mpg', 'mkv', 'mov');
$config['allowed_image_types'] = array('gif', 'png', 'jpg', 'jpeg');

$config['video_file_types_desc'] = 'Video File';

$config['music_file_types'] = '*.mp3;';
$config['music_file_types_desc'] = 'Music File';

//video config
$config['video_types'] = 'mpeg|avi';
//$config['video_max_size']=104857600;//100MB
$config['video_max_size'] = 524288000; //500MB

//ftp
$config['ftp_default_port'] = 21;
$config['ftp_limit'] = 10;


//template config
$config['template_user'] = 0;
$config['template_system'] = 1;
$config['template_size_1280_720'] = array('w' => 480, 'h' => 270);
$config['template_size_720_1280'] = array('w' => 270, 'h' => 480);
$config['template_size_1920_1080'] = array('w' => 960, 'h' => 540);
$config['template_size_1080_1920'] = array('w' => 540, 'h' => 960);

//temolate limit
$config['template_limit'] = 10;

//screen size info
$config['screen_width'] = 640;
$config['screen_height'] = 360;

//预览图大小
$config['template_preview_width'] = 480;
$config['template_preview_reverse_width'] = 152;
$config['template_preview_height'] = 270;

$config['tempate_preview_path'] = '/resources/preview/%d/template';
$config['playlist_preview_path'] = '/resources/preview/%d/campaign/';

//area config

$config['area_type_movie'] = 0;
$config['area_type_image'] = 1;
$config['area_type_image2'] = 3;
$config['area_type_image3'] = 4;
$config['area_type_text'] = 2;
$config['area_type_date'] = 3;
$config['area_type_time'] = 4;
$config['area_type_weather'] = 5;
$config['area_type_logo'] = 8;
$config['area_type_bg'] = 9;
$config['area_type_webpage'] = 7;
$config['area_type_mask'] = 28;
$config['area_type_staticText'] = 18;
$config['area_type_btn'] = 25;
$config['area_type_id'] = 30;

//transmode type
//视频全屏
$config['transmode_type_full'] = 1;
//视频分屏
$config['transmode_type_part'] = 2;
//照片区域
$config['transmode_type_image'] = 3;

//bg color
$config['area_type_movie_color'] = '#336b96';
$config['area_type_image_color'] = '#ffff00';
$config['area_type_image1_color'] = '#95b555';
$config['area_type_image2_color'] = '#5a974b';
$config['area_type_image3_color'] = '#378c6d';
$config['area_type_image4_color'] = '#528B8B';
$config['area_type_text_color'] = '#679fca';
$config['area_type_staticText_color'] = '#7171C6';
$config['area_type_date_color'] = '#5f698c';
$config['area_type_time_color'] = '#4876FF';
$config['area_type_weather_color'] = '#4d7ea3';
$config['area_type_logo_color'] = '#7EC0EE';
$config['area_type_webpage_color'] = '#0072E3';
$config['area_type_btnGroup_color'] = '#EBEBEB';
$config['area_type_btn_color'] = '#74afe4';
$config['area_type_mask_color'] = '#ffcc00';

$config['area_border_color'] = '#CCCCCC';
//area media flag
$config['area_media_flag_temp'] = 0;
$config['area_media_flag_ok'] = 1;
$config['area_media_flag_delete'] = 2;
$config['area_media_flag_all'] = array(0, 1, 2);


$config['area_media_size'] = 8;

//时间对应php格式转化
$config['area_date_format_0'] = 'Y/m/d';
$config['area_date_format_1'] = 'Y-m-d';
$config['area_date_format_1'] = 'm/d/Y';

$config['area_time_format_0'] = 'H:i:s';
$config['area_time_format_1'] = 'H:i';

//area text setting
//$config['area_text_speed']=

//campaign/
$config['playlist_type_normal'] = 0; //播放列表
$config['playlist_type_instant_program'] = 1; //即时节目
$config['playlist_type_instant_text'] = 2; //即时字幕

$config['playlist.status.default'] = 0;
$config['playlist.status.published'] = 1;

$config['media.transmode.mapping'] = array(23, 6, 7, 5, 4, 13, 14, 15, 16, 8, 9, 1, 0, 25, 26, 17, 19, 18, 20, 27, 28, 29, 30, 10, 22, 21, 31, 24);

//schedule
$config['schedule.default'] = 0;
$config['schedule.publish'] = 1;

$config['schedule.day'] = 'agendaDay';
$config['schedule.week'] = 'agendaWeek';
$config['schedule.month'] = 'month';
$config['schedule.default_event_minutes'] = 120;

$config['oneday'] = 24 * 60 * 60;

//客户端离线判断时间，单位分钟
$config['player.offline.limit'] = 11;
$config['player.model.0'] = 'VGA';
$config['player.model.1'] = 'HDMI_50';
$config['player.model.2'] = 'HDMI_60';

//客户机事件状态
$config['event_type_login'] = 1;
$config['event_type_heartbeat'] = 2;
$config['event_type_download'] = 3;
$config['event_type_playback'] = 4;
$config['event_type_time'] = 5;


$config['accesslog'] = true;

//Email Settings

$config['email.from_name'] = 'miatek';
$config['email.from_mail'] = 'support@miatek.com';
$config['email.smtp_server'] = 'smtp.exmail.qq.com';
$config['email.smtp_port'] = 465;
$config['email.reply_to'] = 'support@miatek.com';
$config['email.password'] = 'Trnf6PGwSNxnnmT9';


$config['area_video'] = 101;
$config['area_pic1'] = 102;
$config['area_pic2'] = 103;
$config['area_pic3'] = 104;
$config['area_pic4'] = 105;


$config['new_offline_email'] = true;
$config['xml_news_support'] = true;
$config['player_capcity'] = true;
$config['xslot_on'] = true;
$config['cost_entry'] = true;
$config['player_pics'] = true;

$config['news_duration'] = 10;
$config['cam_with_player'] = true;

$config['has_weather'] = true;

$config['with_sub_folders'] = true;
$config['digooh_timer_per_hour'] = true;

$config['campaign_with_tags'] = true;
$config['with_partners'] = true;
$config['ssp_feature'] = true;
$config['xslot_on'] = true;
$config['cost_entry'] = true;

$config['with_template'] = false;
$config['with_partners'] = true;
$config['ssp_feature'] = true;
$config['xslot_on'] = true;
$config['cost_entry'] = FALSE;
$config['socket_server'] = "127.0.0.1";
$config['tcp_port'] = 4702;
$config['kdh_logo'] = TRUE;
$config['has_peripherial'] = false;
$config['has_sensor'] = false;
$config['with_radius_search'] = false;

$config['with_id_zone'] = true;
if (!$config['with_template']) {
	$config['with_id_zone'] = false;
}
$config['digooh_player_form_validation'] = true;
$config['refresh_camapign_while_saving_player'] = true;
$config['new_playback_detail'] = true;
$config['with_transition'] = false;
/* End of file config.php */
/* Location: ./application/config/config.php */

$config['redis_password'] = "PW";
$config['medium_with_weekNtime'] = true;
$config['date_range_from_folder'] = true;