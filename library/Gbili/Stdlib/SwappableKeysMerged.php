<?php
namespace Gbili\Stdlib;

/**
 * config arrays should be adaptable to convenience
 * Sometimes you want to set for example the listeners
 * for the foo, and for bar.
 * The convenient way to write this would be:
 *      ['listeners' => [
 *          'foo' => [
 *               'listener1', 
 *               'listener2'
 *           ],
 *          'bar' => [
 *               'listener1', 
 *               'listener2'
 *           ],
 *      ]
 * In another scenario, where foo has many configs other than
 * listeners, and bar has none, you may want to write it like:
 *      ['foo' => [
 *          'listeners' => [
 *               'listener1', 
 *               'listener2'
 *           ],
 *          'host' => [
 *               'somedomain.com', 
 *           ],
 *          'pass' => [
 *               'password', 
 *           ],
 *      ]
 * This class is meant to allow this
 * Pass in some config, and try to fetch the value of
 * some config keys in either order: 
 *     [listeners][foo]
 *     [foo][listeners]
 */
class SwappableKeysMerged
{
    /**
     * Try to find a value in config using the swappablekeys 
     * in any order
     * @param array $swappableKeys keys that are nested in any
     * order in the param $in
     * @param array $config the config where the keys have to be found
     * and whose value will be returned
     * @return mixed:config value or default if not found
     */
    public function get(array $swappableKeys, $config, $notFound)
    {
        $foundConf = [];
        foreach ($swappableKeys as $k => $key) {
            if (isset($config[$key])) {
                $passSk = $swappableKeys;
                unset($passSk[$k]);
                $content = $this->get($passSk, $config[$key], 'inner_not_found');
                unset($config[$key]);
                if ($content !== 'inner_not_found') {
                    if (!is_array($content)) {
                        $content = [$content];
                    }
                    $foundConf = array_merge_recursive($content, $foundConf);
                }
            }
        }
        if (empty($foundConf)) { // no foundConf was set
            if (empty($swappableKeys) && (!empty($config))) {
                $foundConf = $config; //conf contains the actual result
            } else { //if not in leaf or conf has nothing
                $foundConf = $notFound;
            }

        }
        return $foundConf;
    }
}
