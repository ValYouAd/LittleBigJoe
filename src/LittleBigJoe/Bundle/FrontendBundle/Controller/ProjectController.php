<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Controller;

use LittleBigJoe\Bundle\CoreBundle\Entity\ProjectProduct;
use LittleBigJoe\Bundle\CoreBundle\Entity\ProjectProductComment;
use LittleBigJoe\Bundle\FrontendBundle\Form\ProjectProductCommentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use LittleBigJoe\Bundle\CoreBundle\Entity\Project;
use LittleBigJoe\Bundle\CoreBundle\Entity\ProjectReward;
use LittleBigJoe\Bundle\CoreBundle\Entity\Entry;
use LittleBigJoe\Bundle\CoreBundle\Entity\EntryComment;
use LittleBigJoe\Bundle\CoreBundle\Entity\Comment;
use LittleBigJoe\Bundle\CoreBundle\Entity\ProjectHelp;
use LittleBigJoe\Bundle\FrontendBundle\Form\CommentType;
use LittleBigJoe\Bundle\FrontendBundle\Form\EditProjectType;
use LittleBigJoe\Bundle\FrontendBundle\Form\EntryType;
use LittleBigJoe\Bundle\FrontendBundle\Form\ReportProjectType;
use LittleBigJoe\Bundle\FrontendBundle\Form\EntryCommentType;
use LittleBigJoe\Bundle\FrontendBundle\Form\HelpProjectType;
use Symfony\Component\Validator\Constraints\Image;

class ProjectController extends Controller
{
    /**
     * Default handler for project
     *
     * @Route("/projects", name="littlebigjoe_frontendbundle_project")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $favoriteProjects = $em->getRepository('LittleBigJoeCoreBundle:Project')->findFavorite();
        $popularProjects = $em->getRepository('LittleBigJoeCoreBundle:Project')->findPopular(4, '-7 days');
        $recentlyUpdatedProjects = $em->getRepository('LittleBigJoeCoreBundle:Project')->findRecentlyUpdated();

        return array(
            'favoriteProjects'        => $favoriteProjects,
            'popularProjects'         => $popularProjects,
            'recentlyUpdatedProjects' => $recentlyUpdatedProjects
        );
    }

    /**
     * Projects supported (likes of funds) by the logged user
     *
     * @Route("/projects-supported", name="littlebigjoe_frontendbundle_project_supported_projects")
     * @Template("LittleBigJoeFrontendBundle:Project:list.html.twig")
     */
    public function supportedProjectsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->get('security.context')->getToken()->getUser();

        // If the current user is not logged, redirect him to login page
        if (!is_object($currentUser)) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                'You must be logged in to create a project'
            );

            $request->getSession()->set('_security.main.target_path', 'littlebigjoe_frontendbundle_project');

            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        $query = $em->getRepository('LittleBigJoeCoreBundle:Project')->findSupported($currentUser, null);

        $paginator = $this->get('knp_paginator');
        $projects = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            $this->container->getParameter('nb_elements_by_page')
        );

        return array(
            'title'    => 'Projects I\'m supporting',
            'projects' => $projects
        );
    }

    /**
     * Projects from the followed people by the logged user
     *
     * @Route("/projects-of-followed-users", name="littlebigjoe_frontendbundle_project_users_followed_projects")
     * @Template("LittleBigJoeFrontendBundle:Project:list.html.twig")
     */
    public function usersFollowedProjectsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->get('security.context')->getToken()->getUser();

        // If the current user is not logged, redirect him to login page
        if (!is_object($currentUser)) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                'You must be logged in to access to the projects of users you follow'
            );

            $request->getSession()->set('_security.main.target_path', 'littlebigjoe_frontendbundle_project_users_followed_projects');

            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        $followedUsers = $currentUser->getFollowedUsers();
        $followedUsersIds = array();

        if (!empty($followedUsers)) {
            foreach ($followedUsers as $followedUser) {
                array_push($followedUsersIds, $followedUser->getId());
            }
        }

        if (!empty($followedUsersIds)) {
            $query = $em->getRepository('LittleBigJoeCoreBundle:Project')->findUsersFollowersProjects($followedUsersIds, null);

            $paginator = $this->get('knp_paginator');
            $projects = $paginator->paginate(
                $query,
                $this->get('request')->query->get('page', 1),
                $this->container->getParameter('nb_elements_by_page')
            );
        } else {
            $projects = array();
        }

        return array(
            'title'    => 'Projects of users I follow',
            'projects' => $projects
        );
    }

    /**
     * Projects from the followed people by the logged user
     *
     * @Route("/projects-of-followed-brands", name="littlebigjoe_frontendbundle_project_brands_followed_projects")
     * @Template("LittleBigJoeFrontendBundle:Project:list.html.twig")
     */
    public function brandsFollowedProjectsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->get('security.context')->getToken()->getUser();

        // If the current user is not logged, redirect him to login page
        if (!is_object($currentUser)) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                'You must be logged in to access to the projects of brands you follow'
            );

            $request->getSession()->set('_security.main.target_path', 'littlebigjoe_frontendbundle_project_brands_followed_projects');

            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        $followedBrands = $currentUser->getFollowedBrands();
        $followedBrandsIds = array();

        if (!empty($followedBrands)) {
            foreach ($followedBrands as $followedBrand) {
                array_push($followedBrandsIds, $followedBrand->getId());
            }
        }

        if (!empty($followedBrandsIds)) {
            $query = $em->getRepository('LittleBigJoeCoreBundle:Project')->findBrandsFollowersProjects($followedBrandsIds, null);

            $paginator = $this->get('knp_paginator');
            $projects = $paginator->paginate(
                $query,
                $this->get('request')->query->get('page', 1),
                $this->container->getParameter('nb_elements_by_page')
            );
        } else {
            $projects = array();
        }

        return array(
            'title'    => 'Projects of brands I follow',
            'projects' => $projects
        );
    }

    /**
     * Latest projects
     *
     * @Route("/latest-projects", name="littlebigjoe_frontendbundle_project_latest_projects")
     * @Template("LittleBigJoeFrontendBundle:Project:list.html.twig")
     */
    public function latestProjectsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('LittleBigJoeCoreBundle:Project')->findLatest(null);

        $paginator = $this->get('knp_paginator');
        $projects = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            $this->container->getParameter('nb_elements_by_page')
        );

        return array(
            'title'    => 'Latest projects',
            'projects' => $projects
        );
    }

    /**
     * Popular projects
     *
     * @Route("/popular-projects", name="littlebigjoe_frontendbundle_project_popular_projects")
     * @Template("LittleBigJoeFrontendBundle:Project:list.html.twig")
     */
    public function popularProjectsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('LittleBigJoeCoreBundle:Project')->findPopular(null);

        $paginator = $this->get('knp_paginator');
        $projects = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            $this->container->getParameter('nb_elements_by_page')
        );

        return array(
            'title'    => 'Popular projects',
            'projects' => $projects
        );
    }

    /**
     * Popular week projects
     *
     * @Route("/popular-projects-this-week", name="littlebigjoe_frontendbundle_project_popular_week_projects")
     * @Template("LittleBigJoeFrontendBundle:Project:list.html.twig")
     */
    public function popularWeekProjectsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('LittleBigJoeCoreBundle:Project')->findPopular(null, '-7 days');

        $paginator = $this->get('knp_paginator');
        $projects = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            $this->container->getParameter('nb_elements_by_page')
        );

        return array(
            'title'    => 'Popular week projects',
            'projects' => $projects
        );
    }

    /**
     * Funding projects
     *
     * @Route("/funding-projects", name="littlebigjoe_frontendbundle_project_funding_projects")
     * @Template("LittleBigJoeFrontendBundle:Project:list.html.twig")
     */
    public function fundingProjectsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('LittleBigJoeCoreBundle:Project')->findFunding(null);

        $paginator = $this->get('knp_paginator');
        $projects = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            $this->container->getParameter('nb_elements_by_page')
        );

        return array(
            'title'    => 'Projects to fund',
            'projects' => $projects
        );
    }

    /**
     * Top funded projects
     *
     * @Route("/top-funded-projects", name="littlebigjoe_frontendbundle_project_top_funded_projects")
     * @Template("LittleBigJoeFrontendBundle:Project:list.html.twig")
     */
    public function topFundedProjectsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('LittleBigJoeCoreBundle:Project')->findTopFunded(null);

        $paginator = $this->get('knp_paginator');
        $projects = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            $this->container->getParameter('nb_elements_by_page')
        );

        return array(
            'title'    => 'Top funded projects',
            'projects' => $projects
        );
    }

    /**
     * Almost ending projects
     *
     * @Route("/almost-ending-projects", name="littlebigjoe_frontendbundle_project_almost_ending_projects")
     * @Template("LittleBigJoeFrontendBundle:Project:list.html.twig")
     */
    public function almostEndingProjectsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('LittleBigJoeCoreBundle:Project')->findAlmostEnding(null);

        $paginator = $this->get('knp_paginator');
        $projects = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            $this->container->getParameter('nb_elements_by_page')
        );

        return array(
            'title'    => 'Almost ending projects',
            'projects' => $projects
        );
    }

    /**
     * Favorite projects
     *
     * @Route("/favorite-projects", name="littlebigjoe_frontendbundle_project_favorite_projects")
     * @Template("LittleBigJoeFrontendBundle:Project:list.html.twig")
     */
    public function favoriteProjectsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('LittleBigJoeCoreBundle:Project')->findFavorite(null);

        $paginator = $this->get('knp_paginator');
        $projects = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            $this->container->getParameter('nb_elements_by_page')
        );

        return array(
            'title'    => 'Favorite projects',
            'projects' => $projects
        );
    }

    /**
     * Recently updated projects
     *
     * @Route("/recently-updated-projects", name="littlebigjoe_frontendbundle_project_recently_updated_projects")
     * @Template("LittleBigJoeFrontendBundle:Project:list.html.twig")
     */
    public function recentlyUpdatedProjectsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('LittleBigJoeCoreBundle:Project')->findRecentlyUpdated(null);

        $paginator = $this->get('knp_paginator');
        $projects = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            $this->container->getParameter('nb_elements_by_page')
        );

        return array(
            'title'    => 'Recently updated projects',
            'projects' => $projects
        );
    }

    /**
     * Create new project
     *
     * @Route("/launch-my-project", name="littlebigjoe_frontendbundle_project_create_project")
     * @Template("LittleBigJoeFrontendBundle:Project:create.html.twig")
     */
    public function createProjectAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $currentUser = $this->get('security.context')->getToken()->getUser();
        // If the current user is not logged, redirect him to login page
        if (!is_object($currentUser)) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                'You must be logged in to create a project'
            );

            $request->getSession()->set('_security.main.target_path', 'littlebigjoe_frontendbundle_project_create_project');

            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        // Make sure the private user dir is created
        $dirName = __DIR__ . '/../../../../../web/uploads/tmp/user/' . preg_replace('/[^a-z0-9_\-]/i', '_', $currentUser->getEmail());
        if (!file_exists($dirName)) {
            mkdir($dirName, 0755);
        }

        // Get session vars
        $projectMedias = $this->getRequest()->getSession()->get('projectMedias');
        if (empty($projectMedias)) {
            $projectMedias = array();
            $this->getRequest()->getSession()->set('projectMedias', $projectMedias);
        }

        $project = new Project();
        // Set default data like creator and default language for project
        $project->setUser($currentUser);
        $project->setLanguage($currentUser->getDefaultLanguage());
        $project->setAmountCount(0);
        $project->setLikesCount(0);
        $project->setCreatedAt(new \DateTime());
        $project->setStatus('1');

        // Create form flow
        $flow = $this->get('littlebigjoefrontend.flow.project.createProject');
        $flow->bind($project);
        $form = $flow->createForm();

        if ($flow->isValid($form)) {
            // Handle file upload in first step
            $photo = $this->_fixUploadFile($project->getPhoto());
            $flow->saveCurrentStepData($form);

            // If we're not on the final step
            if ($flow->nextStep()) {
                // Create form for next step
                $form = $flow->createForm();
            } else {
                // Remap entities
                $brand = $em->getRepository('LittleBigJoeCoreBundle:Brand')->find($project->getBrand()->getId());
                $project->setBrand($brand);
                $brand->addProject($project);

                $productType = $em->getRepository('LittleBigJoeCoreBundle:ProductType')->find($project->getProductType()->getId());
                $project->setProductType($productType);
                $productType->addProject($project);

                if (!empty($projectMedias))
                {
                    foreach ($projectMedias as $key => $projectMedia)
                    {
                        if ($projectMedia['type'] == 'image')
                        {
                            $projectImage = $em->getRepository('LittleBigJoeCoreBundle:ProjectImage')->find($projectMedia['id']);
                            $project->addImage($projectImage);
                            $projectImage->setProject($project);
                        }
                        else if ($projectMedia['type'] == 'video')
                        {
                            $projectVideo = $em->getRepository('LittleBigJoeCoreBundle:ProjectVideo')->find($projectMedia['id']);
                            $project->addVideo($projectVideo);
                            $projectVideo->setProject($project);
                        }
                    }
                }

                // Persist form data
                $em->persist($project);
                $em->flush();

                // Create project directory if it doesn't exist
                if (!is_dir($this->get('kernel')->getRootDir() . '/../web/uploads/projects/' . $project->getId())) {
                    mkdir($this->get('kernel')->getRootDir() . '/../web/uploads/projects/' . $project->getId(), 0777);
                }

                // Move tmp file from server, to project directory
                $matches = array();
                preg_match_all('/\b(?:(?:https?):\/\/' . $this->getRequest()->getHost() . ')[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&]/i', $project->getDescription(), $matches, PREG_PATTERN_ORDER);
                foreach ($matches[0] as $key => $match) {
                    if (@fopen($match, 'r')) {
                        // Move file
                        $filePath = preg_replace('/\b(?:(?:https?):\/\/' . $this->getRequest()->getHost() . ')/i', '', $match);
                        copy($this->get('kernel')->getRootDir() . '/../web' . $filePath, $this->get('kernel')->getRootDir() . '/../web/uploads/projects/' . $project->getId() . '/' . basename($filePath));

                        // Update description field
                        $description = preg_replace('#' . $filePath . '#', '/uploads/projects/' . $project->getId() . '/' . basename($filePath), $project->getDescription());
                        $project->setDescription($description);
                    }
                }

                // Retrieve the uploaded photo, and associate it with project
                if ($this->getRequest()->getSession()->get('tmpUploadedFilePath') != null) {
                    $fileInfo = new UploadedFile(
                        $this->getRequest()->getSession()->get('tmpUploadedFilePath'),
                        $this->getRequest()->getSession()->get('tmpUploadedFile'),
                        MimeTypeGuesser::getInstance()->guess($this->getRequest()->getSession()->get('tmpUploadedFilePath')),
                        filesize($this->getRequest()->getSession()->get('tmpUploadedFilePath'))
                    );
                    $project->setPhoto($fileInfo);

                    $evm = $em->getEventManager();
                    $uploadableManager = $this->container->get('stof_doctrine_extensions.uploadable.manager');
                    $uploadableListener = $uploadableManager->getUploadableListener();
                    $uploadableListener->setDefaultPath('uploads/projects/' . $project->getId());
                    $evm->removeEventListener(array('postFlush'), $uploadableListener);
                    $uploadableManager->markEntityToUpload($project, $project->getPhoto());
                }

                // Move tmp project medias from server, to project directory
                if (!empty($projectMedias)) {
                    foreach ($projectMedias as $projectMedia) {
                        if ($projectMedia['type'] == 'image') {
                            $projectImage = $em->getRepository('LittleBigJoeCoreBundle:ProjectImage')->find($projectMedia['id']);
                            $filePath = $projectImage->getPath();

                            copy($this->get('kernel')->getRootDir() . '/../web/' . $filePath, $this->get('kernel')->getRootDir() . '/../web/uploads/projects/' . $project->getId() . '/' . basename($filePath));
                            $path = preg_replace('#' . $filePath . '#', 'uploads/projects/' . $project->getId() . '/' . basename($filePath), $projectImage->getPath());
                            $projectImage->setPath($path);
                        }
                    }
                }

                // Persist form data and redirect user
                $em->persist($project);
                $em->flush();

                // Delete session data
                $this->getRequest()->getSession()->remove('tmpUploadedFile');
                $this->getRequest()->getSession()->remove('tmpUploadedFilePath');
                $this->getRequest()->getSession()->remove('tmpUploadedFileRelativePath');

                // Reset flow data
                $flow->reset();
                $this->getRequest()->getSession()->set('projectMedias', null);

                return $this->redirect($this->generateUrl('littlebigjoe_frontendbundle_project_show', array('id' => $project->getId(), 'slug' => $project->getSlug())));
            }
        }

        return $this->render('LittleBigJoeFrontendBundle:Project:new.html.twig', array(
            'form'          => $form->createView(),
            'flow'          => $flow,
            'projectMedias' => $projectMedias
        ));
    }

    /**
     * Edit specific project
     *
     * @Route("/project/{id}-{slug}/edit", name="littlebigjoe_frontendbundle_project_edit")
     * @Template("LittleBigJoeFrontendBundle:Project:edit.html.twig")
     */
    public function editProjectAction(Request $request, $id, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->get('security.context')->getToken()->getUser();
        $project = $em->getRepository('LittleBigJoeCoreBundle:Project')->findBySlugI18n($id, $slug);

        if (!$project) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }

        // If the current user is not logged, redirect him to login page
        if (!is_object($currentUser)) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                'You must be logged in to edit a project'
            );

            // Force base url to make sure environment is not specified in the URL
            $this->get('router')->getContext()->setBaseUrl('');
            $request->getSession()->set('_security.main.target_path', $this->generateUrl('littlebigjoe_frontendbundle_project_edit', array('id' => $project->getId(), 'slug' => $project->getSlug())));

            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        // If the current user is not the project owner
        if ($currentUser != $project->getUser() && (false === $this->get('security.context')->isGranted('ROLE_SUPER_ADMIN'))) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                'You must be the project owner to edit the project'
            );

            return $this->redirect($this->generateUrl('littlebigjoe_frontendbundle_project_show', array('id' => $project->getId(), 'slug' => $project->getSlug())));
        }

        // If the project is already ended, show a different form
        if ($project->getEndedAt() != null) {
            $editForm = $this->createForm(new EditProjectType(array('locale' => $request->getLocale())), $project);
            $editForm->handleRequest($request);

            if ($editForm->isValid()) {
                $em->persist($project);
                $em->flush();

                return $this->redirect($this->generateUrl('littlebigjoe_frontendbundle_home'));
            }

            // Project deletion
            $deleteForm = $this->createFormBuilder()
                ->setAction($this->generateUrl('littlebigjoe_frontendbundle_project_edit', array('id' => $project->getId(), 'slug' => $project->getSlug())))
                ->setMethod('DELETE')
                ->add('submit', 'submit', array('label' => 'Delete this project', 'attr' => array('class' => 'btn btn-danger')))
                ->getForm();
            $deleteForm->handleRequest($request);

            if ($deleteForm->isValid()) {
                $project->setDeletedAt(new \Datetime());
                $em->persist($project);
                $em->flush();

                return $this->redirect($this->generateUrl('littlebigjoe_frontendbundle_home'));
            }

            return $this->render('LittleBigJoeFrontendBundle:Project:edit_ended.html.twig', array(
                'entity'     => $project,
                'form'       => $editForm->createView(),
                'deleteForm' => $deleteForm->createView(),
            ));
        } // If the project is still available, show multi steps form (to edit all project datas)
        else {
            // Get session vars
            $projectMedias = $this->getRequest()->getSession()->get('projectMedias');
            if (empty($projectMedias)) {
                $this->getRequest()->getSession()->set('projectMedias', $project->getMedias());
                $projectMedias = $this->getRequest()->getSession()->get('projectMedias');
            }

            // Create form flow
            $flow = $this->get('littlebigjoefrontend.flow.project.editProject');
            $flow->bind($project);
            $form = $flow->createForm();

            if ($flow->isValid($form)) {
                // Handle file upload in first step
                $photo = $this->_fixUploadFile($project->getPhoto());
                $flow->saveCurrentStepData($form);

                // If we're not on the final step
                if ($flow->nextStep()) {
                    // Create form for next step
                    $form = $flow->createForm();
                } else {
                    // Remap entities
                    $productType = $em->getRepository('LittleBigJoeCoreBundle:ProductType')->find($project->getProductType()->getId());
                    $project->setProductType($productType);
                    $productType->addProject($project);

                    $projectImages = $project->getImages();
                    if (!empty($projectImages)) {
                        foreach ($projectImages as $key => $projectImage) {
                            $projectImage = $em->getRepository('LittleBigJoeCoreBundle:ProjectImage')->find($projectImage);
                            $project->addImage($projectImage);
                            $projectImage->setProject($project);
                        }
                    }

                    $projectVideos = $project->getVideos();
                    if (!empty($projectVideos)) {
                        foreach ($projectVideos as $key => $projectVideo) {
                            $projectVideo = $em->getRepository('LittleBigJoeCoreBundle:ProjectVideo')->find($projectVideo);
                            $project->addVideo($projectVideo);
                            $projectVideo->setProject($project);
                        }
                    }

                    // Persist form data
                    $em->persist($project);
                    $em->flush();

                    // Create project directory if it doesn't exist
                    if (!is_dir($this->get('kernel')->getRootDir() . '/../web/uploads/projects/' . $project->getId())) {
                        mkdir($this->get('kernel')->getRootDir() . '/../web/uploads/projects/' . $project->getId(), 0777);
                    }

                    // Move tmp file from server, to project directory
                    $matches = array();
                    preg_match_all('/\b(?:(?:https?):\/\/' . $this->getRequest()->getHost() . ')[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&]/i', $project->getDescription(), $matches, PREG_PATTERN_ORDER);
                    foreach ($matches[0] as $key => $match) {
                        if (@fopen($match, 'r')) {
                            // Move file
                            $filePath = preg_replace('/\b(?:(?:https?):\/\/' . $this->getRequest()->getHost() . ')/i', '', $match);
                            copy($this->get('kernel')->getRootDir() . '/../web' . $filePath, $this->get('kernel')->getRootDir() . '/../web/uploads/projects/' . $project->getId() . '/' . basename($filePath));

                            // Update description field
                            $description = preg_replace('#' . $filePath . '#', '/uploads/projects/' . $project->getId() . '/' . basename($filePath), $project->getDescription());
                            $project->setDescription($description);
                        }
                    }

                    // Retrieve the uploaded photo, and associate it with project
                    if ($this->getRequest()->getSession()->get('tmpUploadedFilePath') != null) {
                        $fileInfo = new UploadedFile(
                            $this->getRequest()->getSession()->get('tmpUploadedFilePath'),
                            $this->getRequest()->getSession()->get('tmpUploadedFile'),
                            MimeTypeGuesser::getInstance()->guess($this->getRequest()->getSession()->get('tmpUploadedFilePath')),
                            filesize($this->getRequest()->getSession()->get('tmpUploadedFilePath'))
                        );
                        $project->setPhoto($fileInfo);

                        $evm = $em->getEventManager();
                        $uploadableManager = $this->container->get('stof_doctrine_extensions.uploadable.manager');
                        $uploadableListener = $uploadableManager->getUploadableListener();
                        $uploadableListener->setDefaultPath('uploads/projects/' . $project->getId());
                        $evm->removeEventListener(array('postFlush'), $uploadableListener);
                        $uploadableManager->markEntityToUpload($project, $project->getPhoto());
                    }

                    // Move tmp project medias from server, to project directory
                    if (!empty($projectMedias)) {
                        foreach ($projectMedias as $projectMedia) {
                            if ($projectMedia['type'] == 'image') {
                                $projectImage = $em->getRepository('LittleBigJoeCoreBundle:ProjectImage')->find($projectMedia['id']);
                                if (!empty($projectImage) && $projectImage instanceof ProjectImage) {
                                    $filePath = $projectImage->getPath();
                                    copy($this->get('kernel')->getRootDir() . '/../web/' . $filePath, $this->get('kernel')->getRootDir() . '/../web/uploads/projects/' . $project->getId() . '/' . basename($filePath));
                                    $path = preg_replace('#' . $filePath . '#', 'uploads/projects/' . $project->getId() . '/' . basename($filePath), $projectImage->getPath());
                                    $projectImage->setPath($path);
                                }
                            }
                        }
                    }

                    // Persist form data and redirect user
                    $em->persist($project);
                    $em->flush();

                    // Delete session data
                    $this->getRequest()->getSession()->remove('tmpUploadedFile');
                    $this->getRequest()->getSession()->remove('tmpUploadedFilePath');

                    // Reset flow data
                    $flow->reset();
                    $this->getRequest()->getSession()->set('projectMedias', null);

                    return $this->redirect($this->generateUrl('littlebigjoe_frontendbundle_project_show', array('id' => $project->getId(), 'slug' => $project->getSlug())));
                }
            }

            return $this->render('LittleBigJoeFrontendBundle:Project:edit.html.twig', array(
                'project'       => $project,
                'form'          => $form->createView(),
                'flow'          => $flow,
                'projectMedias' => $projectMedias
            ));
        }
    }

    /**
     * Create product associated to specific project
     *
     * @Route("/project/{id}-{slug}/create-product", name="littlebigjoe_frontendbundle_project_create_product")
     * @Template("LittleBigJoeFrontendBundle:Project:Product/new.html.twig")
     */
    public function createProductAction(Request $request, $id, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->get('security.context')->getToken()->getUser();
        $project = $em->getRepository('LittleBigJoeCoreBundle:Project')->findBySlugI18n($id, $slug);

        if (!$project) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }

        // If the current user is not logged, redirect him to login page
        if (!is_object($currentUser)) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                'You must be logged in to edit a project'
            );

            // Force base url to make sure environment is not specified in the URL
            $this->get('router')->getContext()->setBaseUrl('');
            $request->getSession()->set('_security.main.target_path', $this->generateUrl('littlebigjoe_frontendbundle_project_edit', array('id' => $project->getId(), 'slug' => $project->getSlug())));

            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        // If the current user is not an LBJ admin or brand admin
        $brandIds = array();
        foreach ($currentUser->getBrands() as $brand) {
            $brandIds[] = $brand->getId();
        }
        if (!$currentUser->hasRole('ROLE_SUPER_ADMIN') && !$currentUser->hasRole('ROLE_BRAND_ADMIN') &&
            !in_array($project->getBrand()->getId(), $brandIds)
        ) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                'You must be an administrator to create a product'
            );

            return $this->redirect($this->generateUrl('littlebigjoe_frontendbundle_project_show', array('id' => $project->getId(), 'slug' => $project->getSlug())));
        }

        // If the product is already validated OR project already in "Funding phase"
        if (($project->getProduct() != null && $project->getProduct()->getValidatedAt() != null) || $project->getStatus() == '2') {
            $this->get('session')->getFlashBag()->add(
                'notice',
                'The product is already validated !'
            );

            return $this->redirect($this->generateUrl('littlebigjoe_frontendbundle_project_show', array('id' => $project->getId(), 'slug' => $project->getSlug())));
        }

        // Make sure the private user dir is created
        $dirName = __DIR__ . '/../../../../../web/uploads/tmp/user/' . preg_replace('/[^a-z0-9_\-]/i', '_', $currentUser->getEmail());
        if (!file_exists($dirName)) {
            mkdir($dirName, 0755);
        }

        // Get session vars
        $productMedias = $this->getRequest()->getSession()->get('productMedias');
        $projectFields = $this->getRequest()->getSession()->get('projectFields', array());
        if (empty($productMedias)) {
            $productMedias = $project->getMedias();
            $this->getRequest()->getSession()->set('productMedias', $productMedias);
        }

        // Create form flow
        $projectProduct = new ProjectProduct();
        $projectProduct->setDescription($project->getDescription());
        $project->setProduct($projectProduct);

        $flow = $this->get('littlebigjoefrontend.flow.project.createProduct');
        $flow->bind($projectProduct);
        $form = $flow->createForm();

        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData($form);

            // If we're not on the final step
            if ($flow->nextStep()) {
                // Create form for next step
                $form = $flow->createForm();
            } else {
                $project->setProduct(null);

                if (!empty($projectFields)) {
                    $project->setAmountRequired($projectFields['amountRequired']);

                    if (!empty($projectFields['rewards']))
                    {
                        foreach ($projectFields['rewards'] as $key => $reward) {
                            $projectReward = new ProjectReward();
                            $projectReward->setAmount($reward['amount']);
                            $projectReward->setTitle($reward['title']);
                            $projectReward->setDescription($reward['description']);
                            $projectReward->setStock($reward['stock']);
                            $projectReward->setMaxQuantityByUser($reward['maxQuantityByUser']);
                            $projectReward->setProject($project);

                            $em->persist($projectReward);
                            $em->flush();
                        }
                    }
                }

                $projectProduct->setSubmittedAt(new \DateTime());

                // Remap entities
                $projectProduct->setProject($project);
                $project->setProduct($projectProduct);

                if (!empty($productMedias))
                {
                    foreach ($productMedias as $key => $productMedia)
                    {
                        if ($productMedia['type'] == 'image')
                        {
                            $productImage = $em->getRepository('LittleBigJoeCoreBundle:ProjectImage')->find($productMedia['id']);
                            $projectProduct->addImage($productImage);
                            $productImage->setProduct($projectProduct);
                        }
                        else if ($productMedia['type'] == 'video')
                        {
                            $productVideo = $em->getRepository('LittleBigJoeCoreBundle:ProjectVideo')->find($productMedia['id']);
                            $projectProduct->addVideo($productVideo);
                            $productVideo->setProduct($projectProduct);
                        }
                    }
                }
                
                // Persist form data
                $em->persist($projectProduct);
                $em->flush();

                // Create product directory if it doesn't exist
                if (!is_dir($this->get('kernel')->getRootDir() . '/../web/uploads/projects/' . $project->getId() . '/product')) {
                    mkdir($this->get('kernel')->getRootDir() . '/../web/uploads/projects/' . $project->getId() . '/product', 0777);
                }

                // Move tmp file from server, to project directory
                $matches = array();
                preg_match_all('/\b(?:(?:https?):\/\/' . $this->getRequest()->getHost() . ')[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&]/i', $projectProduct->getDescription(), $matches, PREG_PATTERN_ORDER);
                foreach ($matches[0] as $key => $match) {
                    if (@fopen($match, 'r')) {
                        // Move file
                        $filePath = preg_replace('/\b(?:(?:https?):\/\/' . $this->getRequest()->getHost() . ')/i', '', $match);
                        copy($this->get('kernel')->getRootDir() . '/../web' . $filePath, $this->get('kernel')->getRootDir() . '/../web/uploads/projects/' . $project->getId() . '/product/' . basename($filePath));

                        // Update description field
                        $description = preg_replace('#' . $filePath . '#', '/uploads/projects/' . $project->getId() . '/product/' . basename($filePath), $projectProduct->getDescription());
                        $projectProduct->setDescription($description);
                    }
                }

                // Move tmp project medias from server, to project directory
                if (!empty($productMedias)) {
                    foreach ($productMedias as $productMedia) {
                        if ($productMedia['type'] == 'image') {
                            $productImage = $em->getRepository('LittleBigJoeCoreBundle:ProjectImage')->find($productMedia['id']);
                            $filePath = $productImage->getPath();

                            copy($this->get('kernel')->getRootDir() . '/../web/' . $filePath, $this->get('kernel')->getRootDir() . '/../web/uploads/projects/' . $project->getId() . '/product/' . basename($filePath));
                            $path = preg_replace('#' . $filePath . '#', 'uploads/projects/' . $project->getId() . '/product/' . basename($filePath), $productImage->getPath());
                            $productImage->setPath($path);
                        }
                    }
                }

                // Persist form data and redirect user
                $em->persist($projectProduct);
                $em->flush();

                // Delete session data
                $this->getRequest()->getSession()->remove('tmpUploadedFile');
                $this->getRequest()->getSession()->remove('tmpUploadedFilePath');

                // Reset flow data
                $flow->reset();
                $this->getRequest()->getSession()->set('productMedias', null);
                $this->getRequest()->getSession()->set('projectFields', null);

                return $this->redirect($this->generateUrl('littlebigjoe_frontendbundle_project_show', array('id' => $project->getId(), 'slug' => $project->getSlug())));
            }
        }

        return $this->render('LittleBigJoeFrontendBundle:Project:Product/new.html.twig', array(
            'form'          => $form->createView(),
            'flow'          => $flow,
            'project'       => $project,
            'projectProduct' => $projectProduct,
            'productMedias' => $productMedias
        ));
    }

    /**
     * Allows to save project logo in project creation during step 1
     *
     * @param UploadedFile $file
     *
     * @return string
     */
    public function _fixUploadFile($file)
    {
        $currentUser = $this->get('security.context')->getToken()->getUser();

        if (!empty($file) && $file instanceof UploadedFile) {
            $dirName = __DIR__ . '/../../../../../web/uploads/tmp/user/' . preg_replace('/[^a-z0-9_\-]/i', '_', $currentUser->getEmail());
            if (!file_exists($dirName)) {
                mkdir($dirName, 0777);
            }
            // Move uploaded file to tmp directory, and save path in session
            $tmpFile = $file->move($dirName . '/', sha1($file->getClientOriginalName() . uniqid(mt_rand(), true)));

            if (!empty($tmpFile)) {
                $tmpFilePath = $tmpFile->getPath() . '/' . $tmpFile->getFilename();
                $this->getRequest()->getSession()->set('tmpUploadedFile', $tmpFile->getFilename());
                $this->getRequest()->getSession()->set('tmpUploadedFileRelativePath', '/uploads/tmp/user/' . preg_replace('/[^a-z0-9_\-]/i', '_', $currentUser->getEmail()) . '/');
                $this->getRequest()->getSession()->set('tmpUploadedFilePath', $tmpFilePath);
            }
        }

        return $this->getRequest()->getSession()->get('tmpUploadedFilePath');
    }

    /**
     * Specific project
     *
     * @Route("/project/{id}-{slug}", name="littlebigjoe_frontendbundle_project_show")
     * @Template()
     */
    public function showAction(Request $request, $id, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        $showLikePopup = $request->query->get('likePopup', false);
        $showFundingPopup = $request->query->get('fundingPopup', false);
        $currentUser = $this->get('security.context')->getToken()->getUser();
        $entity = $em->getRepository('LittleBigJoeCoreBundle:Project')->findBySlugI18n($id, $slug);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }

        // Generate stats for chart
        $stats = $em->getRepository('LittleBigJoeCoreBundle:ProjectLike')->findLikesStats($entity->getCreatedAt(), new \DateTime(), $entity->getId());
        $dateStats = array();
        $likesStats = array();

        foreach ($stats as $key => $stat) {
            $dateStats[] = $stat['date'];
            $likesStats[] = $stat['nbLikes'];
        }

        // Create the help project form
        $projectHelp = new ProjectHelp();
        $projectHelp->setProject($entity);
        $options = array(
            'loggedFb'      => $this->get('security.context')->isGranted('ROLE_FACEBOOK'),
            'loggedTwitter' => $this->get('security.context')->isGranted('ROLE_TWITTER'),
        );
        $helpProjectForm = $this->createForm(new HelpProjectType($options), $projectHelp);

        // Create the entry form
        $entry = new Entry();
        $entry->setProject($entity);
        $entryForm = $this->createForm(new EntryType(), $entry);

        // Create the entry comment form
        $entryComment = new EntryComment();
        $options = array('project' => $entity, 'user' => $currentUser);
        $entryCommentForm = $this->createForm(new EntryCommentType($options), $entryComment);

        // Create the comment form
        $comment = new Comment();
        $comment->setProject($entity);
        $comment->setIsVisible(true);
        $options = array('user' => $currentUser);
        $commentForm = $this->createForm(new CommentType($options), $comment);

        // Create the reporting form
        $options = array(
            'reportReasons' => array(
                $this->get('translator')->trans('Intellectual property infringement'),
                $this->get('translator')->trans('I think this project should not be on LittleBigJoe'),
                $this->get('translator')->trans('This is spam'),
                $this->get('translator')->trans('Other')
            )
        );
        $reportForm = $this->createForm(new ReportProjectType($options));

        // Create the funding form
        $fundingForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('littlebigjoe_frontendbundle_payment_project'))
            ->setMethod('POST')
            ->add('projectId', 'hidden', array(
                'data' => $entity->getId()
            ))
            ->add('submit', 'submit', array(
                'label' => 'Fund this project',
                'attr'  => array(
                    'class' => 'btn btn-success'
                )
            ))
            ->getForm();


        // Create the product validation form
        $validationForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('littlebigjoe_frontendbundle_project_product_change_status', array('id' => $id, 'slug' => $slug)))
            ->setMethod('POST')
            ->add('projectId', 'hidden', array(
                'data' => $entity->getId()
            ))
            ->add('submitValidate', 'submit', array(
                'label' => 'Validate',
                'attr'  => array(
                    'class' => 'btn btn-success'
                )
            ))
            ->getForm();

        // Create the product decline form
        $declineForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('littlebigjoe_frontendbundle_project_product_change_status', array('id' => $id, 'slug' => $slug)))
            ->setMethod('POST')
            ->add('projectId', 'hidden', array(
                'data' => $entity->getId()
            ))
            ->add('declineMessage', 'textarea', array(
                'label' => 'Decline message'
            ))
            ->getForm();

        // Set some vars used in the template to filter data
        $contributions = $entity->getContributions();
        $usersIds = array();
        $usersAmounts = array();
        if (!empty($contributions)) {
            foreach ($contributions as $contribution) {
                if (!in_array($contribution->getUser()->getId(), $usersIds)) {
                    $usersIds[] = $contribution->getUser()->getId();
                    $usersAmounts[$contribution->getUser()->getId()] = 0;
                }
                if ($contribution->getIsAnonymous() == false) {
                    $usersAmounts[$contribution->getUser()->getId()] += $contribution->getMangopayAmount();
                }
            }
        }

        return array(
            'entity'             => $entity,
            'usersIds'           => $usersIds,
            'usersAmounts'       => $usersAmounts,
            'dateStats'          => $dateStats,
            'likesStats'         => json_encode($likesStats),
            'showLikePopup'      => $showLikePopup,
            'showFundingPopup'   => $showFundingPopup,
            'help_project_form'  => $helpProjectForm->createView(),
            'entry_form'         => $entryForm->createView(),
            'entry_comment_form' => $entryCommentForm->createView(),
            'comment_form'       => $commentForm->createView(),
            'funding_form'       => $fundingForm->createView(),
            'report_form'        => $reportForm->createView(),
            'validation_form'    => $validationForm->createView(),
            'decline_form'       => $declineForm->createView(),
            'current_date'       => new \Datetime()
        );
    }

    /**
     * Project preview
     *
     * @Route("/project/preview", name="littlebigjoe_frontendbundle_project_preview")
     * @Template("LittleBigJoeFrontendBundle:Project:preview.html.twig")
     */
    public function previewAction($entity, $isPreview = true)
    {
        $photo = '';
        // Get session vars
        $projectMedias = $this->getRequest()->getSession()->get('projectMedias', array());
        $productMedias = $this->getRequest()->getSession()->get('productMedias', array());

        // Retrieve the uploaded photo, and associate it with project
        if ($this->getRequest()->getSession()->get('tmpUploadedFile') != null && $this->getRequest()->getSession()->get('tmpUploadedFileRelativePath') != null) {
            $photo = $this->getRequest()->getSession()->get('tmpUploadedFileRelativePath') .
                $this->getRequest()->getSession()->get('tmpUploadedFile');
        }

        return array(
            'entity'        => $entity,
            'isPreview'     => $isPreview,
            'photo'         => $photo,
            'projectMedias' => $projectMedias,
            'productMedias' => $productMedias,
            'current_date'  => new \Datetime()
        );
    }

    /**
     * Show project product comments
     *
     * @Route("/project/{id}-{slug}/feedbacks", name="littlebigjoe_frontendbundle_project_product_feedbacks")
     * @Template("LittleBigJoeFrontendBundle:Project:Product/comments.html.twig")
     */
    public function showFeedbacksAction(Request $request, $id, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->get('security.context')->getToken()->getUser();
        $project = $em->getRepository('LittleBigJoeCoreBundle:Project')->findBySlugI18n($id, $slug);

        if (!$project) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }

        if ($project->getProduct() == null) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                'There\'s no product for this project'
            );

            return $this->redirect($this->generateUrl('littlebigjoe_frontendbundle_project_show', array('id' => $project->getId(), 'slug' => $project->getSlug())));
        }

        // If the current user is not logged, redirect him to login page
        if (!is_object($currentUser)) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                'You must be logged in to access to the product feedbacks'
            );

            // Force base url to make sure environment is not specified in the URL
            $this->get('router')->getContext()->setBaseUrl('');
            $request->getSession()->set('_security.main.target_path', $this->generateUrl('littlebigjoe_frontendbundle_project_product_feedbacks', array('id' => $project->getId(), 'slug' => $project->getSlug())));

            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        // If the current user is not an LBJ admin or brand admin or project owner
        $brandIds = array();
        foreach ($currentUser->getBrands() as $brand) {
            $brandIds[] = $brand->getId();
        }
        if (!$currentUser->hasRole('ROLE_SUPER_ADMIN') && !$currentUser->hasRole('ROLE_BRAND_ADMIN') &&
            !in_array($project->getBrand()->getId(), $brandIds) && $project->getUser()->getId() != $currentUser->getId()
        ) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                'You must be the owner or an administrator to access to the product feedbacks'
            );

            return $this->redirect($this->generateUrl('littlebigjoe_frontendbundle_project_show', array('id' => $project->getId(), 'slug' => $project->getSlug())));
        }

        $comment = new ProjectProductComment();
        $comment->setUser($currentUser);
        $comment->setProduct($project->getProduct());

        $form = $this->createForm(new ProjectProductCommentType(), $comment);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($comment);
            $em->flush();

            return $this->redirect($this->generateUrl('littlebigjoe_frontendbundle_project_product_feedbacks', array('id' => $project->getId(), 'slug' => $project->getSlug())));
        }

        return array(
            'entity' => $project->getProduct(),
            'form'   => $form->createView()
        );
    }

    /**
     * Change status of product
     *
     * @Route("/project/{id}-{slug}/change-status", name="littlebigjoe_frontendbundle_project_product_change_status")
     * @Method("POST")
     */
    public function changeStatusAction(Request $request, $id, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->get('security.context')->getToken()->getUser();
        $project = $em->getRepository('LittleBigJoeCoreBundle:Project')->findBySlugI18n($id, $slug);

        if (!$project) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }

        if ($project->getProduct() == null) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                'There\'s no product for this project'
            );

            return $this->redirect($this->generateUrl('littlebigjoe_frontendbundle_project_show', array('id' => $project->getId(), 'slug' => $project->getSlug())));
        }

        // If the current user is not logged, redirect him to login page
        if (!is_object($currentUser)) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                'You must be logged in to change the status of a product'
            );

            // Force base url to make sure environment is not specified in the URL
            $this->get('router')->getContext()->setBaseUrl('');
            $request->getSession()->set('_security.main.target_path', $this->generateUrl('littlebigjoe_frontendbundle_project_product_change_status', array('id' => $project->getId(), 'slug' => $project->getSlug())));

            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        // If the current user is not an LBJ admin or project owner
        if (!$currentUser->hasRole('ROLE_SUPER_ADMIN') && $project->getUser()->getId() != $currentUser->getId()) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                'You must be the owner or an administrator to change the status of the product'
            );

            return $this->redirect($this->generateUrl('littlebigjoe_frontendbundle_project_show', array('id' => $project->getId(), 'slug' => $project->getSlug())));
        }

        $formData = $request->request->get('form');

        // If the product sheet is validated, change project status
        if (isset($formData['submitValidate']) && $project->getId() == $formData['projectId']) {
            $project->setStatus(2);
            $project->getProduct()->setValidatedAt(new \DateTime());

            $em->persist($project);
            $em->flush();

            return $this->redirect($this->generateUrl('littlebigjoe_frontendbundle_project_show', array('id' => $project->getId(), 'slug' => $project->getSlug())));
        } // If the product sheet is declined, notify brand admin
        elseif (!empty($formData['declineMessage']) && $project->getId() == $formData['projectId']) {
            $productComment = new ProjectProductComment();
            $productComment->setContent(strip_tags($formData['declineMessage']));
            $productComment->setProduct($project->getProduct());
            $productComment->setUser($currentUser);

            $project->getProduct()->addComment($productComment);
            $em->persist($project);
            $em->flush();

            // Notify brand admins
            $brandAdmins = $em->getRepository('LittleBigJoeCoreBundle:User')->findByRoleAndBrand('ROLE_BRAND_ADMIN', $project->getBrand());
            if (!empty($brandAdmins)) {
                foreach ($brandAdmins as $brandAdmin) {
                    $email = \Swift_Message::newInstance()
                        ->setContentType('text/html')
                        ->setSubject($this->container->get('translator')->trans('%currentUser% has declined the product %product%', array('%currentUser%' => $currentUser, '%product%' => $project->getProduct())))
                        ->setFrom($this->container->getParameter('default_email_address'))
                        ->setTo(array($brandAdmin->getEmail() => $brandAdmin))
                        ->setBody(
                            $this->container->get('templating')->render('LittleBigJoeFrontendBundle:Email:decline_product.html.twig', array(
                                'projectOwner'  => $currentUser,
                                'admin'         => $brandAdmin,
                                'project'       => $project,
                                'declineReason' => strip_tags($formData['declineMessage']),
                                'url'           => $this->container->get('request')->getSchemeAndHttpHost()
                            ), 'text/html')
                        );
                    $this->container->get('mailer')->send($email);
                }
            }

            return $this->redirect($this->generateUrl('littlebigjoe_frontendbundle_project_show', array('id' => $project->getId(), 'slug' => $project->getSlug())));
        }
    }
}
