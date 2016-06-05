<?php namespace LaravelFCM\Message;

use Exception;
use ReflectionClass;

class OptionsBuilder {

	protected $collapseKey;
	protected $priority;
	protected $contentAvailable = false;
	protected $delayWhileIdle = false;
	protected $timeToLive;
	protected $restrictedPackageName;
	protected $dryRun = false;

	/**
	 * Form more information about options, please refer to google official documentation :
	 * @link http://firebase.google.com/docs/cloud-messaging/http-server-ref#downstream-http-messages-json
	 */
	public function __construct()
	{

	}

	/**
	 * This parameter identifies a group of messages
	 * A maximum of 4 different collapse keys is allowed at any given time.
	 *
	 * @param String $collapseKey
	 *
	 * @return OptionsBuilder current instance of the builders
	 */
	public function setCollapseKey($collapseKey)
	{
		$this->collapseKey = $collapseKey;

		return $this;
	}

	/**
	 * Sets the priority of the message. Valid values are "normal" and "high."
	 * By default, messages are sent with normal priority
	 *
	 * @param String $priority
	 *
	 * @return OptionsBuilder current instance of the builder
	 * @throws InvalidOptionException
	 */
	public function setPriority($priority)
	{
		if (!OptionsPriorities::isValid($priority)) {
			throw new InvalidOptionException('priority is not valid, please refer to the documentation or use the constants of the class "OptionsPriorities"');
		}
		$this->priority = $priority;

		return $this;
	}

	/**
	 * support only Android and Ios
	 *
	 * An inactive client app is awoken.
	 * On iOS, use this field to represent content-available in the APNS payload.
	 * On Android, data messages wake the app by default.
	 * On Chrome, currently not supported.
	 *
	 * @param boolean $contentAvailable
	 *
	 * @return OptionsBuilder current instance of the builder
	 */
	public function setContentAvailable($contentAvailable)
	{
		$this->contentAvailable = $contentAvailable;

		return $this;
	}

	/**
	 * When this parameter is set to true, it indicates that the message should not be sent until the device becomes active.
	 *
	 * @param boolean $delayWhileIdle
	 *
	 * @return OptionsBuilder current instance of the builder
	 */
	public function setDelayWhileIdle($delayWhileIdle)
	{
		$this->delayWhileIdle = $delayWhileIdle;

		return $this;
	}

	/**
	 * This parameter specifies how long the message should be kept in FCM storage if the device is offline
	 *
	 * @param int $timeToLive (in second) min:0 max:2419200
	 *
	 * @return OptionsBuilder current instance of the builder
	 * @throws InvalidOptionException
	 */
	public function setTimeToLive($timeToLive)
	{
		if ($timeToLive < 0 || $timeToLive > 2419200) {
			throw new InvalidOptionException("time to live must be between 0 and 2419200, current value is: {$timeToLive}");
		}
		$this->timeToLive = $timeToLive;

		return $this;
	}

	/**
	 * This parameter specifies the package name of the application where the registration tokens must match in order to receive the message.
	 *
	 * @param string $restrictedPackageName
	 *
	 * @return OptionsBuilder current instance of the builder
	 */
	public function setRestrictedPackageName($restrictedPackageName)
	{
		$this->restrictedPackageName = $restrictedPackageName;

		return $this;
	}

	/**
	 * This parameter, when set to true, allows developers to test a request without actually sending a message.
	 * It should only be used for the development
	 *
	 * @param boolean $isDryRun
	 *
	 * @return OptionsBuilder current instance of the builder
	 */
	public function setDryRun($isDryRun)
	{
		$this->dryRun = $isDryRun;

		return $this;
	}

	/**
	 * @return null|string
	 */
	public function getCollapseKey()
	{
		return $this->collapseKey;
	}

	/**
	 * @return null|string
	 */
	public function getPriority()
	{
		return $this->priority;
	}

	/**
	 * @return boolean
	 */
	public function isContentAvailable()
	{
		return $this->contentAvailable;
	}

	/**
	 * @return boolean
	 */
	public function isDelayWhileIdle()
	{
		return $this->delayWhileIdle;
	}

	/**
	 * @return null|int
	 */
	public function getTimeToLive()
	{
		return $this->timeToLive;
	}

	/**
	 * @return null|string
	 */
	public function getRestrictedPackageName()
	{
		return $this->restrictedPackageName;
	}

	/**
	 * @return boolean
	 */
	public function isDryRun()
	{
		return $this->dryRun;
	}

	/**
	 * build an instance of Options
	 *
	 * @return Options
	 */
	public function build()
	{
		return new Options($this);
	}

}

class InvalidOptionException extends Exception {}

final class OptionsPriorities
{

	/**
	 * @const high priority : iOS, these correspond to APNs priorities 10.
	 */
	const high = "high";

	/**
	 * @const normal priority : iOS, these correspond to APNs priorities 5
	 */
	const normal = "normal";

	/**
	 * @return array priorities available in fcm
	 */
	static function getPriorities()
	{
		$class = new ReflectionClass(__CLASS__);
		return $class->getConstants();
	}

	/**
	 * check if this priority is supported by fcm
	 *
	 * @param $priority
	 *
	 * @return bool
	 */
	static function isValid($priority)
	{
		return in_array($priority, static::getPriorities());
	}
}