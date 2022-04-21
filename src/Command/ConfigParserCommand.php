<?php
declare(strict_types=1);

namespace App\Command;

use App\Services\ConfigParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigParserCommand extends Command
{
    protected static $defaultName = 'app:parser-files';

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
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->configParser->loadFiles('./fixtures/config.json', './fixtures/config.local.json');

        $this->configParser->mergeData();

        return Command::SUCCESS;
    }
}