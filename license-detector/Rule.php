<?php

/**
 * @package php-license-detector
 * @author Sami "SychO" Mazouz
 * @version 1.1.0
 * @license MIT
 */

namespace LicenseDetector;

/**
 * Represents a rule
 */
class Rule
{
	/**
	 * @var array
	 */
	public const TYPES = ['permissions', 'limitations', 'conditions'];

	/**
	 * @var LicenseDetector\RuleType
	 */
	public RuleType $type;

	/**
	 * @var string
	 */
	protected string $tag;

	/**
	 * @var string
	 */
	protected string $description;

	/**
	 * @var string
	 */
	protected string $label;

	/**
	 * @var bool
	 */
	protected bool $value;

	/**
	 * Constructor
	 * @param string $tag
	 */
	public function __construct(string $tag, ?string $description = null, ?string $label = null, ?RuleType $type = null)
	{
		$this->tag = $tag;
		$this->description = $description;
		$this->label = $label;
		$this->type = $type;
		$this->value = false;
	}

	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return $this->description;
	}

	/**
	 * @return string
	 */
	public function getLabel(): string
	{
		return $this->label;
	}

	/**
	 * @return string
	 */
	public function getTag(): string
	{
		return $this->tag;
	}

	/**
	 * @param bool $v
	 */
	public function setValue(bool $v): bool
	{
		$this->value = $v;
	}
}
