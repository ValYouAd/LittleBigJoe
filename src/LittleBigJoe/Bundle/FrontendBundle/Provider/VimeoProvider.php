<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Provider;

use LittleBigJoe\Bundle\CoreBundle\Entity\ProjectVideo;

class VimeoProvider extends BaseProvider
{
    /**
     * @param $videoUrl
     * @return Video|null
     */
    public function getVideo($videoUrl)
    {
        $videoId = $this->getVideoId($videoUrl);
        if ($videoId) {
            $url = sprintf('http://vimeo.com/api/oembed.json?url=http://vimeo.com/%s', $videoId);

            $video = new ProjectVideo();
            try {
                $metadata = $this->loadMetadata($url);
                $video->setProviderVideoId($videoId);
                $video->setProviderMetadata($metadata);

                return $video;
            } catch (\RuntimeException $e) {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * @param $videoId
     * @return bool
     */
    public function getVideoId($videoId)
    {
        if (preg_match("/vimeo\.com\/(\d+)/", $videoId, $matches)) {
            return $matches[1];
        } else
            return false;
    }

    /**
     * @param $template
     * @param $video
     * @param null $width
     * @param bool $hd
     * @return mixed
     */
    public function getPlayer($video, $width = null, $hd = true, $autoPlay = true)
    {
        $videoParameters = array();
        if ($hd)
            $videoParameters[] = 'forcedQuality=hd720';
        if ($autoPlay)
            $videoParameters[] = 'autoplay=1';

        $parameters = array(
            'video' => $video,
            'videoParameters' => implode('&', $videoParameters),
        );
        $parameters = array_merge($parameters, $this->getPlayerWidth($width, $video));

        return $this->getTemplating()->render('HeroVideoBundle:Provider:player_vimeo.html.twig', $parameters);
    }

    /**
     * @param $video
     * @return string
     */
    public function getEmbedUrl($video)
    {
        return sprintf('http://vimeo.com/moogaloop.swf?clip_id=%s', $video->getProviderVideoId());
    }
} 