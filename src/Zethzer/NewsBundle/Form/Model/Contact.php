<?php

namespace Zethzer\NewsBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

class Contact
{
    /**
     * Email
     * @var string
     *
     * @Assert\Email()
     */
    protected $email;

    /**
     * Subject
     * @var string
     *
     * @Assert\NotBlank()
     */
    protected $subject;

    /**
     * Content
     * @var string
     *
     * @Assert\NotBlank()
     */
    protected $content;

    public function getEmail() {
      return $this->email;
    }

    public function setEmail($email) {
      $this->email = $email;
    }

    public function getSubject() {
      return $this->subject;
    }

    public function setSubject($subject) {
      $this->subject = $subject;
    }

    public function getContent() {
      return $this->content;
    }

    public function setContent($content) {
      $this->content = $content;
    }
}