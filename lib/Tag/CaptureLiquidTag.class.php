<?php
/**
 * Captures the output inside a block and assigns it to a variable
 * 
 * @example
 * {% capture foo %} bar {% endcapture %}
 *
 * @package Liquid
 */
class CaptureLiquidTag extends LiquidBlock
{
	/**
	 * The variable to assign to
	 *
	 * @var string
	 */
	var $to;


	/**
	 * Constructor
	 *
	 * @param string $markup
	 * @param Array $tokens
	 * @param LiquidFileSystem $file_system
	 * @return CaptureLiquidTag
	 */
	function __construct($markup, &$tokens, &$file_system)
	{
		$syntax_regexp = new LiquidRegexp('/(\w+)/');
		
		if($syntax_regexp->match($markup))
		{
			$this->to = $syntax_regexp->matches[1];
			parent::__construct($markup, $tokens, $file_system);
		}
		else
		{
			throw new LiquidException("Syntax Error in 'capture' - Valid syntax: assign [var] = [source]"); // harry
		}
	}


	/**
	 * Renders the block
	 *
	 * @param LiquidContext $context
	 */
	function render(& $context)
	{
		$output = parent::render($context);
		
		$context->set($this->to, $output);
	}		
}