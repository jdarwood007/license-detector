<?php

/**
 * @package php-license-detector
 * @author Sami "SychO" Mazouz
 * @version 1.1.0
 * @license MIT
 */
declare(strict_types=1);

namespace LicenseDetector;

/**
 * Represents a type of rule; permissions, conditions or limitations.
 */
class RuleType
{
	/**
	 * @var string
	 */
	protected string $name;

	/**
	 * @var string
	 */
	protected array $rules = [];

	/**
	 * Constructor
	 * @param string $name
	 * @param array $rules (optional)
	 */
	public function __construct(string $name, ?array $rules = null)
	{
		$this->name = $name;

		if (!empty($rules)) {
			$this->fillRules($rules);
		}
	}

	/**
	 * @param array $rules
	 */
	public function fillRules(array $rules): void
	{
		if (empty($rules)) {
			return;
		}

		foreach ($rules as $rule) {
			$r = new Rule(
				$rule['tag'] ?? null,
				$rule['description'] ?? null,
				$rule['label'] ?? null,
				$this,
			);
			$r->setValue($rule['value'] ?? false);

			if (!empty($rule['tag'])) {
				$this->rules[$rule['tag']] = $r;
			} else {
			$this->rules[] = $r;
			}
		}
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return array
	 */
	public function getRules(): array
	{
		return $this->rules;
	}

	/**
	 */
	public function setRules(array $rules): void
	{
		$this->rules = $rules;
	}

	/**
	 * @param LicenseDetector\RuleType $rule_type
	 * @return bool
	 */
	public function equals(RuleType $rule_type): bool
	{
		return (bool) ($this->name === $rule_type->name);
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->getName();
	}
}
