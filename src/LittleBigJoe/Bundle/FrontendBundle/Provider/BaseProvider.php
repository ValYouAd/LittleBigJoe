<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Provider;

use Buzz\Browser;

abstract class BaseProvider
{
    protected $browser;
    protected $metadata;
    protected $templating;

    /**
     * @param \Buzz\Browser $browser
     */
    public function __construct(Browser $browser, $templating)
    {
        $this->browser = $browser;
        $this->templating = $templating;
    }

    protected function loadMetadata($url)
    {
        try {
            $response = $this->browser->get($url);
        } catch (\RuntimeException $e) {
            throw new \RuntimeException('Unable to retrieve the video information for :' . $url, null, $e);
        }

        $metadata = json_decode($response->getContent(), true);

        if (!$metadata) {
            throw new \RuntimeException('Unable to decode the video information for :' . $url);
        }

        return $metadata;
    }

    /**
     * @return mixed
     */
    public function getTemplating()
    {
        return $this->templating;
    }

    /**
     * @param $width
     * @param $video
     * @return array
     */
    protected function getPlayerWidth($width, $video)
    {
        if ($width == 0) {
            $playerWidth = '100%';
            $playerHeight = '100%';
        }
        elseif ($width) {
            $playerWidth = $width;
            $playerHeight = ($width * $video->getHeight()) / $video->getWidth();
        } else {
            $playerWidth = $video->getWidth();
            $playerHeight = $video->getHeight();
        }

        return array(
            'playerWidth' => $playerWidth,
            'playerHeight' => $playerHeight
        );
    }

    public function getPlayer($video, $width = null) {}
} 