<?php

namespace Graphpish\Cli;

interface PluginI {
	/**
	 * Plugins must have an empty constructor, as we got only args for cli()
	 */
	public function __construct();
	/**
	 * Takes the command line paramers and runs
	 */
	public function cli(array $argv);
}