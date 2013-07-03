# Quote all table names with '{' and '}', and prefix all system tables with 'core.'

# DROP TABLE IF EXISTS `user`;
CREATE TABLE `{user}` (
    `id`                        INT(10) UNSIGNED    NOT NULL auto_increment,    # `id`
    `username`                  VARCHAR(16)         NOT NULL,                   # `username`
    `time_created`              INT UNSIGNED        NOT NULL,                   # `created_at`
    `time_updated`              INT UNSIGNED        NOT NULL,                   # `updated_at`
    `name`                      VARCHAR(255),                                   # `name`
    `bio_raw`                   TEXT,                                           # `bio_raw`
  # `seen_notification_id`      INT(10) unsigned    NOT NULL DEFAULT 0,         # `seen_notification_id`
    `time_last_posted`          INT UNSIGNED        DEFAULT NULL,               # `last_posted_at`
    `email`                     VARCHAR(255)        NOT NULL,                   # `email`
    `password_hash`             VARCHAR(64),                                    # `password_hash`
    `salt`                      VARCHAR(32),                                    # `salt`
    `active`                    boolean,                                        # `active`
  # `username_lower`            VARCHAR(20)         NOT NULL,                   # `username_lower`
    `auth_token`                VARCHAR(32),                                    # `auth_token`
    `time_last_seen`            INT UNSIGNED        DEFAULT NULL,               # `last_seen_at`
    `website`                   VARCHAR(255),                                   # `website`
    `admin`                     boolean             NOT NULL DEFAULT false,
    `avatar`                    VARCHAR(128)        DEFAULT NULL,               # added
  # `moderator`                 boolean             NOT NULL DEFAULT false,     # `moderator`
  # `last_emailed_at`           INT UNSIGNED        DEFAULT NULL,               # `last_emailed_at`
  # `email_digests`             boolean             NOT NULL DEFAULT true,      # `email_digests`
  # `trust_level_id`            INT(10) unsigned    NOT NULL DEFAULT 1,         # `trust_level_id`
    `bio_cooked`                TEXT,                                           # `bio_cooked`
  # `email_private_messages`    boolean             DEFAULT true,               # `email_private_messages`
  # `email_direct`              boolean             NOT NULL DEFAULT true,      # `email_direct`
  # `approved`                  boolean             NOT NULL DEFAULT false,     # `approved`
  # `approved_by_id`            INT(10) unsigned,                               # `approved_by_id`
  # `approved_at`               DATETIME,                                       # `approved_at`
  # `topics_entered`            INT(10) unsigned    NOT NULL DEFAULT 0,         # `topics_entered`
  # `posts_read_count`          INT(10) unsigned    NOT NULL DEFAULT 0,         # `posts_read_count`
  # `digest_after_days`         INT(10) unsigned    NOT NULL DEFAULT 0,         # `digest_after_days`
  # `time_previous_visit`       INT UNSIGNED,                                   # `previous_visit_at`
    PRIMARY KEY (`id`),
    UNIQUE KEY (`name`)
)ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

# DROP TABLE IF EXISTS `category`;
CREATE TABLE `{category}` (
    `id`                INT(10) unsigned    NOT NULL auto_increment,
    `name`              VARCHAR(32)         NOT NULL,
    `color`             CHAR(6)             NOT NULL DEFAULT 'AB9364',
  # `topic_id`          INT(10) unsigned    DEFAULT NULL,
    `last_topic_id`     INT(10) unsigned    DEFAULT NULL,
    `top1_topic_id`     INT(10) unsigned    DEFAULT NULL,
    `top2_topic_id`     INT(10) unsigned    DEFAULT NULL,
  # `top1_user_id`      INT(10) unsigned    DEFAULT NULL,
  # `top2_user_id`      INT(10) unsigned    DEFAULT NULL,
    `topic_count`       INT(10) unsigned    NOT NULL DEFAULT 0,
    `time_created`      INT UNSIGNED        NOT NULL,                           # `created_at`
    `time_updated`      INT UNSIGNED        NOT NULL,                           # `updated_at`
    `user_id`           INT(10) unsigned    NOT NULL,
    `topics_year`       INT(10) unsigned    NOT NULL DEFAULT 0,
    `topics_month`      INT(10) unsigned    NOT NULL DEFAULT 0,
    `topics_week`       INT(10) unsigned    NOT NULL DEFAULT 0,
    `slug`              VARCHAR(255)        NOT NULL DEFAULT '',
    PRIMARY KEY (`id`),
    UNIQUE KEY (`name`)
)ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

# DROP TABLE IF EXISTS `topic`;
CREATE TABLE `{topic}` (
    `id`                    INT(10) unsigned    NOT NULL auto_increment,
    `title`                 VARCHAR(255)        NOT NULL,
    `time_last_posted`      INT UNSIGNED        NOT NULL,                       # `last_posted_at`
    `time_created`          INT UNSIGNED        NOT NULL,                       # `created_at`
    `time_updated`          INT UNSIGNED        NOT NULL,                       # `updated_at`
    `views`                 INT(10) unsigned    NOT NULL DEFAULT 0,
    `posts_count`           INT(10) unsigned    NOT NULL DEFAULT 0,
    `user_id`               INT(10) unsigned    NOT NULL,
    `last_post_user_id`     INT(10) unsigned    NOT NULL DEFAULT 0,
    `reply_count`           INT(10) unsigned    NOT NULL DEFAULT 0,
    `featured_user1_id`     INT(10) unsigned    DEFAULT NULL,
    `featured_user2_id`     INT(10) unsigned    DEFAULT NULL,
    `featured_user3_id`     INT(10) unsigned    DEFAULT NULL,
    `featured_user4_id`     INT(10) unsigned    DEFAULT NULL,
  # `avg_time`              INT(10) unsigned    DEFAULT NULL,
    `time_deleted`          INT UNSIGNED        DEFAULT NULL,                   # `deleted_at`
  # `highest_post_number`   INT(10) unsigned    NOT NULL DEFAULT 0,
  # `image_url`             VARCHAR(255)        NOT NULL ,
    `off_topic_count`       INT(10) unsigned    NOT NULL DEFAULT 0,
    `offensive_count`       INT(10) unsigned    NOT NULL DEFAULT 0,
    `like_count`            INT(10) unsigned    NOT NULL DEFAULT 0,
  # `incoming_link_count`   INT(10) unsigned    NOT NULL DEFAULT 0,
    `bookmark_count`        INT(10) unsigned    NOT NULL DEFAULT 0,
    `star_count`            INT(10) unsigned    NOT NULL DEFAULT 0,
    `category_id`           INT(10) unsigned    NOT NULL,
    `visible`               BOOLEAN             NOT NULL DEFAULT true,
  # `moderator_posts_count` INT(10) unsigned    NOT NULL DEFAULT 0,
    `closed`                BOOLEAN             NOT NULL DEFAULT false,
    `pinned`                BOOLEAN             NOT NULL DEFAULT false,
  # `archived`              BOOLEAN             NOT NULL DEFAULT false,
  # `bumped_at`             DATETIME            NOT NULL DEFAULT 0,
  # `sub_tag`               VARCHAR(255),
  # `has_best_of`           BOOLEAN             NOT NULL DEFAULT false,
    `meta_data`             VARCHAR(255),
  # `vote_count`            BOOLEAN             NOT NULL DEFAULT false,
  # `archetype`             VARCHAR(255)        NOT NULL,
    PRIMARY KEY (`id`),
    KEY (`time_last_posted`),
    KEY (`time_deleted`)
)ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

# DROP TABLE IF EXISTS `post`;
CREATE TABLE `{post}` (
    `id`                        INT(10) unsigned    NOT NULL auto_increment,
    `user_id`                   INT(10) unsigned    NOT NULL,
    `topic_id`                  INT(10) unsigned    NOT NULL,
  # `reply_to_post_id`          INT(10) unsigned    DEFAULT NULL,
    `post_number`               INT(10) unsigned    COMMENT 'position in the topic',
  # `reply_to_post_number`      INT(10) unsigned    DEFAULT NULL,
    `raw`                       TEXT                NOT NULL,
    `cooked`                    TEXT                NOT NULL,
    `time_created`              INT UNSIGNED        NOT NULL,                   # `created_at`
    `time_updated`              INT UNSIGNED        NOT NULL,                   # `updated_at`
  # `cached_version`            INT(10) unsigned    NOT NULL DEFAULT 1,
    `reply_count`               INT(10) unsigned    NOT NULL DEFAULT 0,
    `quote_count`               INT(10) unsigned    NOT NULL DEFAULT 0,
    `time_deleted`              INT UNSIGNED        DEFAULT NULL,               # `deleted_at`
    `off_topic_count`           INT(10) unsigned    NOT NULL DEFAULT 0,
    `offensive_count`           INT(10) unsigned    NOT NULL DEFAULT 0,
    `like_count`                INT(10) unsigned    NOT NULL DEFAULT 0,
  # `incoming_link_count`       INT(10) unsigned    NOT NULL DEFAULT 0,
    `bookmark_count`            INT(10) unsigned    NOT NULL DEFAULT 0,
  # `avg_time`                  INT(10) unsigned,
  # `score`                     DOUBLE PRECISION,
  # `reads`                     INT(10) unsigned    NOT NULL DEFAULT 0,
  # `post_type`                 INT(10) unsigned    NOT NULL DEFAULT 1,
  # `vote_count`                INT(10) unsigned    NOT NULL DEFAULT 0,
  # `sort_order`                INT(10),
  # `last_editor_id`            INT(10),
    PRIMARY KEY (`id`),
    KEY (`topic_id`),
    KEY (`post_number`),
    KEY (`time_deleted`)
)ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

# DROP TABLE IF EXISTS `post_reply`;
CREATE TABLE `{post_reply}` (
    `id`                        INT(10) UNSIGNED    NOT NULL auto_increment,
    `post_id`                   INT(10) UNSIGNED    NOT NULL,
    `reply_to_post_id`          INT(10) UNSIGNED    NOT NULL,
    `time_created`              INT UNSIGNED        NOT NULL,
    PRIMARY KEY (`post_id`),
    KEY (`reply_to_post_id`),
    UNIQUE KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

# DROP TABLE IF EXISTS `topic_user`;
CREATE TABLE `{topic_user}` (
    `id`                        INT(10) UNSIGNED    NOT NULL auto_increment,
    `user_id`                   INT(10) UNSIGNED    NOT NULL,
    `topic_id`                  INT(10) UNSIGNED    NOT NULL,
    `starred`                   BOOLEAN             NOT NULL DEFAULT false,
    `posted`                    BOOLEAN             NOT NULL DEFAULT false,
    `last_read_post_number`     INT(10) UNSIGNED    NOT NULL DEFAULT 1,
    `seen_post_count`           INT(10) UNSIGNED    NOT NULL DEFAULT 0,
    `time_starred`              INT UNSIGNED        NOT NULL,                   # `starred_at`
    `time_muted`                INT UNSIGNED        NOT NULL,                   # `muted_at`
    `time_last_visited`         INT UNSIGNED        NOT NULL,                   # `last_visited_at`
    `notification_level`        INT UNSIGNED        NOT NULL,
  # `first_visited_at`          timestamp without time zone,
  # `notifications`             integer DEFAULT 2,
  # `notifications_changed_at`  timestamp without time zone,
  # `notifications_reason_id`   integer,
    PRIMARY KEY (`id`),
    KEY (`user_id`),
    KEY (`topic_id`),
    KEY (`notification_level`),
    UNIQUE KEY (`user_id`, `topic_id`)
)ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

# DROP TABLE IF EXISTS `post_action`;
CREATE TABLE `{post_action}` (
    `id`                        INT(10) UNSIGNED    NOT NULL auto_increment,
    `post_id`                   INT(10) UNSIGNED    NOT NULL,
    `user_id`                   INT(10) UNSIGNED    NOT NULL,
    `post_action_type_id`       INT(10) UNSIGNED    NOT NULL,
    `time_deleted`              INT UNSIGNED        DEFAULT NULL,               # `deleted_at`
    `time_created`              INT UNSIGNED        NOT NULL,                   # `created_at` 
    `time_updated`              INT UNSIGNED        NOT NULL,                   # `updated_at`
    PRIMARY KEY (`id`),
    KEY (`post_id`, `user_id`, `post_action_type_id`),
    KEY (`post_id`),
    KEY (`user_id`),
    KEY (`post_action_type_id`),
    KEY (`time_deleted`)
)ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

# DROP TABLE IF EXISTS `post_action_type`;
CREATE TABLE `{post_action_type}` (
    `id`                        INT(10) UNSIGNED    NOT NULL,
    `name_key`                  VARCHAR(50)         NOT NULL,
    `is_flag`                   BOOLEAN             NOT NULL DEFAULT false,
    `icon`                      VARCHAR(20),
    `time_created`              INT UNSIGNED        NOT NULL,                   # `created_at`
    `time_updated`              INT UNSIGNED        NOT NULL,                   # `updated_at`
    PRIMARY KEY (`id`),
    KEY (`is_flag`)
)ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

# DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `{notifications}` (
    `id`                        INT(10) UNSIGNED    NOT NULL auto_increment,
    `notification_type`         INT(10) UNSIGNED    NOT NULL,
    `user_id`                   INT(10) UNSIGNED    NOT NULL,
    `data`                      VARCHAR(255)        NOT NULL,
    `read`                      BOOLEAN             NOT NULL DEFAULT false,
    `time_created`              INT UNSIGNED        NOT NULL,                   # `created_at`
    `time_updated`              INT UNSIGNED        NOT NULL,                   # `updated_at`
    `topic_id`                  INT(10) UNSIGNED,
    `post_number`               INT(10) UNSIGNED,
    `post_action_id`            INT(10) UNSIGNED,
    PRIMARY KEY (`id`),
    KEY (`user_id`),
    KEY (`read`)
)ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

# DROP TABLE IF EXISTS `user_action`;
CREATE TABLE `{user_action}` (
    `id`                        INT(10) UNSIGNED    NOT NULL auto_increment,
    `action_type`               INT(10) UNSIGNED    NOT NULL,
    `user_id`                   INT(10) UNSIGNED    NOT NULL,
    `target_topic_id`           INT(10) UNSIGNED    NOT NULL,
    `target_post_id`            INT(10) UNSIGNED    NOT NULL,
    `target_user_id`            INT(10) UNSIGNED    NOT NULL,
    `acting_user_id`            INT(10) UNSIGNED    NOT NULL,
    `time_created`              INT(10) UNSIGNED    NOT NULL,
    `time_updated`              INT(10) UNSIGNED    NOT NULL,
    PRIMARY KEY (`id`),
    KEY (`user_id`),
    KEY (`action_type`),
    KEY (`target_user_id`)
)ENGINE=InnoDB DEFAULT CHARACTER SET utf8;
# `action_type`:
# USER_ACTION_BOOKMARK      = 1;
# USER_ACTION_LIKE          = 2;
# USER_ACTION_STAR          = 3;
# USER_ACTION_RESPONSE      = 4;