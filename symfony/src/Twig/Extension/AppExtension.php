<?php
/**
 *  * Created by PhpStorm.
 * User: sergey_h
 * Date: 03.12.2019
 * Time: 11:15
 */

namespace App\Twig\Extension;


use App\Entity\Media\Media;
use App\Service\Media\MediaManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    /**
     * @var MediaManager
     */
    private $mediaManager;

    public function __construct(MediaManager $mediaManager)
    {
        $this->mediaManager = $mediaManager;
    }

    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('file_exists', [$this, 'fileExists']),
        ];
    }

    /**
     * @param Media $media
     * @return bool
     */
    public function fileExists(Media $media)
    {
        return $this->mediaManager->fileExists($media);
    }
}