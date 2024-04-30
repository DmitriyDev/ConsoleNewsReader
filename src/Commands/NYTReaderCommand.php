<?php

namespace App\Commands;

use App\Readers\ArticleReaderFactory;
use App\Readers\RSSReader;
use App\Readers\SourceEnum;

use Feed;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'nyt:reader', description: 'Reader for NYT')]
class NYTReaderCommand extends Command
{
    protected static string $name = 'nyt:reader';

    protected static string $description = 'Reader for NYT';

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $rssReader = new RSSReader();
        $sourceArticles = $rssReader->read(SourceEnum::NY_TIMES->value);
        $io = new SymfonyStyle($input, $output);
        $io->writeln("Last articles from NYTimes:");

        $this->writeArticleList($io, $sourceArticles);
        $totalArticles = $sourceArticles->item->count();

        do {
            $answer = (int)$io->ask('Select article to read (article id): ');
            if ($answer <= 0 && $answer > $totalArticles) {
                $io->error('Wrong article ID');
            }

            $this->writeArticle($io, $sourceArticles, $answer);

            switch ($io->choice('What next?', ['next', 'refresh', 'quit'])) {
                case 'next':
                {
                    $isFinished = false;
                    break;
                }
                case 'refresh':
                    return $this->execute($input, $output);
                case 'quit':
                default:
                {
                    $io->writeln('Bye!');
                    $isFinished = true;
                }
            }
        } while (!$isFinished);

        return Command::SUCCESS;
    }

    private function writeArticleList(SymfonyStyle $io, Feed $sourceArticles): void
    {
        $i = -1;
        foreach ($sourceArticles->item as $article) {
            $i++;
            if (!str_contains($article->link, '.html')) {
                $io->writeln(sprintf('[--] %s (Unable to parse)', $article->title));
                continue;
            }
            $io->writeln(sprintf('[%d] %s', $i, $article->title));
        }
    }

    private function writeArticle(SymfonyStyle $io, Feed $sourceArticles, int $articleId): void
    {
        $article = $sourceArticles->item[$articleId];
        $io->success($article->title);
        $io->success($article->link);
        $reader = (new ArticleReaderFactory())->getArticleReader(SourceEnum::NY_TIMES);

        $io->writeln(sprintf('%s', $reader->readArticle($article->link)));
    }

}