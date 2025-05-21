<?php

declare(strict_types=1);

use LicenseDetector\Detector;
use PHPUnit\Framework\TestCase;

final class DetectorTest extends TestCase
{
	/**
	 * @var LicenseDetector\Detector
	 */
	private ?Detector $detector;

	/**
	 */
	public function testParse(): void
	{
		$license = $this->detector->parseByPath('LICENSE');
		$license->printDebug();

		$this->assertTrue($license->isValid());
	}

	/**
	 */
	public function testLongLicense()
	{
		$license = $this->detector->parseByPath('tests/LICENSE.txt');
		$license->printDebug();

		$this->assertTrue($license->isValid());
	}

	/**
	 */
	protected function setUp(): void
	{
		$this->detector = new Detector();
	}

	/**
	 */
	protected function tearDown(): void
	{
		$this->detector = null;
	}
}
