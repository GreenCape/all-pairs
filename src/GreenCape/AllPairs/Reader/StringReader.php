<?php

namespace GreenCape\AllPairs;

class StringReader implements Reader
{
	private $fileContent = array();

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
		$parameterDefinition = array();
		foreach ($this->fileContent as $line)
		{
			$line = trim($line);
			if (empty($line) || $line[0] == '#')
			{
				continue;
			}
			$lineTokens = explode($labelDelimiter, $line, 2);
			if (count($lineTokens) != 2)
			{
				continue;
			}
			$values  = explode($valueDelimiter, $lineTokens[1]);
			for ($i = 0; $i < count($values); ++$i)
			{
				$values[$i] = trim($values[$i]);
			}
			$parameterDefinition[] = new Parameter($values, trim($lineTokens[0]));
		}

		return $parameterDefinition;
	}

	public function getSubModules()
	{
		return null;
	}

	public function getConstraints()
	{
		return null;
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
				$comment = array_shift($match);
				if ($comment > '')
				{
					print("Comment: »" . $comment . "«\n");
				}
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

				print("Parameter: »" . $label . "«. Values »" . implode('«, »', $values) . "«\n");
				continue;
			}
			if (($stage == 0 || $stage == 1) && preg_match($this->pattern($SUBMODULE), $line, $match))
			{
				array_shift($match);
				$values = preg_split($this->pattern($COMMA), array_shift($match));
				$order = array_shift($match);

				print("Order: »" . $order . "«. Parameters »" . implode('«, »', $values) . "«\n");
				$stage = 1;
				continue;
			}
			if (preg_match($this->pattern($CONDITION, 'i'), $line, $match))
			{
				array_shift($match);
				$condition = array_shift($match);
				$then = array_shift($match);
				$else = array_shift($match);

				print("Constraint: IF »" . $condition . "« THEN »" . $then);
				if (!empty($else))
				{
					print("« ELSE »" . $else);
				}
				print("«\n");
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
		print("\n\n");
	}

	private function pattern($pattern, $modifier = '')
	{
		return '~' . str_replace('~', '\~', $pattern) . '~' . $modifier;
	}
}
