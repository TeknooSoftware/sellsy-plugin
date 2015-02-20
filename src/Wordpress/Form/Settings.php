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
                'title' => __('Utilisateur Token', 'wpsellsy'),
                'desc' => '',
                'std' => '',
                'type' => 'text',
                'section' => 'sellsy_connexion',
                'originalKey' => 'WPIutilisateur_token' //To be compliant with official Sellsy plugin
            ),
            self::ACCESS_SECRET => array(
                'title' => __('Utilisateur Secret', 'wpsellsy'),
                'desc' => '',
                'std' => '',
                'type' => 'text',
                'section' => 'sellsy_connexion',
                'originalKey' => 'WPIutilisateur_secret' //To be compliant with official Sellsy plugin
            ),
            /* Section Options du plugin */
            self::OPPORTUNITY_CREATION => array(
                'title' => __('Créer', 'wpsellsy'),
                'desc' => '',
                'type' => 'radio',
                'std' => '',
                'section' => 'sellsy_options',
                'choices' => array(
                    'prospectOnly' => __('Un prospect seulement', 'wpsellsy'),
                    'prospectOpportunity' => __('Un prospect et une opportunité', 'wpsellsy')
                ),
                'originalKey' => 'WPIcreer_prospopp' //To be compliant with official Sellsy plugin
            ),
            self::OPPORTUNITY_SOURCE => array(
                'title' => __('Nom des sources pour les opportunités', 'wpsellsy'),
                'desc' => __('Vous devez renseigner ce champ si vous souhaitez créer une opportunité en plus d\'un prospect. La source doit exister sur votre compte <a href="https://www.sellsy.com/?_f=prospection_prefs&action=sources" target="_blank">Sellsy.com</a>. Pour renseigner plusieurs sources, séparez-les par des virgules' , 'wpsellsy'),
                'std' => '',
                'type' => 'text',
                'section' => 'sellsy_options',
                'originalKey' => 'WPInom_opp_source' //To be compliant with official Sellsy plugin
            ),
            self::FORM_NAME => array(
                'title' => __('Nom du formulaire', 'wpsellsy'),
                'desc' => '',
                'std' => '',
                'type' => 'text',
                'section' => 'sellsy_display',
                'originalKey' => 'WPInom_form' //To be compliant with official Sellsy plugin
            ),
            self::DISPLAY_FORM_NAME => array(
                'title' => __('Afficher le nom du formulaire', 'wpsellsy'),
                'desc' => __('Vous permet d\'afficher ou de masquer le nom du formulaire inclus dans vos pages et/ou articles via le shortcode.', 'wpsellsy'),
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
                'title' => __('Diviser par colonne', 'wpsellsy'),
                'desc' => __('Vous permet d\'afficher le formulaire dans plusieurs colonnes.', 'wpsellsy'),
                'type' => 'text',
                'std' => '1',
                'section' => 'sellsy_display',
                'originalKey' => 'WPIaff_form' //To be compliant with official Sellsy plugin
            ),
            self::COLUMNS_CLASS => array(
                'title' => __('Class HTML des colonnes', 'wpsellsy'),
                'desc' => __('Vous permet de spécifier les class HTML à utiliser pour vos colonnes', 'wpsellsy'),
                'type' => 'text',
                'std' => '',
                'section' => 'sellsy_display',
                'originalKey' => 'WPIaff_form' //To be compliant with official Sellsy plugin
            ),
            self::FORM_CUSTOM_HEADER => array(
                'title' => __('En tête HTML du formulaire', 'wpsellsy'),
                'desc' => __('Vous permet de définir du code HTML au début du formulaire', 'wpsellsy'),
                'type' => 'textarea',
                'std' => '',
                'section' => 'sellsy_display',
                'originalKey' => null //To be compliant with official Sellsy plugin
            ),
            self::FORM_CUSTOM_FOOTER => array(
                'title' => __('Pied HTML du formulaire', 'wpsellsy'),
                'desc' => __('Vous permet de définir du code HTML à la fin du formulaire', 'wpsellsy'),
                'type' => 'textarea',
                'std' => '',
                'section' => 'sellsy_display',
                'originalKey' => null //To be compliant with official Sellsy plugin
            ),
            self::MESSAGE_SENT => array(
                'title' => __('Message de confirmation', 'wpsellsy'),
                'desc' => __('Vous permet de spécifier le message de confirmation lorsque le message a été envoyé', 'wpsellsy'),
                'type' => 'textarea',
                'std' => __( 'Votre message a été envoyé. Merci de votre visite.', 'wpsellsy' ),
                'section' => 'sellsy_display',
                'originalKey' => null //To be compliant with official Sellsy plugin
            ),
            self::MESSAGE_ERROR => array(
                'title' => __('Message d\'erreur', 'wpsellsy'),
                'desc' => __('Vous permet de spécifier l\'en-tête du message d\'erreur', 'wpsellsy'),
                'type' => 'textarea',
                'std' => __( 'Votre message n\'a pas été envoyé, vérifiez la saisie des champs suivant :', 'wpsellsy' ),
                'section' => 'sellsy_display',
                'originalKey' => null //To be compliant with official Sellsy plugin
            ),
            self::SUBMIT_NOTIFICATION => array(
                'title' => __('Envoyer une copie à', 'wpsellsy'),
                'desc' => '',
                'std' => '',
                'type' => 'text',
                'section' => 'sellsy_notification',
                'originalKey' => 'WPIenvoyer_copie' //To be compliant with official Sellsy plugin
            ),
            self::FROM_NOTIFICATION => array(
                'title' => __('Expediteur', 'wpsellsy'),
                'desc' => '',
                'std' => '',
                'type' => 'text',
                'section' => 'sellsy_notification',
                'originalKey' => null //Not present in official plugin
            ),
            /* Section Activer validation Client */
            self::ENABLE_HTML_CHECK => array(
                'title' => __('Activer', 'wpsellsy'),
                'desc' => __('La validation Javascript permet de vérifier les informations saisies avant que le formulaire soit soumis au serveur (sans rafraîchissement de la page).', 'wpsellsy'),
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
                'title' => __('Afficher', 'wpsellsy'),
                'desc' => __('Sélectionner les champs à afficher', 'wpsellsy'),
                'type' => 'multiselect',
                'std' => '',
                'section' => 'sellsy_Champs',
                'choices' => $availableOrderedFieldsList,
                'originalKey' => null //Not present in official plugin
            ),
            self::MANDATORIES_FIELDS => array(
                'title' => __('Champs obligatoires', 'wpsellsy'),
                'desc' => __('Sélectionner les champs obligatoires. (Enregistrer pour rafraichir la liste)', 'wpsellsy'),
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
            'sellsy_connexion'	=> __('Connexion à votre compte Sellsy', 'wpsellsy'),
            'sellsy_options' => __('Options du plugin', 'wpsellsy'),
            'sellsy_display' => __('Options d\'affichage', 'wpsellsy'),
            'sellsy_notification' => __('Notification', 'wpsellsy'),
            'sellsy_frontValid' => __('Validation côté client', 'wpsellsy'),
            'sellsy_Champs' => __('Sélection des champs', 'wpsellsy')
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