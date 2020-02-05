<?php

namespace App\Controller;

use App\Command\SetFinishedAtCommand;
use App\Entity\User;
use App\Form\Type\FieldType;
use App\Security\FieldVoter;
use App\Service\GameService;
use Exception;
use JMS\JobQueueBundle\Entity\Job;
use Swift_Mailer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Field;

/**
 * Class GameController
 */
class GameController extends AbstractController
{
    /** @var GameService */
    private $gameService;

    /**
     * GameController constructor.
     *
     * @param GameService $gameService
     */
    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    /**
     * @Route ("/create ", name = "create")
     *
     * @return RedirectResponse
     */
    public function createAction()
    {
        $field = new Field();

        $user = $this->getUser();
        $field->setUser($user);

        $data = $this->gameService->fillData($field->getVertical(), $field->getHorizontal(), $field->getBombAmount());
        $field->setData($data);

        $em = $this->getDoctrine()->getManager();
        $em->persist($field);
        $em->flush();

        $job = new Job(SetFinishedAtCommand::NAME, ['id' => $field->getId()]);
        $date = new \DateTime();
        $job->setExecuteAfter($date->modify('5 minutes'));
        $em->persist($job);
        $em->flush();

        return $this->redirectToRoute('game', ['id' => $field->getId()]);
    }

    /**
     * @Route ("/statistics", name = "statistics")
     *
     * @param Swift_Mailer $mailer
     *
     * @return Response
     */
    public function statisticsAction(Swift_Mailer $mailer)
    {
        $user = $this->getUser();

        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('send@example.com')
            ->setTo('recipient@example.com')
            ->setBody('TEST 123', 'text/html')
        ;

        $mailer->send($message);


        $fields = $this
            ->getDoctrine()
            ->getRepository(Field::class)
            ->findBy(['user' => $this->getUser()]);

        return $this->render('statistics.html.twig', [
            'all_fields' => $fields,
        ]);

    }

    /**
     * @Route ("/settings/{id}", name = "settings", methods={"GET","PUT"})
     *
     * @param Request $request
     * @param Field   $field
     *
     * @return Response
     */
    public function settingsAction(Request $request, Field $field)
    {
        $this->denyAccessUnlessGranted(FieldVoter::EDIT_SETTINGS, $field);

        $form = $this->createForm(FieldType::class, $field, [
            'method' => 'PUT',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $this->gameService->fillData($field->getVertical(), $field->getHorizontal(), $field->getBombAmount());
            $field->setData($data);

            $em = $this->getDoctrine()->getManager();
            $em->persist($field);
            $em->flush();

            return $this->redirectToRoute('game', ['id' => $field->getId()]);
        }

        return $this->render('settings.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route ("/game/{id}", name = "game")
     *
     * @param Request $request
     * @param Field   $field
     *
     * @return string
     */
    public function gameAction(Request $request, Field $field)
    {
        $this->denyAccessUnlessGranted(FieldVoter::EDIT, $field);

        return $this->render('game/game.html.twig', [
            'field_view' => $field,
        ]);
    }

    /**
     * @Route ("/game/click/{id}", name = "click")
     *
     * @param Request $request
     * @param Field $field
     *
     * @return string
     *
     * @throws Exception
     */
    public function clickAction(Request $request, Field $field)
    {
        $x = $request->get('x');
        $y = $request->get('y');

        $data = $field->getData();

        if ($data[$x][$y]['bomb']) {
            $result = ['bomb' => true] + $data;
        } else {
            $result = $this->gameService->getResult($x, $y, $field);

            if (($field->getHorizontal() * $field->getVertical() - $field->getBombAmount()) === $this->gameService->openCellAmount($field->getData())) {
                $field->setFinishedAt(new \DateTime());
                $result['finishedAt'] = $field->getFinishedAt();
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($field);
            $em->flush();
        }

        return new JsonResponse($result, Response::HTTP_OK);
    }


    /**
     * @Route ("/game/markBomb/{id}", name = "markBomb")
     *
     * @param Request $request
     * @param Field $field
     *
     * @return JsonResponse
     */
    public function markBomb(Request $request, Field $field)
    {
        $x = $request->get('x');
        $y = $request->get('y');

        $data = $field->getData();

        $data[$x][$y]['marked'] = $data[$x][$y]['marked'] ? false : true;

        $field->setData($data);

        $em = $this->getDoctrine()->getManager();
        $em->persist($field);
        $em->flush();

        return new JsonResponse([], Response::HTTP_OK);
    }

    /**
     * @Route ("/game/dbClick/{id}", name = "dbClick")
     *
     * @param Request $request
     * @param Field $field
     *
     * @return JsonResponse
     */
    public function dbClickAction(Request $request, Field $field)
    {
        $x = $request->get('x');
        $y = $request->get('y');

        $data = $field->getData();

        if ($data[$x][$y]['clicked']) {
            $amountMarked = $this->gameService->nearMarked($x, $y, $field);

            if ($data[$x][$y]['nearBomb'] == $amountMarked) {
                $result = $this->gameService->openNear($x, $y, $field);
            } else {
                return new JsonResponse([], Response::HTTP_OK);
            }
        } else {
            return new JsonResponse([], Response::HTTP_OK);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($field);
        $em->flush();

        return new JsonResponse($result, Response::HTTP_OK);
    }
}
