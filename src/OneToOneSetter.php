<?php

namespace GollumSF\EntityRelationSetter;

use Doctrine\Persistence\Proxy;

trait OneToOneSetter {
	
	protected function oneToOneSet($value, ?string $fieldName = null, ?string $targetName = null): self {
		
		if ($fieldName === null) {
			$trace = debug_backtrace();
			$calledMethod = $trace[1]['function'];
			$fieldName = lcfirst(substr($calledMethod, 3));
		}
		
		if ($targetName === null) {
			$class = get_called_class();
			if (is_subclass_of($class, Proxy::class)) {
				$class = get_parent_class($class);
			}
			$targetName = $class;
			if (($index = strrpos($targetName, '\\')) !== false) {
				$targetName = substr($targetName, $index + 1);
			}
			$targetName = lcfirst($targetName);
		}
		
		$methodSet = 'set'.ucfirst($targetName);
		
		$oldValue = $this->$fieldName;
		$diff = $oldValue !== $value;
		if ($diff && $oldValue) {
			if (!method_exists($oldValue, $methodSet)) {
				throw new \LogicException(sprintf('Method %s not exist or not public on class %s.', $methodSet, get_class($oldValue)));
			}
			$this->$fieldName = null;
			$oldValue->$methodSet(null);
		}
		
		
		$this->$fieldName = $value;
		if ($diff && $value) {
			if (!method_exists($value, $methodSet)) {
				throw new \LogicException(sprintf('Method %s not exist or not public on class %s.', $methodSet, get_class($value)));
			}
			$value->$methodSet($this);
		}
		return $this;
	}
}