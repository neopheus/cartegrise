<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Utils\{PaymentUtils, PaymentResponseTreatment, StatusTreatment};
use App\Entity\Demande;
use App\Entity\NotificationEmail;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Manager\SessionManager;
use App\Manager\DemandeManager;
use App\Manager\FraisTreatmentManager;
use App\Manager\TransactionManager;
use App\Manager\HistoryTransactionManager;
use App\Manager\NotificationEmailManager;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class PaymentController extends AbstractController
{
    /**
     * @Route("/demande/{demande}/payment", name="payment_demande")
     */
    public function index(Demande $demande)
    {
        
        return $this->render(
            "payment/index.html.twig", 
            ['demande' => $demande]
        );
    }

    /**
     * @Route("/payment/request/{demande}", name="payment_request")
     */
    public function request(
        Demande $demande, 
        PaymentUtils $paymentUtils, 
        ParameterBagInterface $parameterBag, 
        DemandeManager $demandeManager, 
        TransactionManager $transactionManager,
        FraisTreatmentManager $fraisTreatmentManager
    )
    {
        $amount = $fraisTreatmentManager->fraisTotalOfCommande($demande->getCommande());
        $email = $this->getUser()->getEmail();
        $demandeManager->checkPayment($demande);
        $idTransaction = $transactionManager->generateIdTransaction($demande->getTransaction());
        $facture = $transactionManager->generateNumFacture($demande->getTransaction());
        $amount *=100;
        $paramDynamical = [
            'amount' => $amount,
            'customer_email' => $email,
        ];
        $param = $parameterBag->get('payment_params');
        $bin   = $parameterBag->get('payment_binary');
        $param = array_merge($param, $paramDynamical);
        $response = $paymentUtils->request($param, $bin);
        $demande->getTransaction()->setTransactionId($response['transactionId']);
        $transactionManager->save($demande->getTransaction());
        
        return new Response($response['template']);
    }

    /**
     * @Route("/payment/ipn", name="instant_payment_notification")
     */
    public function notification(
        Request $request, 
        SessionManager $sessionManager, 
        \Swift_Mailer $mailer,
        PaymentUtils $paymentUtils,
        ParameterBagInterface $parameterBag, 
        PaymentResponseTreatment $responseTreatment, 
        StatusTreatment $statusTreatment,
        HistoryTransactionManager $historyTransactionManager,
        TransactionManager $transactionManager,
        DemandeManager $demandeManager,
        NotificationEmailManager $notificationManager
    )
    {
        $response = $request->request->get('DATA');
        $responses = $this->getResponse($response, $paymentUtils, $parameterBag, $responseTreatment);
        $adminEmails = $notificationManager->getAllEmailOf(NotificationEmail::PAIMENT_NOTIF);
        // send mail
            $this->addHistoryTransaction($responses, $historyTransactionManager);
            $transaction = $transactionManager->findByTransactionId($responses["transaction_id"]);
            $files = [];
            if ($transaction->getStatus() === 00) {
                $file = $demandeManager->generateFacture($transaction->getDemande());
                $files = [$file];
            }
            $this->sendMail($mailer, $responses, $responses["customer_email"], $adminEmails, $files);
        // end send mail

        return new Response('ok');
    }

    /**
     * @Route("/payment/success", name="payment_success")
     */
    public function success(
        Request $request,
        PaymentUtils $paymentUtils,
        ParameterBagInterface $parameterBag,
        PaymentResponseTreatment $responseTreatment,
        TransactionManager $transactionManager
    )
    {
        $response = $request->request->get('DATA');
        // dd($response);
        $responses = $this->getResponse($response, $paymentUtils, $parameterBag, $responseTreatment);
        $transaction = $transactionManager->findByTransactionId($responses["transaction_id"]);
        $facture = $transactionManager->generateNumFacture($transaction);
        $transaction->setFacture($facture);
        $transactionManager->save($transaction);

        return $this->render(
                'transaction/transactionResponse.html.twig',
                [
                    'responses' => $responses,
                    'transaction' => $transaction,
                ]
        );
    }

    /**
     * @Route("/payment/cancel", name="payment_cancel")
     */
    public function cancel(
        Request $request,
        PaymentUtils $paymentUtils,
        ParameterBagInterface $parameterBag, 
        PaymentResponseTreatment $responseTreatment
    )
    {
        $response = $request->request->get('DATA');
        $responses = $this->getResponse($response, $paymentUtils, $parameterBag, $responseTreatment);

        return $this->render(
                'transaction/transactionResponse.html.twig',
                [
                    'responses' => $responses,
                    'transaction' => null,
                ]
        );
    }

    /**
     * @Route("/payment/{demande}/facture", name="payment_facture")
     */
    public function facture(Demande $demande, FraisTreatmentManager $fraisTreatmentManager, DemandeManager $demandeManager)
    {
        $file = $demandeManager->generateFacture($demande);

        return new BinaryFileResponse($file);
    }

    // price with TVA
    private function calculateTOTAL($prix)
    {
        return ($prix) + ($prix * 20 / 100);
    }

    // calculate TVA 20%
    private function calculateTVA($prix)
    {
        return $prix * 20 / 100;
    }

    // to get response
    private function getResponse($response, $paymentUtils, $parameterBag, $responseTreatment)
    {
        $param = $parameterBag->get('payment_params');
        $bin   = $parameterBag->get('payment_binary');
        $return = $paymentUtils->decode($bin['response'], $param['pathfile'], $response);
    
        return $responses = $responseTreatment->getResponse($return);
    }

    //function to send email with response in sherlock treatment
    public function sendMail($mailer, $responses, $mail , $admins = [], $attachments=[])
    {
        $this->send($mailer, $mail, $responses, '', $attachments);
        $this->send($mailer, $admins, $responses, "chère Admin, ", $attachments);
    }
    //function to send email unit
    public function send($mailer, $mail, $responses, $adminPrepend='', $attachments)
    {
            $message = (new \Swift_Message($adminPrepend.'Transaction  n°: ' .$responses["transaction_id"]. ' de ' . $responses["customer_email"] ))
            ->setFrom('no-reply@cgofficiel.fr');
            if ($adminPrepend != '' && is_iterable($mail) && count($mail)>0) {
                $message->setTo(array_shift($mail))
                ->setBcc($mail);
            } else {
                $message->setTo($mail);
            }
            $message
            ->setBody(
                $this->renderView(
                    'email/registration.mail.twig',
                    array('responses' => $responses)
                ),
                'text/html'
            );
            foreach ($attachments as $attachment){
                $message->attach(\Swift_Attachment::fromPath($attachment));
            }
            $mailer->send($message);
    }

    public function addHistoryTransaction($responses, HistoryTransactionManager $historyTransactionManager)
    {
        $historyTransactionManager->saveResponseTransaction($responses);
    }
}

