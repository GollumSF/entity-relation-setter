<?php

namespace GollumSF\EntityRelationSetter;

trait ManyToOneSetter {
	
	protected function manyToOneSet($value, $fieldName, $targetName): self {
		$addMethod = 'add'.ucfirst($targetName);
		$removeMethod = 'remove'.ucfirst($targetName);
		$oldValue = $this->$fieldName;
		$diff = $oldValue && $value;

		$this->$fieldName = $value;
		if ($diff) {
			if ($oldValue) $oldValue->$removeMethod($this);
			if ($value   ) $value   ->$addMethod($this);
		};
		return $this;
	}
}