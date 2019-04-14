<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Doctrine\QueryBuilder;

use Zend\Hydrator\HydratorPluginManagerInterface;

/**
 * Alias ZF\Hal\Extractor\EntityExtractor to the appropriate class based on
 * which version of zend-hydrator we detect. HydratorPluginManagerInterface
 * is added in v3.
 */
if (interface_exists(HydratorPluginManagerInterface::class, true)) {
    class_alias(Hydrator\Strategy\CollectionLinkHydratorV3::class, Hydrator\Strategy\CollectionLink::class, true);
} else {
    class_alias(Hydrator\Strategy\CollectionLinkHydratorV2::class, Hydrator\Strategy\CollectionLink::class, true);
}
