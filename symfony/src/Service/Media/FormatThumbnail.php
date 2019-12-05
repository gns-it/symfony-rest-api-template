<?php
/**
 * @author Sergey Hashimov <hashimov.sergey@gmail.com>
 */

namespace App\Service\Media;

use App\Entity\Media\Media;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Provider\MediaProviderInterface;
use Sonata\MediaBundle\Thumbnail\FormatThumbnail as SonataFormatThumbnail;

class FormatThumbnail extends SonataFormatThumbnail
{

    /**
     * {@inheritdoc}
     */
    public function generatePublicUrl(MediaProviderInterface $provider, MediaInterface $media, $format)
    {
        if (MediaProviderInterface::FORMAT_REFERENCE === $format) {
            /** @var Media $media */
            $path = $provider->getReferenceImage($media);
        } else {
            $path = "{$provider->generatePath($media)}/thumb_{$media->getProviderReference()}";
        }

        return $path;
    }

    /**
     * {@inheritdoc}
     */
    public function generatePrivateUrl(MediaProviderInterface $provider, MediaInterface $media, $format)
    {
        if (MediaProviderInterface::FORMAT_REFERENCE === $format) {
            return $provider->getReferenceImage($media);
        }

        /** @var Media $media */
        return "{$provider->generatePath($media)}/thumb_{$media->getProviderReference()}";
    }

}