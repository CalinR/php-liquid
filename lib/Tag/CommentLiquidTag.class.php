<?php
/**
 * Creates a comment; everything inside will be ignored
 *
 * @example
 * {% comment %} This will be ignored {% endcomment %}
 * 
 * @package Liquid
 */
class CommentLiquidTag extends LiquidBlock
{
	/**
	 * Renders the block
	 *
	 * @param LiquidContext $context
	 * @return string
	 */
	function render(&$context)
	{
		return '';
	}	
}