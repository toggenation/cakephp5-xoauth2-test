<?php
declare(strict_types=1);

namespace App\Command;

use App\Mailer\Transport\GmailApiTransport;
use Cake\Chronos\Chronos;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Mailer\Mailer;

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
    public function execute(Arguments $args, ConsoleIo $io): int|null|null
    {
        $mailer = new Mailer();

        $mailer->setEmailFormat('html')
            ->setTo('james@toggen.com.au', 'James McDonald')
            ->setFrom('jmcd1973@gmail.com', 'James 1973 Gmail')
            ->setSubject('Test of the Gmail Send XOAUTH2 ' . Chronos::now('Australia/Melbourne')->toAtomString())
            ->setTransport(new GmailApiTransport())
            ->viewBuilder()
            ->setTemplate('gmail_api')
            ->setLayout('gmail_api')
            ->setVars([
                'one' => 'One',
                'two' => 'Two',
            ]);

        $mailer->deliver();

        $io->out('Message sent');
    }
}
