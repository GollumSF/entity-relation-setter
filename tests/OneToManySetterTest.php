<?php
//
namespace Test\GollumSF\EntityRelationSetter;

use Doctrine\Common\Collections\ArrayCollection;
use GollumSF\EntityRelationSetter\ManyToOneSetter;
use GollumSF\EntityRelationSetter\OneToManySetter;
use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use PHPUnit\Framework\TestCase;

class Address2 {

	use ManyToOneSetter;

	private $country2;
	
	/**
	 * @param ?Country2 $country
	 * @return $this
	 */
	public function setCountry2($country): self {
		return $this->manyToOneSet($country);
	}
}

class Country2 {

	use OneToManySetter;

	private $address2s;

	public function __construct() {
		$this->address2s = new ArrayCollection();
	}
	
	/**
	 * @param Address2 $address
	 * @return $this
	 */
	public function addAddress2($address): self {
		return $this->oneToManyAdd($address);
	}
	
	/**
	 * @param Address2 $address
	 * @return $this
	 */
	public function removeAddress2($address): self {
		return $this->oneToManyRemove($address);
	}
}

if (interface_exists ('Doctrine\Persistence\Proxy')) {
	class ProxyCountry2 extends Country2 implements \Doctrine\Persistence\Proxy {
		public function __load() {}
		public function __isInitialized() {}
	}
} else
if (interface_exists ('Doctrine\Common\Persistence\Proxy')) {
	class ProxyCountry2 extends Country2 implements \Doctrine\Common\Persistence\Proxy {
		public function __load() {}
		public function __isInitialized() {}
	}
}

class OneToManySetterTest extends TestCase {
	
	use ReflectionPropertyTrait;
	
	public function testSet() {
		$address1 = new Address2();
		$address2 = new Address2();
		$country = new Country2();
		$proxyCountry = new ProxyCountry2();

		$this->assertEquals($country->addAddress2($address1), $country);
		$this->assertEquals($this->reflectionGetValue($country, 'address2s')->getValues(), [ $address1 ]);
		$this->assertEquals($this->reflectionGetValue($address1, 'country2'), $country);
		$this->assertEquals($this->reflectionGetValue($address2, 'country2'), null);
		$this->assertEquals($country->addAddress2($address2), $country);
		$this->assertEquals($this->reflectionGetValue($country, 'address2s')->getValues(), [ $address1, $address2 ]);
		$this->assertEquals($this->reflectionGetValue($address1, 'country2'), $country);
		$this->assertEquals($this->reflectionGetValue($address2, 'country2'), $country);
		$this->assertEquals($country->addAddress2($address2), $country);
		$this->assertEquals($this->reflectionGetValue($country, 'address2s')->getValues(), [ $address1, $address2 ]);
		$this->assertEquals($this->reflectionGetValue($address1, 'country2'), $country);
		$this->assertEquals($this->reflectionGetValue($address2, 'country2'), $country);
		$this->assertEquals($country->removeAddress2($address2), $country);
		$this->assertEquals($this->reflectionGetValue($country, 'address2s')->getValues(), [ $address1 ]);
		$this->assertEquals($this->reflectionGetValue($address1, 'country2'), $country);
		$this->assertEquals($this->reflectionGetValue($address2, 'country2'), null);
		$this->assertEquals($country->removeAddress2($address2), $country);
		$this->assertEquals($this->reflectionGetValue($country, 'address2s')->getValues(), [ $address1 ]);
		$this->assertEquals($this->reflectionGetValue($address1, 'country2'), $country);
		$this->assertEquals($this->reflectionGetValue($address2, 'country2'), null);

		// Proxy

		$this->assertEquals($proxyCountry->addAddress2($address1), $proxyCountry);
		$this->assertEquals($this->reflectionGetValue($country, 'address2s')->getValues(), []);
		$this->assertEquals($this->reflectionGetValue($proxyCountry, 'address2s', Country2::class)->getValues(), [ $address1 ]);
		$this->assertEquals($this->reflectionGetValue($address1, 'country2'), $proxyCountry);

		$this->assertEquals($proxyCountry->removeAddress2($address1), $proxyCountry);
		$this->assertEquals($this->reflectionGetValue($proxyCountry, 'address2s', Country2::class)->getValues(), []);
		$this->assertEquals($this->reflectionGetValue($address1, 'country2'), null);
		
	}
}
