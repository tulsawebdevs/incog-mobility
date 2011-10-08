<?php
class InlineVideoComponent {
	
	//convert using 
	//ffmpeg2theora -V 200 -A 64 999.flv
	//result should be a proper .ogv file

	function create($document_id) {
		//get storage path
		//copy to temp?
		//check result
		//if good, ffmpeg it and mv to "./video/$document_id .ogv"
	}
	
}
?>