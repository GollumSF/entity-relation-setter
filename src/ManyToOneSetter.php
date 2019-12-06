<?php

namespace GollumSF\EntityRelationSetter;

trait ManyToOneSetter {
	
	protected function manyToOneSet($value, $fieldName, $targetName): self {
		$addMethod = 'add'.ucfirst($targetName);
		$removeMethod = 'remove'.ucfirst($targetName);
		$diff = $this->$fieldName && $value;
		
		if ($diff && $this->$fieldName) $this->$fieldName->$removeMethod($this);
		$this->$fieldName = $value;
		if ($diff && $value) $value->$addMethod($this);
		return $this;
	}
}