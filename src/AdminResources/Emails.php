<?php

namespace Invictushub\MsGraph\AdminResources;

use Invictushub\MsGraph\Facades\MsGraphAdmin;
use Exception;

class Emails extends MsGraphAdmin
{
    private string $userId = '';

    private string $top = '';

    private string $skip = '';

    private string $subject = '';

    private string $body = '';

    private string $comment = '';

    private string $id = '';

    private array $to = [];

    private array $cc = [];

    private array $bcc = [];

    private array $attachments = [];

    public function userid(string $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function top(string$top): static
    {
        $this->top = $top;

        return $this;
    }

    public function skip(string $skip): static
    {
        $this->skip = $skip;

        return $this;
    }

    public function id(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function to(array $to): static
    {
        $this->to = $to;

        return $this;
    }

    public function cc(array $cc): static
    {
        $this->cc = $cc;

        return $this;
    }

    public function bcc(array $bcc): static
    {
        $this->bcc = $bcc;

        return $this;
    }

    public function subject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function body(string $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function comment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function attachments(array $attachments): static
    {
        $this->attachments = $attachments;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function get(array $params = []): array
    {
        if ($this->userId === '') {
            throw new Exception('userId is required.');
        }

        $top = request('top', $this->top);
        $skip = request('skip', $this->skip);

        if ($params == []) {
            $params = http_build_query([
                '$top' => $top,
                '$skip' => $skip,
                '$count' => 'true',
                '$orderby' => 'sentDateTime desc',
            ]);
        } else {
            $params = http_build_query($params);
        }

        //get messages from folderId
        $emails = MsGraphAdmin::get('users/'.$this->userId.'/messages?'.$params);

        $data = MsGraphAdmin::getPagination($emails, $top, $skip);

        return [
            'emails' => $emails,
            'total' => $data['total'],
            'top' => $data['top'],
            'skip' => $data['skip'],
        ];
    }

    /**
     * @throws Exception
     */
    public function find(string $id): MsGraphAdmin
    {
        if ($this->userId == null) {
            throw new Exception('userid is required.');
        }

        return MsGraphAdmin::get('users/'.$this->userId.'/messages/'.$id);
    }

    public function findAttachments(string $id): MsGraphAdmin
    {
        return MsGraphAdmin::get('users/'.$this->userId.'/messages/'.$id.'/attachments');
    }

    public function findInlineAttachments(array $email): array
    {
        $attachments = self::findAttachments($email['id']);

        //replace every case of <img='cid:' with the base64 image
        $email['body']['content'] = preg_replace_callback(
            '~cid.*?"~',
            function (array $m) use ($attachments) {
                //remove the last quote
                $parts = explode('"', $m[0]);

                //remove cid:
                $contentId = str_replace('cid:', '', $parts[0]);

                //loop over the attachments
                foreach ($attachments['value'] as $file) {
                    //if there is a match
                    if ($file['contentId'] == $contentId) {
                        //return a base64 image with a quote
                        return 'data:'.$file['contentType'].';base64,'.$file['contentBytes'].'"';
                    }
                }

                return true;
            },
            $email['body']['content']
        );

        return $email;
    }

    /**
     * @throws Exception
     */
    public function send(): MsGraphAdmin
    {
        if (strlen($this->userId) === 0) {
            throw new Exception('userId is required.');
        }

        if (count($this->to) === 0) {
            throw new Exception('To is required.');
        }

        if (strlen($this->subject) === 0) {
            throw new Exception('Subject is required.');
        }

        if (strlen($this->comment) > 0) {
            throw new Exception('Comment is only used for replies and forwarding, please use body instead.');
        }

        return MsGraphAdmin::post('users/'.$this->userId.'/sendMail', self::prepareEmail());
    }

    /**
     * @throws Exception
     */
    public function reply(): MsGraphAdmin
    {
        if (strlen($this->userId) === 0) {
            throw new Exception('userId is required.');
        }

        if (strlen($this->id ) === 0) {
            throw new Exception('email id is required.');
        }

        if (strlen($this->body) > 0) {
            throw new Exception('Body is only used for sending new emails, please use comment instead.');
        }

        return MsGraphAdmin::post('users/'.$this->userId.'/messages/'.$this->id.'/replyAll', self::prepareEmail());
    }

    /**
     * @throws Exception
     */
    public function forward(): MsGraphAdmin
    {
        if (strlen($this->userId) === 0) {
            throw new Exception('userId is required.');
        }

        if (strlen($this->id) === 0) {
            throw new Exception('email id is required.');
        }

        if (strlen($this->body) > 0) {
            throw new Exception('Body is only used for sending new emails, please use comment instead.');
        }

        return MsGraphAdmin::post('users/'.$this->userId.'/messages/'.$this->id.'/forward', self::prepareEmail());
    }

    /**
     * @throws Exception
     */
    public function delete(string $id): MsGraphAdmin
    {
        if ($this->userId == null) {
            throw new Exception('userId is required.');
        }

        return MsGraphAdmin::delete('users/'.$this->userId.'/messages/'.$id);
    }

    protected function prepareEmail(): array
    {
        $subject = $this->subject;
        $body = $this->body;
        $comment = $this->comment;
        $to = $this->to;
        $cc = $this->cc;
        $bcc = $this->bcc;
        $attachments = $this->attachments;

        $toArray = [];
        if ($to != null) {
            foreach ($to as $email) {
                $toArray[]['emailAddress'] = ['address' => $email];
            }
        }

        $ccArray = [];
        if ($cc != null) {
            foreach ($cc as $email) {
                $ccArray[]['emailAddress'] = ['address' => $email];
            }
        }

        $bccArray = [];
        if ($bcc != null) {
            foreach ($bcc as $email) {
                $bccArray[]['emailAddress'] = ['address' => $email];
            }
        }

        $attachmentarray = [];
        if ($attachments != null) {
            foreach ($attachments as $file) {
                $path = pathinfo($file);

                $attachmentarray[] = [
                    '@odata.type' => '#microsoft.graph.fileAttachment',
                    'name' => $path['basename'],
                    'contentType' => mime_content_type($file),
                    'contentBytes' => base64_encode(file_get_contents($file)),
                ];
            }
        }

        $envelope = [];
        if ($subject != null) {
            $envelope['message']['subject'] = $subject;
        }
        if ($body != null) {
            $envelope['message']['body'] = [
                'contentType' => 'html',
                'content' => $body,
            ];
        }
        if ($toArray != null) {
            $envelope['message']['toRecipients'] = $toArray;
        }
        if ($ccArray != null) {
            $envelope['message']['ccRecipients'] = $ccArray;
        }
        if ($bccArray != null) {
            $envelope['message']['bccRecipients'] = $bccArray;
        }
        if ($attachmentarray != null) {
            $envelope['message']['attachments'] = $attachmentarray;
        }
        if ($comment != null) {
            $envelope['comment'] = $comment;
        }

        return $envelope;
    }
}
