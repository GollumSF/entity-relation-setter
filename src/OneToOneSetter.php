<?php

namespace App\Traits;

trait OneToOneSetter {
	
	protected function oneToOneSet($value, $fieldName, $targetName): self {
		$methodSet = 'set'.ucfirst($targetName);
		$diff = $this->$fieldName !== $value;
		if ($diff && $this->$fieldName) $this->purchaseAddress->$methodSet(null);
		$this->$fieldName = $value;
		if ($diff && $value) $value->$methodSet($this);
		return $this;
	}
}