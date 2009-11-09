<?php

namespace lithium\storage\cache\adapters;

/**
 * A minimal in-memory cache.
 *
 * This Memory adapter provides basic support for `write`, `read`, `delete`
 * and `clear` cache functionality, as well as allowing the first four
 * methods to be filtered as per the Lithium filtering system.
 *
 * This cache adapter does not implement any expiry-based cache invalidation
 * logic, as the cached data will only persist for the lifetime of the current request.
 *
 * As a result, this cache adapter is best suited for generic memoization of data, and
 * should not be used for for anything that must persist longer than the current
 * request cycle.
 */
class Memory extends \lithium\core\Object {

	/**
	 * Array used to store cached data by this adapter
	 *
	 * @var array
	 */
	protected $_cache = array();

	/**
	 * Magic method to provide an accessor (getter) to protected class variables.
	 *
	 * @param  string $variable The variable requested
	 * @return mixed Variable if it exists, null otherwise.
	 */
	public function __get($variable) {
		if (isset($this->{"_$variable"})) {
			return $this->{"_$variable"};
		}
	}

	/**
	 * Read value(s) from the cache
	 *
	 * @param string $key        The key to uniquely identify the cached item
	 * @param object $conditions Conditions under which the operation should proceed
	 * @return mixed Cached value if successful, false otherwise
	 * @todo Refactor to use RES_NOTFOUND for return value checks
	 */
	public function read($key, $conditions = null) {
		$cache =& $this->_cache;

		return function($self, $params, $chain) use (&$cache) {
			extract($params);
			return isset($cache[$key]) ? $cache[$key] : null;
		};
	}

	/**
	 * Write value(s) to the cache
	 *
	 * @param string $key        The key to uniquely identify the cached item
	 * @param mixed  $value      The value to be cached
	 * @param object $conditions Conditions under which the operation should proceed
	 * @return boolean True on successful write, false otherwise
	 */
	public function write($key, $data, $conditions = null) {
		$cache =& $this->_cache;

		return function($self, $params, $chain) use (&$cache) {
			extract($params);
			return (bool)($cache[$key] = $data);
		};
	}

	/**
	 * Delete value from the cache
	 *
	 * @param string $key        The key to uniquely identify the cached item
	 * @param object $conditions Conditions under which the operation should proceed
	 * @return mixed True on successful delete, false otherwise
	 */
	public function delete($key, $conditions = null) {
		$cache =& $this->_cache;

		return function($self, $params, $chain) use (&$cache) {
			extract($params);
			if (isset($cache[$key])) {
				unset($cache[$key]);
				return true;
			} else {
				return false;
			}
		};
	}

	/**
	 * Clears user-space cache
	 *
	 * @return mixed True on successful clear, false otherwise
	 */
	public function clear() {
		foreach ($this->_cache as $key => &$value) {
			unset($this->_cache[$key]);
		}
		return true;
	}

	/**
	 * This adapter is always enabled, as it has no external dependencies.
	 *
	 * @return boolean True
	 */
	public function enabled() {
		return true;
	}

	/**
	 * Garbage collection (GC) is not enabled for this adapter
	 *
	 * @return boolean False
	 */
	public function clean() {
		return false;
	}

}
?>