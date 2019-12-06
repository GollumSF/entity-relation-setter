<?php

namespace GollumSF\EntityRelationSetter;

trait ManyToManySetter {
	
	protected function manyToManyAdd($value, $fieldName, $targetName): self {
		$addMethod = 'add'.ucfirst($targetName);
		if (!$this->$fieldName->contains($value)) {
			$this->$fieldName->add($value);
			$value->$addMethod($this);
		}
		return $this;
	}
	
	protected function manyToManyRemove($value, $fieldName, $targetName): self {
		$removeMethod = 'remove'.ucfirst($targetName);
		if ($this->$fieldName->contains($value)) {
			$this->$fieldName->removeElement($value);
			$value->$removeMethod(null);
		}
		return $this;
	}
}