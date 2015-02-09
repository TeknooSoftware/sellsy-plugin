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
    protected $settings = [];

    /**
     * Section list in admin panel
     * @var array
     */
    protected $sections = [];

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
    const SUBMIT_NOTIFICATION = 'submitNotification';
    const FORM_NAME = 'formName';
    const DISPLAY_FORM_NAME = 'displayFormName';
    const ENABLE_HTML_CHECK = 'enableHtmlCheck';
    const FIELDS_SELECTED = 'fieldsSelected';
    const MANDATORIES_FIELDS = 'mandatoriesFields';

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
        $element = 'prospect';
        if (isset($this->options[self::OPPORTUNITY_CREATION])) {
            switch ($this->options[self::OPPORTUNITY_CREATION]) {
                case 'prospectOnly':
                case 'prospectOpportunity':
                    $element = 'prospect';
                    break;
            }
        }

        return [
            /* Section Connexion Sellsy */
            self::CONSUMER_TOKEN => [
                'title' => __('Consumer Token', 'wpsellsy'),
                'desc' => '',
                'std' => '',
                'type' => 'text',
                'section' => 'sellsy_connexion',
                'originalKey' => 'WPIconsumer_token' //To be compliant with official Sellsy plugin
            ],
            self::CONSUMER_SECRET => [
                'title' => __('Consumer Secret', 'wpsellsy'),
                'desc' => '',
                'std' => '',
                'type' => 'text',
                'section' => 'sellsy_connexion',
                'originalKey' => 'WPIconsumer_secret' //To be compliant with official Sellsy plugin
            ],
            self::ACCESS_TOKEN => [
                'title' => __('Utilisateur Token', 'wpsellsy'),
                'desc' => '',
                'std' => '',
                'type' => 'text',
                'section' => 'sellsy_connexion',
                'originalKey' => 'WPIutilisateur_token' //To be compliant with official Sellsy plugin
            ],
            self::ACCESS_SECRET => [
                'title' => __('Utilisateur Secret', 'wpsellsy'),
                'desc' => '',
                'std' => '',
                'type' => 'text',
                'section' => 'sellsy_connexion',
                'originalKey' => 'WPIutilisateur_secret' //To be compliant with official Sellsy plugin
            ],
            /* Section Options du plugin */
            self::OPPORTUNITY_CREATION => [
                'title' => __('Créer', 'wpsellsy'),
                'desc' => '',
                'type' => 'radio',
                'std' => '',
                'section' => 'sellsy_options',
                'choices' => [
                    'prospectOnly' => __('Un prospect seulement', 'wpsellsy'),
                    'prospectOpportunity' => __('Un prospect et une opportunité', 'wpsellsy')
                ],
                'originalKey' => 'WPIcreer_prospopp' //To be compliant with official Sellsy plugin
            ],
            self::SUBMIT_NOTIFICATION => [
                'title' => __('Envoyer une copie à', 'wpsellsy'),
                'desc' => '',
                'std' => '',
                'type' => 'text',
                'section' => 'sellsy_options',
                'originalKey' => 'WPIenvoyer_copie' //To be compliant with official Sellsy plugin
            ],
            self::OPPORTUNITY_SOURCE => [
                'title' => __('Nom de la source pour les opportunités', 'wpsellsy'),
                'desc' => __('Vous devez renseigner ce champ si vous souhaitez créer une opportunité en plus d\'un prospect. La source doit exister sur votre compte <a href="https://www.sellsy.com/?_f=prospection_prefs&action=sources" target="_blank">Sellsy.com</a>.' , 'wpsellsy'),
                'std' => '',
                'type' => 'text',
                'section' => 'sellsy_options',
                'originalKey' => 'WPInom_opp_source' //To be compliant with official Sellsy plugin
            ],
            self::FORM_NAME => [
                'title' => __('Nom du formulaire', 'wpsellsy'),
                'desc' => '',
                'std' => '',
                'type' => 'text',
                'section' => 'sellsy_options',
                'originalKey' => 'WPInom_form' //To be compliant with official Sellsy plugin
            ],
            self::DISPLAY_FORM_NAME => [
                'title' => __('Afficher le nom du formulaire', 'wpsellsy'),
                'desc' => __('Vous permet d\'afficher ou de masquer le nom du formulaire inclus dans vos pages et/ou articles via le shortcode.', 'wpsellsy'),
                'type' => 'radio',
                'std' => '',
                'section' => 'sellsy_options',
                'choices' => [
                    'displayTitle' => __('Oui', 'wpsellsy'),
                    'none' => __('Non', 'wpsellsy')
                ],
                'originalKey' => 'WPIaff_form' //To be compliant with official Sellsy plugin
            ],
            /* Section Activer validation Client */
            self::ENABLE_HTML_CHECK => [
                'title' => __('Activer', 'wpsellsy'),
                'desc' => __('La validation Javascript permet de vérifier les informations saisies avant que le formulaire soit soumis au serveur (sans rafraîchissement de la page).', 'wpsellsy'),
                'type' => 'radio',
                'std' => '',
                'section' => 'sellsy_jsValid',
                'choices' => [
                    'enableJsValidation' => __('Oui', 'wpsellsy'),
                    'disableJsValidation' => __('Non', 'wpsellsy')
                ],
                'originalKey' => 'WPIjsValid' //To be compliant with official Sellsy plugin
            ],
            /* Section Champs */
            self::FIELDS_SELECTED => [
                'title' => __('Afficher', 'wpsellsy'),
                'desc' => __('Sélectionner les champs à afficher', 'wpsellsy'),
                'type' => 'multiselect',
                'std' => '',
                'section' => 'sellsy_Champs',
                'choices' => $this->sellsyPlugin->listCustomFields($element),
                'originalKey' => null //Not present in official plugin
            ],
            self::MANDATORIES_FIELDS => [
                'title' => __('Champs obligatoires', 'wpsellsy'),
                'desc' => __('Sélectionner les champs obligatoires', 'wpsellsy'),
                'type' => 'multiselect',
                'std' => '',
                'section' => 'sellsy_Champs',
                'choices' => array_map(
                    function($field){
                        return $field->getName();
                    },
                    $this->sellsyPlugin->listSelectedFields()
                ),
                'originalKey' => null //Not present in official plugin
            ],
        ];
    }

    /**
     * Return the list of available sections in the wordpress admin
     * @return array
     */
    public function loadSections()
    {
        return [
            'sellsy_connexion'	=> __('Connexion à votre compte Sellsy', 'wpsellsy'),
            'sellsy_options' => __('Options du plugin', 'wpsellsy'),
            'sellsy_jsValid' => __('Validation Javascript (requiert jQuery)', 'wpsellsy'),
            'sellsy_Champs' => __('Sélection des champs', 'wpsellsy')
        ];
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
        $defaultsSettings = [
            'id' => 'champ_defaut',
            'title' => 'Champ par défaut',
            'desc' => __('Description par défaut', 'wpsellsy'),
            'std' => '',
            'type' => 'text',
            'section' => 'sellsy_connexion',
            'choices' => [],
            'class'	=> ''
        ];

        $setting = wp_parse_args($setting, $defaultsSettings);

        $fieldSettings = [
            'type' => $setting['type'],
            'id' => $setting['id'],
            'desc' => $setting['desc'],
            'std' => $setting['std'],
            'choices' => $setting['choices'],
            'label_for' => $setting['id'],
            'class' => $setting['class']
        ];

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