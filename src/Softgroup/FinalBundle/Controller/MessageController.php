<?php

namespace Softgroup\FinalBundle\Controller;

use Softgroup\FinalBundle\Entity\Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Message controller.
 *
 * @Route("/")
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
            $timeAsSalt =  $request->get("REQUEST_TIME_FLOAT");// PHP super global should never be used $_SERVER["REQUEST_TIME_FLOAT"];
            $rawHash=hash('sha1',$message->getMessagetext().$timeAsSalt,false);
            $index=(rand(0,strlen($routeString)))%strlen($rawHash);
            $urlString=[];
            for($i=0;$i<20;$i++)
            {
                $urlString[]=$routeString[($i+ord($rawHash[$index]))%strlen($routeString)];
                $index=($i+ord($rawHash[$index]))%strlen($rawHash);
            }
            $message->setUrl(implode($urlString));
            $message->setCreatorip($request->getClientIp());
            if (!is_null($message->getPlainPassword())) {
                $message->setPassword(password_hash($message->getPlainPassword(), PASSWORD_DEFAULT));
            }
            $em->persist($message);
            $em->flush();
            $request->getSession()->set('url',$message->getUrl());

            return $this->redirectToRoute('show_url');
        }

        return $this->render('SoftgroupFinalBundle:message:new.html.twig', array(
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

                '<html>
                <meta charset="UTF-8" />
                <meta http-equiv="x-ua-compatible" content="IE=edge" />
                <title>Welcome!</title>
                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
                <link type="text/css" rel="stylesheet" href="{{ asset(\'css/main.css\') }}">
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
                <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
                <link rel="icon" type="image/x-icon" href="{{ asset(\'favicon.ico\') }}" />
                <style type="text/css">
                    #blockBody{
                        min-width: 1100px;
                        background-color: #F5F5DC;
                    }
                    #header{
                        border: 1px solid black;
                        left: 0; right: 0;
                        height: 120px;
                        margin: 10px;
                    }
                    #menu{
                        border: 1px solid black;
                        left: 0; right: 0;
                        height: 40px;
                        margin: 10px;
                        padding: 20px 20px 40px 20px;
                    }
                    #center{
                        border: 1px solid black;
                        min-height: 550px;
                        margin-left: 273px;
                        margin-right: 273px;
                        background-color: #F5F5DC;
                    }
                    #leftBlock{
                        border: 1px solid black;
                        width: 250px;
                        float: left;
                        margin-left: 10px;
                        min-height:550px;
                    }
                    #rightBlock{
                        border: 1px solid black;
                        width: 250px;
                        float: right;
                        margin-right: 10px;
                        min-height:550px;
                    }
                    #footer{
                        border: 1px solid black;
                        left: 0; right: 0; bottom: 0px;
                        height: 150px;
                        clear: both;
                        margin: 10px;
                    }
                    h1,h2,h4{
                        text-align: center;
                    }
                </style>
                
                <body>
                
                
                <div id="blockBody">
                    <div id="header">
                        <h1>OnesRead</h1>
                        <p style="text-align: center">Відправка повідомлень, які будуть самоліквідуватися після читання</p>
                    </div>
                    <div id="menu">
                    </div>
                    <div id="leftBlock">
                    </div>
                    <div id="rightBlock">
                    </div>
                    <div id="center">
                         <h2>Ваше повідомлення сформоване по адресу : </h2> '
                . '<span><h4>' . $this->generateUrl('message_new', array(), UrlGeneratorInterface::ABSOLUTE_URL).'secret/'.$url .
                '</h4></span><br><a href="'.$this->generateUrl('message_new').'">to main</a>
                    </div>
                    <div id="footer">
                    </div>
                </div>
               </body>
             </html>'
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
    public function targetAction($url, Request $request)
    {
        date_default_timezone_set('UTC');
        $em = $this->getDoctrine()->getManager();
        $message = $em->getRepository('SoftgroupFinalBundle:Message')->findOneByUrl($url);
        $nowTime = new \DateTime('now', new \DateTimeZone('UTC'));
        if($message) {
            $messageTime = $message->getDeleteto();
            if(!$message->getDeletedate()&&($messageTime)) {
                if($messageTime < $nowTime) {
                    $message->setDeletedate($nowTime);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($message);
                    $em->flush();
                }
            }
            if($message->getDeletedate()){
                return $this->redirectToRoute('message_new');
            }
            else
            {
                $session=new Session();
                if (($message->getPassword())&&(!$session->get('passcheck'))) {
                    return $this->redirectToRoute('password_check', array('url' => $message->getUrl()));
                }
                $session->clear();
                $deletedMessage = $message->getMessagetext();
                if(!$message->getDeleteto()) {$message->setDeletedate($nowTime);}
                $message->setReaderip($request->getClientIp());
                $em = $this->getDoctrine()->getManager();
                $em->persist($message);
                $em->flush();
                if ($message->getEmail()) {
                    $emailMessage = \Swift_Message::newInstance()
                        ->setSubject('TestMail')
                        ->setFrom('admin@onesread.host-panel.net')
                        ->setTo($message->getEmail())
                        ->setBody('Test Email Message ' . $deletedMessage);
                    $this->get('mailer')->send($emailMessage);
                }
                return new Response('
                <meta charset="UTF-8" />
                <meta http-equiv="x-ua-compatible" content="IE=edge" />
                <title>Welcome!</title>
                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
                <link type="text/css" rel="stylesheet" href="{{ asset(\'css/main.css\') }}">
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
                <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
                <link rel="icon" type="image/x-icon" href="{{ asset(\'favicon.ico\') }}" />
                <style type="text/css">
                <style type="text/css">
                    #blockBody{
                        min-width: 1100px;
                        background-color: #F5F5DC;
                    }
                    #header{
                        border: 1px solid black;
                        left: 0; right: 0;
                        height: 120px;
                        margin: 10px;
                    }
                    #menu{
                        border: 1px solid black;
                        left: 0; right: 0;
                        height: 40px;
                        margin: 10px;
                        padding: 20px 20px 40px 20px;
                    }
                    #center{
                        border: 1px solid black;
                        min-height: 550px;
                        margin-left: 273px;
                        margin-right: 273px;
                        background-color: #F5F5DC;
                    }
                    #leftBlock{
                        border: 1px solid black;
                        width: 250px;
                        float: left;
                        margin-left: 10px;
                        min-height:550px;
                    }
                    #rightBlock{
                        border: 1px solid black;
                        width: 250px;
                        float: right;
                        margin-right: 10px;
                        min-height:550px;
                    }
                    #footer{
                        border: 1px solid black;
                        left: 0; right: 0; bottom: 0px;
                        height: 150px;
                        clear: both;
                        margin: 10px;
                    }
                    h1,h2,h4{
                        text-align: center;
                    }
                </style>
                    <body>
                      <div id="blockBody">
                    <div id="header">
                        <h1>OnesRead</h1>
                        <p style="text-align: center">Відправка повідомлень, які будуть самоліквідуватися після читання</p>
                    </div>
                    <div id="menu">
                    </div>
                    <div id="leftBlock">
                    </div>
                    <div id="rightBlock">
                    </div>
                    <div id="center">
                         <h2>Ваше повідомлення :</h2></br>' . $deletedMessage . '
                    </div>
                    <div id="footer">
                    </div>
                </div>  
                   </body></html>'
                );
            }
        }
        throw new NotFoundHttpException('Sorry not existing!');
    }
    /**
     * Lists target message
     *
     * @Route("/secret/{url}/passcheck", name="password_check")
     * @Method({"GET", "POST"})
     */
    public function passAction(Request $request, $url)
    {
        $form = $this->createFormBuilder()
            ->add('Password', 'Symfony\Component\Form\Extension\Core\Type\PasswordType')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $message = $em->getRepository('SoftgroupFinalBundle:Message')->findOneByUrl($url);
            $task = $form->getData();
            if (crypt($task['Password'],$message->getPassword())==$message->getPassword())
            {
                $session = new Session();
                $session->clear();
                $session->set('passcheck',true);
                return $this->redirectToRoute('message_target',array('url'=>$url));
            }

            return new Response('
                <meta charset="UTF-8" />
                <meta http-equiv="x-ua-compatible" content="IE=edge" />
                <title>Welcome!</title>
                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
                <link type="text/css" rel="stylesheet" href="{{ asset(\'css/main.css\') }}">
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
                <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
                <link rel="icon" type="image/x-icon" href="{{ asset(\'favicon.ico\') }}" />
                <style type="text/css">
                <style type="text/css">
                    #blockBody{
                        min-width: 1100px;
                        background-color: #F5F5DC;
                    }
                    #header{
                        border: 1px solid black;
                        left: 0; right: 0;
                        height: 120px;
                        margin: 10px;
                    }
                    #menu{
                        border: 1px solid black;
                        left: 0; right: 0;
                        height: 40px;
                        margin: 10px;
                        padding: 20px 20px 40px 20px;
                    }
                    #center{
                        border: 1px solid black;
                        min-height: 550px;
                        margin-left: 273px;
                        margin-right: 273px;
                        background-color: #F5F5DC;
                    }
                    #leftBlock{
                        border: 1px solid black;
                        width: 250px;
                        float: left;
                        margin-left: 10px;
                        min-height:550px;
                    }
                    #rightBlock{
                        border: 1px solid black;
                        width: 250px;
                        float: right;
                        margin-right: 10px;
                        min-height:550px;
                    }
                    #footer{
                        border: 1px solid black;
                        left: 0; right: 0; bottom: 0px;
                        height: 150px;
                        clear: both;
                        margin: 10px;
                    }
                    h1,h2,h4{
                        text-align: center;
                    }
                </style>
                    <body>
                      <div id="blockBody">
                    <div id="header">
                        <h1>OnesRead</h1>
                        <p style="text-align: center">Відправка повідомлень, які будуть самоліквідуватися після читання</p>
                    </div>
                    <div id="menu">
                    </div>
                    <div id="leftBlock">
                    </div>
                    <div id="rightBlock">
                    </div>
                    <div id="center">
                         <h2 style="color: red">Невірний пароль!</h2>
                    </div>
                    <div id="footer">
                    </div>
                </div>  
                   </body></html>'
            );
        }
        return $this->render('SoftgroupFinalBundle:message:password.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}