<?php

namespace Graphpish\Cli;

interface RendererI {
	/**
	 * Plugins must have an empty constructor, as we got only args for render()
	 */
	public function __construct();
	/**
	 * Takes the command line paramers and runs
	 */
	public function render(\Graphpish\Graph\Graph $graph);
}