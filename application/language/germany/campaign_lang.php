<?php
$lang['publish.status'] = 'Status';
$lang['text'] = 'Text';
$lang['campaign.new'] = 'Neue Kampagne';
$lang['choose.template'] = 'Choose Template';
$lang['playtime.default'] = 'Default';
$lang['playtime.custom'] = 'Custom';
$lang['rotate'] = 'Rotate';
$lang['file.size'] = 'Size';
$lang['language'] = 'Language';
$lang['play_method'] = 'Ausspielart';
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
$lang['edit.campaign'] = 'Bearbeiten Kampagne';

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


$lang['media_name'] = 'Medien Name';

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
$lang['playlist.area_forget'] = 'Herausnehmen';
$lang['image.size.control'] = 'Image Size Control';
$lang['image.size.control.fit'] = 'Fit';
$lang['image.size.control.fill'] = 'Fill';
$lang['edit.webpage.config'] = 'Edit Webpage Setting';
$lang['fromat.hh.mm'] = 'Format:(HH:MM)';
$lang['fromat.webpage.hh.mm'] = 'Format:(HH:MM:00)';
$lang['fromat.hh.mm.ss'] = 'Format:(HH:MM:SS)';
$lang['campaign.exist'] = 'The campaign name "%s" already exsit ! Please create different one.';
$lang['priority'] = 'Priorität';
$lang['priority.dedicated'] = 'Dedicate';
$lang['priority.high'] = 'Partner';
$lang['priority.low'] = 'Buchung';
$lang['priority.fillin'] = 'Fill In';
$lang['campaign.length'] = 'Length';
$lang['campaign.count'] = 'Häufigkeit';
$lang['campaign.count.number'] = 'Ausspielungen pro Stunde';
$lang['campaign.count.percent'] = 'Prozentsatz';
$lang['campaign.count.total'] = 'Anzahl Ausspielungen';
$lang['campaign.media.expired'] = "This tag(%s)'s files are expired. Please go to media library to correct its date range.";
$lang['has.expires.files'] = 'The date range of media files are expired. Please either delete or modify new dates.';
$lang['max.cam.length'] = 'The maximum combined length of all campaigns for this creteria is one hour.';
$lang['campaign.ob.no.intersection'] = "Warning:Please modify campaign time range to match The player[%s]'s timer";
$lang['campaign.ob.comon'] = 'Warnung: Überbuchung am „%s“ in der Kampagne „%s“ und Display  „%s“.</br>Lösungsmöglichkeiten:</br></br>';

$lang['campaign.ob.solution'] = "<p style='text-align:left'> </p>";


$lang['campaign.ob.opiton1'] = "<p style='text-align:left'>1) Prozentsatz oder Anzahl der Ausspielungen reduzieren.</p>";
$lang['campaign.ob.opiton2'] = "<p style='text-align:left'>2) Die Laufzeit der Kampagne oder die Kriterien (Anzahl Display) erhöhen.</p>";
$lang['campaign.ob.opiton3'] = "<p style='text-align:left'>3) Die Anzahl der Displays erhöhen</p>";
$lang['campaign.ob.opiton4'] = "<p style='text-align:left'>4) De-select conflicted campaign(%s).</p>";
$lang['campaign.total.too.small'] = "The total view number of [%s] is too small for all players to share. Minimum should be %d. ";
$lang['campaign.ob.not.match'] = "The campaign[%s]'s time range and player[%s]'s timer have no overlapping on %s, Please check player's timer and set right time range.";
$lang['campaign.refresh.msg'] = '<p style="text-align:left">"Refresh" will re-publish all campaigns to update their playlists when add, delete or change to criteria(player) or timer. It will take time to do till "Refresh Success" appears.</p></br>';
$lang['campaign.refresh.success'] = 'Erfolgreich aktualisiert.';
$lang['campaign.need.same.time'] = '<p>If Play Method=Percentage or Type=Fill-in, all files must have same length by definition.  Or change to "Total View".</p></br>';
$lang['campaign.ob.tv.too.big'] = '<p>Total View value of [%s] is overbooking. Suggested maximum # is [%s].</p></br>';
$lang['campaign.expired.date'] = "<p>Invalid date range! (must either include today's date or future date range)</p></br>";
$lang['upload.medias'] = 'Upload Medias';

$lang['player.selected'] = 'Gerät ausgewählt';
$lang['avarage.usage'] = 'Durchschnittliche Auslastung';
$lang['total.free'] = 'Total free ad time';
$lang['least.common'] = 'Verfügbarkeit pro Stunde';
$lang['btn.calculate'] = 'Berechnen';
$lang['cal_ob_msg'] = '<p>Based on your input data above, it will cause overbooking. Please decrease your input value then calculate again.</p></br>
';
$lang['total.times'] = 'Anzahl Ausspielungen';
$lang['cost'] = 'Kosten der Kampagne';

$lang['save.tips'] = 'Click "Save" button to display times and cost.';
$lang['dest.player'] = 'Gebuchte Displays';
$lang['criteria_player'] = 'Kriterien/Displays';

$lang['priority.trail'] = 'Trial';
$lang['trail.warn'] = 'Trial campaign may be taken out for regular campaigns if overbooking occurs.';
$lang['campaign.exclusive.intersection'] = "<p>The campaign[%s] has already published with exclusive tags[%s].</p></br>";
$lang['campaign.exclusive.intersection2'] = "<p>The exclusive campaign[%s] has already published with tags[%s].</p></br>";
$lang['warn.play.length'] = '<p>Warnung: Spielzeit der Datei ist nicht genau 10 Sekunden - dies kann die Loop Struktur beeinflussen.</p></br>';
$lang['weather.placeholder'] = "Weather placeholder";
$lang['priority.reservation'] = 'Reservierung';
$lang['with.expired'] = "mit abgelaufen";
$lang['priority.simple'] = 'Einfach';
$lang['customerid'] = 'Kunde-Nr.';
$lang['contractid'] = 'Vertrags-Nr.';
$lang['agencyid'] = 'Agentur-Nr.';
$lang['customername'] = 'Kunde';
$lang['grouped'] = 'Zusammenhängende Wiedergabe';
$lang['exclude.players'] = 'Ausgeschlossene Displays';
$lang['campaign.percentage.too.small'] =  "<p style='text-align:left'>Warning: Percentage value is too samll. </p>";
$lang['locked'] = 'Auf aktuell gebuchte Displays begrenzen';
$lang['contactname'] = 'Vertrag';
$lang['customertype'] = 'Kundentyp';
$lang['campaignvalue'] = 'Auftragswert';
$lang['customertype.local'] = 'Lokaler Kunde';
$lang['customertype.national'] = 'Nationaler Kunde';
$lang['customertype.external'] = 'Extern Kunde';
$lang['customertype.own'] = 'Eigenwerbung';
$lang['dyna_dclp'] = 'Dynamische DCLP Kriterien für die Auswahl';
$lang['individual_dclp'] = 'Individuelle DCLP Auswahl mit unterschiedlichen Kriterien';
$lang['internal_features'] = 'Interne Merkmale';
$lang['days.per.week'] = 'Tage pro Woche';
$lang['days.in.campaign'] = 'Tage in der Kampagne';
$lang['selected.players'] = 'Ausgewählte Displays';
$lang['excluded.players'] = 'Ausgeschlossene Displays';
$lang['refresh'] = 'Aktualisieren';
$lang['minimum.player'] = 'Bitte wählen Sie mindestens ein Kriterium oder Gerät';
$lang['ob.player.cnt'] = 'Anzahl überbuchter Geräte';
$lang['cal.result'] = 'Ergebnis der Berechnung';
$lang['device.count'] = 'Anzahl der Geräte';
$lang['whole.day'] = "Ganztägig";
$lang['video.limit.warning'] = "Aufgrund der Wiedergabe von Videos auf %d Displays kann die Kampagne nicht gespeichert oder veröffentlicht werden.";
$lang['with.partners'] = "Mit Partnern";
$lang['update.frequency'] = "Update frequency";
$lang['booked'] = "Gebucht";
$lang['Programmatic.fillIn'] = 'Programmatische Fill In Buchung';
$lang['master.zone'] = "Master guiding zone";
$lang['master.zone.help'] = "Campaign playtime will depend on master guiding zone for all other zones. ";
$lang['regular'] = "Regular";
$lang['offline'] = "Offline";
$lang['save_and_publish'] = "Speichern & Veröffentlichen";
$lang['save_and_pause'] = "Speichern & Pause";
$lang['ask_for_approve'] = "Genehmigung anfordern";

$lang['approve_and_publish'] = "Genehmigen & Veröffentlichen";
$lang['deny_and_save'] = "Verweigern & Speichern";
$lang['unproven'] = "Unbewiesen";
$lang['approved'] = "Genehmigt";
$lang['approval'] = "Zulassung";

$lang['comments'] = "Bemerkungen";
$lang['last.comment'] = "Letzter Kommentar";
$lang['seeking.approval'] = "Genehmigung beantragen";
$lang['lack.integrity'] = "Fehlende Motive oder Daten";
$lang['cash.resigter.help'] = "*Bitte beachten: es darf nur EIN Kassenartikel-ID pro Kampagne eingetragen werden";
$lang['priority.extension'] = "Erweiterung";
$lang['main.campaign'] = "Hauptkampagne";
$lang['extended.campaigns'] = "erweiterte Kampagnen";
$lang['item_number'] = "Artikelnummer";
$lang['replace_main'] = "Medien der Hauptkampagne Ersetzen";
$lang['with_replaced_media'] = "mit ersetzten Medien";
