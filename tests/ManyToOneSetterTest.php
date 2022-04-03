<?php
//
namespace Test\GollumSF\EntityRelationSetter;

use Doctrine\Common\Collections\ArrayCollection;
use GollumSF\EntityRelationSetter\ManyToOneSetter;
use GollumSF\EntityRelationSetter\OneToManySetter;
use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use PHPUnit\Framework\TestCase;

class Address {

	use ManyToOneSetter;

	private $country;

	private $town;
	
	/**
	 * @param ?Country $country
	 * @return $this
	 */
	public function setCountry($country): self {
		return $this->manyToOneSet($country);
	}
	
	/**
	 * @param ?Town $town
	 * @return $this
	 */
	public function setTown( $town): self {
		return $this->manyToOneSet($town);
	}
}

if (interface_exists ('Doctrine\Persistence\Proxy')) {
	class ProxyAddress extends Address implements \Doctrine\Persistence\Proxy {
		public function __load() {}
		public function __isInitialized() {}
	}
} else
if (interface_exists ('Doctrine\Common\Persistence\Proxy')) {
	class ProxyAddress extends Address implements \Doctrine\Common\Persistence\Proxy {
		public function __load() {}
		public function __isInitialized() {}
	}
}

class Country {

	use OneToManySetter;

	private $addresses;

	public function __construct() {
		$this->addresses = new ArrayCollection();
	}
	
	/**
	 * @param Address $address
	 * @return $this
	 */
	public function addAddress($address): self {
		return $this->oneToManyAdd($address);
	}
	
	/**
	 * @param Address $address
	 * @return $this
	 */
	public function removeAddress($address): self {
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
		$this->assertEquals($this->reflectionGetValue($address2, 'country', Address::class), null);
		$this->assertEquals($address2->setCountry($country), $address2);
		$this->assertEquals($this->reflectionGetValue($country, 'addresses')->getValues(), [ $address1, $address2 ]);
		$this->assertEquals($this->reflectionGetValue($address1, 'country'), $country);
		$this->assertEquals($this->reflectionGetValue($address2, 'country', Address::class), $country);
		$this->assertEquals($address2->setCountry($country), $address2);
		$this->assertEquals($this->reflectionGetValue($country, 'addresses')->getValues(), [ $address1, $address2 ]);
		$this->assertEquals($this->reflectionGetValue($address1, 'country'), $country);
		$this->assertEquals($this->reflectionGetValue($address2, 'country', Address::class), $country);
		$this->assertEquals($address2->setCountry(null), $address2);
		$this->assertEquals($this->reflectionGetValue($country, 'addresses')->getValues(), [ $address1]);
		$this->assertEquals($this->reflectionGetValue($address1, 'country'), $country);
		$this->assertEquals($this->reflectionGetValue($address2, 'country', Address::class), null);
		$this->assertEquals($address2->setCountry(null), $address2);
		$this->assertEquals($this->reflectionGetValue($country, 'addresses')->getValues(), [ $address1 ]);
		$this->assertEquals($this->reflectionGetValue($address1, 'country'), $country);
		$this->assertEquals($this->reflectionGetValue($address2, 'country', Address::class), null);
	}

	public function testSetExceptionAdd() {
		$address = new Address();
		$town = new Town();

		$this->expectException(\LogicException::class);
		$address->setTown($town);
	}

	public function testSetExceptionRemove() {
		$address = new Address();
		$town = new Town();
		
		$this->reflectionSetValue($address, 'town', $town);
		
		$this->expectException(\LogicException::class);
		$address->setTown(null);
	}
}
