<?php

namespace GollumSF\EntityRelationSetter;

trait OneToManySetter {
	
	protected function oneToManyAdd($value, $fieldName, $targetName): self {
		$methodSet = 'set'.ucfirst($targetName);
		if (!$this->$fieldName->contains($value)) {
			$this->$fieldName->add($value);
			$value->$methodSet($this);
		}
		return $this;
	}
	
	protected function oneToManyRemove($value, $fieldName, $targetName): self {
		$methodSet = 'set'.ucfirst($targetName);
		if ($this->$fieldName->contains($value)) {
			$this->$fieldName->removeElement($value);
			$value->$methodSet(null);
		}
		return $this;
	}
}