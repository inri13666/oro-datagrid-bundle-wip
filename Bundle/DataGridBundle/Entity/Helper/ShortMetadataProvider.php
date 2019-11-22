<?php

namespace Oro\Bundle\DataGridBundle\Entity\Helper;

use Doctrine\Common\Persistence\Mapping\AbstractClassMetadataFactory;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Provides short form of metadata that can be used to check whether a manageable entity
 * is a final entity or a mapped superclass.
 * @see \Oro\Bundle\DataGridBundle\Entity\Helper\ShortClassMetadata
 */
class ShortMetadataProvider
{
    private const ALL_SHORT_METADATA_CACHE_KEY = 'oro_entity.all_short_metadata';

    /** @var array */
    private $metadataCache;

    /**
     * Gets short form of metadata for all entities registered in a given entity manager.
     *
     * @param ObjectManager $manager        The entity manager
     * @param bool          $throwException Whether to throw exception in case if metadata cannot be retrieved
     *
     * @return ShortClassMetadata[]
     */
    public function getAllShortMetadata(ObjectManager $manager, $throwException = true)
    {
        if (null === $this->metadataCache) {
            $metadataFactory = $manager->getMetadataFactory();
            $cacheDriver = $metadataFactory instanceof AbstractClassMetadataFactory
                ? $metadataFactory->getCacheDriver()
                : null;
            if ($cacheDriver) {
                $metadataCache = $cacheDriver->fetch(static::ALL_SHORT_METADATA_CACHE_KEY);
                if (false === $metadataCache) {
                    $metadataCache = $this->loadAllShortMetadata($manager, $throwException);
                    if (null !== $metadataCache) {
                        $cacheDriver->save(static::ALL_SHORT_METADATA_CACHE_KEY, $metadataCache);
                    }
                }
                $this->metadataCache = $metadataCache;
            } else {
                $this->metadataCache = $this->loadAllShortMetadata($manager, $throwException);
            }
        }

        return $this->metadataCache ?? [];
    }

    /**
     * @param ObjectManager $manager
     * @param bool          $throwException
     *
     * @return ShortClassMetadata[]|null
     */
    private function loadAllShortMetadata(ObjectManager $manager, $throwException)
    {
        if ($throwException) {
            $allMetadata = $manager->getMetadataFactory()->getAllMetadata();
        } else {
            try {
                $allMetadata = $manager->getMetadataFactory()->getAllMetadata();
            } catch (\Exception $e) {
                return null;
            }
        }

        $result = [];
        foreach ($allMetadata as $metadata) {
            $shortMetadata = new ShortClassMetadata($metadata->getName());
            if ($metadata instanceof ClassMetadata && $metadata->isMappedSuperclass) {
                $shortMetadata->isMappedSuperclass = true;
            }
            $result[] = $shortMetadata;
        }

        return $result;
    }
}
