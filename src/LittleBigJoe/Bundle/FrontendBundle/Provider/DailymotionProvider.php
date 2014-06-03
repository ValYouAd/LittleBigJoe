<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Provider;

use LittleBigJoe\Bundle\CoreBundle\Entity\ProjectVideo;

class DailymotionProvider extends BaseProvider
{
    /**
     * @param $videoUrl
     * @return Video|null
     */
    public function getVideo($videoUrl)
    {
        $videoId = $this->getVideoId($videoUrl);
        if ($videoId) {
            $url = sprintf('http://www.dailymotion.com/services/oembed?url=http://www.dailymotion.com/video/%s&format=json', $videoId);

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
        if (preg_match("/www.dailymotion.com\/video\/([0-9a-zA-Z]*)_/", $videoId, $matches)) {
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
            $videoParameters[] = 'autoPlay=1';

        $parameters = array(
            'video' => $video,
            'videoParameters' => implode('&', $videoParameters),
        );
        $parameters = array_merge($parameters, $this->getPlayerWidth($width, $video));

        return $this->getTemplating()->render('LittleBigJoeFrontendBundle:Provider:player_dailymotion.html.twig', $parameters);
    }

    /**
     * @param $video
     * @return string
     */
    public function getEmbedUrl($video)
    {
        return sprintf('http://www.dailymotion.com/swf/video/%s?autoPlay=1', $video->getProviderVideoId());
    }
} 