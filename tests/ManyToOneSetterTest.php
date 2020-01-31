<?php
//
namespace Test\GollumSF\EntityRelationSetter;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\Proxy;
use GollumSF\EntityRelationSetter\ManyToOneSetter;
use GollumSF\EntityRelationSetter\OneToManySetter;
use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use PHPUnit\Framework\TestCase;

class Address {

	use ManyToOneSetter;

	private $country;

	private $town;

	public function setCountry(?Country $country): self {
		return $this->manyToOneSet($country);
	}

	public function setTown(?Town $town): self {
		return $this->manyToOneSet($town);
	}
}

class ProxyAddress extends Address implements Proxy {
	public function __load() {}
	public function __isInitialized() {}
}

class Country {

	use OneToManySetter;

	private $addresses;

	public function __construct() {
		$this->addresses = new ArrayCollection();
	}

	public function addAddress(Address $address): self {
		return $this->oneToManyAdd($address);
	}

	public function removeAddress(Address $address): self {
		return $this->oneToManyRemove($address);
	}
}

class Town {
}

class ManyToOneSetterTest extends TestCase {
	
	use ReflectionPropertyTrait;
	
	public function testSet() {
		$address1 = new Address();
		$address2 = new ProxyAddress();
		$country = new Country();

		$this->assertEquals($address1->setCountry($country), $address1);
		$this->assertEquals($this->reflectionGetValue($country, 'addresses')->getValues(), [ $address1 ]);
		$this->assertEquals($this->reflectionGetValue($address1, 'country'), $country);
		$this->assertEquals($this->reflectionGetValue($address2, 'country'), null);
		$this->assertEquals($address2->setCountry($country), $address2);
		$this->assertEquals($this->reflectionGetValue($country, 'addresses')->getValues(), [ $address1, $address2 ]);
		$this->assertEquals($this->reflectionGetValue($address1, 'country'), $country);
		$this->assertEquals($this->reflectionGetValue($address2, 'country'), $country);
		$this->assertEquals($address2->setCountry($country), $address2);
		$this->assertEquals($this->reflectionGetValue($country, 'addresses')->getValues(), [ $address1, $address2 ]);
		$this->assertEquals($this->reflectionGetValue($address1, 'country'), $country);
		$this->assertEquals($this->reflectionGetValue($address2, 'country'), $country);
		$this->assertEquals($address2->setCountry(null), $address2);
		$this->assertEquals($this->reflectionGetValue($country, 'addresses')->getValues(), [ $address1]);
		$this->assertEquals($this->reflectionGetValue($address1, 'country'), $country);
		$this->assertEquals($this->reflectionGetValue($address2, 'country'), null);
		$this->assertEquals($address2->setCountry(null), $address2);
		$this->assertEquals($this->reflectionGetValue($country, 'addresses')->getValues(), [ $address1 ]);
		$this->assertEquals($this->reflectionGetValue($address1, 'country'), $country);
		$this->assertEquals($this->reflectionGetValue($address2, 'country'), null);
	}

	public function testSetExceprionAdd() {
		$address = new Address();
		$town = new Town();

		$this->expectException(\LogicException::class);
		$address->setTown($town);
	}

	public function testSetExceprionRemove() {
		$address = new Address();
		$town = new Town();
		
		$this->reflectionSetValue($address, 'town', $town);
		
		$this->expectException(\LogicException::class);
		$address->setTown(null);
	}
	
//	
////	protected function oneToManyAdd($value, string $fieldName = null, $targetName = null): self {
////		
////		if ($fieldName === null) {
////			$trace = debug_backtrace();
////			$calledMethod = $trace[1]['function'];
////			$fieldName = lcfirst(substr($calledMethod, 3));
////			$fieldName = Inflector::pluralize($fieldName);
////		}
////		
////		if ($targetName === null) {
////			$class = get_called_class();
////			if ($class instanceof Proxy) {
////				$class = get_parent_class($class);
////			}
////			$targetName = $class;
////			if (($index = strrpos($targetName, '\\')) !== false) {
////				$targetName = substr($targetName, $index + 1);
////			}
////			$targetName = lcfirst($targetName);
////		}
////		
////		$methodSet = 'set'.ucfirst($targetName);
////		if (!$this->$fieldName->contains($value)) {
////			$this->$fieldName->add($value);
////			$value->$methodSet($this);
////		}
////		return $this;
////	}
////	
////	protected function oneToManyRemove($value, string $fieldName = null, $targetName = null): self {
////		
////		if ($fieldName === null) {
////			$trace = debug_backtrace();
////			$calledMethod = $trace[1]['function'];
////			$fieldName = lcfirst(substr($calledMethod, 6));
////			$fieldName = Inflector::pluralize($fieldName);
////		}
////		
////		if ($targetName === null) {
////			$class = get_called_class();
////			if ($class instanceof Proxy) {
////				$class = get_parent_class($class);
////			}
////			$targetName = $class;
////			if (($index = strrpos($targetName, '\\')) !== false) {
////				$targetName = substr($targetName, $index + 1);
////			}
////			$targetName = lcfirst($targetName);
////		}
////		
////		$methodSet = 'set'.ucfirst($targetName);
////		if ($this->$fieldName->contains($value)) {
////			$this->$fieldName->removeElement($value);
////			$value->$methodSet(null);
////		}
////		return $this;
////	}
}