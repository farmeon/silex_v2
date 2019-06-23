<?php

namespace Controller;

use Entities\Authors;
use Silex\Application;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Silex\Api\ControllerProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AuthorsController extends AbstractController implements ControllerProviderInterface
{

    /**
     * @param Application $app
     * @return mixed
     */
    public function connect(Application $app)
    {
        $factory = $app['controllers_factory'];
        $factory->get("/", [$this, 'index'])->bind('author_index');
        $factory->get("/show/{id}", [$this, 'show'])->bind('author_show');
        $factory->match("/create", [$this, 'create'])->bind('author_insert');
        $factory->match("/update/{id}", [$this, 'update'])->bind('author_update');
        $factory->get("/delete/{id}", [$this, 'delete'])->bind('author_delete');

        return $factory;
    }

    /**
     * List all authors
     * @param Application $app
     * @return mixed
     */
    public function index(Application $app)
    {
        $authors = $app['orm.em']->getRepository(Authors::class)
            ->findAll();

        return $app['twig']->render('/admin/authors/authors.html.twig', [
            'authors' => $authors
        ]);
    }

    /**
     * Show author by id
     * @param Application $app
     * @param int $id
     * @return mixed
     */
    public function show(Application $app, int $id)
    {
        $author = $app['orm.em']->getRepository(Authors::class)
            ->find($id);

        if (!$author) {
            $app->abort(404, 'No author found for id ' . $id);
        }

        return $app['twig']->render('/admin/authors/show.html.twig', [
            'authors' => $author
        ]);
    }

    /**
     * Update author by id
     * @param Application $app
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function update(Application $app, Request $request, int $id)
    {
        $author = $app['orm.em']->getRepository(Authors::class)
            ->find($id);

        if (!$author) {
            $app->abort(404, 'No author found for id ' . $id);
        }

        $form = $app['form.factory']->createBuilder(FormType::class, $author)
            ->add('name')
            ->add('description')
            ->add('submit', SubmitType::class, [
                'label' => 'Save',
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            $app['orm.em']->flush();
            $app['session']->getFlashBag()->add('success', 'Author update success!');

            return $app->redirect($app['url_generator']->generate('author_show', ['id' => $author->getId()]));
        }

        return $app['twig']->render('/admin/authors/update.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Delete author by id
     * @param Application $app
     * @param int $id
     * @return mixed
     */
    public function delete(Application $app, int $id)
    {
        $author = $app['orm.em']->getRepository(Authors::class)
            ->find($id);

        if (!$author) {
            $app->abort(404, 'No author found for id '.$id);
        }
        $app['orm.em']->remove($author);
        $app['orm.em']->flush();

        return $app->redirect($app['url_generator']->generate('author_index'));
    }

}