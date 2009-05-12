<?php

$plugin['version'] = '0.2';
$plugin['author'] = 'Walker Hamilton';
$plugin['author_uri'] = 'http://www.walkerhamilton.com';
$plugin['description'] = 'This plugin is for quick flash movie embed via the article writing tabby thingy. (with click to embed/run swfobject)';

$plugin['type'] = 0; 


@include_once('zem_tpl.php');

if (0) {
?>
# --- BEGIN PLUGIN HELP ---
h1. wlk_flem

v0.2: actually supporting the paramstr setting. Also, corrected the help docs.

This plugin is for quick flash movie embed via the article writing tabby thingy. (with click to embed/run swfobject)

@<txp:wlk_flem flashmovie="3" poster="5" flashversion="9" paramstr="$jso$.addParam("salign","tl");"  />@

h2. Installation

Since you can read this help, you have installed the plugin to txp.
Did you activate it?

h2. Usage

Place the wlk_flem tag in an article, form or page.

Go to _admin_ -> _preferences_ -> _advanced preferences_ and create a custom form field called flashmovie.

@
<txp:wlk_flem width="320" height="240" />
@

It takes a few options:

* *width* - Specifies width. Defaults to: 320px (checks for custom_field "width").
* *height* - Specifies height. Defaults to: 256px (checks for custom_field "height").
* *bgcolor* - Checks to see if youve set a hex color for the background behind the flash movie. Defaults to: FFFFFF
* *scale* - Checks to see if you've definied what the flash should do. Defaults to: noscale
* *flashversion* - The minimum version of flash that swfobject will deliver the movie to. Defaults to: 9 (yeah, that's right, only the newest for us.)
* *flashmovie* - This is used to specify the flash movie in the tag.
* *poster* - This is used if you want to specify a poster for click to embed action.
* *moviefieldname* - This allows you to specify a particular custom_field by name that wlk_flem should check in to see if a movie is set. (defaults to "flashmovie")
* *posterfieldname* - This allows you to specify a particular custom_field by name that wlk_flem should check in to see if a poster image is set. (defaults to "poster")
* *widthfieldname* - This allows you to specify a particular custom_field by name that wlk_flem should check in for the flash embed width. (defaults to "width")
* *heightfieldname* - This allows you to specify a particular custom_field by name that wlk_flem should check in for the flash embed height. (defaults to "height")
* *paramstr* - manually set addParams or whatever other functions swfobject supports. Should look like javascript, except with $jso$ instead of the object name.


h2. Embedding a specific movie in a page:

If you specify video as an attribute you can use the file number from the files tab (if you uploaded the video in this manner) 

bc.. <txp:wlk_flem flashmovie="6" />

p. or you can use the full path to the file.

bc.. <txp:wlk_flem video="http://www.mydomain.com/movies/myflashmovie.swf" />

p. or

bc.. <txp:wlk_flem video="/movies/myflashmovie.swf" />

h2. Attaching a flashmovie to an article:

In this case, you use that custom 'flashmovie' field and enter the full path or your file number in that field. Then the form being used to output the article simply needs <txp:wlk_flem /> in it somewhere (with any other attributes you want to specify - e.g. width, height).

My form looks like this:

bc.. <h3><txp:permlink><txp:title /></txp:permlink></h3>
<txp:wlk_flem poster="4" />
<txp:body />

p. That outputs the article on the page where this form is used with the title of the article as a header then a div with image #4 from the images tab in it. Then the text that was in the body field of the article form.

h2. Linking up the javascript.

You must first upload "swfobject.js":http://blog.deconcept.com/swfobject/ to your server. (you'll need to download it, unzip it, then upload it)

Then put this in the header of the page you wish to display movies on (every page....yes):

bc.. <txp:wlk_flemscriptlink path="http://mydomain.com/the/path/to/swfobject.js" />

p. Where you find "http://mydomain.com/the/path/to/swfobject.js", make sure you put the full path to the file.

# --- END PLUGIN HELP ---
<?php
}

# --- BEGIN PLUGIN CODE ---
	function wlk_flem($atts)
	{
		global $prefs;
		global $permlink_mode;
		global $thisarticle;

		extract(lAtts(array(
			'flashmovie'=>'',
			'poster'=>'',
			'pmset'=>'true',
			'bgcolor'=> (!empty($prefs['wlk_flem_bgcolor']))?$prefs['wlk_flem_bgcolor']:'FFFFFF',
			'version'=> (!empty($prefs['wlk_flem_flashversion']))?$prefs['wlk_flem_flashversion']:'9',
			'width'=> (!empty($prefs['wlk_flem_width']))?$prefs['wlk_flem_width']:'320',
			'height'=> (!empty($prefs['wlk_flem_height']))?$prefs['wlk_flem_height']:'256',
			'scale'=> (!empty($prefs['wlk_flem_scale']))?$prefs['wlk_flem_scale']:'noscale',
			'paramstr'=> (!empty($prefs['wlk_flem_paramstr']))?$prefs['wlk_flem_paramstr']:'',
			'moviefieldname'=>(!empty($prefs['wlk_flem_moviefieldname']))?$prefs['wlk_flem_moviefieldname']:'flashmovie',
			'posterfieldname'=>(!empty($prefs['wlk_flem_posterfieldname']))?$prefs['wlk_flem_posterfieldname']:'poster',
			'widthfieldname'=>(!empty($prefs['wlk_flem_widthfieldname']))?$prefs['wlk_flem_widthfieldname']:'width',
			'heightfieldname'=>(!empty($prefs['wlk_flem_heightfieldname']))?$prefs['wlk_flem_heightfieldname']:'height',
            'debug'=> false
		),$atts));

		/* 
		
			Checking to make sure the flashmovie is set and there
		
		*/
		if(!empty($flashmovie))
		{
			if(preg_match("/\//", $flashmovie))
			{
					if(file_exists($flashmovie))
					{
						$flashmovie = hu.$flashmovie;
						$msg[] = 'txp:wlk_flem: flashmovie='.$atts['flashmovie'].'. File found and used with txp pref path from root '.$prefs["path_from_root"];
					} else {
						$flashmovie = $flashmovie;
						$msg[] = 'txp:wlk_flem Warning: flashmovie='.$atts['flashmovie'].'. Plugin can\'t verify file existension. Make sure that given file '.$atts['flashmovie'].' exists and has vaild path.';
					}
			} else {
				if(is_numeric($flashmovie))
				{
					$flashmovie = wlk_flem_get_file('id="'.addslashes($flashmovie).'"');
					$msg[] =($flashmovie == false)?'txp:wlk_flem Error: video='.$atts['video'].'. No file with this id is stored in txp.':'';
				} else {
					$flashmovie = wlk_flem_get_file('filename="'.addslashes($flashmovie).'"');
					$msg[] =($flashmovie == false)?'txp:wlk_flem Error: video='.$atts['video'].'. No file with this name is stored in txp.':'';
				}
			}
		} else {
			if(!empty($thisarticle[$flashmoviefieldname]))
			{
				if(is_numeric($thisarticle[$flashmoviefieldname]))
				{
						$flashmovieflashmovie = wlk_flem_get_file('id="'.addslashes($thisarticle[$fieldname]).'"');
						$msg[] =($flashmovieflashmovie == false)?'txp:wlk_flem Error: CustomField['.$fieldname.']='.$thisarticle[$fieldname].'. No file with this id is stored in txp.':'';
				} else if(preg_match("/\//", $thisarticle[$flashmoviefieldname])) {
					if(file_exists($thisarticle[$flashmoviefieldname]))
					{
						$flashmovie = hu.$thisarticle[$flashmoviefieldname];
						$msg[] = 'txp:wlk_flem: CustomField['.$flashmoviefieldname.']='.$thisarticle[$flashmoviefieldname].'. File found and used with txp pref path from root'.$prefs["path_from_root"];
					} else {
						$flashmovie = $thisarticle[$flashmoviefieldname];
						$msg[] = 'txp:wlk_flem Warning: CustomField[video]='.$thisarticle[$flashmoviefieldname].'. Make sure that given file exists and has vaild path.';
					}
				} else {
					$flashmovie = wlk_flem_get_file('filename="'.addslashes($thisarticle[$flashmoviefieldname]).'"');
					$msg[] =($flashmovie == false)?'txp:wlk_flem Error: CustomField['.$flashmoviefieldname.']='.$thisarticle[$flashmoviefieldname].'. No file with this name is stored in txp.':'';
				}
			} else {
				$msg[] = 'txp:wlk_flem Error: No flash movie defined. Use the attribute flashmovie or a custom-field named flashmovie to define a flash file.';
			}
		}
		//End video check
		
		/*
		
			Checking For PosterMovie being set
		
		*/
		if(!empty($poster))
		{
			if(preg_match("/\//", $poster))
			{
					if(file_exists($poster))
					{
						$poster = hu.$poster;
						$msg[] = 'txp:wlk_flem: poster='.$atts['poster'].'. File found and used with txp pref path from root'.$prefs["path_from_root"];
					} else {
						$poster = $poster;
						$msg[] = 'txp:wlk_flem Warning: poster='.$atts['poster'].'. Plugin can\'t verify file existence. Make sure that given file '.$atts['poster'].' exists and has vaild path.';
					}
			} else {
				if(is_numeric($poster))
				{
					$poster = wlk_flem_get_front('id="'.addslashes($poster).'"');
					$msg[] =($poster == false)?'txp:wlk_flem Error: video='.$atts['poster'].'. No image or file with this id is stored in txp.':'';
				} else {
					$poster = wlk_flem_get_front('name="'.addslashes($poster).'"');
					$msg[] =($poster == false)?'txp:wlk_flem Error: video='.$atts['poster'].'. No image or file with this name is stored in txp.':'';
				}
			}
		} else {
			if(!empty($thisarticle[$posterfieldname]))
			{
				if(preg_match("/\//", $thisarticle[$posterfieldname]))
				{
					if(file_exists($thisarticle[$posterfieldname]))
					{
						$poster = hu.$thisarticle[$posterfieldname];
						$msg[] = 'txp:wlk_flem: CustomField['.$posterfieldname.']='.$thisarticle[$posterfieldname].'. File found and used with txp pref path from root'.$prefs["path_from_root"];
					} else {
						$poster = $thisarticle[$posterfieldname];
						$msg[] = 'txp:wlk_flem Warning: CustomField['.$posterfieldname.']='.$thisarticle[$posterfieldname].'. Make sure that given file exists and has vaild path.';
					}
				} else {
					if(is_numeric($thisarticle[$posterfieldname]))
					{
						$poster = wlk_flem_get_front('id="'.addslashes($thisarticle[$posterfieldname]).'"', $pmtype);
						$msg[] =($poster == false)?'txp:wlk_flem Error: CustomField['.$posterfieldname.']='.$thisarticle[$posterfieldname].'. No file with this id is stored in txp.':'';
					} else{
						$poster = wlk_flem_get_front('name="'.addslashes($thisarticle[$posterfieldname]).'"', $pmtype);
						$msg[] =($poster == false)?'txp:wlk_flem Error: CustomField['.$posterfieldname.']='.$thisarticle[$posterfieldname].'. No file with this name is stored in txp.':'';
					}
				}
			} else {
				$pmset = false;
			}
		}
		//end poster check
		$out = array();
		$uuid = substr(str_replace('.', '', uniqid('', true)), 3, 10);
		
		if($debug && $flashmovie != '')
		{
			if($pmset)
            {
				$out[]='
					<div id="'.$uuid.'flashreplacement" style="width:'.$width.';height:'.$height.';">
						<a href="javascript:void(0);" onclick="Cl'.$uuid.'Ick()"><img src="'.$poster.'" width="'.$width.'" height="'.$height.'" alt="jpeg that will be replaced by flash" title="click here to start flash content" /></a>
					</div>
					';
			} else {
				$out[]='
					<div id="'.$uuid.'flashreplacement" style="width:'.$width.';height:'.$height.';">
						<p>Sorry, but you do not have the minimum version ('.$version.') of flash player required to play this flash movie. You can install or upgrade flash <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">here</a>.</p>
					</div>
					';			
			}
            $out[]='
            	<script type="text/javascript">
			';
            if($pmset)
            {
            	$out[]='function Cl'.$uuid.'Ick() {';
            }
			$out[] = '
					var v'.$uuid.'r = new SWFObject("'.$flashmovie.'", "'.$uuid.'flash", "'.$width.'", "'.$height.'", "'.$version.'", "#'.$bgcolor.'");
					v'.$uuid.'r.addParam("scale", "'.$scale.'");';
			if($paramstr!='')
			{
				$out[] = '
				'.str_replace('$jso$', 'v'.$uuid.'r', $paramstr).'
				';
			}
			$out[] = '
					v'.$uuid.'r.write("'.$uuid.'flashreplacement");
			';
			//v'.$uuid.'r.addParam("scale", "noscale");
			if($pmset)
            {
            	$out[]='}';
            }
			$out[]= '
				</script>
			';
            echo implode("",$msg);
            $return = implode("",$out);
            $return .= htmlentities($return);
            return $return;
		} else if(!$debug && $flashmovie != '') {
			if($pmset)
            {
				$out[]='
					<div id="'.$uuid.'flashreplacement" style="width:'.$width.';height:'.$height.';">
						<a href="javascript:void(0);" onclick="Cl'.$uuid.'Ick()"><img src="'.$poster.'" width="'.$width.'" height="'.$height.'" alt="jpeg that will be replaced by flash" title="click here to start flash content" /></a>
					</div>
					';
			} else {
				$out[]='
					<div id="'.$uuid.'flashreplacement" style="width:'.$width.';height:'.$height.';">
						<p>Sorry, but you do not have the minimum version ('.$version.') of flash player required to play this flash movie. You can install or upgrade flash <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">here</a>.</p>
					</div>
					';			
			}
            $out[]='
            	<script type="text/javascript">
			';
            if($pmset)
            {
            	$out[]='function Cl'.$uuid.'Ick() {';
            }
			$out[] = '
					var v'.$uuid.'r = new SWFObject("'.$flashmovie.'", "'.$uuid.'flash", "'.$width.'", "'.$height.'", "'.$version.'", "#'.$bgcolor.'");
					v'.$uuid.'r.addParam("scale", "'.$scale.'");';
			if($paramstr!='')
			{
				$out[] = '
				'.str_replace('$jso$', 'v'.$uuid.'r', $paramstr).'
				';
			}
			$out[] = '
					v'.$uuid.'r.write("'.$uuid.'flashreplacement");
			';
			if($pmset)
            {
            	$out[]='}';
            }
			$out[]= '
				</script>
			';
            return implode("",$out);
		}
	}

	function wlk_flemscriptlink($atts) {
		$jsembed = '<script type="text/javascript" src="'.$atts['path'].'"></script>';
		return $jsembed;
	}

	//-----------------------------------
	//				Get File
	//------------------------------------

    function wlk_flem_get_file($where)
    {
		global $permlink_mode;
		$thisfile = fileDownloadFetchInfo($where);
		if(!empty($thisfile['filename']))
        {
			$player = hu.'files/'.$thisfile['filename'];
		} else {
			$player = false;
		}
		return $player;
	}

    function wlk_flem_get_front($where)
    {
		global $permlink_mode, $txpcfg;
		$query = safe_query('SELECT id, ext FROM '.$txpcfg['table_prefix'].'txp_image WHERE '.$where.' LIMIT 1');
		$imgrow = mysql_fetch_row($query);
		$thisfile = array('filename'=>$imgrow[0].$imgrow[1]);
		$player = hu.'images/';
		if(!empty($thisfile['filename']))
        {
			$player .= $thisfile['filename'];
		} else {
			$player = false;
		}
		return $player;
	}


	
# --- END PLUGIN CODE ---

?>