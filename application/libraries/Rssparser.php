<?php
/*
 =====================================================================
 lastRSS 0.9.1
 
 Simple yet powerfull PHP class to parse RSS files.
 
 by Vojtech Semecky, webmaster @ webdot . cz
 
 Latest version, features, manual and examples:
 http://lastrss.webdot.cz/
 ----------------------------------------------------------------------
 LICENSE
 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License (GPL)
 as published by the Free Software Foundation; either version 2
 of the License, or (at your option) any later version.
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU General Public License for more details.
 To read the license please visit http://www.gnu.org/copyleft/gpl.html
 ======================================================================
 */


/**
 * lastRSS
 * Simple yet powerfull PHP class to parse RSS files.
 */
class RSSParser
{

    var $CI;
    // -------------------------------------------------------------------
    // Public properties
    // -------------------------------------------------------------------
    var $default_cp = 'UTF-8';
    var $CDATA = 'nochange';
    var $cp = '';
    var $items_limit = 0;
    var $stripHTML = TRUE;
    var $date_format = '';
    var $rss_content = '';
    var $hash = 0;
    var $offset = 0;
    var $uid = 0;
    var $tid = 0;
    var $cache_path = 'cached/';
    var $urlmd5; //url md5 value
    var $rss_cache_prefix = 'rsscache_';

    // -------------------------------------------------------------------
    // Private variables
    // -------------------------------------------------------------------
    var $channeltags = array('title', 'link', 'description', 'language', 'copyright', 'managingEditor', 'webMaster', 'lastBuildDate', 'rating', 'docs');
    var $itemtags = array('title', 'link', 'description', 'author', 'category', 'comments', 'enclosure', 'guid', 'pubDate', 'source');
    var $imagetags = array('title', 'url', 'link', 'width', 'height');
    var $textinputtags = array('title', 'description', 'name', 'link');

    /**
     * Constructor
     *
     * Simply determines whether the mcrypt library exists.
     *
     */
    public function __construct()
    {
        $this->CI = &get_instance();

        $this->rss_cache_prefix = $this->CI->config->item('rss_cache_prefix');
        $this->cache_path = $this->CI->config->item('rss_cache_path');

        log_message('debug', "RSSParser Class Initialized");
    }

    private function checkXML($content)
    {
        $rx = '/<?xml.*version=[\'|\"](.*?)[\'|\"].*?>/m';
        if (preg_match($rx, $content, $m)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    private function writeErrorLog($url)
    {
        $file = $this->CI->config->item('base_path') . '/logs/rss_fail_' . date('y-m-d') . '.txt';
        $fp = @fopen($file, "a+");
        if ($fp) {
            $str = "RSS[" . $this->tid . "]更新失败" . date("Y-m-d H:i:s") . chr(13) . chr(10) . $url;
            fwrite($fp, $str, strlen($str));
            fclose($fp);
        }
    }

    /*
     *  get remote rss content
     */
    private function getRemoteRSS($url, $read_cached = FALSE)
    {
        $mtime = 0;
        $cached_file = $this->cache_path . $this->rss_cache_prefix . $this->urlmd5;
        if (file_exists($cached_file)) {
            $mtime = filemtime($cached_file);
            if ($read_cached) {
                $Content = file_get_contents($cached_file);
                return array('cached' => TRUE, 'content' => $Content, 'mtime' => $mtime);
            }
        }

        $client = new GuzzleHttp\Client();

        $options = [];
        if ($mtime) {
            $options['headers'] = ['If-Modified-Since' => gmdate("D, d M Y H:i:s", $mtime) . " GMT"];
        }

        try {
            $res = $client->get(
                $url,
                $options
            );
        } catch (GuzzleHttp\Exception\ClientException $e) {
            return FALSE;
        }



        $statusCode = $res->getStatusCode();

        if ($statusCode == 304) {
            $Content = file_get_contents($cached_file);
            return array('cached' => TRUE, 'content' => $Content, 'mtime' => $mtime);
        }

        if ($statusCode != 200) {
            return FALSE;
        }

        if ($res->hasHeader('Last-Modified')) {
            $last_modified = $res->getHeader('Last-Modified');
            $mtime = strtotime($last_modified[0]);
        }
        $Content =  $res->getBody()->getContents();

        if (trim($Content) != '') {
            if ($this->checkXML($Content)) {
                file_put_contents($cached_file, $Content);
                //修改修改时间
                if ($mtime > 0) {
                    touch($cached_file, $mtime);
                } else {
                    $mtime = filemtime($cached_file);
                }
                return array('cached' => FALSE, 'content' => $Content, 'mtime' => $mtime);
                //return $Content;
            }
        }

        return FALSE;
    }

    /*
     *  format the request url
     */
    private function formatURL($url)
    {
        if (preg_match("/http:/i", $url)) {
            return $url;
        } else
            return "http://" . $url;
    }
    /*
     *  return the string's hashcode so that will cache in file
     */
    private function DJBHash($str) // 0.22 
    {
        $hash = 0;

        $n = strlen($str);

        for ($i = 0; $i < $n; $i++) {
            $hash += ($hash << 5) + ord($str[$i]);
        }
        return  $hash % 701819;
    }
    // -------------------------------------------------------------------

    // Parse RSS file and returns associative array.
    // -------------------------------------------------------------------
    public function Get($rss_url, $parsed = TRUE, $read_cached = FALSE)
    {
        // Open and load RSS file
        $this->urlmd5 = md5($rss_url);
        $fileinfo = $this->getRemoteRSS($rss_url, $read_cached);
        if ($parsed) {
            if ($fileinfo) {
                $this->rss_content = $fileinfo['content'];
                $result = $this->Parse($rss_url);
                $result['cached'] = $fileinfo['cached'];
                $result['mtime'] = $fileinfo['mtime'];

                return $result;
            } else {
                return FALSE;
            }
        } else {
            return $fileinfo;
        }
    }
    // -------------------------------------------------------------------

    // Modification of preg_match(); return trimed field with index 1
    // from 'classic' preg_match() array output
    // -------------------------------------------------------------------
    private function my_preg_match($pattern, $subject)
    {
        // start regullar expression
        preg_match($pattern, $subject, $out);
        // if there is some result... process it and return it
        if (isset($out[1])) {
            // Process CDATA (if present)
            if ($this->CDATA == 'content') { // Get CDATA content (without CDATA tag)
                $out[1] = strtr($out[1], array('<![CDATA[' => '', ']]>' => ''));
            } elseif ($this->CDATA == 'strip') { // Strip CDATA
                $out[1] = strtr($out[1], array('<![CDATA[' => '', ']]>' => ''));
            }
            // If code page is set convert character encoding to required

            if ($this->cp != '')
                //$out[1] = $this->MyConvertEncoding($this->rsscp, $this->cp, $out[1]);
                $out[1] = iconv($this->rsscp, $this->cp . '//TRANSLIT', $out[1]);
            // Return result
            return trim($out[1]);
        } else {
            // if there is NO result, return empty string
            return '';
        }
    }
    // -------------------------------------------------------------------

    // Replace HTML entities &something; by real characters
    // -------------------------------------------------------------------
    /*function unhtmlentities ($string) {
     // Get HTML entities table
     $trans_tbl = get_html_translation_table (HTML_ENTITIES, ENT_QUOTES);
     // Flip keys<==>values
     $trans_tbl = array_flip ($trans_tbl);
     // Add support for &apos; entity (missing in HTML_ENTITIES)
     $trans_tbl += array('&apos;' => "'");
     // Replace entities by values
     return strtr ($string, $trans_tbl);
     }*/
    private function  unhtmlentities($string)
    {
        //Fixed &#x2019;=>string
        //Fixed &apos;=>'
        $string = html_entity_decode(str_replace("&apos;", "'", $string), ENT_QUOTES, $this->rsscp);
        if (TRUE) {
            return $string;
        }
        // replace numeric entities
        $string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
        $string = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $string);

        // replace literal entities
        $trans_tbl = get_html_translation_table(HTML_ENTITIES);
        $trans_tbl = array_flip($trans_tbl);
        return strtr($string, $trans_tbl);
    }
    // -------------------------------------------------------------------

    // Parse() is private method used by Get() to load and parse RSS file.
    // Don't use Parse() in your scripts - use Get($rss_file) instead.
    // -------------------------------------------------------------------
    private function Parse($rss_url)
    {
        $content = '<?xml version="1.0" encoding="UTF-8"?>' . chr(10) . chr(10) . '<rss><![CDATA[';
        // Parse document encoding
        $result['encoding'] = $this->my_preg_match("'encoding=[\'\"](.*?)[\'\"]'si", $this->rss_content);
        // if document codepage is specified, use it
        if ($result['encoding'] != '') {
            $this->rsscp = $result['encoding'];
            if ($result['encoding'] == 'ISO-8859-1' || $result['encoding'] == 'iso-8859-1') {
                $this->rss_content = utf8_encode($this->rss_content);  //转成UTF-8编码
            }
        } // This is used in my_preg_match()
        // otherwise use the default codepage
        else {
            $this->rsscp = $this->default_cp;
            $result['encoding'] = $this->default_cp;
        } // This is used in my_preg_match()
        // Parse CHANNEL info

        preg_match("'<channel.*?>(.*?)</channel>'si", $this->rss_content, $out_channel);
        //print_r($out_channel);die();
        if (!empty($out_channel)) {
            foreach ($this->channeltags as $channeltag) {
                $temp = $this->my_preg_match("'<$channeltag.*?>(.*?)</$channeltag>'si", $out_channel[1]);
                if ($temp != '')
                    $result[$channeltag] = $temp; // Set only if not empty
            }
        }
        //get title
        if (!isset($result['title'])) {
            //get the head title in channel if $result['title'] not exist
            preg_match("'<title.*?>(.*?)</title>'si", $this->rss_content, $ret);
            if (count($ret) > 1)
                $result['title'] = $ret[1];
            else
                $result['title'] = '';
        } //get language
        if (!isset($result['language'])) {
            preg_match("'<language.*?>(.*?)</language>'si", $this->rss_content, $ret);
            if (count($ret) > 1)
                $result['language'] = $ret[1];
            else
                $result['language'] = '';
        }
        // If date_format is specified and lastBuildDate is valid
        if ($this->date_format != '' && ($timestamp = strtotime($result['lastBuildDate'])) !== -1) {
            // convert lastBuildDate to specified date format
            $result['lastBuildDate'] = date($this->date_format, $timestamp);
        }
        // Parse TEXTINPUT info

        preg_match("'<textinput(|[^>]*[^/])>(.*?)</textinput>'si", $this->rss_content, $out_textinfo);
        // This a little strange regexp means:
        // Look for tag <textinput> with or without any attributes, but skip truncated version <textinput /> (it's not beggining tag)
        if (isset($out_textinfo[2])) {
            foreach ($this->textinputtags as $textinputtag) {
                $temp = $this->my_preg_match("'<$textinputtag.*?>(.*?)</$textinputtag>'si", $out_textinfo[2]);
                if ($temp != '')
                    $result['textinput_' . $textinputtag] = $temp; // Set only if not empty
            }
        }
        // Parse IMAGE info
        preg_match("'<image.*?>(.*?)</image>'si", $this->rss_content, $out_imageinfo);
        if (isset($out_imageinfo[1])) {
            foreach ($this->imagetags as $imagetag) {
                $temp = $this->my_preg_match("'<$imagetag.*?>(.*?)</$imagetag>'si", $out_imageinfo[1]);
                if ($temp != '')
                    $result['image_' . $imagetag] = $temp; // Set only if not empty
            }
        }
        // Parse ITEMS
        if (strpos($rss_url, 'cnnespanol.cnn.com')) {
            $i = 0;
            $result['items'] = array(); // create array even if there are no items
            //$xml = simplexml_load_file($rss_url, 'SimpleXMLElement', LIBXML_NOCDATA);
            $xml = simplexml_load_file($rss_url, 'SimpleXMLElement', LIBXML_NOCDATA);
            //print_r($xml);
            foreach ($xml->channel->item as $item) {
                // If number of items is lower then limit: Parse one item
                if ($i < $this->items_limit || $this->items_limit == 0) {
                    foreach ($this->itemtags as $itemtag) {
                        $temp = $this->my_preg_match("'<$itemtag.*?>(.*?)</$itemtag>'si", $item);
                        if ($temp != '')
                            $result['items'][$i][$itemtag] = $temp; // Set only if not empty
                    }
                    // Strip HTML tags and other bullshit from DESCRIPTION
                    if ($this->stripHTML && isset($item->description)) {
                        $result['items'][$i]['description'] = strip_tags($this->unhtmlentities(strip_tags($item->description)));
                        $content .= $result['items'][$i]['description'] . '<<';
                    }
                    // Strip HTML tags and other bullshit from TITLE
                    if ($this->stripHTML && isset($item->title))
                        $result['items'][$i]['title'] = strip_tags($this->unhtmlentities(strip_tags($item->title)));
                    // If date_format is specified and pubDate is valid
                    if ($this->date_format != '' && ($timestamp = strtotime($item->pubDate)) !== -1) {
                        // convert pubDate to specified date format
                        $result['items'][$i]['pubDate'] = date($this->date_format, $timestamp);
                    }
                    // Item counter
                    $i++;
                }
            }
        } else {
            preg_match_all("'<item(| .*?)>(.*?)</item>'si", $this->rss_content, $items);
            $rss_items = $items[2];
            $i = 0;
            $result['items'] = array(); // create array even if there are no items
            foreach ($rss_items as $rss_item) {
                // If number of items is lower then limit: Parse one item
                if ($i < $this->items_limit || $this->items_limit == 0) {
                    foreach ($this->itemtags as $itemtag) {
                        $temp = $this->my_preg_match("'<$itemtag.*?>(.*?)</$itemtag>'si", $rss_item);
                        if ($temp != '')
                            $result['items'][$i][$itemtag] = $temp; // Set only if not empty
                    }
                    // Strip HTML tags and other bullshit from DESCRIPTION
                    if ($this->stripHTML && isset($result['items'][$i]['description'])) {
                        $result['items'][$i]['description'] = strip_tags($this->unhtmlentities(strip_tags($result['items'][$i]['description'])));
                        $content .= $result['items'][$i]['description'] . '<<';
                    }
                    //$result['items'][$i]['description'] = $this->unhtmlentities($result['items'][$i]['description']);
                    //echo $result['items'][$i]['description'];
                    // Strip HTML tags and other bullshit from TITLE
                    if ($this->stripHTML && isset($result['items'][$i]['title']))
                        $result['items'][$i]['title'] = strip_tags($this->unhtmlentities(strip_tags($result['items'][$i]['title'])));
                    // If date_format is specified and pubDate is valid
                    if ($this->date_format != '' && ($timestamp = strtotime($result['items'][$i]['pubDate'])) !== -1) {
                        // convert pubDate to specified date format
                        $result['items'][$i]['pubDate'] = date($this->date_format, $timestamp);
                    }
                    // Item counter
                    $i++;
                }
            }
        }
        /*
		$i = 0;
        $result['items'] = array(); // create array even if there are no items
        //$xml = simplexml_load_file($rss_url, 'SimpleXMLElement', LIBXML_NOCDATA);
        $xml = simplexml_load_file($rss_url, 'SimpleXMLElement', LIBXML_NOCDATA); 
		
        foreach($xml->channel->item as $item) {
            // If number of items is lower then limit: Parse one item
            if ($i < $this->items_limit || $this->items_limit == 0) {
                foreach ($this->itemtags as $itemtag) {
                    $temp = $this->my_preg_match("'<$itemtag.*?>(.*?)</$itemtag>'si", $item);
                    if ($temp != '')
                        $result['items'][$i][$itemtag] = $temp; // Set only if not empty
                }
                // Strip HTML tags and other bullshit from DESCRIPTION
                if ($this->stripHTML && isset($item->description)) {
                    $result['items'][$i]['description'] = strip_tags($this->unhtmlentities(strip_tags($item->description)));
					$content .= $result['items'][$i]['description'].'<<';
                }
                // Strip HTML tags and other bullshit from TITLE
                if ($this->stripHTML && isset($item->title))
                    $result['items'][$i]['title'] = strip_tags($this->unhtmlentities(strip_tags($item->title)));
                // If date_format is specified and pubDate is valid
                if ($this->date_format != '' && ($timestamp = strtotime($item->pubDate)) !== - 1) {
                    // convert pubDate to specified date format
                    $result['items'][$i]['pubDate'] = date($this->date_format, $timestamp);
                }
                // Item counter
                $i++;
            }
        }*/

        /*
        preg_match_all("'<item(| .*?)>(.*?)</item>'si", $this->rss_content, $items);
        $rss_items = $items[2];
        $i = 0;
        $result['items'] = array(); // create array even if there are no items
        foreach ($rss_items as $rss_item) {
            // If number of items is lower then limit: Parse one item
            if ($i < $this->items_limit || $this->items_limit == 0) {
                foreach ($this->itemtags as $itemtag) {
                    $temp = $this->my_preg_match("'<$itemtag.*?>(.*?)</$itemtag>'si", $rss_item);
                    if ($temp != '')
                        $result['items'][$i][$itemtag] = $temp; // Set only if not empty
                }
                // Strip HTML tags and other bullshit from DESCRIPTION
                if ($this->stripHTML && isset($result['items'][$i]['description'])) {
                    $result['items'][$i]['description'] = strip_tags($this->unhtmlentities(strip_tags($result['items'][$i]['description'])));
					$content .= $result['items'][$i]['description'].'<<';
                }
                //$result['items'][$i]['description'] = $this->unhtmlentities($result['items'][$i]['description']);
                //echo $result['items'][$i]['description'];
                // Strip HTML tags and other bullshit from TITLE
                if ($this->stripHTML && isset($result['items'][$i]['title']))
                    $result['items'][$i]['title'] = strip_tags($this->unhtmlentities(strip_tags($result['items'][$i]['title'])));
                // If date_format is specified and pubDate is valid
                if ($this->date_format != '' && ($timestamp = strtotime($result['items'][$i]['pubDate'])) !== - 1) {
                    // convert pubDate to specified date format
                    $result['items'][$i]['pubDate'] = date($this->date_format, $timestamp);
                }
                // Item counter
                $i++;
            }
        }*/
        $content .= chr(10) . chr(10) . ']]></rss>';
        $result['items_count'] = $i;
        $result['content'] = $content;
        return $result;
    }
}
