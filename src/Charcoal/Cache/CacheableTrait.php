<?php

namespace Charcoal\Cache;

// Dependencies from `PHP`
use \InvalidArgumentException as InvalidArgumentException;

/**
* A default implementation, as trait, of `the CachableInterface`.
*
* There is one abstract method: `cache_data()`
*/
trait CacheableTrait
{
    /**
    * @var CacheInterface $cache
    */
    private $cache;
    /**
    * @var string $cache_key
    */
    private $cache_key;
    /**
    * @var integer $cache_ttl
    */
    private $cache_ttl;
    /**
    * @var boolean $use_cache
    */
    private $use_cache = true;

    /**
    * Set the object's Cache
    *
    * @param CacheInterface $cache
    * @return CacheableInterface Chainable
    */
    public function set_cache(CacheInterface $cache)
    {
        $this->cache = $cache;
        return $this;
    }

    /**
    * Get the object's Cache
    *
    * @return CacheInterface
    */
    public function cache()
    {
        if ($this->cache === null) {
            $this->cache = $this->create_cache();
        }
        return $this->cache;
    }

    /**
    * @param array $data Optional
    * @return CacheInterface
    */
    public function create_cache(array $data = null)
    {
        $cache = CacheFactory::instance()->get('memcache');
        if (is_array($data)) {
            $cache->set_data($data);
        }
        return $cache;
    }

    /**
    * Set the object's cache key
    *
    * @param string $cache_key
    * @throws InvalidArgumentException if cache key is not a string
    * @return CacheableInterface Chainable
    */
    public function set_cache_key($cache_key)
    {
        if (!is_string($cache_key)) {
            throw new InvalidArgumentException('Cache key must be a string.');
        }
        $this->cache_key = $cache_key;
        return $this;
    }

    /**
    * Get the object's cache key
    *
    * @return string
    */
    public function cache_key()
    {
        if ($this->cache_key === null) {
            $this->cache_key = $this->generate_cache_key();
        }
        return $this->cache_key;
    }

    /**
    * @return string
    */
    protected function generate_cache_key()
    {
        return '';
    }

    /**
    * Set the object's custom Time-To-Live in cache
    *
    * @param integer $ttl
    * @return CacheableInterface Chainable
    */
    public function set_cache_ttl($ttl)
    {
        $this->cache_ttl = $ttl;
        return $this;
    }

    /**
    * @return integer
    */
    public function cache_ttl()
    {
        if ($this->cache_ttl === null) {
            $this->cache_ttl = $this->cache()->config()->default_ttl();
        }
        return $this->cache_ttl;
    }

    /**
    * @param boolean $use_cache
    * @throws InvalidArgumentException if use_cache is not a boolean
    * @return CacheableInterface Chainable
    */
    public function set_use_cache($use_cache)
    {
        if (!is_bool($use_cache)) {
            throw new InvalidArgumentException('Use cache must be a boolean.');
        }
        $this->use_cache = $use_cache;
        return $this;
    }

    /**
    * @return boolean
    */
    public function use_cache()
    {
        return ($this->use_cache && $this->cache_key() && $this->cache()->enabled());
    }

    /**
    * @return mixed
    */
    abstract public function cache_data();

    /**
    * @param mixed   $data
    * @param integer $ttl
    * @return boolean
    */
    public function cache_store($data = null, $ttl = 0)
    {
        if ($this->use_cache() === false) {
            return false;
        }
        $key = $this->cache_key();
        if ($data === null) {
            $data = $this->cache_data();
        }
        $ttl = (($ttl > 0) ? $ttl : $this->cache_ttl());

        return $this->cache()->store($key, $data, $ttl);
    }

    /**
    * @return mixed
    */
    public function cache_load()
    {
        if ($this->use_cache() === false) {
            return false;
        }
        $key = $this->cache_key();
        return $this->cache()->fetch($key);
    }
}
