<?php
declare(strict_types=1);

namespace App\Mailer\Transport;

use Cake\Core\Exception\CakeException;
use Cake\Mailer\Message;
use Cake\Mailer\Transport\SmtpTransport;
use Exception;
use Google\Client;
use Google\Service\Gmail;
use Google\Service\Gmail\Message as GmailMessage;

class GmailApiTransport extends SmtpTransport
{
    public function send(Message $message): array
    {
        $strMessage = $this->messageAsString($message);

        $gmailMessage = new GmailMessage();

        $gmailMessage->setRaw(base64_encode($strMessage));

        $client = $this->getSendClient();

        $service = new Gmail($client);

        $user = 'me'; // the authenticated user

        $results = $service->users_messages->send($user, $gmailMessage);

        return [
            'message' => $results,
            'headers' => $message->getHeaders(),
        ];
    }

    protected function getSendClient()
    {
        $client = new Client();

        $tokenPath = $this->getTokenPath();

        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);

            $client->setAccessToken($accessToken);
        }

        // $client->revokeToken();

        if ($client->isAccessTokenExpired()) {
            $this->getClient();
        }

        return $client;
    }

    protected function getTokenPath(): string
    {
        return ROOT . DS . 'token.json';
    }

    protected function getCredentials(): string
    {
        $credentials = ROOT . DS . 'client_secret_59505482608-rjgil11sumb3k1hv2esmal4jp6p2e5jj.apps.googleusercontent.com.json';

        if (!file_exists($credentials)) {
            throw new Exception('Google Credential JSON missing');
        }

        return $credentials;
    }

    protected function getClient()
    {
        $client = new Client();

        $client->setApplicationName('CakePHP 5 XOAuth2 Test');

        $client->setScopes([
            Gmail::GMAIL_SEND,
        ]);

        $credentials = $this->getCredentials();

        $client->setAuthConfig($credentials);

        $client->setAccessType('offline');

        $client->setPrompt('select_account consent');

        $tokenPath = $this->getTokenPath();

        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);

            $client->setAccessToken($accessToken);
        }

        if ($client->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            } else {
                // Request authorization from the user.
                $authUrl = $client->createAuthUrl();
                printf("Open the following link in your browser:\n%s\n", $authUrl);
                print 'Enter verification code: ';
                $authCode = trim(fgets(STDIN));

                // Exchange authorization code for an access token.
                $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
                $client->setAccessToken($accessToken);

                // Check to see if there was an error.
                if (array_key_exists('error', $accessToken)) {
                    throw new Exception(join(', ', $accessToken));
                }
            }
            // Save the token to a file.
            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }

            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
        }

        return $client;
    }

    protected function messageAsString(Message $message): string
    {
        $this->checkRecipient($message);

        $headers = $message->getHeadersString([
            'from',
            'sender',
            'replyTo',
            'readReceipt',
            'to',
            'cc',
            'subject',
            'returnPath',
        ]);

        $message = $this->_prepareMessage($message);

        $this->_content = ['headers' => $headers, 'message' => $message];

        return $headers . "\r\n\r\n" . $message;
    }

    protected function _prepareMessage(Message $message): string
    {
        $lines = $message->getBody();

        $messages = [];

        foreach ($lines as $line) {
            if (str_starts_with($line, '.')) {
                $messages[] = '.' . $line;
            } else {
                $messages[] = $line;
            }
        }

        return implode("\r\n", $messages);
    }

    protected function checkRecipient(Message $message): void
    {
        if (
            $message->getTo() === []
            && $message->getCc() === []
            && $message->getBcc() === []
        ) {
            throw new CakeException(
                'You must specify at least one recipient.'
                    . ' Use one of `setTo`, `setCc` or `setBcc` to define a recipient.'
            );
        }
    }
}
