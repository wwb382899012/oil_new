<?php

class InvoiceAttachment extends BaseActiveRecord 
{
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 't_invoice_attachment';
	}
}