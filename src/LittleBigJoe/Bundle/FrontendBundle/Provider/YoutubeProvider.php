<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Provider;

use LittleBigJoe\Bundle\CoreBundle\Entity\ProjectVideo;

class YoutubeProvider extends BaseProvider
{
    const PROVIDER_NAME = 'YouTube';

    /**
     * @param $videoUrl
     * @return Video|null
     */
    public function getVideo($videoUrl)
    {
        $videoId = $this->getVideoId($videoUrl);
        if ($videoId) {
            $yt = new \Zend_Gdata_YouTube();
            $yt->setMajorProtocolVersion(2);
            return $this->getVideoFromVideoEntry($yt->getVideoEntry($videoId));
        } else {
            return null;
        }
    }
	
	/**
     * Get Video entity from VideoEntry
     *
     * @param $videoEntry
     */
    public function getVideoFromVideoEntry(\Zend_Gdata_YouTube_VideoEntry $videoEntry, $existingVideoIds = null)
    {
        if ($existingVideoIds) {
            foreach($existingVideoIds as $videoId) {
                if ($videoEntry->getVideoId() == $videoId['providerVideoId'])
                    return null;
            }
        }
        if (!$videoEntry->getVideoViewCount())
            return null;

        $video = new ProjectVideo();
        $video->setName($videoEntry->getVideoTitle())
            ->setDescription($videoEntry->getVideoDescription())
            ->setProviderName('YouTube')
            ->setProviderVideoId($videoEntry->getVideoId())
            ->setViews($videoEntry->getVideoViewCount())
            ->setLength($videoEntry->getVideoDuration())
            ->setVisible(false);

        $videoThumbnails = $videoEntry->getVideoThumbnails();
        $width = 0;
        foreach($videoThumbnails as $videoThumbnail) {
            if ($videoThumbnail['width'] > $width) {
                $width = $videoThumbnail['width'];
                $video->setThumbWidth($width);
                $video->setThumbHeight($videoThumbnail['height']);
                $video->setThumbUrl($videoThumbnail['url']);
            }
        }

        return $video;
    }

    /**
     * @param $videoId
     * @return bool
     */
    public function getVideoId($videoId)
    {
        if (preg_match("/(?<=v(\=|\/))([-a-zA-Z0-9_]+)|(?<=youtu\.be\/)([-a-zA-Z0-9_]+)/i", $videoId, $matches)) {
            return $matches[2];
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
            $videoParameters[] = 'vq=hd720';
        if ($autoPlay)
            $videoParameters[] = 'autoplay=1';

        $parameters = array(
            'video' => $video,
            'videoParameters' => implode('&', $videoParameters),
        );
        $parameters = array_merge($parameters, $this->getPlayerWidth($width, $video));

        return $this->getTemplating()->render('HeroVideoBundle:Provider:player_youtube.html.twig', $parameters);
    }

    /**
     * @param $video
     * @return string
     */
    public function getEmbedUrl($video)
    {
        return sprintf('http://www.youtube.com/v/%s?autohide=1&version=3', $video->getProviderVideoId());
    }
} 