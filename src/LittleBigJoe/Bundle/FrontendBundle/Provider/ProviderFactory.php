<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Provider;

class ProviderFactory
{
    protected $dailymotionProvider;
    protected $youtubeProvider;
    protected $vimeoProvider;

    public function __construct(DailymotionProvider $dailymotionProvider, YoutubeProvider $youtubeProvider, VimeoProvider $vimeoProvider)
    {
        $this->dailymotionProvider = $dailymotionProvider;
        $this->youtubeProvider = $youtubeProvider;
        $this->vimeoProvider = $vimeoProvider;
    }

    /**
     * @param $url
     * @return BaseProvider
     */
    public function getProviderByUrl($url)
    {
        if (strpos($url, 'youtu') !== false) {
            return $this->youtubeProvider;
        } else if (strpos($url, 'dailymotion') !== false) {
            return $this->dailymotionProvider;
        } else if (strpos($url, 'vimeo') !== false) {
            return $this->vimeoProvider;
        }
    }

    /**
     * @param $providerName
     * @return BaseProvider
     */
    public function getProviderByName($providerName)
    {
        switch (strtolower($providerName)) {
            case 'dailymotion':
                return $this->dailymotionProvider;
            case 'youtube':
                return $this->youtubeProvider;
            case 'vimeo':
                return $this->vimeoProvider;
        }
    }
} 