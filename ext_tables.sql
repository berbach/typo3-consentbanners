#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content
(
    ce_consent_module varchar(255) DEFAULT '' NOT NULL,
);
#
# Table structure for table 'tx_consentbanner_domain_model_settings'
#
CREATE TABLE tx_consentbanner_domain_model_settings
(
    title              varchar(255)         DEFAULT ''   NOT NULL,
    description        text,
    layout_type        varchar(20)          DEFAULT ''   NOT NULL,

    accept_all         varchar(30)          DEFAULT ''   NOT NULL,
    confirm_selection  varchar(30)          DEFAULT ''   NOT NULL,
    save_and_close     varchar(30)          DEFAULT ''   NOT NULL,
    advanced_settings  varchar(30)          DEFAULT ''   NOT NULL,
    reject             varchar(30)          DEFAULT ''   NOT NULL,

    privacy_page       int(11) unsigned     DEFAULT '0'  NOT NULL,
    privacy_page_label varchar(255)         DEFAULT ''   NOT NULL,
    confirm_duration   int(11) unsigned     DEFAULT '20' NOT NULL,
    categories         int(11) unsigned     DEFAULT '0'  NOT NULL,
    show_categories    SMALLint(5) unsigned DEFAULT '0'  NOT NULL,
    is_text_link       SMALLint(5) unsigned DEFAULT '0'  NOT NULL,

    deleted            SMALLint(5) unsigned DEFAULT '0'  NOT NULL,
    hidden             SMALLint(5) unsigned DEFAULT '0'  NOT NULL,

);
#
# Table structure for table 'tx_consentbanner_domain_model_category'
#
CREATE TABLE tx_consentbanner_domain_model_category
(

    name              varchar(255)         DEFAULT ''  NOT NULL,
    description       text,
    modules           int(11) unsigned     DEFAULT '0' NOT NULL,
    locked_and_active SMALLint(5) unsigned DEFAULT '0' NOT NULL,
    sorting_foreign   int(11)              DEFAULT '0',
    category_id       int(11)     unsigned DEFAULT NULL,

    deleted           SMALLint(5) unsigned DEFAULT '0' NOT NULL,
    hidden            SMALLint(5) unsigned DEFAULT '0' NOT NULL,

);
#
# Table structure for table 'tx_consentbanner_domain_model_module'
#
CREATE TABLE tx_consentbanner_domain_model_module
(

    name                 varchar(255)         DEFAULT ''  NOT NULL,
    description          text,
    module_target        varchar(255)         DEFAULT ''  NOT NULL,
    placeholder_headline varchar(255),
    placeholder          text,

    accepted_script      text,
    rejected_script      text,

    deleted              smallint(5) unsigned DEFAULT '0' NOT NULL,
    hidden               smallint(5) unsigned DEFAULT '0' NOT NULL,
    sorting_foreign      int(11)              DEFAULT '0',
    module_id            int(11)     unsigned DEFAULT NULL,

--     FOREIGN KEY (category) REFERENCES tx_consentbanner_domain_model_category (uid)
);
#
# Table structure for table 'tx_consentbanner_domain_model_banner'
#
CREATE TABLE tx_consentbanner_domain_model_banner
(
    banner_title                varchar(255)         DEFAULT ''   NOT NULL,
    banner_description          text,
    banner_layout               varchar(20)          DEFAULT ''   NOT NULL,

    user_identification_text    varchar(30)          DEFAULT ''   NOT NULL,
    provider_description_text   varchar(30)          DEFAULT ''   NOT NULL,

    accept_all_text             varchar(30)          DEFAULT ''   NOT NULL,
    confirm_selection_text      varchar(30)          DEFAULT ''   NOT NULL,
    save_and_close_text         varchar(30)          DEFAULT ''   NOT NULL,
    advanced_settings_text      varchar(30)          DEFAULT ''   NOT NULL,
    accept_essential_text       varchar(30)          DEFAULT ''   NOT NULL,

    cookie_infos_show_text      varchar(30)          DEFAULT ''   NOT NULL,
    cookie_infos_close_text     varchar(30)          DEFAULT ''   NOT NULL,

    cookie_name_text            varchar(30)          DEFAULT ''   NOT NULL,
    cookie_lifetime_text        varchar(30)          DEFAULT ''   NOT NULL,
    cookie_provider_text        varchar(30)          DEFAULT ''   NOT NULL,
    cookie_purpose_text         varchar(30)          DEFAULT ''   NOT NULL,
    cookie_description_text     varchar(30)          DEFAULT ''   NOT NULL,

    privacy_link                text,
    imprint_link                text,

    essential_title             varchar(30)          DEFAULT ''   NOT NULL,
    essential_description       text,
    essential_opt_ins           int(11) unsigned     DEFAULT '0'  NOT NULL,

    group_categories            int(11) unsigned     DEFAULT '0'  NOT NULL,

    is_text_link                smallint unsigned DEFAULT '0'  NOT NULL,

    lifetime_banner             int(11) unsigned     DEFAULT '20' NOT NULL,
    lifetime_user_consent       int(11) unsigned     DEFAULT '365' NOT NULL,

    deleted                     smallint unsigned DEFAULT '0'  NOT NULL,
    hidden                      smallint unsigned DEFAULT '0'  NOT NULL,

);

#
# Table structure for table 'tx_consentbanner_domain_model_group_category'
#
CREATE TABLE tx_consentbanner_domain_model_group_category
(

    name              varchar(255)         DEFAULT ''  NOT NULL,
    description       text,
    modules           int(11) unsigned     DEFAULT '0' NOT NULL,
    locked_and_active SMALLint(5) unsigned DEFAULT '0' NOT NULL,
    sorting_foreign   int(11)              DEFAULT '0',
    category_id       int(11)     unsigned DEFAULT NULL,

    deleted           SMALLint(5) unsigned DEFAULT '0' NOT NULL,
    hidden            SMALLint(5) unsigned DEFAULT '0' NOT NULL,

);
#
# Table structure for table 'tx_consentbanner_domain_model_optin_module'
#
CREATE TABLE tx_consentbanner_domain_model_optin_module
(

    name                 varchar(255)         DEFAULT ''  NOT NULL,
    description          text,
    module_target        varchar(255)         DEFAULT ''  NOT NULL,
    placeholder_headline varchar(255),
    placeholder          text,

    accepted_script      text,
    rejected_script      text,

    deleted              smallint(5) unsigned DEFAULT '0' NOT NULL,
    hidden               smallint(5) unsigned DEFAULT '0' NOT NULL,
    sorting_foreign      int(11)              DEFAULT '0',
    module_id            int(11)     unsigned DEFAULT NULL,

);
#
# Table structure for table 'tx_consentbanner_domain_model_consent'
# Define table and fields since it has no TCA
CREATE TABLE tx_consentbanner_domain_model_consent
(
    identification_key varchar(40) DEFAULT '' NOT NULL,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) unsigned DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    -- Weitere Felder hier
    PRIMARY KEY (identification_key)
);
-- #
-- # Table structure for table 'tx_consentbanner_module_categories_mm'
-- #
-- CREATE TABLE tx_consentbanner_module_categories_mm
-- (
--     uid_local       int(11) unsigned DEFAULT '0' NOT NULL,
--     uid_foreign     int(11) unsigned DEFAULT '0' NOT NULL,
--     sorting         int(11) unsigned DEFAULT '0' NOT NULL,
--     sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,
--
--     KEY uid_local (uid_local),
--     KEY uid_foreign (uid_foreign)
-- );
-- #
-- # Table structure for table 'tx_consentbanner_module_categories_mm'
-- #
-- CREATE TABLE tx_consentbanner_categories_banner_mm
-- (
--     uid_local       int(11) unsigned DEFAULT '0' NOT NULL,
--     uid_foreign     int(11) unsigned DEFAULT '0' NOT NULL,
--     sorting         int(11) unsigned DEFAULT '0' NOT NULL,
--     sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,
--
--     KEY uid_local (uid_local),
--     KEY uid_foreign (uid_foreign)
-- );
