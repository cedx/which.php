<?php namespace Which;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument, InputInterface, InputOption};
use Symfony\Component\Console\Output\OutputInterface;
use function Which\which;

/**
 * The `which` command.
 */
#[AsCommand(name: "which", description: "Find the instances of an executable in the system path.")]
class WhichCommand extends Command {

	/**
	 * Configures this command.
	 */
	protected function configure(): void {
		$this
			->addArgument("executable", InputArgument::REQUIRED, "The executable to find")
			->addOption("all", "a", InputOption::VALUE_NONE, "List all instances of executables found (instead of just the first one)")
			->addOption("silent", "s", InputOption::VALUE_NONE, "Silence the output, just return the exit code (0 if any executable is found, otherwise 1)");
	}

	/**
	 * Executes this command.
	 * @param InputInterface $input The input arguments and options.
	 * @param OutputInterface $output The console output.
	 * @return int The exit code.
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int {
		/** @var string $command */
		$command = $input->getArgument("executable");

		/** @var bool $all */
		$all = $input->getOption("all");

		/** @var bool $silent */
		$silent = $input->getOption("silent");

		try {
			$finder = which($command);
			$executables = $all ? $finder->all(throwIfNotFound: true) : $finder->first(throwIfNotFound: true);
			if (!$silent) $output->writeln(is_array($executables) ? $executables : [$executables]);
			return Command::SUCCESS;
		}

		catch (\UnderflowException $e) {
			if (!$silent) $output->writeln($e->getMessage());
			return Command::FAILURE;
		}
	}
}
