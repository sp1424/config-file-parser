<?php
declare(strict_types=1);

namespace App\Command;

use App\Services\ConfigParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Yaml\Exception\ParseException;

class ConfigParserCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:parser-files';

    /**
     * @var string
     */
    protected static $defaultDescription = 'Environment config parser';

    /**
     * @var ConfigParser
     */
    private ConfigParser $configParser;

    /**
     * @param ConfigParser $configParser
     * @param string|null $name
     */
    public function __construct(ConfigParser $configParser, string $name = null)
    {
        $this->configParser = $configParser;
        parent::__construct($name);
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this->addArgument('file1', InputArgument::REQUIRED, 'First file to parse')
            ->addArgument('file2', InputArgument::REQUIRED, 'Second file to parse')
            ->addArgument('traversal', InputArgument::REQUIRED, 'Index to access');
    }

    /**
     * @inheritDoc
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->configParser->loadFiles(
                './'.$input->getArgument('file1'),
                './'.$input->getArgument('file2')
            );
        } catch (ParseException | FileNotFoundException $e){
            $output->writeln('Command failed');
            $output->writeln($e->getMessage());

            return Command::FAILURE;
        }

        $this->configParser->mergeData();
        $output->writeln('Merged data:');
        dump($this->configParser->getMergedContent()); //using debugging function as it has pretty print json
        $output->writeln('Index accessed:');
        $output->writeln($this->configParser->traverseContent($input->getArgument('traversal')));

        return Command::SUCCESS;
    }
}