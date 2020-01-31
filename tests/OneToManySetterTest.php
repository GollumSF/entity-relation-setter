<?php
//
namespace Test\GollumSF\EntityRelationSetter;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\Proxy;
use GollumSF\EntityRelationSetter\ManyToOneSetter;
use GollumSF\EntityRelationSetter\OneToManySetter;
use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use PHPUnit\Framework\TestCase;

class Address2 {

	use ManyToOneSetter;

	private $country2;

	public function setCountry2(?Country2 $country): self {
		return $this->manyToOneSet($country);
	}
}

class Country2 {

	use OneToManySetter;

	private $address2s;

	public function __construct() {
		$this->address2s = new ArrayCollection();
	}

	public function addAddress2(Address2 $address): self {
		return $this->oneToManyAdd($address);
	}

	public function removeAddress2(Address2 $address): self {
		return $this->oneToManyRemove($address);
	}
}

class ProxyCountry2 extends Country2 implements Proxy {
	public function __load() {}
	public function __isInitialized() {}
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
		$this->assertEquals($this->reflectionGetValue($proxyCountry, 'address2s')->getValues(), [ $address1 ]);
		$this->assertEquals($this->reflectionGetValue($address1, 'country2'), $proxyCountry);

		$this->assertEquals($proxyCountry->removeAddress2($address1), $proxyCountry);
		$this->assertEquals($this->reflectionGetValue($proxyCountry, 'address2s')->getValues(), []);
		$this->assertEquals($this->reflectionGetValue($address1, 'country2'), null);
		
	}
}