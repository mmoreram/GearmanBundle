<?php

namespace Mmoreram\GearmanBundle\Service;

use Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class GearmanCacheWrapper implements CacheClearerInterface, CacheWarmerInterface
{

    private GearmanParser $gearmanParser;
    private CacheInterface $cache;
    private string $cacheId;
    private array $workerCollection;

    public function __construct(
        GearmanParser $gearmanParser,
        CacheInterface $cache,
        string $cacheId
    ) {
        $this->gearmanParser = $gearmanParser;
        $this->cache = $cache;
        $this->cacheId = $cacheId;

        $this->load();
    }

    public function getWorkers(): array
    {
        return $this->workerCollection;
    }

    protected function load(): self
    {
        $gearmanParser = $this->gearmanParser;

        $this->workerCollection = $this->cache->get(
            $this->cacheId,
            function (ItemInterface $item) use ($gearmanParser) {
                $item->expiresAfter(3600 * 24);

                return $gearmanParser
                    ->load()
                    ->toArray();
            },
            1.0
        );

        return $this;
    }

    public function clear($cacheDir)
    {
        $this->cache->delete($this->cacheId);
    }

    public function warmUp($cacheDir)
    {
        $this->load();
    }

    /**
     * Checks whether this warmer is optional or not.
     *
     * Optional warmers can be ignored on certain conditions.
     *
     * A warmer should return true if the cache can be
     * generated incrementally and on-demand.
     *
     * As GearmanBundle loads cache incrementaly so is optional
     *
     * @return Boolean true if the warmer is optional, false otherwise
     */
    public function isOptional()
    {
        return true;
    }
}
