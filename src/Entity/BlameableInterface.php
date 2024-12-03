<?php

namespace App\Entity;

interface BlameableInterface
{
    public function getCreatedBy(): ?User;

    public function getUpdatedBy(): ?User;
}
