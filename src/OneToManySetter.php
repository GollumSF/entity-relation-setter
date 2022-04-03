<?php

namespace GollumSF\EntityRelationSetter;

trait OneToManySetter {
	
	/**
	 * @param $value
	 * @param string|null $fieldName
	 * @param string|null $targetName
	 * @return $this
	 */
	protected function oneToManyAdd($value, $fieldName = null, $targetName = null): self {
		
		if ($fieldName === null) {
			$trace = debug_backtrace();
			$calledMethod = $trace[1]['function'];
			$fieldName = lcfirst(substr($calledMethod, 3));
			$fieldName = Pluralize::pluralize($fieldName);
		}
		
		if ($targetName === null) {
			$class = get_called_class();
			if (is_subclass_of($class, 'Doctrine\Persistence\Proxy') || is_subclass_of($class, 'Doctrine\Common\Persistence\Proxy')) {
				$class = get_parent_class($class);
			}
			$targetName = $class;
			if (($index = strrpos($targetName, '\\')) !== false) {
				$targetName = substr($targetName, $index + 1);
			}
			$targetName = lcfirst($targetName);
		}
		
		$methodSet = 'set'.ucfirst($targetName);
		if (!$this->$fieldName->contains($value)) {
			$this->$fieldName->add($value);
			$value->$methodSet($this);
		}
		return $this;
	}
	
	/**
	 * @param $value
	 * @param string|null $fieldName
	 * @param string|null $targetName
	 * @return $this
	 */
	protected function oneToManyRemove($value, $fieldName = null, $targetName = null): self {
		
		if ($fieldName === null) {
			$trace = debug_backtrace();
			$calledMethod = $trace[1]['function'];
			$fieldName = lcfirst(substr($calledMethod, 6));
			$fieldName = Pluralize::pluralize($fieldName);
		}
		
		if ($targetName === null) {
			$class = get_called_class();
			if (is_subclass_of($class, 'Doctrine\Persistence\Proxy') || is_subclass_of($class, 'Doctrine\Common\Persistence\Proxy')) {
				$class = get_parent_class($class);
			}
			$targetName = $class;
			if (($index = strrpos($targetName, '\\')) !== false) {
				$targetName = substr($targetName, $index + 1);
			}
			$targetName = lcfirst($targetName);
		}
		
		$methodSet = 'set'.ucfirst($targetName);
		if ($this->$fieldName->contains($value)) {
			$this->$fieldName->removeElement($value);
			$value->$methodSet(null);
		}
		// Fix reset indexing
		if (!$this->$fieldName->count()) {
			$this->$fieldName->clear();
		}
		return $this;
	}
}
