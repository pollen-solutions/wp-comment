<?php

declare(strict_types=1);

namespace Pollen\WpComment;

use Pollen\WpPost\WpPostQuery;
use Pollen\WpPost\WpPostQueryInterface;
use Pollen\WpUser\WpUserQuery;
use Pollen\WpUser\WpUserQueryInterface;
use Pollen\Support\DateTime;
use Pollen\Support\ParamsBag;
use WP_Comment;
use WP_Comment_Query;

class WpCommentQuery extends ParamsBag implements WpCommentQueryInterface
{
    /**
     * List of built-in classe names by type.
     * @var array<string,string>
     */
    protected static array $builtInClasses = [];

    /**
     * Related comment types.
     * @var array
     */
    protected static array $commentTypes = [];

    /**
     * List of defaults query request arguments.
     * @var array
     */
    protected static array $defaultArgs = [];

    /**
     * Fallback class name.
     * @var string|null
     */
    protected static ?string $fallbackClass = null;

    /**
     * Related WordPress Comment object.
     * @var WP_Comment|null
     */
    protected ?WP_Comment $wp_comment = null;

    /**
     * @param WP_Comment|null $wp_comment
     *
     * @return void
     */
    public function __construct(?WP_Comment $wp_comment = null)
    {
        if ($this->wp_comment = $wp_comment instanceof WP_Comment ? $wp_comment : null) {
            parent::__construct($this->wp_comment->to_array());
        }
    }

    /**
     * @inheritDoc
     */
    public static function build(object $wp_comment): ?WpCommentQueryInterface
    {
        if (!$wp_comment instanceof WP_Comment) {
            return null;
        }

        $classes = self::$builtInClasses;
        $comment_type = $wp_comment->comment_type;

        $class = $classes[$comment_type] ?? (self::$fallbackClass ?: static::class);

        return class_exists($class) ? new $class($wp_comment) : new static($wp_comment);
    }

    /**
     * @inheritDoc
     */
    public static function createFromId(int $comment_id): ?WpCommentQueryInterface
    {
        if (($wp_comment = get_comment($comment_id)) && ($wp_comment instanceof WP_Comment)) {
            if (!$instance = static::build($wp_comment)) {
                return null;
            }
            return $instance::is($instance) ? $instance : null;
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public static function fetchFromArgs(array $args = []): array
    {
        return static::fetchFromWpCommentQuery(new WP_Comment_Query(static::parseQueryArgs($args)));
    }

    /**
     * @inheritDoc
     */
    public static function fetchFromIds(array $ids): array
    {
        return static::fetchFromWpCommentQuery(new WP_Comment_Query(static::parseQueryArgs(['comment__in' => $ids])));
    }

    /**
     * @inheritDoc
     */
    public static function fetchFromWpCommentQuery(WP_Comment_Query $wp_comment_query): array
    {
        $comments = [];

        if ($wpComments = $wp_comment_query->comments) {
            foreach ($wpComments as $wpComment) {
                $comments[] = new static($wpComment);
            }
        }

        return $comments;
    }

    /**
     * @inheritDoc
     */
    public static function is($instance): bool
    {
        return $instance instanceof static &&
            (!($commentTypes = static::$commentTypes) || $instance->typeIn($commentTypes));
    }


    /**
     * @inheritDoc
     */
    public static function parseQueryArgs(array $args = []): array
    {
        $args['type'] = static::$commentTypes;

        return array_merge(static::$defaultArgs, $args);
    }

    /**
     * @inheritDoc
     */
    public static function setBuiltInClass(string $type, string $classname): void
    {
        if ($type === 'any' || empty($type)) {
            self::setFallbackClass($classname);
        } else {
            self::$builtInClasses[$type] = $classname;
        }
    }

    /**
     * @inheritDoc
     */
    public static function setDefaultArgs(array $args): void
    {
        self::$defaultArgs = $args;
    }

    /**
     * @inheritDoc
     */
    public static function setFallbackClass(string $classname): void
    {
        self::$fallbackClass = $classname;
    }

    /**
     * @inheritDoc
     */
    public static function setTypes(array $types): void
    {
        self::$commentTypes= $types;
    }

    /**
     * @inheritDoc
     */
    public function getAgent(): string
    {
        return $this->get('comment_agent', '');
    }

    /**
     * @inheritDoc
     */
    public function getAuthor(): string
    {
        return $this->get('comment_author', '');
    }

    /**
     * @inheritDoc
     */
    public function getAuthorEmail(): string
    {
        return $this->get('comment_author_email', '');
    }

    /**
     * @inheritDoc
     */
    public function getAuthorIp(): string
    {
        return $this->get('comment_author_ip', '');
    }

    /**
     * @inheritDoc
     */
    public function getAuthorUrl(): string
    {
        return $this->get('comment_author_url', '');
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        return $this->get('comment_content', '');
    }

    /**
     * @inheritDoc
     */
    public function getDate(bool $gmt = false): string
    {
        return $gmt
            ? (string)$this->get('comment_date_gmt', '')
            : (string)$this->get('comment_date', '');
    }

    /**
     * @inheritDoc
     */
    public function getDateTime(): DateTime
    {
        return Datetime::createFromTimeString($this->getDate());
    }

    /**
     * @inheritDoc
     */
    public function getEditUrl(): string
    {
        return get_edit_comment_link($this->getId());
    }

    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return (int)$this->get('comment_ID', 0);
    }

    /**
     * @inheritDoc
     */
    public function getMeta(string $meta_key, bool $isSingle = false, $default = null)
    {
        return get_comment_meta($this->getId(), $meta_key, $isSingle) ?: $default;
    }

    /**
     * @inheritDoc
     */
    public function getMetaMulti(string $meta_key, $default = null)
    {
        return $this->getMeta($meta_key, false, $default);
    }

    /**
     * @inheritDoc
     */
    public function getMetaSingle(string $meta_key, $default = null)
    {
        return $this->getMeta($meta_key, true, $default);
    }

    /**
     * @inheritDoc
     */
    public function getQueriedParent(): ?WpCommentQueryInterface
    {
        return self::createFromId($this->getParentId());
    }

    /**
     * @inheritDoc
     */
    public function getParentId(): int
    {
        return (int)$this->get('comment_parent', 0);
    }

    /**
     * @inheritDoc
     */
    public function getQueriedPost(): WpPostQueryInterface
    {
        return WpPostQuery::createFromId($this->getPostId());
    }

    /**
     * @inheritDoc
     */
    public function getPostId(): int
    {
        return (int)$this->get('comment_post_ID', 0);
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return $this->get('comment_type', '');
    }

    /**
     * @inheritDoc
     */
    public function getQueriedUser(): WpUserQueryInterface
    {
        return WpUserQuery::createFromId($this->getUserId());
    }

    /**
     * @inheritDoc
     */
    public function getUserId(): int
    {
        return (int)$this->get('user_id', 0);
    }

    /**
     * @inheritDoc
     */
    public function getWpComment(): WP_Comment
    {
        return $this->wp_comment;
    }

    /**
     * @inheritDoc
     */
    public function isApproved(): bool
    {
        return $this->get('comment_approved', 0) === 1;
    }

    /**
     * @inheritDoc
     */
    public function isSpam(): bool
    {
        return $this->get('comment_approved', '') === 'spam';
    }

    /**
     * @inheritDoc
     */
    public function typeIn(array $comment_types): bool
    {
        return in_array($this->getType(), $comment_types, true);
    }
}