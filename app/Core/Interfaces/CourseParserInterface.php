<?php
namespace App\Core\Interfaces;

use App\Opif;

interface CourseParserInterface
{
    public function parse(Opif $opif);
}
