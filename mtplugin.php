<?php
/*
Plugin Name: Olympics Medal Table
Plugin URI: http://2008gamesbeijing.com
Description: Displays the olympics medal minitable
Version: 1.0
Author: Stephen Merriman
Author URI: http://2008gamesbeijing.com
*/
function olympics_showtable() {
	$lines = explode("\n",olympics_load('http://2008gamesbeijing.com/minitable/mtsmall.txt'));
	$empty = !$lines || !$lines[0];
	?>
	<div id="omt">
	<table cellspacing="0" cellpadding="0" border="0">
	<tr class="hd"><td colspan="5"><a href="http://2008gamesbeijing.com/<?php if (!$empty) echo 'results/'; else echo 'free-medal-table';?>"><strong>Free Olympics Medal Table &raquo;</strong><br/><span class="n">full results &raquo;</span></a></td></tr>
	<tr><td width="76" class="l"><span class="big">Country</span></td><td width="18"><img src="<?php echo get_bloginfo('wpurl');?>/wp-content/plugins/olympic-medal-table/gold.gif" width="15" height="15" alt="G" /></td><td width="18"><img src="<?php echo get_bloginfo('wpurl');?>/wp-content/plugins/olympic-medal-table/silver.gif" width="15" height="15" alt="S" /></td><td width="18"><img src="<?php echo get_bloginfo('wpurl');?>/wp-content/plugins/olympic-medal-table/bronze.gif" width="15" height="15" alt="B" /></td><td width="18">&nbsp;</td></tr>
	<?php
	if ($empty) {
		?>
		<tr class="s1"><td colspan="5">&nbsp;</td></tr>
		<tr class="s2"><td colspan="5">&nbsp;</td></tr>
		<tr class="s1"><td colspan="5"><a class="nu" href="http://2008gamesbeijing.com/free-medal-table/">Get this</a></a></td></tr>
		<tr class="s2"><td colspan="5"><a class="nu" href="http://2008gamesbeijing.com/free-medal-table/">FREE OLYMPIC MEDAL TABLE</a></td></tr>
		<tr class="s1"><td colspan="5"><a class="nu" href="http://2008gamesbeijing.com/free-medal-table/">on your blog</a></td></tr>
		<tr class="s2"><td colspan="5">&nbsp;</td></tr>
		<tr class="s1"><td colspan="5">This will automatically</td></tr>
		<tr class="s2"><td colspan="5">update once results</td></tr>
		<tr class="s1"><td colspan="5">start coming in</td></tr>
		<tr class="s2"><td colspan="5">&nbsp;</td></tr>
		<?php
	} else {
		$type=2;
		for ($i=0; $i<10; $i++) {
			$type=3-$type;
			if ($lines[$i]) { $parts = explode("#",$lines[$i]); ?>
				<tr class="s<?php echo $type;?>"><td class="l"><div class="oh"><a href="http://2008gamesbeijing.com/category/olympics/countries/<?php echo $parts[1];?>"><?php echo $parts[0];?></a></div></td><td><?php echo $parts[2];?></td><td><?php echo $parts[3];?></td><td><?php echo $parts[4];?></td><td><?php echo $parts[2]+$parts[3]+$parts[4];?></td></tr>
			<?php } else { ?>
				<tr class="s<?php echo $type;?>"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
			<?php }
		}
	} ?>
	<tr class="c n"><td colspan="5"><a href="http://2008gamesbeijing.com">2008gamesbeijing.com</a><br/>Powered by the creators of<br/>Throng: <a href="http://www.tvthrong.co.uk">UK</a> <a href="http://www.throng.com.au">AU</a> <a href="http://www.tvthrong.ca">CA</a> <a href="http://www.throng.co.nz">NZ</a>
	</table></div><?php
}
function olympics_addcss() {
	echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl').'/wp-content/plugins/olympic-medal-table/minitable.css" />';
}
function olympics_widget($args) {
	extract($args);
	echo $before_widget;
	echo $before_title.'Olympics Medal Table'.$after_title;
	olympics_showtable();
	echo $after_widget;
}
function olympics_load($url,$options=array('method'=>'get','return_info'=>false)) {
    $url_parts = parse_url($url);
    $info = array(//Currently only supported by curl.
        'http_code'    => 200
    );
    $response = '';

    $send_header = array(
        'Accept' => 'text/*',
        'User-Agent' => 'BinGet/1.00.A (http://www.bin-co.com/php/scripts/load/)'
    );

    ///////////////////////////// Curl /////////////////////////////////////
    //If curl is available, use curl to get the data.
    if(function_exists("curl_init")
                and (!(isset($options['use']) and $options['use'] == 'fsocketopen'))) { //Don't user curl if it is specifically stated to user fsocketopen in the options
        if(isset($options['method']) and $options['method'] == 'post') {
            $page = $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'];
        } else {
            $page = $url;
        }

        $ch = curl_init($url_parts['host']);

        curl_setopt($ch, CURLOPT_URL, $page);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //Just return the data - not print the whole thing.
        curl_setopt($ch, CURLOPT_HEADER, true); //We need the headers
        curl_setopt($ch, CURLOPT_NOBODY, false); //The content - if true, will not download the contents
        if(isset($options['method']) and $options['method'] == 'post' and $url_parts['query']) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $url_parts['query']);
        }
        //Set the headers our spiders sends
        curl_setopt($ch, CURLOPT_USERAGENT, $send_header['User-Agent']); //The Name of the UserAgent we will be using ;)
        $custom_headers = array("Accept: " . $send_header['Accept'] );
        if(isset($options['modified_since']))
            array_push($custom_headers,"If-Modified-Since: ".gmdate('D, d M Y H:i:s \G\M\T',strtotime($options['modified_since'])));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $custom_headers);

        curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt"); //If ever needed...
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        if(isset($url_parts['user']) and isset($url_parts['pass'])) {
            $custom_headers = array("Authorization: Basic ".base64_encode($url_parts['user'].':'.$url_parts['pass']));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $custom_headers);
        }

        $response = curl_exec($ch);
        $info = curl_getinfo($ch); //Some information on the fetch
        curl_close($ch);

    //////////////////////////////////////////// FSockOpen //////////////////////////////
    } else { //If there is no curl, use fsocketopen
        if(isset($url_parts['query'])) {
            if(isset($options['method']) and $options['method'] == 'post')
                $page = $url_parts['path'];
            else
                $page = $url_parts['path'] . '?' . $url_parts['query'];
        } else {
            $page = $url_parts['path'];
        }

        $fp = fsockopen($url_parts['host'], 80, $errno, $errstr, 30);
        if ($fp) {
            $out = '';
            if(isset($options['method']) and $options['method'] == 'post' and isset($url_parts['query'])) {
                $out .= "POST $page HTTP/1.1\r\n";
            } else {
                $out .= "GET $page HTTP/1.0\r\n"; //HTTP/1.0 is much easier to handle than HTTP/1.1
            }
            $out .= "Host: $url_parts[host]\r\n";
            $out .= "Accept: $send_header[Accept]\r\n";
            $out .= "User-Agent: {$send_header['User-Agent']}\r\n";
            if(isset($options['modified_since']))
                $out .= "If-Modified-Since: ".gmdate('D, d M Y H:i:s \G\M\T',strtotime($options['modified_since'])) ."\r\n";

            $out .= "Connection: Close\r\n";

            //HTTP Basic Authorization support
            if(isset($url_parts['user']) and isset($url_parts['pass'])) {
                $out .= "Authorization: Basic ".base64_encode($url_parts['user'].':'.$url_parts['pass']) . "\r\n";
            }

            //If the request is post - pass the data in a special way.
            if(isset($options['method']) and $options['method'] == 'post' and $url_parts['query']) {
                $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
                $out .= 'Content-Length: ' . strlen($url_parts['query']) . "\r\n";
                $out .= "\r\n" . $url_parts['query'];
            }
            $out .= "\r\n";

            fwrite($fp, $out);
            while (!feof($fp)) {
                $response .= fgets($fp, 128);
            }
            fclose($fp);
        }
    }

    //Get the headers in an associative array
    $headers = array();

    if($info['http_code'] == 404) {
        $body = "";
        $headers['Status'] = 404;
    } else {
        //Seperate header and content
        $separator_position = strpos($response,"\r\n\r\n");
        $header_text = substr($response,0,$separator_position);
        $body = substr($response,$separator_position+4);

        foreach(explode("\n",$header_text) as $line) {
            $parts = explode(": ",$line);
            if(count($parts) == 2) $headers[$parts[0]] = chop($parts[1]);
        }
    }

    if($options['return_info']) return array('headers' => $headers, 'body' => $body, 'info' => $info);
    return $body;
}
function olympics_init() {
	register_sidebar_widget('Olympics Medal Table', 'olympics_widget');
}
add_action('wp_head','olympics_addcss');
add_action('plugins_loaded', 'olympics_init');

?>