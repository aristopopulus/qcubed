<?php
	if ($strControlType == 'QLabel'  ||
		!isset($objColumn->Options['FormGen']) ||
		$objColumn->Options['FormGen'] != 'label') {
?>
				case '<?php echo $strPropertyName  ?>Control':
					if (!$this-><?php echo $strControlId  ?>) return $this-><?php echo $strControlId  ?>_Create();
					return $this-><?php echo $strControlId  ?>;
<?php } ?>
				case '<?php echo $strPropertyName  ?>Label':
					if (!$this-><?php echo $strLabelId  ?>) return $this-><?php echo $strLabelId  ?>_Create();
					return $this-><?php echo $strLabelId  ?>;