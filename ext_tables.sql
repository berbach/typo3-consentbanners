#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content
(
    ce_consent_module varchar(255) DEFAULT '' NOT NULL,
);
#
# Table structure for table 'tx_consentbanners_domain_model_category'
#
CREATE TABLE tx_consentbanners_domain_model_settings
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

    deleted            SMALLint(5) unsigned DEFAULT '0'  NOT NULL,
    hidden             SMALLint(5) unsigned DEFAULT '0'  NOT NULL,

);
#
# Table structure for table 'tx_consentbanners_domain_model_category'
#
CREATE TABLE tx_consentbanners_domain_model_category
(

    name              varchar(255)         DEFAULT ''  NOT NULL,
    description       text,
    modules           int(11) unsigned     DEFAULT '0' NOT NULL,
    locked_and_active SMALLint(5) unsigned DEFAULT '0' NOT NULL,

    deleted           SMALLint(5) unsigned DEFAULT '0' NOT NULL,
    hidden            SMALLint(5) unsigned DEFAULT '0' NOT NULL,

);
#
# Table structure for table 'tx_consentbanners_domain_model_module'
#
CREATE TABLE tx_consentbanners_domain_model_module
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

--     FOREIGN KEY (category) REFERENCES tx_consentbanners_domain_model_category (uid)
);
#
# Table structure for table 'tx_consentbanners_module_categories_mm'
#
CREATE TABLE tx_consentbanners_module_categories_mm
(
    uid_local       int(11) unsigned DEFAULT '0' NOT NULL,
    uid_foreign     int(11) unsigned DEFAULT '0' NOT NULL,
    sorting         int(11) unsigned DEFAULT '0' NOT NULL,
    sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

    KEY uid_local (uid_local),
    KEY uid_foreign (uid_foreign)
);
#
# Table structure for table 'tx_consentbanners_module_categories_mm'
#
CREATE TABLE tx_consentbanners_categories_banner_mm
(
    uid_local       int(11) unsigned DEFAULT '0' NOT NULL,
    uid_foreign     int(11) unsigned DEFAULT '0' NOT NULL,
    sorting         int(11) unsigned DEFAULT '0' NOT NULL,
    sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

    KEY uid_local (uid_local),
    KEY uid_foreign (uid_foreign)
);
