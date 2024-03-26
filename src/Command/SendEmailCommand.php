<?php

declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Exception\CakeException;
use Cake\Mailer\Mailer;
use Cake\Mailer\Message;
use Cake\Mailer\Transport\MailTransport;
use Google_Service;

/**
 * SendEmail command.
 */
class SendEmailCommand extends Command
{

    protected array $_content = [];
    /**
     * Hook method for defining this command's option parser.
     *
     * @see https://book.cakephp.org/4/en/console-commands/commands.html#defining-arguments-and-options
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser The built parser.
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);

        return $parser;
    }

    /**
     * Implement this method with your command's logic.
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return int|null|void The exit code or null for success
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        // $mailer = new Mailer();

        $message = new Message();

        // $message->setTransferEncoding('base64');

        $message->addTo('james@toggen.com.au', 'James McDonald');

        $message->setBodyHtml('<h1>Hi James</h1><p>Test para</p>');

        $message->setBodyText("Hi James\n\nTest para");

        $message->setEmailFormat('both');

        $message->setFrom('jmcd1973@gmail.com', 'James 1973 Gmail');

        $this->messageAsString($message);

        dd(
            $this->_content
        );

        // new Google_Service

        // $mailer->setTransport('gmail');

        // $mailer->setTo('james@toggen.com.au', 'James Toggen');

        // $mailer->setFrom('jmcd1973@gmail.com', 'James Gmail');

        // $mailer->deliver('Hi James Test');
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

        return $headers . "\r\n\r\n" . $message . "\r\n\r\n\r\n.";
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
