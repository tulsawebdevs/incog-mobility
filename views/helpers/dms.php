<?php
class DmsHelper extends Helper
{
	var $helpers = array('Html');
//

	function link($kt_object,$link_layout="",$cms_user_type=false,$hidden=false) {
		if (isset($kt_object->status_code)  &&  $kt_object->status_code== 21) {
			//trying to link an object for which logged in user has no perms
			return "";
		}
		//uses object id, title, mime type to show an icon and download link
		if (is_array($kt_object)) {
			if (!isset($kt_object["title"])) {
				//a doc, has diff field names
				$kt_object["title"] =$kt_object["name"]; 
				$kt_object["modified_date"] = $kt_object["modified"];
				$kt_object["mime_icon_path"]  = $kt_object["icon_path"];
			}
//			var_dump($kt_object);
			$id = $kt_object["id"];
			$modified_date =  $kt_object["modified_date"];
			$mime_icon_path = $kt_object["mime_icon_path"];
			$title = $kt_object["title"];
			$folder_id = isset($kt_object["folder_id"]) ? $kt_object["folder_id"]: $kt_object["id"];
			if (!$folder_id) {
				pr("arr did not have folder_id or id index.. ");
			}
		}else if (is_object($kt_object))  {
			$id =isset($kt_object->id) ?  $kt_object->id  : "" ;
			if (!$id) {
			//if a get_document_details request, field is called document_id
				$id  = $kt_object->document_id;
			}
			
			$modified_date =  isset($kt_object->modified_date) ? $kt_object->modified_date  : "";
			$title = isset($kt_object->title) ? $kt_object->title: "";
			$folder_id = isset($kt_object->folder_id) ? $kt_object->folder_id :"";
		}	
		$stamp  = strtotime($modified_date);
		$showtime = strftime("%D",$stamp);
//			if (!$mime_icon_path) {
//				pr("wtf?");
//				pr($kt_object["mime_icon_path"]);
//			}
			if ($link_layout == "modx") {
				$append = $link_layout ? "/ref:ag":"";
			}else{
				$append = $link_layout ? "/layout:$link_layout":"";
			}
			if (!isset($id)) {
				pr("nope");
				pr($kt_object);
				exit;
			}
			if (!$id) {
				pr("asdf");
				pr($kt_object);
				exit;
			}
			$href = "/documents/download/{$id}{$append}";
			$style="";
			if ($hidden) $style="style='display:none'";
			$html  = "<ul class='unbulleted' $style>";
			if ($cms_user_type){
				$href = FULL_BASE_URL."/documents/download/{$id}{$append}/ref:ag";
			}
$src = "/img/icons/{$mime_icon_path}.png";
if (!file_exists("./img/icons/".$mime_icon_path.".png")) {
	$src = "/img/icons/default.jpeg";
}

$html .=  <<<EOF
		<li>
			<a href={$href}><img class="download-icon" src="{$src}" alt="Download {$title}" /> <span class="title">{$title}</span></a>
		</li>
EOF;
		$html .= "</ul>";
		return $html;		  
	}

	function location($kt_object,$cms_user_type="") {
		if (!is_object($kt_object)) {
//			pr("dmshelper::location() did not get a proper object, probably an array");
	//		pr($kt_object);
			$full_path = $kt_object["full_path"];
		}else{
			$full_path = $kt_object->full_path;
		}
		$offset = substr($full_path,0,2) =="./"
			?2
			:0;
		$length  = strrpos($full_path,"/")-$offset;
		$folders = substr($full_path,$offset,$length);
		$href = "/documents/view/".$kt_object["folder_id"];
		if ($cms_user_type) $href .= "/ref:ag";
		$link = "<a href='$href'>$folders</a>";
		$html = "<strong>Location: ".$link."</strong> ";
		$modified_date = is_object($kt_object) ? $kt_object->modified_date : $kt_object['modified'];
		$html .= "<em>Modified ".$modified_date."</em>";
		return $html;
	}

	function pending() {
$html =  <<<EOF
		<p>This login has not yet been identified with any document groups. Please contact TopCon staff to make your account and product lines active.
		</p>
EOF;
		return $html;		  
		
	}
	

	function tree($folder_object,$cms_user_type=false) {
		if (!is_object($folder_object)) {
			$html = "";

			foreach($folder_object as $topLevelFolderId=>$topLevelFolder) {
				//var_dump($topLevelFolder);
				$name = isset($topLevelFolder["name"])
				?$topLevelFolder["name"]
				:$topLevelFolder["title"];
				$href  = "/documents/view/$topLevelFolderId";
					$arel = '';

/*from treeRowsx
	<a class="hasChildren" href="#"><h2>Brands</h2></a>
	<ul>
		<li><a href="#">Distribution</a></li>
		<li><a href="#">Image Library</a></li>
		<li><a href="#">Product News</a></li>
		<li><a href="#">SKU&rsquo;s</a></li>
	</ul>
	<a href="directory.html"><h2>Finance</h2></a>

	<a class="hasChildren" href="#"><h2>Home</h2></a>
*/

					
				if (isset($topLevelFolder["subfolders"]) && sizeof($topLevelFolder["subfolders"])) {
					$html .= <<<EOF
						<a class="hasChildren" {$arel} href={$href}><h2>{$name}</h2></a>
EOF;
					$html .= '<ul>';
					foreach ($topLevelFolder["subfolders"] as $item) {
						$sid = $item["id"];
						$sname = $item["name"];
						$href = "/documents/view/$sid";
						$aclass = "";
					$html .= <<<EOF
						\n<li><a {$arel} href="{$href}">{$sname}</a></li>
EOF;
				}
				$html .= "</ul>";
				}else{
					$html .= <<<EOF
						<a {$arel} href={$href}><h2>{$name}</h2></a></h3>
EOF;
				}
			}
		}
		return $html;
	}
	

	function folderLink($kt_object,$link_layout="",$useLi=true) {
		if (isset($kt_object->status_code)  &&  $kt_object->status_code== 21) {
			//trying to link an object for which logged in user has no perms
			return "";
		}
		//uses object id, title, mime type to show an icon and download link
		if (is_array($kt_object)) {
//			var_dump($kt_object);
			$id = $kt_object["id"];
			$modified_date =  $kt_object["modified_date"];
			$mime_icon_path = $kt_object["mime_icon_path"];
			$title = $kt_object["title"];
			$folder_id = isset($kt_object["folder_id"]) ? $kt_object["folder_id"]: $kt_object["id"];
			if (!$folder_id) {
				pr("arr did not have folder_id or id index.. ");
//				pr($kt_object);
			}
//			die($id);
		}else if (is_object($kt_object))  {
			pr("an object");
			$id =isset($kt_object->id) ?  $kt_object->id  : "" ;
			if (!$id) {
			//if a get_document_details request, field is called document_id
				$id  = $kt_object->document_id;
			}
			$modified_date =  isset($kt_object->modified_date) ? $kt_object->modified_date  : ""; 
			$title = isset($kt_object->title) ? $kt_object->title: "";
			$folder_id = isset($kt_object->folder_id) ? $kt_object->folder_id :"";
//			exit;
		}	
		if (!isset($id) || !$id || !$title) return "";
		$stamp  = strtotime($modified_date);
		$showtime = strftime("%D",$stamp);
		$append = $link_layout ? "/layout:$link_layout":"";
		$class = "unbulleted";
		$aclass="";
		$arel = "";
		$usearr = array(12,210,211,213,212,686,743,847,952,953,954,955,956,957);
//		if (in_array($id,$usearr)) {
		if (false) {
//			$aclass .= "ajaxFolder' ";
			$arel .= " rel='history'";
			$href = "#"."/documents/table/{$id}{$append}";
		}else{
			$href = "/documents/view/{$id}{$append}";
		}
//			
	//	pr("href for id $id: $href");
			  
		$html  = "<ul class='$class'>";
		if ($cms_user_type){
			$href = FULL_BASE_URL."/documents/view/{$id}{$append}/ref:ag";
		}
		if(!$useLi) {
$html .=  <<<EOF
						<a href='{$href}' {$arel}><span class="title"><b>{$title}</b></span></a>
EOF;
		}else{
$html .=  <<<EOF
		<li>
			<a href='{$href}' {$arel}><span class="title"><b>{$title}</b></span></a>
		</li>
EOF;
		}
		$html .= "</ul>";
		return $html;		  
	}	

	function inPlaceThumbUploader($document_id,$folder_id) {
	$html = <<<EOF
<input name="data[Document][{$document_id}][file_name]" 
value="browser" size="15" id="new-document" type="file"></p>
<input type="hidden" name="data[Document][{$document_id}][id]" id="document-id" value="{$document_id}">
<input type="hidden" name="data[Document][{$document_id}][folder_id]" id="folder-id" value="{$folder_id}">
EOF;
		return $html;
		
	}
}
?>