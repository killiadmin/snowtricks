<?php

use App\Entity\Figure;
use PHPUnit\Framework\TestCase;

class FigureUnitTest extends TestCase
{
    public function testIsTrue(): void
    {
        $figure = new Figure();

        $figure->setTitle('title')
            ->setContentFigure('content')
            ->setCategory('category')
            ->setPictureFigure((array)'picture_figure');

        $this->assertTrue($figure->getTitle() === 'title');
        $this->assertTrue($figure->getContentFigure() === 'content');
        $this->assertTrue($figure->getCategory() === 'category');
        $this->assertTrue($figure->getPictureFigure() === (array)'picture_figure');
    }

    public function testIsFalse(): void
    {
        {
            $figure = new Figure();

            $figure->setTitle('title')
                ->setContentFigure('content')
                ->setCategory('category')
                ->setPictureFigure((array)'picture_figure');

            $this->assertFalse($figure->getTitle() === 'false');
            $this->assertFalse($figure->getContentFigure() === 'false');
            $this->assertFalse($figure->getCategory() === 'false');
            $this->assertFalse($figure->getPictureFigure() === 'false');
        }
    }

    public function testIsEmpty(): void
    {
        $figure = new Figure();

        $this->assertEmpty($figure->getTitle());
        $this->assertEmpty($figure->getContentFigure());
        $this->assertEmpty($figure->getCategory());
        $this->assertEmpty($figure->getPictureFigure());
    }
}
