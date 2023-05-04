<?php

namespace Adminx\Common\Mail\Frontend;

use Adminx\Common\Models\FormAnswer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Mail\Factory;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Mail\SentMessage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class FormAnswerMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public FormAnswer $formAnswer)
    {

    }

    /**
     * @param Factory|Mailer $mailer
     */
    public function send($mailer): SentMessage|null
    {
        if ($this->setAppMailConfig()) {
            $mailer = Mail::mailer($this->formAnswer->site->id);
        }

        return parent::send($mailer);
    }

    public function getMailConfig(): array
    {

        return [
            'transport'  => 'smtp',
            'encryption' => $this->formAnswer->site->config->mail->security ?? false,
            'host'       => $this->formAnswer->site->config->mail->host,
            'port'       => $this->formAnswer->site->config->mail->port,
            'username'   => $this->formAnswer->site->config->mail->user,
            'password'   => $this->formAnswer->site->config->mail->password_decrypt(),
        ];
    }

    public function validateSiteMailConfig()
    {
        if (($this->formAnswer->site->config->mail ?? false) && !$this->formAnswer->site->config->mail->checked) {
            $this->formAnswer->site->config->mail->checkConnection();
        }

        return $this->formAnswer->site->config->mail->checked ?? false;
    }

    public function setAppMailConfig()
    {
        if ($this->validateSiteMailConfig()) {

            config([
                       "mail.mailers.{$this->formAnswer->site->id}" => $this->getMailConfig(),
                   ]);

            return true;
        }


        return false;
    }

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {

        $from = [
            'address' => $this->formAnswer->form->config->mail_from_address ?? $this->formAnswer->site->config->mail->from_address ?? config('mail.from.address'),

            'name' => $this->formAnswer->form->config->mail_from_name ?? $this->formAnswer->site->config->mail->from_name ?? $this->formAnswer->site->title ?? config('mail.from.name'),
        ];

        $replyTo = [
            'address' => $this->formAnswer->form->config->mail_reply_to_address ?? $from['address'],

            'name' => $this->formAnswer->form->config->mail_reply_to_name ?? $from['name'],
        ];

        return new Envelope(
            from:    new Address($from['address'], $from['name']),
            //replyTo: $replyTo,
            subject: "{$this->formAnswer->site->title} - Nova resposta de formulário recebida em " . $this->formAnswer->created_at->format('d/m/Y \à\s H:i'),
            tags:    ['contact', 'form']
        );
    }

    /**
     * Get the message headers.
     *
     * @return Headers
     */
    public function headers(): Headers
    {
        return new Headers(
            text: [
                      'List-Unsubscribe' => "<{$this->formAnswer->site->url}>",
                  ],
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content(): Content
    {
        return new Content(
            view: 'adminx-common::layouts.mail.frontend.form-answer',
            with: [
                      'formAnswer' => $this->formAnswer,
                  ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments(): array
    {
        return [];
    }
}
