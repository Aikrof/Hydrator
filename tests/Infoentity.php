<?php

declare(strict_types=1);

namespace Aikrof\Hydrator\Tests;


class Infoentity
{
    /**
     * @var \Aikrof\Hydrator\Tests\InfEntity
     */
    protected $info;

    /**
     * @var string
     */
    protected $text;

    /**
     * @return InfEntity
     */
    public function getInfo(): ?InfEntity
    {
        return $this->info;
    }

    /**
     * @param InfEntity $info
     */
    public function setInfo(InfEntity $info): void
    {
        $this->info = $info;
    }

    /**
     * @return string
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }


}