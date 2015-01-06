<?php
function getSelectHtml($htmlid, $data, $select = '',$onchange='') {
	$head = "<select  style='width:100px' id='$htmlid' onchange='$onchange'><option value=''>未选择</option>";
	foreach ( $data as $key => $value ) {
		if ($select == $value ['value']) {
			$strz .= "<option value='" . $value ['value'] . "' selected>" . $value ['content'] . "</option>";
		} else {
			$strz .= "<option value='" . $value ['value'] . "'>" . $value ['content'] . "</option>";
		}
	}
	$foot = "</select>";
	return $head . $strz . $foot;
}