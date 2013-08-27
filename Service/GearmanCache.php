<?php

namespace Mmoreram\GearmanBundle\Service;

use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Gearman cache class
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
class GearmanCache extends ContainerAware
{

    /**
     * loadCache method. While data is not setted, empty array is loaded.
     * Sets dir and checks it
     */
    public function loadCache()
    {
        $this->getPath();
        $this->checkDir();
        $this->data = $this->__toCache(array());
    }


    /**
     * Cache dir to save all Gearman cached files.
     *
     * @var string
     */
    private $cachedir;


    /**
     * Cache filename
     *
     * @var string
     */
    private $cachefile = 'gearman.cache.php';


    /**
     * Data loaded and serialized ready for save into cache
     *
     * @var string
     */
    private $data = '';


    /**
     * Checks is cache dir is created.
     * Otherwise, it creates it
     * Returns self object
     *
     * @return GearmanCache
     */
    public function checkDir()
    {
        if (!is_dir($this->cachedir)) {
            mkdir($this->cachedir, 0777, true);
        }

        return $this;
    }


    /**
     * Remove gearman cache file
     * Returns self object
     *
     * @return GearmanCache
     */
    public function emptyCache()
    {
        if (is_dir($this->cachedir . $this->cachefile)) {
            unlink($this->cachedir . $this->cachefile);
        }

        return $this;
    }


    /**
     * Returns if cache file exists
     *
     * @return boolean
     */
    public function existsCacheFile()
    {
        return is_file($this->cachedir . $this->cachefile);
    }


    /**
     * Save to cache all gearman cacheable data
     * Returns self object
     *
     * @param array $array Data to set into cache
     *
     * @return GearmanCache
     */
    public function set(Array $array)
    {
        $this->data = "<?php return unserialize('".serialize($array)."');";
        file_put_contents($this->cachedir . $this->cachefile, $this->data);

        return $this;
    }


    /**
     * Save data into cache
     * Returns self object
     *
     * @return boolean Return if saved
     */
    public function save()
    {
        return file_put_contents($this->cachedir . $this->cachefile, $this->data);
    }


    /**
     * Retrieve cache data if is loaded
     * if cache does not exist, return false
     *
     * @return Array
     */
    public function get()
    {
        if (is_file($this->cachedir . $this->cachefile)) {
            return (array) (require($this->cachedir . $this->cachefile));
        }

        return false;
    }


    /**
     * Transform Array object into cache saveable string
     *
     * @param Array $data Data to set into cache
     *
     * @return string
     */
    public function __toCache(Array $data)
    {
        return "<?php return unserialize('".serialize($data)."');";
    }


    /**
     * Return cache dir
     *
     * @return string
     */
    public function getPath()
    {
        if (null === $this->cachedir) {
            $this->cachedir = $this->container->get('kernel')->getCacheDir().'/gearman/';
        }

        return $this->cachedir;
    }
}
