<?php
/*************************************************************************************
    function wikiToHTML - converts all wiki syntax into HTML code!
    @author - R Bertram
    @date - 31/10/2012
*************************************************************************************/

    function wikiToHTML($content) {
        
        $patterns = array();
        
        /* Code Blocks */
        $patterns[0] = '/\[\/code\]/';   // [/code]
        $patterns[1] = '/\[code\sjs\]/'; // [code js]
        $patterns[2] = '/\[code\ssql\]/'; // [code sql]
        $patterns[3] = '/\[code\sphp\]/'; // [code php]
        $patterns[4] = '/\[code\scss\]/'; // [code css]
        $patterns[5] = '/\[code\spython\]/'; // [code css]
        
        /* Headings */
        $patterns[6] = '/(\[h1])(.*)(\[\/h1])/'; // [h1]...[/h1]
        $patterns[7] = '/(\[h2])(.*)(\[\/h2])/'; // [h2]...[/h2]
        $patterns[8] = '/(\[h3])(.*)(\[\/h3])/'; // [h3]...[/h3]
        $patterns[9] = '/(\[h4])(.*)(\[\/h4])/'; // [h4]...[/h4]
        $patterns[10] = '/(\[h5])(.*)(\[\/h5])/'; // [h5]...[/h5]
        $patterns[11] = '/(\[h6])(.*)(\[\/h6])/'; // [h6]...[/h6]
        
        /* Lists */
        $patterns[12] = '/\[ul]/';   // [ul]
        $patterns[13] = '/\[\/ul]/';   // [/ul]
        $patterns[14] = '/\[ol]/';   // [ol]
        $patterns[15] = '/\[\/ol]/';   // [/ol]
        $patterns[16] = '/\[li]/';   // [li]
        $patterns[17] = '/\[\/li]/';   // [/li]
        
        /* Paragraph */
        $patterns[18] = '/\[p]/';   // [ul]
        $patterns[19] = '/\[\/p]/';   // [/ul]
        
        /* Tables */
        $patterns[20] = '/\[table]/';   // [table]
        $patterns[21] = '/\[\/table]/';   // [/table]
        $patterns[22] = '/(\[th)([ a-zA-Z0-9=]*)(])/';   // [th]
        $patterns[23] = '/\[\/th]/';   // [/th]
        $patterns[24] = '/(\[td)([ a-zA-Z0-9=]*)(])/'; // [td]
        $patterns[25] = '/\[\/td]/';   // [/td]
        $patterns[26] = '/\[tr]/';   // [tr]
        $patterns[27] = '/\[\/tr]/';   // [/tr]
        $patterns[28] = '/\[thead]/';   // [thead]
        $patterns[29] = '/\[\/thead]/';   // [/thead]
        $patterns[30] = '/\[tfoot]/';   // [tfoot]
        $patterns[31] = '/\[\/tfoot]/';   // [/tfoot]
        $patterns[32] = '/\[tbody]/';   // [tbody]
        $patterns[33] = '/\[\/tbody]/';   // [/tbody]
		
		/* BR / HR */
        $patterns[34] = '/\[br]/';   // [br]
        $patterns[35] = '/\[hr]/';   // [hr]
		
		//$patterns[36] = '/(\[link http)(|s)(://[0-9a-zA-Z\.\/?=&#\+\-]*)(\])/'; // [link http(s)://fijv.com]
		
		// Link - no title
		$patterns[36] = '/(\[link http)(|s)(:\/\/[^<>\|\]]+)(\])/';
		
		// Link with title
		$patterns[37] = '/(\[link http)(|s)(:\/\/[^<>\|\]]+)( \| )(.[^\[\]<>]*)(\])/';
		        
//-------------------
// Attributes

		$patterns[38] = '/ rows=([0-9]+)/';
		$patterns[39] = '/ cols=([0-9]+)/';

		// Bold, Italic, Underline
		$patterns[40] = '/\[i]/';
		$patterns[41] = '/\[\/i]/';
		$patterns[42] = '/\[u]/';
		$patterns[43] = '/\[\/u]/';
		$patterns[44] = '/\[b]/';
		$patterns[45] = '/\[\/b]/';
		
		$patterns[46] = '/\[img (https?:\/\/)?([^<>\]\[]+)(\.[a-zA-Z]{2,5})(\|([0-9]+%?)?)?(\|([0-9]+%?)?)?]/';
		
		// defition lists
		$patterns[47] = '/\[deflist]/';
		$patterns[48] = '/\[def]/';
		$patterns[49] = '/\[term]/';
		
		$patterns[50] = '/\[\/deflist]/';
		$patterns[51] = '/\[\/def]/';
		$patterns[52] = '/\[\/term]/';

		
      //  $patterns[6] = '\[link target=[a-z0-9:/\#=&?.-]+ img=[a-z0-9.-/]+\]'; // [link target=URL img=x.jpg]
      //(\[h2])([a-zA-Z0-9\W\_]*)(\[\/h2])
        
        $replacements = array();
        $replacements[0] = '</pre>';
        $replacements[1] = '<pre class="brush: js; toolbar:false;">';
        $replacements[2] = '<pre class="brush: sql; toolbar:false;">';
        $replacements[3] = '<pre class="brush: php; toolbar:false;">';
        $replacements[4] = '<pre class="brush: css; toolbar:false;">';
        $replacements[5] = '<pre class="brush: python; toolbar:false;">';
        
        $replacements[6] = '<h1>$2</h1>';
        $replacements[7] = '<h2>$2</h2>';
        $replacements[8] = '<h3>$2</h3>';
        $replacements[9] = '<h4>$2</h4>';
        $replacements[10] = '<h5>$2</h5>';
        $replacements[11] = '<h6>$2</h6>';
        
        $replacements[12] = '<ul>';
        $replacements[13] = '</ul>';
        $replacements[14] = '<ol>';
        $replacements[15] = '</ol>';
        $replacements[16] = '<li>';
        $replacements[17] = '</li>';
        
        $replacements[18] = '<p>';
        $replacements[19] = '</p>';
        
        $replacements[20] = '<table>';
        $replacements[21] = '</table>';
        $replacements[22] = '<th>';
        $replacements[23] = '</th>';
        $replacements[24] = '<td$2>';
        $replacements[25] = '</td>';
        $replacements[26] = '<tr>';
        $replacements[27] = '</tr>';
        $replacements[28] = '<thead>';
        $replacements[29] = '</thead>';
        $replacements[30] = '<tfoot>';
        $replacements[31] = '</tfoot>';
        $replacements[32] = '<tbody>';
        $replacements[33] = '</tbody>';
		
        $replacements[34] = '<br/>';
        $replacements[35] = '<hr/>';
		
		$replacements[36] = '<a href="http$2$3">http$2$3</a>';
		$replacements[37] = '<a href="http$2$3">$5</a>';
        
		$replacements[38] = ' rowspan="$1"';
		$replacements[39] = ' colspan="$1"';
		
		$replacements[40] = '<em>';
		$replacements[41] = '</em>';
		$replacements[42] = '<u>';
		$replacements[43] = '</u>';
		$replacements[44] = '<strong>';
		$replacements[45] = '</strong>';
		
		$replacements[46] = '<img src="$1$2$3" height="$5" width="$7" />';
		
		$replacements[47] = '<dl>';
		$replacements[48] = '<dd>';
		$replacements[49] = '<dt>';
		$replacements[50] = '</dl>';
		$replacements[51] = '</dd>';
		$replacements[52] = '</dt>';
		
        $htmlcontent = preg_replace($patterns, $replacements, $content);
		
		// $htmlcontent = str_replace(PHP_EOL.PHP_EOL,"<br>",$htmlcontent); - this causes issues with code blocks!
		
		return $htmlcontent;
    }
	
	function HTMLToWiki($content) {
	
        $patterns = array();
        
        /* Code Blocks */
        $patterns[0] = '/<\/pre>/';   // [/code]
        $patterns[1] = '/<pre\sclass="brush:\sjs;\stoolbar:false;">/'; // [code js]
        $patterns[2] = '/<pre\sclass="brush:\ssql;\stoolbar:false;">/'; // [code sql]
        $patterns[3] = '/<pre\sclass="brush:\sphp;\stoolbar:false;">/'; // [code php]
        $patterns[4] = '/<pre\sclass="brush:\scss;\stoolbar:false;">/'; // [code css]
        $patterns[5] = '/<pre\sclass="brush:\spython;\stoolbar:false;">/'; // [code css]
        
        /* Headings */
        $patterns[6] = '/(<h1>)(.*)(<\/h1>)/'; // [h1]...[/h1]
        $patterns[7] = '/(<h2>)(.*)(<\/h2>)/'; // [h2]...[/h2]
        $patterns[8] = '/(<h3>)(.*)(<\/h3>)/'; // [h3]...[/h3]
        $patterns[9] = '/(<h4>)(.*)(<\/h4>)/'; // [h4]...[/h4]
        $patterns[10] = '/(<h5>)(.*)(<\/h5>)/'; // [h5]...[/h5]
        $patterns[11] = '/(<h6>)(.*)(<\/h6>)/'; // [h6]...[/h6]
        
        /* Lists */
        $patterns[12] = '/<ul>/';   // [ul]
        $patterns[13] = '/<\/ul>/';   // [/ul]
        $patterns[14] = '/<ol>/';   // [ol]
        $patterns[15] = '/<\/ol>/';   // [/ol]
        $patterns[16] = '/<li>/';   // [li]
        $patterns[17] = '/<\/li>/';   // [/li]
        
        /* Paragraph */
        $patterns[18] = '/<p>/';   // [p]
        $patterns[19] = '/<\/p>/';   // [/p]
        
        /* Tables */
        $patterns[20] = '/<table>/';   // [table]
        $patterns[21] = '/<\/table>/';   // [/table]
        $patterns[22] = '/(<th)([ a-zA-Z0-9=]*)(>)/';   // [th]
        $patterns[23] = '/<\/th>/';   // [/th]
        $patterns[24] = '/(<td)([ a-zA-Z0-9=]*)(>)/'; // [td]
        $patterns[25] = '/<\/td>/';   // [/td]
        $patterns[26] = '/<tr>/';   // [tr]
        $patterns[27] = '/<\/tr>/';   // [/tr]
        $patterns[28] = '/<thead>/';   // [thead]
        $patterns[29] = '/<\/thead>/';   // [/thead]
        $patterns[30] = '/<tfoot>/';   // [tfoot]
        $patterns[31] = '/<\/tfoot>/';   // [/tfoot]
        $patterns[32] = '/<tbody>/';   // [tbody]
        $patterns[33] = '/<\/tbody>/';   // [/tbody]
		
		/* BR / HR */
        $patterns[34] = '/<br>/';   // [br]
        $patterns[35] = '/<hr>/';   // [hr]
		
		// Link
		$patterns[36] = '/(<a href="http)(|s)(:\/\/[^<>]+)(">)(.[^\[\]<>]*)(<\/a>)/';
		//$patterns[36] = '';
		$patterns[37] = '/ /';
		
		$patterns[38] = '/ rows=([0-9]+)/';
		$patterns[39] = '/ cols=([0-9]+)/';

		// Bold, Italic, Underline
		$patterns[40] = '/<i>/';
		$patterns[41] = '/<\/i>/';
		$patterns[42] = '/<u>/';
		$patterns[43] = '/<\/u>/';
		$patterns[44] = '/<b>/';
		$patterns[45] = '/<\/b>/';
		$patterns[46] = '/<strong>/';
		$patterns[47] = '/<\/strong>/';
		$patterns[48] = '/<em>/';
		$patterns[49] = '/<\/em>/';
		
		$patterns[50] = '/<img src="(https?:\/\/)?([^<>]+)(\.[a-zA-Z]{2,5})"( height="([0-9]+%?)?")?( width="([0-9]+%?)?")?( \/)?>/';
		
		$patterns[51] = '/<dl>/';
		$patterns[52] = '/<dd>/';
		$patterns[53] = '/<dt>/';
		
		$patterns[54] = '/<\/dl>/';
		$patterns[55] = '/<\/dd>/';
		$patterns[56] = '/<\/dt>/';
        
        $replacements = array();
		
        $replacements[0] = '[/pre]';
        $replacements[1] = '[pre class="brush: js; toolbar:false;"]';
        $replacements[2] = '[pre class="brush: sql; toolbar:false;"]';
        $replacements[3] = '[pre class="brush: php; toolbar:false;"]';
        $replacements[4] = '[pre class="brush: css; toolbar:false;"]';
        $replacements[5] = '[pre class="brush: python; toolbar:false;"]';
        
        $replacements[6] = '[h1]$2[/h1]';
        $replacements[7] = '[h2]$2[/h2]';
        $replacements[8] = '[h3]$2[/h3]';
        $replacements[9] = '[h4]$2[/h4]';
        $replacements[10] = '[h5]$2[/h5]';
        $replacements[11] = '[h6]$2[/h6]';
        
        $replacements[12] = '[ul]';
        $replacements[13] = '[/ul]';
        $replacements[14] = '[ol]';
        $replacements[15] = '[/ol]';
        $replacements[16] = '[li]';
        $replacements[17] = '[/li]';
        
        $replacements[18] = '[p]';
        $replacements[19] = '[/p]';
        
        $replacements[20] = '[table]';
        $replacements[21] = '[/table]';
        $replacements[22] = '[th]';
        $replacements[23] = '[/th]';
        $replacements[24] = '[td$2]';
        $replacements[25] = '[/td]';
        $replacements[26] = '[tr]';
        $replacements[27] = '[/tr]';
        $replacements[28] = '[thead]';
        $replacements[29] = '[/thead]';
        $replacements[30] = '[tfoot]';
        $replacements[31] = '[/tfoot]';
        $replacements[32] = '[tbody]';
        $replacements[33] = '[/tbody]';
		
        $replacements[34] = '[br]';
        $replacements[35] = '[hr]';
		
		$replacements[36] = '[link http$2$3 | $5]';
		$replacements[37] = ' '; // not needed!
        
		$replacements[38] = ' rowspan="$1"';
		$replacements[39] = ' colspan="$1"';
		
		$replacements[40] = '[i]'; // <i>
		$replacements[41] = '[/i]';
		$replacements[42] = '[u]'; // <u>
		$replacements[43] = '[/u]';
		$replacements[44] = '[b]'; // <b>
		$replacements[45] = '[/b]';
		$replacements[46] = '[b]'; // <strong>
		$replacements[47] = '[/b]';
		$replacements[48] = '[i]'; // <em>
		$replacements[49] = '[/i]';
		
		$replacements[50] = '[img $1$2$3|$5|$7]';
		
		$replacements[51] = '[deflist]';
		$replacements[52] = '[def]';
		$replacements[53] = '[term]';
		
		$replacements[54] = '[/deflist]';
		$replacements[55] = '[/def]';
		$replacements[56] = '[/term]';
		
		$wikicontent = preg_replace($patterns, $replacements, $content);
		
        return safeWiki($wikicontent);
	}
    /*
    what is a regular expression? string which matches a set of characters 'h''e''l''l''o'
    . = matches one character
    \ = escapes character
    */
	
	function safeWiki($uncheckedWiki) {
		// Fix potentially dangerous characters
		$safeWiki = str_replace('<', '&lt;', $uncheckedWiki);
		$safeWiki = str_replace('>', '&gt;', $safeWiki);
		// $safeWiki = str_replace('"', '&quot;', $safeWiki);
		$safeWiki = str_replace('\\', '&#92;', $safeWiki);
		$safeWiki = str_replace('%', '&#37;', $safeWiki);
		
		return $safeWiki;
	}
?>