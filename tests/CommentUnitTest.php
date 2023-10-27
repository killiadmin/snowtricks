<?php

use App\Entity\Comment;
use App\Entity\Figure;
use App\Entity\User;
use DateTime;
use PHPUnit\Framework\TestCase;

class CommentUnitTest extends TestCase
{
    public function testIsTrue(): void
    {
        $comment = new Comment();
        $figure = new Figure();
        $user = new User();
        $datetime = new DateTime();

        $comment->setUserAssociated($user)
            ->setFigureAssociated($figure)
            ->setContentComment('content_comment')
            ->setDateCreate($datetime);

        $this->assertTrue($comment->getUserAssociated() === $user);
        $this->assertTrue($comment->getFigureAssociated() === $figure);
        $this->assertTrue($comment->getContentComment() === 'content_comment');
        $this->assertTrue($comment->getDateCreate() === $datetime);
    }

    public function testIsFalse(): void
    {
        $comment = new Comment();
        $figure = new Figure();
        $user = new User();
        $datetime = new DateTime();

        $comment->setUserAssociated($user)
            ->setFigureAssociated($figure)
            ->setContentComment('content_comment')
            ->setDateCreate($datetime);

        $this->assertFalse($comment->getUserAssociated() === 'false');
        $this->assertFalse($comment->getFigureAssociated() === 'false');
        $this->assertFalse($comment->getContentComment() === 'content_false');
        $this->assertFalse($comment->getDateCreate() === new DateTime());
    }

    public function testIsEmpty(): void
    {
        $comment = new Comment();

        $this->assertEmpty($comment->getUserAssociated());
        $this->assertEmpty($comment->getFigureAssociated());
        $this->assertEmpty($comment->getContentComment());
        $this->assertEmpty($comment->getDateCreate());
    }
}
