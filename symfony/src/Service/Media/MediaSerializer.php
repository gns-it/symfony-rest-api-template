<?php

namespace App\Service\Media;

use App\Entity\Media\Media;
use Sonata\MediaBundle\Provider\MediaProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MediaSerializer
{
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param Media $media
     * @return array
     */
    public function serializeAdditionalFields(Media $media)
    {
        $result = [
            'href' => $this->getMediaHref($media),
            'href_thumbnail' => $this->getMediaHref($media, MediaProviderInterface::FORMAT_ADMIN),
        ];
        $mediaFormat = $this->getMediaFormatsWithHref($media);
        if (!empty($mediaFormat)) {
            $result['formats'] = $mediaFormat;
        }

        return $result;
    }

    /**
     * @param Media $media
     * @param string $format
     * @return string
     */
    public function getMediaHref(Media $media, $format = MediaProviderInterface::FORMAT_REFERENCE)
    {
        $provider = $this->container->get($media->getProviderName());
        $reference = $provider->generatePublicUrl($media, $format);
        $href = getenv('API_HOST').$reference;

        return $href;
    }

    /**
     * @param Media $media
     * @param string $format
     * @return string
     */
    public function getMediaThumbnailHref(Media $media = null, $format = MediaProviderInterface::FORMAT_ADMIN)
    {
        if (null === $media){
            return null;
        }
        $provider = $this->container->get($media->getProviderName());
        $reference = $provider->generatePublicUrl($media, $format);
        $href = getenv('API_HOST').$reference;

        return $href;
    }

    /**
     * @param Media $media
     * @return array
     */
    private function getMediaFormatsWithHref(Media $media)
    {
        $provider = $this->container->get($media->getProviderName());
        $mediaContext = $media->getContext();
        $formats = [];
        foreach ($provider->getFormats() as $providerFormat => $settings) {
            if (0 === strpos($providerFormat, $mediaContext)) {
                $key = substr(str_replace($mediaContext, '', $providerFormat), 1);
                $href = $this->getMediaHref($media, $providerFormat);
                $formats[$key] = $href;
            }
        }

        return $formats;
    }
}