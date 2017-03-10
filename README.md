# Yii2 Slack Error Logger

Send errors (5xx) to a slack channel

## Install

You can install the package using the Composer package manager. You can install it by running this command in your project root:

```sh
composer require ethercreative/yii2-slack-error-logger
```

## Basic Usage

Add the class to your configuration file.

You will need to [create an incoming webhook](https://my.slack.com/services/new/incoming-webhook)

```php
	// ...
	'log' => [
		// ...
		'targets' => [
			[
				'class' => 'ethercreative\log\Error',
				'levels' => ['error'],
				'webhook' => 'https://hooks.slack.com/...',
				'name' => 'The name of your project',
				'username' => 'The username for the user',
				'channel' => '#thechannel',
			],
		],
	],
	// ...
```
