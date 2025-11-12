<?php declare(strict_types=1);

namespace App\Command;

use App\Contexts\Shared\Domain\Email\Email;
use App\Contexts\Shared\Domain\Email\EmailSender;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-email',
    description: 'Send a test email to verify email configuration'
)]
class TestEmailCommand extends Command
{
    public function __construct(
        private readonly EmailSender $emailSender
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'to',
            InputArgument::OPTIONAL,
            'Email address to send to',
            'test@example.com'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $toEmail = $input->getArgument('to');

        $io->title('Testing Email System');
        $io->info('Sending test email to: ' . $toEmail);

        try {
            $htmlBody = <<<HTML
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; }
                    .container { max-width: 600px; margin: 0 auto; background: #f4f4f4; padding: 20px; border-radius: 8px; }
                    h1 { color: #667eea; }
                    .success { color: #28a745; font-weight: bold; }
                </style>
            </head>
            <body>
                <div class="container">
                    <h1>🎮 Email System Test</h1>
                    <p>This is a test email from <strong>Juga en Equipo</strong> platform.</p>
                    <p class="success">✅ If you're reading this, the email system is working correctly!</p>
                    <hr>
                    <p style="color: #666; font-size: 12px;">
                        Sent at: {DATE_TIME}<br>
                        From: Juga en Equipo<br>
                        Environment: {ENV}
                    </p>
                </div>
            </body>
            </html>
            HTML;

            $textBody = <<<TEXT
            Email System Test
            
            This is a test email from Juga en Equipo platform.
            
            ✅ If you're reading this, the email system is working correctly!
            
            ---
            Sent at: {DATE_TIME}
            From: Juga en Equipo
            Environment: {ENV}
            TEXT;

            $dateTime = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
            $env = $_ENV['APP_ENV'] ?? 'unknown';

            $htmlBody = str_replace(['{DATE_TIME}', '{ENV}'], [$dateTime, $env], $htmlBody);
            $textBody = str_replace(['{DATE_TIME}', '{ENV}'], [$dateTime, $env], $textBody);

            $email = new Email(
                $toEmail,
                '🎮 Test Email - Juga en Equipo',
                $htmlBody,
                $textBody
            );

            $this->emailSender->send($email);

            $io->success([
                'Email sent successfully!',
                'Check your inbox at: ' . $toEmail,
                '',
                'If using Mailpit (dev), open: http://localhost:8025',
                'If using SendGrid (prod), check SendGrid Activity Feed'
            ]);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $io->error([
                'Failed to send email!',
                'Error: ' . $e->getMessage(),
                '',
                'Troubleshooting:',
                '1. Check MAILER_DSN in .env file',
                '2. Verify Mailpit is running: docker ps | grep mailpit',
                '3. Check logs: docker logs jee_mailpit',
                '4. Run: php bin/console debug:container EmailSender'
            ]);

            return Command::FAILURE;
        }
    }
}

