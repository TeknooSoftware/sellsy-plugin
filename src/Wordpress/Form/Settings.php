<?php

namespace UniAlteri\Sellsy\Wordpress\Form;

use UniAlteri\Sellsy\Wordpress\OptionsBag;
use UniAlteri\Sellsy\Wordpress\Plugin;

/**
 * Class Settings
 * Class to build setting form in WP to allow admin to manage this plugin
 * @package UniAlteri\Sellsy\Admin
 */
class Settings
{
    /**
     * List of fields in settings forms
     * @var array
     */
    protected $settings = array();

    /**
     * Section list in admin panel
     * @var array
     */
    protected $sections = array();

    /**
     * @var OptionsBag
     */
    protected $options;

    /**
     * @var Plugin
     */
    protected $sellsyPlugin;

    /**
     * Available settings
     */
    const CONSUMER_TOKEN = 'consumerToken';
    const CONSUMER_SECRET = 'consumerSecret';
    const ACCESS_TOKEN = 'accessToken';
    const ACCESS_SECRET = 'accessSecret';
    const OPPORTUNITY_CREATION = 'opportunityCreation';
    const OPPORTUNITY_SOURCE = 'opportunitySource';
    const SPLIT_COLUMNS = 'splitColumns';
    const COLUMNS_CLASS = 'columnsClass';
    const MESSAGE_SENT = 'messageSent';
    const MESSAGE_ERROR = 'messageError';
    const SUBMIT_NOTIFICATION = 'submitNotification';
    const FROM_NOTIFICATION = 'fromNotification';
    const FORM_NAME = 'formName';
    const DISPLAY_FORM_NAME = 'displayFormName';
    const ENABLE_HTML_CHECK = 'enableHtmlCheck';
    const FIELDS_SELECTED = 'fieldsSelected';
    const MANDATORIES_FIELDS = 'mandatoriesFields';
    const FORM_CUSTOM_HEADER = 'customHeader';
    const FORM_CUSTOM_FOOTER = 'customFooter';

    /**
     * Initialize this object
     * @param Plugin $sellsyPlugin
     * @param OptionsBag $options
     */
    public function __construct(Plugin $sellsyPlugin, OptionsBag $options)
    {
        //Register the options bag
        $this->sellsyPlugin = $sellsyPlugin;
        $this->options = $options;

        //Initialize this object
        $this->settings = $this->loadSettings();
        $this->sections = $this->loadSections();

        //If there are no registered options
        $this->initialize(!$options->isDefined());
    }

    /**
     * Return the list of available settings in the wordpress admin
     * @return array
     */
    public function loadSettings()
    {
        //Extract entity type to use to extract custom fields
        $element = 'prospect';
        if (isset($this->options[self::OPPORTUNITY_CREATION])) {
            switch ($this->options[self::OPPORTUNITY_CREATION]) {
                case 'prospectOnly':
                case 'prospectOpportunity':
                    $element = 'prospect';
                    break;
            }
        }

        //Extract usable ordered list of selected fields
        $selectedFieldsList = array_map(
            function($field){
                return $field->getName();
            },
            $this->sellsyPlugin->listSelectedFields()
        );

        //Get available fields
        $availableFields = $this->sellsyPlugin->listCustomFields($element);

        //Reorder them
        $availableOrderedFieldsList = array();
        foreach ($selectedFieldsList as $fieldName=>$name) {
            if (isset($availableFields[$fieldName])) {
                $availableOrderedFieldsList[$fieldName] = $availableFields[$fieldName];
                unset($availableFields[$fieldName]);
            }
        }

        //Add other field at end
        $availableOrderedFieldsList = array_merge($availableOrderedFieldsList, $availableFields);

        return array(
            /* Section Connexion Sellsy */
            self::CONSUMER_TOKEN => array(
                'title' => __('Consumer Token', 'wpsellsy'),
                'desc' => '',
                'std' => '',
                'type' => 'text',
                'section' => 'sellsy_connexion',
                'originalKey' => 'WPIconsumer_token' //To be compliant with official Sellsy plugin
            ),
            self::CONSUMER_SECRET => array(
                'title' => __('Consumer Secret', 'wpsellsy'),
                'desc' => '',
                'std' => '',
                'type' => 'text',
                'section' => 'sellsy_connexion',
                'originalKey' => 'WPIconsumer_secret' //To be compliant with official Sellsy plugin
            ),
            self::ACCESS_TOKEN => array(
                'title' => __('User Token', 'wpsellsy'),
                'desc' => '',
                'std' => '',
                'type' => 'text',
                'section' => 'sellsy_connexion',
                'originalKey' => 'WPIutilisateur_token' //To be compliant with official Sellsy plugin
            ),
            self::ACCESS_SECRET => array(
                'title' => __('User Secret', 'wpsellsy'),
                'desc' => '',
                'std' => '',
                'type' => 'text',
                'section' => 'sellsy_connexion',
                'originalKey' => 'WPIutilisateur_secret' //To be compliant with official Sellsy plugin
            ),
            /* Section Options du plugin */
            self::OPPORTUNITY_CREATION => array(
                'title' => __('Create', 'wpsellsy'),
                'desc' => '',
                'type' => 'radio',
                'std' => '',
                'section' => 'sellsy_options',
                'choices' => array(
                    'prospectOnly' => __('Only a lead', 'wpsellsy'),
                    'prospectOpportunity' => __('A lead with its opportunity', 'wpsellsy')
                ),
                'originalKey' => 'WPIcreer_prospopp' //To be compliant with official Sellsy plugin
            ),
            self::OPPORTUNITY_SOURCE => array(
                'title' => __('Opportunity source names', 'wpsellsy'),
                'desc' => __('You must define this parameter if you must create an opportunity. The source must exist on your <a href="https://www.sellsy.com/?_f=prospection_prefs&action=sources" target="_blank">Sellsy.com</a> account. Several sources can be defined, splited by a comma.' , 'wpsellsy'),
                'std' => '',
                'type' => 'text',
                'section' => 'sellsy_options',
                'originalKey' => 'WPInom_opp_source' //To be compliant with official Sellsy plugin
            ),
            self::FORM_NAME => array(
                'title' => __('Form name', 'wpsellsy'),
                'desc' => '',
                'std' => '',
                'type' => 'text',
                'section' => 'sellsy_display',
                'originalKey' => 'WPInom_form' //To be compliant with official Sellsy plugin
            ),
            self::DISPLAY_FORM_NAME => array(
                'title' => __('Display the form name', 'wpsellsy'),
                'desc' => __('To display the name of the form in your page and article.', 'wpsellsy'),
                'type' => 'radio',
                'std' => '',
                'section' => 'sellsy_display',
                'choices' => array(
                    'displayTitle' => __('Oui', 'wpsellsy'),
                    'none' => __('Non', 'wpsellsy')
                ),
                'originalKey' => 'WPIaff_form' //To be compliant with official Sellsy plugin
            ),
            self::SPLIT_COLUMNS => array(
                'title' => __('Split fields in several columns', 'wpsellsy'),
                'desc' => __('To dispatch fields in several columns. By default, all fields are displayed in a column', 'wpsellsy'),
                'type' => 'text',
                'std' => '1',
                'section' => 'sellsy_display',
                'originalKey' => 'WPIaff_form' //To be compliant with official Sellsy plugin
            ),
            self::COLUMNS_CLASS => array(
                'title' => __('Column HTML classess', 'wpsellsy'),
                'desc' => __('To define the class to use in your HTML column.', 'wpsellsy'),
                'type' => 'text',
                'std' => '',
                'section' => 'sellsy_display',
                'originalKey' => 'WPIaff_form' //To be compliant with official Sellsy plugin
            ),
            self::FORM_CUSTOM_HEADER => array(
                'title' => __('HTML form header', 'wpsellsy'),
                'desc' => __('HTML code to print before the form', 'wpsellsy'),
                'type' => 'textarea',
                'std' => '',
                'section' => 'sellsy_display',
                'originalKey' => null //To be compliant with official Sellsy plugin
            ),
            self::FORM_CUSTOM_FOOTER => array(
                'title' => __('HTML form footer', 'wpsellsy'),
                'desc' => __('HTML code to print after the form', 'wpsellsy'),
                'type' => 'textarea',
                'std' => '',
                'section' => 'sellsy_display',
                'originalKey' => null //To be compliant with official Sellsy plugin
            ),
            self::MESSAGE_SENT => array(
                'title' => __('Confirmation message', 'wpsellsy'),
                'desc' => __('To define the message to display when the lead has been created', 'wpsellsy'),
                'type' => 'textarea',
                'std' => __( 'Thanks, your message has been sent.', 'wpsellsy' ),
                'section' => 'sellsy_display',
                'originalKey' => null //To be compliant with official Sellsy plugin
            ),
            self::MESSAGE_ERROR => array(
                'title' => __('Error message', 'wpsellsy'),
                'desc' => __('To define the message to display when an error has been encounted', 'wpsellsy'),
                'type' => 'textarea',
                'std' => __( 'Your message has not been sent, please check these following fields :', 'wpsellsy' ),
                'section' => 'sellsy_display',
                'originalKey' => null //To be compliant with official Sellsy plugin
            ),
            self::SUBMIT_NOTIFICATION => array(
                'title' => __('Send a notification by email', 'wpsellsy'),
                'desc' => '',
                'std' => '',
                'type' => 'text',
                'section' => 'sellsy_notification',
                'originalKey' => 'WPIenvoyer_copie' //To be compliant with official Sellsy plugin
            ),
            self::FROM_NOTIFICATION => array(
                'title' => __('Email sender', 'wpsellsy'),
                'desc' => '',
                'std' => '',
                'type' => 'text',
                'section' => 'sellsy_notification',
                'originalKey' => null //Not present in official plugin
            ),
            /* Section Activer validation Client */
            self::ENABLE_HTML_CHECK => array(
                'title' => __('Enable HTML5 validation', 'wpsellsy'),
                'desc' => __('Enable frontside validation build on HTML5 capacity (required, email).', 'wpsellsy'),
                'type' => 'radio',
                'std' => '',
                'section' => 'sellsy_frontValid',
                'choices' => array(
                    'enableJsValidation' => __('Oui', 'wpsellsy'),
                    'disableJsValidation' => __('Non', 'wpsellsy')
                ),
                'originalKey' => 'WPIjsValid' //To be compliant with official Sellsy plugin
            ),
            /* Section Champs */
            self::FIELDS_SELECTED => array(
                'title' => __('Fields', 'wpsellsy'),
                'desc' => __('Select and sort fields to display in the form', 'wpsellsy'),
                'type' => 'multiselect',
                'std' => '',
                'section' => 'sellsy_Champs',
                'choices' => $availableOrderedFieldsList,
                'originalKey' => null //Not present in official plugin
            ),
            self::MANDATORIES_FIELDS => array(
                'title' => __('Mandatories Fields', 'wpsellsy'),
                'desc' => __('Select mandatories fields in fhe form', 'wpsellsy'),
                'type' => 'multiselect',
                'std' => '',
                'section' => 'sellsy_Champs',
                'choices' => $selectedFieldsList,
                'originalKey' => null //Not present in official plugin
            ),
        );
    }

    /**
     * Return the list of available sections in the wordpress admin
     * @return array
     */
    public function loadSections()
    {
        return array(
            'sellsy_connexion'	=> __('Connection to Sellsy Account', 'wpsellsy'),
            'sellsy_options' => __('Plugin options', 'wpsellsy'),
            'sellsy_display' => __('Display options', 'wpsellsy'),
            'sellsy_notification' => __('Notification', 'wpsellsy'),
            'sellsy_frontValid' => __('Frontside validation', 'wpsellsy'),
            'sellsy_Champs' => __('Fields selection', 'wpsellsy')
        );
    }

    /**
     * Return sections configured by this object
     * @return array
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * Initialize options of this plugin with default value
     * @param $forceDefault
     * @return $this;
     */
    public function initialize($forceDefault=false)
    {
        //Set default value in options bag
        foreach ($this->settings as $id=>&$params) {
            if (true === $forceDefault && isset($params['std'])) { //by pass fields without default value
                $this->options[$id] = $params['std'];
            } elseif (isset($params['originalKey'])) {
                //Option defined in old format, convert it to new format
                $originalKey = $params['originalKey'];
                if (!isset($this->options[$id]) && isset($this->options[$originalKey])) {
                    $this->options[$id] = $this->options[$originalKey];
                    unset($this->options[$originalKey]);
                }
            }
        }

        //Save option bag in wordpress
        $this->options->save();

        return $this;
    }

    /**
     * Method to build form in wordpress admin to manage this plugin
     * @param callable $displaySectionCallback
     * @param callable $displayFieldsCallback
     * @return $this
     */
    public function buildForms($displaySectionCallback, $displayFieldsCallback)
    {
        if (!is_callable($displaySectionCallback) || !is_callable($displayFieldsCallback)) {
            return $this;
        }

        foreach ($this->sections as $slug=>$title) {
            \add_settings_section($slug, $title, $displaySectionCallback, 'slswp-admPage');
        }

        foreach ($this->settings as $id=>$setting) {
            $setting['id'] = $id;
            $this->createInput($setting, $displayFieldsCallback);
        }

        return $this;
    }

    /**
     * Create input from a setting in the form
     * @param array $setting
     * @param callable $displayCallback
     * @return $this
     */
    public function createInput(&$setting, $displayCallback)
    {
        $defaultsSettings = array(
            'id' => 'champ_defaut',
            'title' => 'Champ par défaut',
            'desc' => __('Description par défaut', 'wpsellsy'),
            'std' => '',
            'type' => 'text',
            'section' => 'sellsy_connexion',
            'choices' => array(),
            'class'	=> ''
        );

        $setting = wp_parse_args($setting, $defaultsSettings);

        $fieldSettings = array(
            'type' => $setting['type'],
            'id' => $setting['id'],
            'desc' => $setting['desc'],
            'std' => $setting['std'],
            'choices' => $setting['choices'],
            'label_for' => $setting['id'],
            'class' => $setting['class']
        );

        add_settings_field(
            $setting['id'],
            $setting['title'],
            $displayCallback,
            'slswp-admPage',
            $setting['section'],
            $fieldSettings
        );

        return $this;
    }
}