<?php
$Read->FullRead("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '{$DB}'");
$hiddens=['1'=>'id','2'=>'name'];
$form[]='<div class="box box100">';
$form[]='            <div class="panel_header default">';
$form[]='                <h2 class="'.$ICON.'">Dados sobre o '.$APP_TITLE.'!</h2>';
$form[]='            </div>';
$form[]='            <div class="panel">';
foreach ($Read->getResult() as $key => $value) {
	extract($value);
	$type="text";
	
	if($COLUMN_NAME!=$ID && $COLUMN_NAME != $status):
		
		if(strchr($COLUMN_NAME,"name")){
			$type="hidden";
		}
		
		if($type=="hidden"):
		$form[]="      <input  type=\"hidden\" name=\"{$COLUMN_NAME}\" value=\"<?=$".$COLUMN_NAME.";?>\"/>";
		else:
			if($DATA_TYPE=='decimal' || $DATA_TYPE=='numeric'):
				$form[]="<label class=\"label\">";
				$form[]="      <span class=\"legend\">{$COLUMN_NAME}:</span>";
				$form[]="      <input style=\"font-size: 1.4em;\" type=\"text\" name=\"{$COLUMN_NAME}\" value=\"<?=$".$COLUMN_NAME.";?>\" required/>";
				$form[]="</label>";
			elseif ($DATA_TYPE=='timestamp'):
				$form[]="<label class=\"label\">";
				$form[]="      <span class=\"legend\">{$COLUMN_NAME}:</span>";
				$form[]="      <input style=\"font-size: 1.4em;\" class=\"formTime\"  type=\"text\" name=\"{$COLUMN_NAME}\" value=\"<?=$".$COLUMN_NAME." ? date('d/m/Y H:i', strtotime($".$COLUMN_NAME.")) : date('d/m/Y H:i')?>\" required/>";
				$form[]="</label>";
			else:
				$form[]="<label class=\"label\">";
				$form[]="      <span class=\"legend\">{$COLUMN_NAME}:</span>";
				$form[]="      <input style=\"font-size: 1.4em;\" type=\"text\" name=\"{$COLUMN_NAME}\" value=\"<?=$".$COLUMN_NAME.";?>\" required/>";
				$form[]="</label>";
			endif;
		endif;
	endif;
	
}
$form[]='	</div>';
$form[]='</div>';

return implode(PHP_EOL,$form);

