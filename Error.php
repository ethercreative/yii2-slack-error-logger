<?php

namespace ethercreative\log;

use Yii;
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
		if (!$this->webhook) throw new InvalidConfigException("Webhook is required.");
		if (!$this->name) throw new InvalidConfigException("Name is required.");
		if (!$this->username) throw new InvalidConfigException("Username is required.");
		if (!$this->channel) throw new InvalidConfigException("Channel is required.");

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

			$statusCode = Yii::$app->response->statusCode;
			$statusText  = Yii::$app->response->statusText;

			if (is_string($message) || ((int) $statusCode < 500 && (int) $statusCode !== 0))
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
						'value' => $statusCode . ' ' . $statusText,
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
