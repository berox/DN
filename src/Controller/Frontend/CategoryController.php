<?php
/**
 * Created by PhpStorm.
 * User: Mouncef
 * Date: 19/03/2018
 * Time: 21:15
 */

namespace App\Controller\Frontend;


use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Order;
use App\Entity\User;
use App\Utils\Constant;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zend\Code\Scanner\Util;

class CategoryController extends Controller
{
    /**
     * @Route("/category/{category}/{page}", name="category_index", requirements={"page"="\d+"})
     */
    public function index($category, $page = 1, $nbArticles = 4){

        $em = $this->getDoctrine()->getManager();
        $selectedCategory = $em->getRepository(Category::class)->findOneBy([
            'name' => $category
        ]);

        if (!$selectedCategory){
            throw new NotFoundHttpException('Category doesn\'t  exists');
        }

        $articles = $em->getRepository(Category::class)
            ->getPaginatedArticles($page, $selectedCategory->getCategoryId(), $nbArticles)
        ;

        return $this->render('frontend/category/index.html.twig', [
            'category'  =>  $selectedCategory,
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/category/{category}/allArticles", name="category_all_articles")
     */
    public function all(Request $request, $category){

        $em = $this->getDoctrine()->getManager();

        $selectedCategory = $em->getRepository(Category::class)->findOneBy([
            'name'  =>  $category
        ]);

        if (!$selectedCategory){
            throw new NotFoundHttpException('Category doesn\'t  exists');
        }

        $articles = $em->getRepository(Category::class)->getCategoryArticles($selectedCategory->getCategoryId());

        return $this->render('frontend/category/all.html.twig',[
            'category'  =>  $selectedCategory,
            'articles'  =>  $articles
        ]);
    }

    /**
     * @Route("/article/{id}", name="article_index")
     */
    public function articleItem($id) {

        $em = $this->getDoctrine()->getManager();

        $article = $em->getRepository(Article::class)->find($id);

        if(!$article) {
            throw new NotFoundHttpException('Article with id : '.$id.' not found');
        }

        $newArticles = $em->getRepository(Article::class)->getEightNewArticles();

        return $this->render('frontend/article/index.html.twig', [
            'article'   =>  $article,
            'newArticles'   => $newArticles
        ]);

    }
}