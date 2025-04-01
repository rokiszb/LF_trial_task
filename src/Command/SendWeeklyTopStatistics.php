<?php

namespace App\Command;

use App\Repository\NewsRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

#[AsCommand(
    name: 'app:send-top-statistics',
    description: 'Sends weekly Top 10 statistics to the designated email address',
)]
class SendWeeklyTopStatistics extends Command
{
    public function __construct(
        private NewsRepository $newsRepository,
        private MailerInterface $mailer,
        private string $recipientEmail = 'admin@example.com' // Default value, should be configured via service config
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('This command sends the Top 10 most viewed news statistics to a designated email address');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Preparing weekly Top 10 statistics email...');

        $topNews = $this->newsRepository->findTopViewed(10);

        // Send statistics email
        $email = new TemplatedEmail();
        $email
            ->from('no-reply@yourdomain.com')
            ->to($this->recipientEmail)
            ->subject('Weekly Top 10 News Statistics')
            ->htmlTemplate('emails/top_statistics.html.twig')
            ->context([
                'top_news' => $topNews,
                'date_range' => [
                    'start' => (new \DateTime('7 days ago'))->format('Y-m-d'),
                    'end' => (new \DateTime())->format('Y-m-d'),
                ],
            ]);

        $this->mailer->send($email);

        $output->writeln('Weekly Top 10 statistics email sent successfully to ' . $this->recipientEmail);

        return Command::SUCCESS;
    }
}
