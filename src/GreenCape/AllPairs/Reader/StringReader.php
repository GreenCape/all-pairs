<?php

namespace GreenCape\AllPairs;

class StringReader implements Reader
{
	private $fileContent = array();
	private $parameters = array();
	private $subModules = array();
	private $constraints = array();

	public function __construct($content)
	{
		$this->parse($content);
	}

	/**
	 * @param string $labelDelimiter
	 * @param string $valueDelimiter
	 *
	 * @return Parameter[]
	 */
	public function getParameters($labelDelimiter = ':', $valueDelimiter = ',')
	{
		return $this->parameters;
	}

	public function getSubModules()
	{
		return $this->subModules;
	}

	public function getConstraints()
	{
		return $this->constraints;
	}

	private function parse($content)
	{
		$labelDelimiter = preg_quote(':');
		$valueDelimiter = preg_quote(',');

		$OPT_SPACE = '\s*';
		$EOL = $OPT_SPACE . '(?:[\r\n]+|$)';
		$COLON = $OPT_SPACE . $labelDelimiter . $OPT_SPACE;
		$COMMA = $OPT_SPACE . $valueDelimiter . $OPT_SPACE;
		$WORD = '[^' . $labelDelimiter . $valueDelimiter . ']+?';
		$WORDS = '(' . $WORD . '(?:' . $COMMA . $WORD . ')*)';
		$COMMENT = '#' . $OPT_SPACE . '(.*)' . $EOL;
		$LABEL = '(' . $WORD . ')' . $COLON;

		$PARAMETER = $LABEL . $WORDS;
		$SUBMODULE = '\{' . $OPT_SPACE . $WORDS . $OPT_SPACE . '\}' . $OPT_SPACE . '@' . $OPT_SPACE . '(\d+)';

		$CONDITION = 'IF\s*(.*?)\s*THEN\s*(.*?)\s*(?:ELSE\s*(.*?)\s*)?;';

		$this->fileContent = preg_split($this->pattern($EOL), $content);

		$stage = 0; $buffer = '';
		foreach ($this->fileContent as $lineNo => $line)
		{
			if (!empty($buffer))
			{
				$line = $buffer . ' ' . $line;
				$buffer = '';
			}
			if (preg_match($this->pattern($COMMENT), $line, $match))
			{
				$line = str_replace(array_shift($match), '', $line);
			}
			$line = trim($line);
			if (empty($line))
			{
				continue;
			}
			if ($stage == 0 && preg_match($this->pattern($PARAMETER . $EOL), $line, $match))
			{
				array_shift($match);
				$label = array_shift($match);
				$values = preg_split($this->pattern($COMMA), array_shift($match));
				$this->parameters[] = new Parameter($values, $label);

				continue;
			}
			if (($stage == 0 || $stage == 1) && preg_match($this->pattern($SUBMODULE), $line, $match))
			{
				array_shift($match);
				$values = preg_split($this->pattern($COMMA), array_shift($match));
				$order = array_shift($match);
				$this->subModules[] = array(
					'order' => $order,
					'parameters' => $values
				);
				$stage = 1;

				continue;
			}
			if (preg_match($this->pattern($CONDITION, 'i'), $line, $match))
			{
				array_shift($match);
				$condition = array_shift($match);
				$then = array_shift($match);
				$else = array_shift($match);
				$this->constraints = array(
					'if' => $condition,
					'then' => $then,
					'else' => $else
				);
				$stage = 2;

				continue;
			}
			if (preg_match($this->pattern('IF\s+\[', 'i'), $line))
			{
				$buffer = $line;

				$stage = 2;
				continue;
			}
			print("\n\nUnknown syntax in line {$lineNo}: »{$line}«\n\n");
		}
	}

	private function pattern($pattern, $modifier = '')
	{
		return '~' . str_replace('~', '\~', $pattern) . '~' . $modifier;
	}
}
