<?php

/**
 * Generic notification Mailable for Laravel 12.
 *
 * Demonstrates the transactional email pattern used in tekavogtil for
 * notifying the site owner when a new form submission arrives.
 *
 * Key patterns shown:
 *  ✓ Constructor property promotion (PHP 8.1+) — model is auto-available in view
 *  ✓ Reply-to set to the submitter's email — owner can reply directly
 *  ✓ Norwegian subject line
 *  ✓ Resend mailer via Laravel Mail
 *  ✓ Blade email template with the model data
 *
 * ─── .env configuration (Resend) ─────────────────────────────────────────
 *
 *   MAIL_MAILER=resend
 *   RESEND_KEY=re_your_api_key_here
 *   MAIL_FROM_ADDRESS=noreply@yourdomain.no
 *   MAIL_FROM_NAME="Your Company"
 *
 * ─── Dispatch the mailable ───────────────────────────────────────────────
 *
 * In your controller, after saving the submission:
 *
 *   use Illuminate\Support\Facades\Mail;
 *
 *   Mail::to(config('mail.owner_address', 'owner@example.com'))
 *       ->send(new NewSubmissionNotification($submission));
 *
 * ─── Queue it for better UX (optional) ───────────────────────────────────
 *
 *   Mail::to('owner@example.com')
 *       ->queue(new NewSubmissionNotification($submission));
 *
 * Add 'implements ShouldQueue' to the class and run: php artisan queue:work
 *
 * ─────────────────────────────────────────────────────────────────────────
 */

namespace App\Mail;

use App\Models\FormSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewSubmissionNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Constructor uses PHP 8.1+ property promotion.
     * The public $submission property is automatically available
     * in the Blade email template — no need to pass it explicitly.
     */
    public function __construct(public FormSubmission $submission)
    {
    }

    /**
     * The email envelope — defines the subject and reply-to header.
     *
     * Setting reply-to to the submitter's email means when the owner
     * clicks "Reply" in their email client, the response goes directly
     * to the person who submitted the form.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [
                new Address(
                    address: $this->submission->email,
                    name: $this->submission->name,
                ),
            ],
            subject: 'Ny innsending fra ' . $this->submission->name,
        );
    }

    /**
     * The email content — points to the Blade template.
     *
     * Template lives at: resources/views/mail/new-submission.blade.php
     * The $submission model is available in the template automatically
     * because the constructor property is public.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.new-submission',
        );
    }
}


/*
 * ─── Blade email template ─────────────────────────────────────────────────
 *
 * File: resources/views/mail/new-submission.blade.php
 *
 * ──────────────────────────────────────────────────────────────────────────
 *
 * <!DOCTYPE html>
 * <html>
 * <body style="font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: 0 auto; padding: 24px;">
 *
 *   <h2>Ny innsending fra {{ $submission->name }}</h2>
 *
 *   <table style="width: 100%; border-collapse: collapse;">
 *     <tr>
 *       <td style="padding: 8px 0; font-weight: bold; width: 120px;">Navn</td>
 *       <td style="padding: 8px 0;">{{ $submission->name }}</td>
 *     </tr>
 *     <tr>
 *       <td style="padding: 8px 0; font-weight: bold;">E-post</td>
 *       <td style="padding: 8px 0;">{{ $submission->email }}</td>
 *     </tr>
 *     <tr>
 *       <td style="padding: 8px 0; font-weight: bold;">Telefon</td>
 *       <td style="padding: 8px 0;">{{ $submission->phone ?? '—' }}</td>
 *     </tr>
 *     <tr>
 *       <td style="padding: 8px 0; font-weight: bold; vertical-align: top;">Melding</td>
 *       <td style="padding: 8px 0;">{{ $submission->message }}</td>
 *     </tr>
 *   </table>
 *
 *   <hr style="margin: 24px 0; border: none; border-top: 1px solid #eee;">
 *   <p style="color: #999; font-size: 12px;">
 *     Innsendt {{ now()->format('d.m.Y \k\l. H:i') }} CET
 *   </p>
 *
 * </body>
 * </html>
 *
 * ──────────────────────────────────────────────────────────────────────────
 */
