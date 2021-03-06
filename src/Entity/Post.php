<?php
/**
 * Copyright (c) 2018 Wolf Utz <wpu@hotmail.de>
 *
 * This file is part of the OmegaBlog project.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 */
class Post extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank()
     *
     * @var string
     */
    private $title = "";

    /**
     * @ORM\Column(type="string", length=128, unique=true)
     *
     * @var string
     */
    private $slug = "";

    /**
     * @ORM\Column(type="text")
     *
     * @var string
     */
    private $teaser = "";

    /**
     * @ORM\Column(type="text")
     *
     * @var string
     */
    private $body = "";

    /**
     * @ManyToMany(targetEntity="App\Entity\Tag",cascade={"persist"}, inversedBy="posts")
     * @JoinTable(name="post_tag_mm",
     *      joinColumns={@JoinColumn(name="post_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="tag_id", referencedColumnName="id")}
     * )
     *
     * @var ArrayCollection|null
     */
    private $tags = null;

    /**
     * @OneToMany(targetEntity="App\Entity\Comment", mappedBy="post", cascade={"persist", "remove"})
     *
     * @var ArrayCollection|null
     */
    private $comments = null;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     *
     * @var User|null
     */
    private $user = null;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    private $enableComments = true;

    /**
     * Post constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->tags = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * Generates the slug.
     */
    public function generateSlug()
    {
        // TODO: Find a better solution!
        $this->slug = rtrim(strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $this->title))), '-');
    }

    /**
     * @return string
     */
    public function getTeaser()
    {
        return $this->teaser;
    }

    /**
     * @param string $teaser
     */
    public function setTeaser($teaser)
    {
        $this->teaser = $teaser;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return ArrayCollection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param ArrayCollection $tags
     */
    public function setTags(ArrayCollection $tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return ArrayCollection|null
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param ArrayCollection $comments
     */
    public function setComments(ArrayCollection $comments)
    {
        $this->comments = $comments;
    }

    /**
     * @return bool
     */
    public function isEnableComments()
    {
        return $this->enableComments;
    }

    /**
     * @param bool $enableComments
     */
    public function setEnableComments($enableComments)
    {
        $this->enableComments = $enableComments;
    }

    /**
     * Helper method to remove the mm relation between a post and a tag.
     *
     * @param Tag $tag
     */
    public function removeTag(Tag $tag)
    {
        if (!$this->tags->contains($tag)) {
            return;
        }
        $this->tags->removeElement($tag);
        $tag->removePost($this);
    }
}
