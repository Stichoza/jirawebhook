<?php

namespace Stichoza\JiraWebhooksData\Models;

use Stichoza\JiraWebhooksData\Exceptions\JiraWebhookDataException;

/**
 * Class that parses JIRA issue single comment data and gives access to it.
 *
 * @author  Chewbacca <chewbacca@devadmin.com>
 * @author  Stichoza <me@stichoza.com>
 */
class JiraIssueComment extends AbstractModel
{
    /**
     * JIRA comment self url
     */
    public ?string $self;

    /**
     * JIRA comment ID
     */
    public int $id;

    /**
     * JIRA comment author
     */
    public JiraUser $author;

    /**
     * JIRA comment text
     */
    public string $body;

    /**
     * JIRA comment update author
     */
    public JiraUser $updateAuthor;

    /**
     * JIRA comment create data time
     */
    public ?string $created;

    /**
     * JIRA comment update data time
     */
    public ?string $updated;

    /**
     * @var array<string> Array of required keys in data
     */
    protected array $required = [
        'id',
        'author',
        'updateAuthor',
        'body',
    ];

    /**
     * @throws JiraWebhookDataException
     */
    public function __construct(array $data = null)
    {
        if ($data !== null) {
            $this->validate($data);

            $this->self = $data['self'] ?? null;
            $this->id = (int) $data['id'];
            $this->author = new JiraUser($data['author']);
            $this->body = $data['body'] ?? '';
            $this->updateAuthor = new JiraUser($data['updateAuthor']);
            $this->created = $data['created'] ?? null;
            $this->updated = $data['updated'] ?? null;
        }
    }

    /**
     * Get array of user nicknames that referenced in comment
     */
    public function getMentionedUsersNicknames(): array
    {
        preg_match_all("/\[~(.*?)\]/", $this->body, $matches);

        return $matches[1];
    }

    /**
     * Remove code and quote blocks from comment body
     */
    public function plainBody(): string
    {
        return preg_replace("/\{code(.*?)\}(.*?)\{code\}|\{quote\}(.*?)\{quote\}/", '', $this->body);
    }

    public function getBody(int $length = null, int $start = 0): string
    {
        return mb_substr($this->body, $start, $length);
    }
}
