<?php

namespace KALMARS\Api\Resources;

use IO\Extensions\Functions\ExternalContent;
use IO\Services\TemplateConfigService;
use Plenty\Plugin\Http\Response;
use Plenty\Plugin\Http\Request;
use IO\Api\ApiResource;
use IO\Api\ApiResponse;
use IO\Api\ResponseCode;
use IO\Constants\LogLevel;
use IO\Helper\ReCaptcha;
use IO\Services\NotificationService;
use Plenty\Plugin\Mail\Contracts\MailerContract;
use Plenty\Plugin\Mail\Models\ReplyTo;
use Plenty\Plugin\Templates\Twig;

/**
 * Class ContactMailResource
 * @package IO\Api\Resources
 */
class ContactMailResource extends ApiResource
{
    private $templateConfigService;

    /**
     * ContactMailResource constructor.
     * @param Request $request
     * @param ApiResponse $response
     */
    public function __construct(
        Request $request,
        ApiResponse $response,
        TemplateConfigService $templateConfigService)
    {
        parent::__construct($request, $response);
        $this->templateConfigService = $templateConfigService;
    }

    public function store():Response
    {
        $mailTemplate = $this->request->get('template', '');
        $contactData = $this->request->get('contactData',[]);

        if( !ReCaptcha::verify($this->request->get('recaptchaToken', null)) )
        {
            /**
             * @var NotificationService $notificationService
             */
            $notificationService = pluginApp(NotificationService::class);
            $notificationService->addNotificationCode(LogLevel::ERROR, 13);

            return $this->response->create("", ResponseCode::BAD_REQUEST);
        }

        $response = $this->sendMail($mailTemplate, $contactData);

        if($response)
        {
            return $this->response->create($response, ResponseCode::CREATED);
        }
        else
        {
            return $this->response->create($response, ResponseCode::BAD_REQUEST);
        }

    }

    public function sendMail($mailTemplate, $contactData = [])
    {
        $recipient = $this->templateConfigService->get('contact.shop_mail');
        if(isset($contactData['recipient'])){
            $recipient = $contactData['recipient'];
        }

        if(!strlen($recipient) || !strlen($mailTemplate))
        {
            return false;
        }

        /**
         * @var Twig
         */
        $twig = pluginApp(Twig::class);

        $mailTemplateParams = [];
        $mailTemplateParams['fields'] = [];
        foreach($contactData['fields'] as $value)
        {
            $mailTemplateParams['fields'][] = [
                'name' => $value['name'],
                'value' => nl2br($value['value'])
            ];
        }

        $renderedMailTemplate = $twig->render($mailTemplate, $mailTemplateParams);

        if(!strlen($renderedMailTemplate))
        {
            return false;
        }

        $cc = [];
        if(isset($contactData['cc']) && $contactData['cc'] == 'true')
        {
            $cc[] = $contactData['userMail'];
        }

        /**
         * @var MailerContract $mailer
         */
        $mailer = pluginApp(MailerContract::class);

        /**
         * @var ReplyTo $replyTo
         */
        $replyTo = pluginApp(ReplyTo::class);
        $replyTo->mailAddress = $contactData['userMail'];
        $replyTo->name = 'Customer';

        $mailer->sendHtml($renderedMailTemplate, $recipient, $contactData['subject'], $cc, [], $replyTo);

        return true;
    }
}
