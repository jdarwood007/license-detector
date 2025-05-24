<?php

/**
 * @package php-license-detector
 * @author Sami "SychO" Mazouz
 * @version 1.1.0
 * @license MIT
 */
declare(strict_types=1);

namespace LicenseDetector;

use Symfony\Component\Yaml\Yaml;

/**
 * Instantiates a license and matches it to the list of existing licenses
 */
#[\AllowDynamicProperties]
class License
{
	/**
	 * @var string
	 */
	public string $title = '';

	/**
	 * @var string
	 */
	public string $spdx_id = '';

	/**
	 * @var string
	 */
	public string $redirect_from = '';

	/**
	 * @var bool
	 */
	public bool $featured = false;

	/**
	 * @var bool
	 */
	public bool $hidden = false;

	/**
	 * @var string
	 */
	public string $description = '';

	/**
	 * @var string
	 */
	public string $how = '';

	/**
	 * @var string
	 */
	public string $note = '';

	/**
	 * @var string
	 */
	public string $nickname = '';

	/**
	 * @var ?array
	 */
	public ?array $using = [];

	/**
	 * @var array
	 */
	public array $rules = [];

	/**
	 * @var array
	 */
	public array $permissions = [];

	/**
	 * @var array
	 */
	public array $conditions = [];

	/**
	 * @var array
	 */
	public array $limitations = [];

	/**
	 * @var float
	 */
	public array $stats = [];

	/**
	 * @var string
	 */
	protected string $contents = '';

	/**
	 * @var string
	 */
	protected string $body = '';

	/**
	 * Constructor
	 *
	 * @param ?string $contents
	 */
	public function __construct(?string $contents = null)
	{
		if (!empty($contents)) {
			$this->contents = $contents;
			$this->parse();
		}
	}

	/**
	 * @return string
	 */
	public function getContents(): string
	{
		return $this->contents;
	}

	/**
	 * @return string
	 */
	public function getBody(): string
	{
		return $this->body;
	}

	/**
	 * @return string
	 */
	public function setBody(string $body): void
	{
		$this->body = $body;
	}

	/**
	 * @var string
	 */
	public function getCleanBody(): string
	{
		return substr(preg_replace('/\s/s', '', $this->body), 0, 1000);
	}

	/**
	 * @return array
	 */
	public function getAllRules(): array
	{
		$rules = [];

		foreach ($this->rules as $type) {
			$rules += $type->rules;
		}

		return $rules;
	}

	/**
	 */
	public function parse(): void
	{
		preg_match_all('/---\s(.*)\n---\s+(.*)/s', $this->contents, $matches, PREG_SET_ORDER, 0);

		if (!empty($matches[0][2])) {
			$this->body = $matches[0][2];
		}

		if (!empty($matches[0][1])) {
			try {
				$this->setAdvanced(Yaml::parse($matches[0][1]));
			} catch (\Exception $e) {
				echo('Could not parse yaml content.');
			}
		}

		if (empty($matches)) {
			$this->body = $this->contents;
			$this->matchToLicense();
		}
	}

	/**
	 * @param array $data
	 */
	public function setAdvanced(array $data): void
	{
		foreach ($data as $k => $v) {
			$this->{str_replace('-', '_', $k)} = $v;
		}

		$this->fillRules($data);
	}

	/**
	 * @param array $data
	 */
	public function fillRules(array $data): void
	{
		foreach (Rule::TYPES as $type) {
			if (!isset($data[$type])) {
				continue;
			}

			$this->rules[$type] = new RuleType($type);

			$rules = [];

			foreach (Detector::$rules as $rule) {
				if (in_array($rule->getTag(), $data[$type])) {
					$rules[$rule->getTag()] = $rule;
				}
			}

			$this->rules[$type]->setRules($rules);
		}
	}

	/**
	 */
	public function matchToLicense(): void
	{
		foreach (Detector::$licenses as $license) {
			similar_text($this->getCleanBody(), $license->getCleanBody(), $percent);

			if (!empty($this->stats['highest_percentage']) && $percent < $this->stats['highest_percentage']) {
				continue;
			}

			$this->stats['highest_match'] = $license;
			$this->stats['highest_percentage'] = $percent;

			if ($this->stats['highest_percentage'] > 90) {
				$data = [];

				foreach ($license as $key => $property) {
					if ($key !== 'contents' && $key !== 'body') {
						$data[$key] = $property;
					}
				}

				unset($data['stats']);

				$this->setAdvanced($data);
				$this->stats['percentage'] = $percent;
				break;
			}
		}
	}

	/**
	 * @return bool
	 */
	public function isValid(): bool
	{
		return !empty($this->title);
	}

	/**
	 * @return string
	 */
	public function printDebug(): void
	{
		$echo = "\nMatched: " . ($this->title ?? '<em>None</em>') . "\n";
		$echo .= 'Percentage: ' . ($this->stats['percentage'] ?? '0') . "%\n\n";
		$echo .= 'Highest match: ' . ($this->stats['highest_match']->title ?? '<em>None</em>') . "\n";
		$echo .= 'Percentage: ' . ($this->stats['highest_percentage'] ?? '0') . '%';

		echo $echo;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->title;
	}
}
