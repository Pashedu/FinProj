<?php

namespace Softgroup\FinalBundle\Controller;

use Softgroup\FinalBundle\Entity\Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Message controller.
 *
 * @Route("message")
 */
class MessageController extends Controller
{
    /**
     * Creates a new message entity.
     *
     * @Route("/", name="message_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $message = new Message();
        $form = $this->createForm('Softgroup\FinalBundle\Form\MessageType', $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $routeString = 'ABCDEFGHIJKLMNOPQRSTUWXYZabcdefghijklmnopqrstuwxyz0123456789';
            $timeAsSalt = $_SERVER["REQUEST_TIME_FLOAT"];
            $rawHash=hash('sha1',$message->getMessagetext().$timeAsSalt,false);
            $index=(rand(0,strlen($routeString)))%strlen($rawHash);
            $urlString=[];
            for($i=0;$i<20;$i++)
            {
                $urlString[]=$routeString[($i+ord($rawHash[$index]))%strlen($routeString)];
                $index=($i+ord($rawHash[$index]))%strlen($rawHash);
            }
            $message->setUrl(implode($urlString));
            $em->persist($message);
            $em->flush();
            $request->getSession()->set('url',$message->getUrl());

            return $this->redirectToRoute('show_url');
        }

        return $this->render('message/new.html.twig', array(
            'message' => $message,
            'form' => $form->createView(),
        ));
    }
    /**
     * @Route("/secret/", name="show_url")
     * @Method("GET")
     */
    public function showurlAction(Request $request)
    {
        $url=$request->getSession()->get('url');
        if($url) {
            $request->getSession()->remove('url');
            return new Response(
                '<html><body> Note link ready  '
                . '<span>' . $this->generateUrl('message_new', array(), UrlGeneratorInterface::ABSOLUTE_URL).'secret/'.$url .
                '</span><br><a href="'.$this->generateUrl('message_new').'">to main</a></body></html>'
            );
        }
        else return $this->redirectToRoute('message_new');
    }

    /**
     * Lists target message
     *
     * @Route("/secret/{url}", name="message_target")
     * @Method("GET")
     */
    public function targetAction($url)
    {
        $em = $this->getDoctrine()->getManager();
        $message = $em->getRepository('SoftgroupFinalBundle:Message')->findOneByUrl($url);
        $nowTime = new \DateTime('now', new \DateTimeZone('UTC'));

        if($message) {
            if( $message->getDeleteto())
            {
                $messageTime = $message->getDeleteto();


                if($messageTime > $nowTime)
                {
                    if(!$message->getDeletedate())
                    {
                        $message->setDeletedate($nowTime);
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($message);
                        $em->flush();
                    }
                    return $this->redirectToRoute('message_new');
                }
            }
            if($message->getDeletedate()){
                return $this->redirectToRoute('message_new');
            }
            else {
                $deletedMessage = $message->getMessagetext();
                $message->setDeletedate($nowTime);
                $em = $this->getDoctrine()->getManager();
                $em->persist($message);
                $em->flush();
                if($message->getEmail()) {
                    $emailMessage = \Swift_Message::newInstance()
                        ->setSubject('TestMail')
                        ->setFrom('pashedu@sendgrid.com')
                        ->setTo($message->getEmail())
                        ->setBody('Test Email Message '.$deletedMessage);
                    $this->get('mailer')->send($emailMessage);
                }
                return new Response(
                    '<html><body>' . $deletedMessage . '</body></html>'
                );
            }
        }
        else {
            throw new NotFoundHttpException('Sorry not existing!');
        }
    }

}
