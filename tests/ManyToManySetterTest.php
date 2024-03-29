<?php

namespace Test\GollumSF\EntityRelationSetter;

use Doctrine\Common\Collections\ArrayCollection;
use GollumSF\EntityRelationSetter\ManyToManySetter;
use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use PHPUnit\Framework\TestCase;


class Post {

	use ManyToManySetter;

	private $tags;

	public function __construct() {
		$this->tags = new ArrayCollection();
	}

	public function addTag(Tag $tag): self {
		return $this->manyToManyAdd($tag);
	}

	public function removeTag(Tag $tag): self {
		return $this->manyToManyRemove($tag);
	}
}

if (interface_exists('Doctrine\Persistence\Proxy')) {
	class ProxyPost extends Post implements \Doctrine\Persistence\Proxy {
		public function __load() {}
		public function __isInitialized() {}
	}
} else
if (interface_exists('Doctrine\Common\Persistence\Proxy')) {
	class ProxyPost extends Post implements \Doctrine\Common\Persistence\Proxy {
		public function __load() {}
		public function __isInitialized() {}
	}
}

class Tag {

	use ManyToManySetter;

	private $posts;

	public function __construct() {
		$this->posts = new ArrayCollection();
	}

	public function addPost(Post $post): self {
		return $this->manyToManyAdd($post);
	}

	public function removePost(Post $post): self {
		return $this->manyToManyRemove($post);
	}
}

if (interface_exists ('Doctrine\Persistence\Proxy')) {
	class ProxyTag extends Tag implements \Doctrine\Persistence\Proxy {
		public function __load() {}
		public function __isInitialized() {}
	}
} else
if (interface_exists ('Doctrine\Common\Persistence\Proxy')) {
	class ProxyTag extends Tag implements \Doctrine\Common\Persistence\Proxy {
		public function __load() {}
		public function __isInitialized() {}
	}
}

class ManyToManySetterTest extends TestCase {
	
	use ReflectionPropertyTrait;
	
	public function testAddAndRemove() {
		$post1 = new Post();
		$post2 = new ProxyPost();
		$tag1 = new Tag();
		$tag2 = new ProxyTag();

		// Add
		
		$this->assertEquals($post1->addTag($tag1), $post1);
		$this->assertEquals($this->reflectionGetValue($post1, 'tags')->getValues(), [ $tag1 ]);
		$this->assertEquals($this->reflectionGetValue($tag1, 'posts')->getValues(), [ $post1 ]);
		$this->assertEquals($this->reflectionGetValue($post2, 'tags', Post::class)->getValues(), []);
		$this->assertEquals($this->reflectionGetValue($tag2, 'posts', Tag::class)->getValues(), []);


		$this->assertEquals($post1->addTag($tag2), $post1);
		$this->assertEquals($this->reflectionGetValue($post1, 'tags')->getValues(), [ $tag1, $tag2 ]);
		$this->assertEquals($this->reflectionGetValue($tag1, 'posts')->getValues(), [ $post1 ]);
		$this->assertEquals($this->reflectionGetValue($post2, 'tags', Post::class)->getValues(), []);
		$this->assertEquals($this->reflectionGetValue($tag2, 'posts', Tag::class)->getValues(), [ $post1 ]);

		$this->assertEquals($post2->addTag($tag1), $post2);
		$this->assertEquals($this->reflectionGetValue($post1, 'tags')->getValues(), [ $tag1, $tag2 ]);
		$this->assertEquals($this->reflectionGetValue($tag1, 'posts')->getValues(), [ $post1, $post2 ]);
		$this->assertEquals($this->reflectionGetValue($post2, 'tags', Post::class)->getValues(), [ $tag1 ]);
		$this->assertEquals($this->reflectionGetValue($tag2, 'posts', Tag::class)->getValues(), [ $post1 ]);

		$this->assertEquals($post2->addTag($tag2), $post2);
		$this->assertEquals($this->reflectionGetValue($post1, 'tags')->getValues(), [ $tag1, $tag2 ]);
		$this->assertEquals($this->reflectionGetValue($tag1, 'posts')->getValues(), [ $post1, $post2 ]);
		$this->assertEquals($this->reflectionGetValue($post2, 'tags', Post::class)->getValues(), [ $tag1, $tag2 ]);
		$this->assertEquals($this->reflectionGetValue($tag2, 'posts', Tag::class)->getValues(), [ $post1, $post2 ]);

		$this->assertEquals($post2->addTag($tag2), $post2);
		$this->assertEquals($this->reflectionGetValue($post1, 'tags')->getValues(), [ $tag1, $tag2 ]);
		$this->assertEquals($this->reflectionGetValue($tag1, 'posts')->getValues(), [ $post1, $post2 ]);
		$this->assertEquals($this->reflectionGetValue($post2, 'tags', Post::class)->getValues(), [ $tag1, $tag2 ]);
		$this->assertEquals($this->reflectionGetValue($tag2, 'posts', Tag::class)->getValues(), [ $post1, $post2 ]);
		
		// Remove

		$this->assertEquals($post2->removeTag($tag2), $post2);
		$this->assertEquals($this->reflectionGetValue($post1, 'tags')->getValues(), [ $tag1, $tag2 ]);
		$this->assertEquals($this->reflectionGetValue($tag1, 'posts')->getValues(), [ $post1, $post2 ]);
		$this->assertEquals($this->reflectionGetValue($post2, 'tags', Post::class)->getValues(), [ $tag1 ]);
		$this->assertEquals($this->reflectionGetValue($tag2, 'posts', Tag::class)->getValues(), [ $post1 ]);

		$this->assertEquals($post2->removeTag($tag1), $post2);
		$this->assertEquals($this->reflectionGetValue($post1, 'tags')->getValues(), [ $tag1, $tag2 ]);
		$this->assertEquals($this->reflectionGetValue($tag1, 'posts')->getValues(), [ $post1 ]);
		$this->assertEquals($this->reflectionGetValue($post2, 'tags', Post::class)->getValues(), []);
		$this->assertEquals($this->reflectionGetValue($tag2, 'posts', Tag::class)->getValues(), [ $post1 ]);

		$this->assertEquals($post1->removeTag($tag2), $post1);
		$this->assertEquals($this->reflectionGetValue($post1, 'tags')->getValues(), [ $tag1 ]);
		$this->assertEquals($this->reflectionGetValue($tag1, 'posts')->getValues(), [ $post1 ]);
		$this->assertEquals($this->reflectionGetValue($post2, 'tags', Post::class)->getValues(), []);
		$this->assertEquals($this->reflectionGetValue($tag2, 'posts', Tag::class)->getValues(), []);

		$this->assertEquals($post1->removeTag($tag1), $post1);
		$this->assertEquals($this->reflectionGetValue($post1, 'tags')->getValues(), []);
		$this->assertEquals($this->reflectionGetValue($tag1, 'posts')->getValues(), []);
		$this->assertEquals($this->reflectionGetValue($post2, 'tags', Post::class)->getValues(), []);
		$this->assertEquals($this->reflectionGetValue($tag2, 'posts', Tag::class)->getValues(), []);

		$this->assertEquals($post1->removeTag($tag1), $post1);
		$this->assertEquals($this->reflectionGetValue($post1, 'tags')->getValues(), []);
		$this->assertEquals($this->reflectionGetValue($tag1, 'posts')->getValues(), []);
		$this->assertEquals($this->reflectionGetValue($post2, 'tags', Post::class)->getValues(), []);
		$this->assertEquals($this->reflectionGetValue($tag2, 'posts', Tag::class)->getValues(), []);
		
	}
}
