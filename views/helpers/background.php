<?php
/*
 * TODO: review checklist/checkbox options for better encapsulation e.g. nore four args to checklist()
 */
class BackgroundHelper extends HtmlHelper {


	function userInfo($forceFlush=false,$sid='') {
		$html= "";
		$data = "stamp=".time();
		if($sid) {
			$data .= "&sid=$sid";
		}else if (isset($_GET["sid"]) &&  strlen($_GET["sid"])) {
			$data .= "&sid=".$_GET["sid"];
		}
		$url = "/documents/bgUsers/bg:1/?d=0";
//		$url .= "&flushmem=1";
		if($forceFlush) $url .= "&flushmem=1";
		$html .= <<<EOF
<script language='javascript'>
$('document').ready(function(){
	var spinnerhtml ='<img src="/img/ajax-loader.gif" width="16px" height="16px" alt=" " /> ';
	$("#sidebar ul").html(spinnerhtml);
	$("#action-nav").hide();
		$.ajax({
		type:"GET",
		url:"{$url}",
		data:"{$data}",
		success:function(json) {
			$("#sidebar").html(json);
			$.ajax({
			type:"GET",
			url:"/documents/bgTopnav",
			data:"{$data}",
			success:function(json) {
				$("#action-nav").html(json);
				$("#action-nav").show();
			},
			error:function(XMLHttpRequest, textStatus, errorThrown) {
				$("#for-email-address span.label-title")
				.text("err.")
				.addClass("error");
				$("li.loading-indicator").css("display", "none");
				return false;
			}
			});


		},
		error:function(XMLHttpRequest, textStatus, errorThrown) {
			$("#for-email-address span.label-title")
			.text("err.")
			.addClass("error");
			$("li.loading-indicator").css("display", "none");
			return false;
		}
		});

	});
</script>
EOF;
		return $html;		
	}
	function docs($folder_id=1,$sid='') {
		$stamp = time();
		$data = "d=0";
		if($sid) {
			$data .= "&sid=$sid";
		}else if (isset($_GET["sid"]) &&  strlen($_GET["sid"])) {
			$data .= "&sid=".$_GET["sid"];
		}		
		$url = "/documents/loadArchive/bg:1/";
		$html= "";
		$html .= <<<EOF
<script language='javascript'>
$('document').ready(function(){
		$.ajax({
		type:"GET",
		url:"{$url}",
		data:"{$data}",
		success:function(json) {
			if ( json.indexOf("problem retrieving")!="-1") {
			/*alert('re try failed.. ');
                                var ptcenc = $("#ptcenc").val();
                                var utcenc = $("#utcenc").val();
                              udata = "{$data}"+"&utcenc="+utcenc+"&ptcenc="+ptcenc;
				$.ajax({
					type:"GET",
					url:"{$url}",
					data:udata,
                                        success:function(json) {
                                                if (json.indexOf("problem retrieving")!="-1") {
	                                                $("#documents").html("There was a problem retrieving the document tree. Please contact the webmaster for assistance");
                                              }else{
                                                $(".documentsListWrapper").html(json);
                                                }
                                        },
                                        error:function(XMLHttpRequest, textStatus, errorThrown) {
                                        $("#for-email-address span.label-title")
                                        .text("err.")
                                        .addClass("error");
                                        $("li.loading-indicator").css("display", "none");
                                        return false;
                                        }

				});
				*/
			}else{
				$(".documentsListWrapper").html(json);
			}
		},
		error:function(XMLHttpRequest, textStatus, errorThrown) {
			$("#for-email-address span.label-title")
			.text("err.")
			.addClass("error");
			$("li.loading-indicator").css("display", "none");
			return false;
		}
		});
	});
</script>
EOF;
		return $html;
	}
}
?>
