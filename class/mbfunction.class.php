<?php
/*
** Support for Multi-byte filename. By Yoshi.Sakai, bluemooninc.biz  2004/4/11
*/
class mb_func{
	var $encoding_translation = 0;

	function mb_func(){
		if (extension_loaded('mbstring')){
			$this->encoding_translation = intval(ini_get('mbstring.encoding_translation'));
		}
	}
	function internal2x($str,$x) {
		global $xoopsModuleConfig;
	
		if (extension_loaded('mbstring')){
			return  mb_convert_encoding($str,$x,mb_internal_encoding());
		} else {
			return $str;
		}
	}
	function internal2utf8($str) {
		global $xoopsModuleConfig;
	
		if (extension_loaded('mbstring')){
			return  mb_convert_encoding($str,'UTF-8',mb_internal_encoding());
		} else {
			return $str;
		}
	}
	function utf8tointernal($str,$forceCnvert=false) {
		global $xoopsModuleConfig;
		if (extension_loaded('mbstring')){
			if ( $this->encoding_translation != 1 || $forceCnvert )
				return mb_convert_encoding($str,mb_internal_encoding(),'UTF-8');
			else
				return $str;
		} else {
			return $str;
		}
	}	//
	// modified by hoshiyan@hoshiba-farm.com  2005.02.03, 2006.03.03
	//
	function _strcut($text, $start, $end){
		$text = $this->formatBlogText4Preview($text);
		if ( strlen($text) >= $end ) {
			$msg = xoops_substr($text, 0, $end-1);
		} else {
			$msg = $text;
		}
		return $msg;
	}
	//
	// added by hoshiyan,hoshiba-farm.com, 2004-10-15
	//
	function formatBlogText4Preview($srcStr) {
		$dstStr = preg_replace("/\[(url|img)[^]]*\].*\[\/(url|img)\]/","",$srcStr);
		$dstStr = strip_tags($dstStr);
		$dstStr = preg_replace("/([\r\n])[\s]+/","",$dstStr);
		return $dstStr;
    }
	//
	// for Content-disposition by funran7
	//     2006/05/12 add $d_enc tweak by yoshis
	function cnv_mbstr4browser($str,$browser) {
		global $xoopsModuleConfig;
		if (extension_loaded('mbstring')){
			$d_enc = mb_internal_encoding();
			//$d_enc = mb_detect_encoding($str,"SJIS,UTF-8,EUC-JP,ASCII");
			if ( $d_enc != "ASCII" ){
				switch($browser){
				case 'MOZILLA':
			        $fn = "filename*=ISO-2022-JP''" .  rawurlencode(mb_convert_encoding($str,"ISO-2022-JP",$d_enc));
			        break;
				case 'IE':
			        $fn = "filename=" .  mb_convert_encoding($str,"SJIS",$d_enc);
			        break;
				case 'OPERA':
			        $fn = "filename=\"" . mb_convert_encoding($str,"UTF-8",$d_enc) . "\"";
			        break;
				case 'SAFARI':
			        $fn = "filename=\"" . mb_convert_encoding($str,"UTF-8",$d_enc) . "\"";
			        break;
			    default:
					$fn = "filename=\"" . $browser . "\"";
			        break;
			    }
			} else {
				$fn = "filename=$str";
			}
	    } else {
			$fn = "filename=$str";
	    }
	    return $fn;
	}
}
?>
