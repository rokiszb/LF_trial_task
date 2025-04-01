<?php

namespace App\Scheduler\Task;

use App\Repository\NewsRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Scheduler\Attribute\AsPeriodicTask;

#[AsPeriodicTask(frequency: '1 week', method: 'sendStatistics',)]
class SendWeeklyTopStatistics
{
    public function __construct(
        private NewsRepository $newsRepository,
        private MailerInterface $mailer,
        private string $recipientEmail,
    ){}

    public function sendStatistics(): void
    {
        // Get Top 10 news
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
    }
}
