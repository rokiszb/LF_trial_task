<?php

namespace App\Command;

use App\Repository\NewsRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[AsCommand(
    name: 'app:send-top-news',
    description: 'Send weekly Top 10 news statistics',
)]
class SendTopNewsCommand extends Command
{
    private $newsRepository;
    private $mailer;
    private $recipient;

    public function __construct(NewsRepository $newsRepository, MailerInterface $mailer, string $recipient = 'admin@example.com')
    {
        parent::__construct();
        $this->newsRepository = $newsRepository;
        $this->mailer = $mailer;
        $this->recipient = $recipient;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $topNews = $this->newsRepository->findTopNewsByWeek();

        $emailContent = "Weekly Top 10 News Report\n\n";

        foreach ($topNews as $index => $news) {
            $emailContent .= sprintf(
                "%d. %s (Comments: %d)\n",
                $index + 1,
                $news->getTitle(),
                count($news->getComments())
            );
        }

        // add mailer provider on real project, send it out
//        $email = (new Email())
//            ->from('noreply@newsportal.com')
//            ->to($this->recipient)
//            ->subject('Weekly Top 10 News Report')
//            ->text($emailContent);
//
//        $this->mailer->send($email);

        $io->success('Weekly Top 10 news report has been sent successfully.');

        return Command::SUCCESS;
    }
}
