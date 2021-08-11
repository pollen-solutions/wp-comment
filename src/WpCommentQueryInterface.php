<?php

declare(strict_types=1);

namespace Pollen\WpComment;

use Pollen\Support\ParamsBagInterface;
use Pollen\Support\DateTime;
use Pollen\WpPost\WpPostQueryInterface;
use Pollen\WpUser\WpUserQueryInterface;
use WP_Comment;
use WP_Comment_Query;

interface WpCommentQueryInterface extends ParamsBagInterface
{
    /**
     * Build an instance from WP_Comment object.
     *
     * @param WP_Comment|object $wp_comment
     *
     * @return static
     */
    public static function build(object $wp_comment): ?WpCommentQueryInterface;

    /**
     * Create an instance from comment ID.
     *
     * @param int $comment_id
     *
     * @return static|null
     */
    public static function createFromId(int $comment_id): ?WpCommentQueryInterface;

    /**
     * Retrieve list of instances from a list of query arguments.
     * @see https://developer.wordpress.org/reference/classes/wp_comment_query/
     *
     * @param array $args
     *
     * @return array
     */
    public static function fetchFromArgs(array $args = []): array;

    /**
     * Retrieve list of instances from a list of user IDs.
     * @see https://developer.wordpress.org/reference/classes/wp_comment_query/
     *
     * @param int[] $ids
     *
     * @return array
     */
    public static function fetchFromIds(array $ids): array;

    /**
     * Retrieve list of instances from WP_Comment_Query object.
     * @see https://developer.wordpress.org/reference/classes/wp_query/
     *
     * @param WP_Comment_Query $wp_comment_query
     *
     * @return array
     */
    public static function fetchFromWpCommentQuery(WP_Comment_Query $wp_comment_query): array;

    /**
     * Check class instance integrity.
     *
     * @param WpCommentQueryInterface|object|mixed $instance
     *
     * @return bool
     */
    public static function is($instance): bool;

    /**
     * Parse comment query arguments.
     *
     * @param array $args
     *
     * @return array
     */
    public static function parseQueryArgs(array $args = []): array;

    /**
     * Set a built-in class name by type.
     *
     * @param string $type
     * @param string $classname
     *
     * @return void
     */
    public static function setBuiltInClass(string $type, string $classname): void;

    /**
     * Set the defaults list of comment query arguments.
     *
     * @param array $args
     *
     * @return void
     */
    public static function setDefaultArgs(array $args): void;

    /**
     * Set the fallback class.
     *
     * @param string $classname
     *
     * @return void
     */
    public static function setFallbackClass(string $classname): void;

    /**
     * Set list of related comment types.
     *
     * @param array $types
     *
     * @return void
     */
    public static function setTypes(array $types): void;

    /**
     * Gets browser agent.
     *
     * @return string
     */
    public function getAgent(): string;

    /**
     * Gets author name.
     *
     * @return string
     */
    public function getAuthor(): string;

    /**
     * Gets author email.
     *
     * @return string
     */
    public function getAuthorEmail(): string;

    /**
     * Gets author IP.
     *
     * @return string
     */
    public function getAuthorIp(): string;

    /**
     * Gets author website url.
     *
     * @return string
     */
    public function getAuthorUrl(): string;

    /**
     * Gets comment content message.
     *
     * @return string
     */
    public function getContent(): string;

    /**
     * Gets comment creation date.
     *
     * @param boolean $gmt
     *
     * @return string
     */
    public function getDate(bool $gmt = false): string;

    /**
     * Gets comment creation datetime object.
     *
     * @return DateTime
     */
    public function getDateTime(): DateTime;

    /**
     * Gets comment edit url.
     *
     * @return string
     */
    public function getEditUrl(): string;

    /**
     * Gets comment ID.
     *
     * @return int
     */
    public function getId(): int;

    /**
     * Get comment meta.
     *
     * @param string $meta_key
     * @param bool $isSingle
     * @param mixed $default
     *
     * @return mixed
     */
    public function getMeta(string $meta_key, bool $isSingle = false, $default = null);

    /**
     * Gets comment meta multi.
     *
     * @param string $meta_key
     * @param mixed $default
     *
     * @return mixed
     */
    public function getMetaMulti(string $meta_key, $default = null);

    /**
     * Gets comment meta single.
     *
     * @param string $meta_key
     * @param mixed $default
     *
     * @return mixed
     */
    public function getMetaSingle(string $meta_key, $default = null);

    /**
     * Gets parent comment query instance.
     *
     * @return static|null
     */
    public function getQueriedParent(): ?WpCommentQueryInterface;

    /**
     * Gets parent comment ID.
     *
     * @return int
     */
    public function getParentId(): int;

    /**
     * Gets related post query instance.
     *
     * @return WpPostQueryInterface
     */
    public function getQueriedPost(): WpPostQueryInterface;

    /**
     * Gets related post ID.
     *
     * @return int
     */
    public function getPostId(): int;

    /**
     * Gets comment type.
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Gets related user query instance.
     *
     * @return WpUserQueryInterface
     */
    public function getQueriedUser(): WpUserQueryInterface;

    /**
     * Gets related user ID.
     *
     * @return int
     */
    public function getUserId(): int;

    /**
     * Gets related WP_Comment object.
     *
     * @return WP_Comment
     */
    public function getWpComment(): WP_Comment;

    /**
     * Check if comment is approved.
     *
     * @return bool
     */
    public function isApproved(): bool;

    /**
     * Check if comment is considered as spam.
     *
     * @return bool
     */
    public function isSpam(): bool;

    /**
     * Check if comment is in type names.
     *
     * @param string[] $comment_types
     *
     * @return bool
     */
    public function typeIn(array $comment_types): bool;
}