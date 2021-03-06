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

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Exception\ConfigurationNotFoundException;
use App\Form\CommentType;
use App\Repository\PostRepository;
use App\Service\ConfigurationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class PostController.
 */
class PostController extends Controller
{
    /**
     * @var PostRepository|null
     */
    protected $paginationPostRepository = null;

    /**
     * @var null|ConfigurationService
     */
    protected $configurationService = null;

    /**
     * PostController constructor.
     *
     * @param PostRepository       $repo
     * @param ConfigurationService $configurationService
     */
    public function __construct(PostRepository $repo, ConfigurationService $configurationService)
    {
        $this->paginationPostRepository = $repo;
        $this->configurationService = $configurationService;
    }

    /**
     * This action shows a list of recent posts.
     *
     * @Route("/", name="post_list")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws ConfigurationNotFoundException
     */
    public function list(Request $request)
    {
        $currentPage = $request->query->get('currentPage') > 0 ? $request->query->get('currentPage') : 1;
        $limit = $this->configurationService->getConfigurationEntry('post_limit');
        $posts = $this->paginationPostRepository->findAllVisiblePaginated($currentPage, $limit);
        $maxPages = ceil($posts->count() / $limit);

        return $this->render('post/list.html.twig', [
            'posts' => $posts,
            'currentPage' => $currentPage,
            'limit' => $limit,
            'maxPages' => $maxPages,
        ]);
    }

    /**
     * This action shows a list of recent posts.
     *
     * @Route("/posts/page/{currentPage}", name="post_ajax_list")
     *
     * @param int $currentPage
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws ConfigurationNotFoundException
     */
    public function ajaxList(int $currentPage = 1)
    {
        $limit = $this->configurationService->getConfigurationEntry('post_limit');
        $posts = $this->paginationPostRepository->findAllVisiblePaginated($currentPage, $limit);

        return $this->render('post/ajax_list.html.twig', [
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/{slug}", name="post_show")
     *
     * @ParamConverter("post", class="App\Entity\Post")
     *
     * @param Post    $post
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Post $post, Request $request)
    {
        if ($post->isHidden()) {
            throw new NotFoundHttpException("The post is marked as hidden!");
        }
        $form = $this->createForm(CommentType::class, new Comment());
        $form->handleRequest($request);

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'comments' => $post->getComments(),
            'form' => $form->createView(),
        ]);
    }
}
