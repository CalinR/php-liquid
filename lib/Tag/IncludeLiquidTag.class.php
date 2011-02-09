<?php

/**
 * Includes another, partial, template
 * 
 * @example
 * {% include 'foo' %}
 * 
 * Will include the template called 'foo'
 * 
 * {% include 'foo' with 'bar' %}
 * 
 * Will include the template called 'foo', with a variable called foo that will have the value of 'bar'
 * 
 * {% include 'foo' for 'bar' %}
 * 
 * Will loop over all the values of bar, including the template foo, passing a variable called foo
 * with each value of bar
 *
 * @package Liquid
 */
class IncludeLiquidTag extends LiquidTag
{
	/**
	 * @var string The name of the template
	 */
	private $template_name;
	
	/**
	 * @var bool True if the variable is a collection
	 */
	private $collection;
	
	/**
	 * @var mixed The value to pass to the child template as the template name
	 */
	private $variable;
	
	/**
	 * @var LiquidDocument The LiquidDocument that represents the included template
	 */
	private $document;


	/**
	 * Constructor
	 *
	 * @param string $markup
	 * @param array $tokens
	 * @param LiquidFileSystem $file_system
	 * @return IncludeLiquidTag
	 */
	public function __construct($markup, &$tokens, &$file_system)
	{
		$regex = new LiquidRegexp('/("[^"]+"|\'[^\']+\')(\s+(with|for)\s+('.LIQUID_QUOTED_FRAGMENT.'+))?/');
							
		if($regex->match($markup))
		{
			
			$this->template_name = substr($regex->matches[1], 1, strlen($regex->matches[1]) - 2);
				
			if(isset($regex->matches[1]))
			{
				$this->collection = (isset($regex->matches[3])) ? ($regex->matches[3] == "for") : null;
				$this->variable = (isset($regex->matches[4])) ? $regex->matches[4] : null;
			}
			
			$this->extract_attributes($markup);
		}
		else
		{
			throw new LiquidException("Error in tag 'include' - Valid syntax: include '[template]' (with|for) [object|collection]");
		}
		
		parent::__construct($markup, $tokens, $file_system);
	}


	/**
	 * Parses the tokens
	 *
	 * @param array $tokens
	 */
	function parse($tokens)
	{
		if(!isset($this->file_system))
		{
			throw new LiquidException("No file system");
		} 
		
		// read the source of the template and create a new sub document
		$source = $this->file_system->read_template_file($this->template_name);
		$tokens = LiquidTemplate::tokenize($source);
		$this->document = new LiquidDocument($tokens, $this->file_system);
	}


	/**
	 * Renders the node
	 *
	 * @param LiquidContext $context
	 */
	function render(& $context)
	{
		$result = '';
		$variable = $context->get($this->variable);
		
		$context->push();
		
		foreach($this->attributes as $key => $value)
		{
			$context->set($key, $context->get($value));
		}
		
		if ($this->collection)
		{
			foreach($variable as $item)
			{
				$context->set($this->template_name, $item);
				$result .= $this->document->render($context);
			}
		}
		else
		{
			if (!is_null($this->variable))
			{
				$context->set($this->template_name, $variable);
			}
			
			$result .= $this->document->render($context);
		}
		
		$context->pop();
		
		return $result;
	}
}