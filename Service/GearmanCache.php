<?php

namespace Mmoreram\GearmanBundle\Service;

use Symfony\Component\HttpKernel\Kernel;

/**
 * Gearman cache class
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
class GearmanCache
{

    /**
     * @var array
     *
     * Data loaded from cache
     */
    private $data;


    /**
     * @var string
     *
     * Cache filename
     */
    private $cacheFile;


    /**
     * @var string
     *
     * Cachedir
     */
    private $cacheDir;


    /**
     * Construct method
     *
     * @param string $cacheFilename Cache filename
     */
    public function __construct(Kernel $kernel, $cacheFilename)
    {
        $this->cacheDir = $kernel->getCacheDir();
        $this->cacheFile = $this->cacheDir . $cacheFilename;
    }



    /**
     * loadCache method. While data is not setted, empty array is loaded.
     * Sets dir and checks it
     */
    public function load()
    {
        if ($this->existsCacheFile()) {

            $this->data = return (array) (require($this->cacheFile));
        }
    }


    /**
     * Returns if cache file exists
     *
     * @return boolean
     */
    public function existsCacheFile()
    {
        return is_file($this->cacheFile);
    }


    /**
     * Remove gearman cache file
     * Returns self object
     *
     * @return GearmanCache
     */
    public function empty()
    {
        if (is_dir($this->cacheFile)) {

            unlink($this->cacheFile);
        }

        return $this;
    }


    /**
     * Save to cache all gearman cacheable data
     * Returns self object
     *
     * @param array $array Data to set into cache
     *
     * @return GearmanCache
     */
    public function set(Array $data)
    {
        $this->data = "<?php return unserialize('" . serialize($data) . "');";
        file_put_contents($this->cacheFile, $this->data);

        return $this;
    }


    /**
     * Get data loaded
     *
     * @return array Data
     */
    public function get()
    {
        return $this->data;
    }
}
