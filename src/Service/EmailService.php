<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class EmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig,
        private string $adminEmail = 'contact@armandadjibako.com'
    ) {}

    public function sendContactNotification(array $messageData): bool
    {
        try {
            $htmlContent = $this->twig->render('emails/contact_notification.html.twig', [
                'message' => $messageData
            ]);

            $email = (new Email())
                ->from($this->adminEmail)
                ->to($this->adminEmail)
                ->subject('Nouveau message de contact - Portfolio')
                ->html($htmlContent);

            $this->mailer->send($email);
            return true;
        } catch (\Exception $e) {
            // Log l'erreur si nécessaire
            return false;
        }
    }

    public function sendContactConfirmation(string $toEmail, array $messageData): bool
    {
        try {
            $htmlContent = $this->twig->render('emails/contact_confirmation.html.twig', [
                'message' => $messageData
            ]);

            $email = (new Email())
                ->from($this->adminEmail)
                ->to($toEmail)
                ->subject('Confirmation de réception - Portfolio Armand Adjibako')
                ->html($htmlContent);

            $this->mailer->send($email);
            return true;
        } catch (\Exception $e) {
            // Log l'erreur si nécessaire
            return false;
        }
    }

    private function getContactNotificationTemplate(array $messageData): string
    {
        return "
        <html>
        <body style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <h2 style='color: #008d4d;'>Nouveau message de contact reçu</h2>
            
            <div style='background: #f5f5f5; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                <p><strong>Nom :</strong> {$messageData['nom_expediteur']}</p>
                <p><strong>Email :</strong> {$messageData['email_expediteur']}</p>
                <p><strong>Sujet :</strong> {$messageData['sujet']}</p>
                <p><strong>Date :</strong> " . date('d/m/Y H:i') . "</p>
            </div>
            
            <div style='background: white; padding: 20px; border: 1px solid #ddd; border-radius: 8px;'>
                <h3>Message :</h3>
                <p style='white-space: pre-wrap;'>" . htmlspecialchars($messageData['message']) . "</p>
            </div>
            
            <p style='margin-top: 30px; color: #666; font-size: 14px;'>
                Ce message a été envoyé depuis le formulaire de contact de votre portfolio.
            </p>
        </body>
        </html>";
    }

    private function getContactConfirmationTemplate(array $messageData): string
    {
        return "
        <html>
        <body style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='text-align: center; margin-bottom: 30px;'>
                <h1 style='color: #008d4d;'>Merci pour votre message !</h1>
            </div>
            
            <p>Bonjour <strong>{$messageData['nom_expediteur']}</strong>,</p>
            
            <p>Nous avons bien reçu votre message concernant : <strong>\"{$messageData['sujet']}\"</strong></p>
            
            <div style='background: #f5f5f5; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                <p style='margin: 0;'><strong>Résumé de votre message :</strong></p>
                <p style='margin: 10px 0 0 0; white-space: pre-wrap;'>" . htmlspecialchars($messageData['message']) . "</p>
            </div>
            
            <p>Nous vous répondrons dans les plus brefs délais.</p>
            
            <p>Cordialement,<br>
            <strong>Armand S. ADJIBAKO</strong><br>
            Développeur Full Stack</p>
            
            <hr style='margin: 30px 0; border: none; border-top: 1px solid #eee;'>
            
            <div style='text-align: center; color: #666; font-size: 12px;'>
                <p>Portfolio Armand Adjibako - Développeur Full Stack</p>
                <p>Email: contact@armandadjibako.com</p>
            </div>
        </body>
        </html>";
    }

    public function sendProjectInquiry(string $toEmail, array $projectData): bool
    {
        try {
            $htmlContent = $this->twig->render('emails/project_inquiry.html.twig', [
                'project' => $projectData
            ]);

            $email = (new Email())
                ->from($this->adminEmail)
                ->to($this->adminEmail)
                ->subject('Nouvelle demande de projet - Portfolio')
                ->html($htmlContent);

            $this->mailer->send($email);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function sendProjectConfirmation(string $toEmail, array $projectData): bool
    {
        try {
            $htmlContent = $this->twig->render('emails/project_confirmation.html.twig', [
                'project' => $projectData
            ]);

            $email = (new Email())
                ->from($this->adminEmail)
                ->to($toEmail)
                ->subject('Confirmation de demande de projet - Portfolio Armand Adjibako')
                ->html($htmlContent);

            $this->mailer->send($email);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function getProjectInquiryTemplate(array $projectData): string
    {
        return "
        <html>
        <body style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <h2 style='color: #008d4d;'>Nouvelle demande de projet</h2>
            
            <div style='background: #f5f5f5; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                <p><strong>Nom :</strong> {$projectData['nom']}</p>
                <p><strong>Email :</strong> {$projectData['email']}</p>
                <p><strong>Entreprise :</strong> {$projectData['entreprise']}</p>
                <p><strong>Budget :</strong> {$projectData['budget']}</p>
                <p><strong>Type de projet :</strong> {$projectData['type_projet']}</p>
                <p><strong>Date souhaitée :</strong> {$projectData['date_souhaitee']}</p>
            </div>
            
            <div style='background: white; padding: 20px; border: 1px solid #ddd; border-radius: 8px;'>
                <h3>Description du projet :</h3>
                <p style='white-space: pre-wrap;'>" . htmlspecialchars($projectData['description']) . "</p>
            </div>
        </body>
        </html>";
    }
}
