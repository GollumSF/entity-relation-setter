<?php

namespace GollumSF\EntityRelationSetter;

use Doctrine\Common\Persistence\Proxy;

trait OneToOneSetter {
	
	protected function oneToOneSet($value, ?string $fieldName = null, ?string $targetName = null): self {
		
		if ($fieldName === null) {
			$trace = debug_backtrace();
			$calledMethod = $trace[1]['function'];
			$fieldName = lcfirst(substr($calledMethod, 3));
		}
		
		if ($targetName === null) {
			$class = get_called_class();
			if ($class instanceof Proxy) {
				$class = get_parent_class($class);
			}
			$targetName = $class;
			if (($index = strrpos($targetName, '\\')) !== false) {
				$targetName = substr($targetName, $index + 1);
			}
			$targetName = lcfirst($targetName);
		}
		
		$methodSet = 'set'.ucfirst($targetName);
		
		$diff = $this->$fieldName !== $value;
		if ($diff && $this->$fieldName) {
			if (!method_exists($this->$fieldName, $methodSet)) {
				throw new \LogicException(sprintf('Method %s not exist or not public on class %s.', $methodSet, get_class($this->$fieldName)));
			}
			$this->purchaseAddress->$methodSet(null);
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