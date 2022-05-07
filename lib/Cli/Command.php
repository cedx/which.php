<?php declare(strict_types=1);
namespace Which\Cli;

use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\{InputArgument, InputInterface, InputOption};
use Symfony\Component\Console\Output\OutputInterface;
use function Which\which;

/**
 * The console command.
 */
class Command extends \Symfony\Component\Console\Command\Command {

	/**
	 * The command name.
	 * @var string
	 */
	protected static $defaultName = "which";

	/**
	 * Configures the current command.
	 */
	protected function configure(): void {
		$this
			->setDescription("Find the instances of an executable in the system path.")
			->addArgument("executable", InputArgument::REQUIRED, "The executable to find")
			->addOption("all", "a", InputOption::VALUE_NONE, "List all instances of executables found, instead of just the first one");
	}

	/**
	 * Executes the current command.
	 * @param InputInterface $input The input arguments and options.
	 * @param OutputInterface $output The console output.
	 * @return int The exit code.
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int {
		/** @var string $command */
		$command = $input->getArgument("executable");

		/** @var bool $all */
		$all = $input->getOption("all");

		try {
			$executables = $all ? which($command)->all(throwIfNotFound: true) : which($command)->first(throwIfNotFound: true);
			if (!is_array($executables)) $executables = [$executables];
			$output->writeln($executables);
			return 0;
		}

		catch (\UnderflowException $e) { return 1; }
		catch (\Throwable $e) { throw new RuntimeException($e->getMessage(), 2); }
	}
}
