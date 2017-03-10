<?php

namespace ethercreative\log;

use yii\base\InvalidConfigException;

use Maknz\Slack\Client;

class Error extends \yii\log\Target
{
	public
		$webhook,
		$name,
		$username,
		$channel,
		$link_names = true;

	public function export()
	{
		if (!$webhook) throw new InvalidConfigException("Webhook is required.");
		if (!$name) throw new InvalidConfigException("Name is required.");
		if (!$username) throw new InvalidConfigException("Username is required.");
		if (!$channel) throw new InvalidConfigException("Channel is required.");

		$client = new Client($this->webhook, [
			'username' => $this->username,
			'channel' => $this->channel,
			'link_names' => $this->link_names,
		]);

		$url = \Yii::$app->request->absoluteUrl;

		foreach ($this->messages as $message)
		{
			$_message = $message;

			$message = $message[0];

			if (is_string($message) || $message->statusCode < 500)
				continue;

			$string = $this->formatMessage($_message);

			$string = (explode("\n", $string))[0];

			$data = [
				'fallback' => 'fallback',
				'color' => 'danger',
				'fields' => [
					[
						'title' => 'Project',
						'value' => $this->name,
						'short' => true,
					],
					[
						'title' => 'URL',
						'value' => $url,
						'short' => true,
					],
					[
						'title' => 'Error',
						'value' => $message->statusCode . ' ' . $message->getMessage(),
						'short' => true,
					],
					[
						'title' => 'Line #',
						'value' => $message->getLine(),
						'short' => true,
					],
					[
						'title' => 'File',
						'value' => $message->getFile(),
					],
					[
						'title' => 'Text',
						'value' => $string,
					],
				],
			];

			$client->attach($data)->send();
		}
	}
}
